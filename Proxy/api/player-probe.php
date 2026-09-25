<?php
/**
 * READ-ONLY diagnostic. Proxies one whitelisted upstream GET with the caller's
 * own auth headers and returns the response's shape (status, content type, and
 * the exact JSON keys) so the VIP/balance mapper can be reconciled against live
 * data. It never decrypts anything and redacts token/password-ish fields.
 *
 * Access: admin session OR ?key=<admin key>. Delete this file if unused.
 */

require_once dirname(__DIR__) . '/admin/store.php';

$app = config_load();
$admin = is_array($app['admin'] ?? null) ? $app['admin'] : [];

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

function probe_out(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

$keyOk = false;
$given = (string) ($_GET['key'] ?? ($_SERVER['HTTP_X_ADMIN_KEY'] ?? ''));
if ($given !== '' && (string) ($admin['key'] ?? '') !== '') {
    $keyOk = hash_equals((string) $admin['key'], $given);
}
$sessOk = false;
if (!$keyOk) {
    session_name((string) ($admin['cookie'] ?? 'px_sid'));
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'secure' => $secure, 'samesite' => 'Lax']);
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $sessOk = !empty($_SESSION['px_admin']);
}
if (!$keyOk && !$sessOk) {
    probe_out(403, ['ok' => false, 'error' => 'Append ?key=<admin key> or sign in to the admin panel.']);
}

$path = (string) ($_GET['path'] ?? '/wps/member/info');
if (!preg_match('#^/wps/(member|wallets|agent|system)/[A-Za-z0-9/_.-]*$#', $path)) {
    probe_out(400, ['ok' => false, 'error' => 'Only whitelisted /wps/{member|wallets|agent|system}/... GET paths.']);
}

/** Recursively drop secret-ish keys so the preview can never leak a token. */
function probe_redact($v)
{
    if (!is_array($v)) {
        return $v;
    }
    $out = [];
    foreach ($v as $k => $val) {
        if (is_string($k) && preg_match('/token|password|secret|authorization|jwt/i', $k)) {
            $out[$k] = '***redacted***';
            continue;
        }
        $out[$k] = probe_redact($val);
    }
    return $out;
}

$up = rtrim((string) ($app['upstream'] ?? ''), '/');
$result = ['request' => ['method' => 'GET', 'path' => $path], 'ok' => false];
if ($up !== '' && function_exists('curl_init')) {
    $headers = ['Accept: application/json,text/plain,*/*'];
    foreach (['Cookie' => 'HTTP_COOKIE', 'Authorization' => 'HTTP_AUTHORIZATION',
        'Merchant' => 'HTTP_MERCHANT', 'Device' => 'HTTP_DEVICE', 'Language' => 'HTTP_LANGUAGE',
        'X-Gateway-Version' => 'HTTP_X_GATEWAY_VERSION', 'Encryption' => 'HTTP_ENCRYPTION'] as $h => $sk) {
        $val = (string) ($_SERVER[$sk] ?? '');
        if ($val !== '') {
            $headers[] = $h . ': ' . $val;
        }
    }
    $ch = curl_init($up . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => (string) ($app['user_agent'] ?? 'Mozilla/5.0'),
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ct = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    $result['upstream'] = ['status' => $status, 'content_type' => $ct];
    $result['ok'] = ($body !== false);
    $decoded = json_decode((string) $body, true);
    $result['is_json'] = is_array($decoded);
    if (is_array($decoded)) {
        $result['keys'] = array_keys($decoded);
        if (isset($decoded['value']) && is_array($decoded['value'])) {
            $result['value_keys'] = array_keys($decoded['value']);
        }
        $result['preview'] = substr(json_encode(probe_redact($decoded), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 0, 2000);
    } else {
        $result['preview'] = substr((string) $body, 0, 400);
    }
}
probe_out(200, $result);
