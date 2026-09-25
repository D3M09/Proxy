<?php
/**
 * One-shot credential handoff for the /player-login page.
 *
 * The page itself never contains the password. After the admin's browser loads
 * /player-login?token=..., the page holds only a short-lived single-use handoff
 * id and exchanges it here, exactly once, for {username, password}. Requires the
 * admin session; the handoff is burned under the store lock.
 */

require_once dirname(__DIR__) . '/admin/store.php';

$app = config_load();
$admin = is_array($app['admin'] ?? null) ? $app['admin'] : [];
session_name((string) ($admin['cookie'] ?? 'px_sid'));
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'secure' => $secure, 'samesite' => 'Lax']);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

function cred_reply(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    cred_reply(405, ['ok' => false, 'error' => 'method']);
}
if (empty($_SESSION['px_admin'])) {
    cred_reply(403, ['ok' => false, 'error' => 'auth']);
}
$site = (string) ($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '');
if ($site !== '' && $site !== 'same-origin' && $site !== 'none') {
    cred_reply(403, ['ok' => false, 'error' => 'origin']);
}

$in = json_decode((string) file_get_contents('php://input'), true);
$h = is_array($in) ? (string) ($in['h'] ?? '') : '';
$row = player_token_burn($h);
if (!$row) {
    cred_reply(410, ['ok' => false, 'message' => 'This login link was already used or has expired.']);
}

$username = (string) ($row['username'] ?? '');
$player = null;
foreach (players_load() as $p) {
    if (strcasecmp((string) ($p['username'] ?? ''), $username) === 0) {
        $player = $p;
        break;
    }
}
if ($player === null) {
    cred_reply(404, ['ok' => false, 'message' => 'Player not found.']);
}

// Decrypt in memory only; never persisted, never logged, sent once.
$password = vault_decrypt($player['vault'] ?? null);
admin_audit_append([
    'action' => 'player_credential_handoff',
    'admin'  => (string) ($_SESSION['px_user'] ?? ''),
    'target' => $username,
    'ip'     => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
]);
if ($password === null || $password === '') {
    cred_reply(409, ['ok' => false, 'message' => 'No reusable password is stored for this account. Use Copy login ID.']);
}

cred_reply(200, ['ok' => true, 'username' => $username, 'password' => $password]);
