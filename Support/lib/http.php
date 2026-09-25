<?php

declare(strict_types=1);

/** True when the request arrived over HTTPS (directly or behind a proxy). */
function is_https(): bool
{
    if (is_https_forced_by_config()) {
        return true;
    }
    $https = $_SERVER['HTTPS'] ?? '';
    if ($https !== '' && strtolower((string) $https) !== 'off') {
        return true;
    }
    if (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
        return true;
    }
    return (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
}

/** Set to true in config.php if TLS terminates in front of PHP without forwarded headers. */
function is_https_forced_by_config(): bool
{
    return defined('FORCE_HTTPS') && FORCE_HTTPS === true;
}

function boot_session(string $name): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    // Refuse a session id the server never issued (session fixation) and never
    // accept one from the URL, so a crafted link cannot plant a session.
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name($name);
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => is_https(),
        'path' => '/',
    ]);
    session_start();
}

/** Cookie the console's colour theme is remembered in. */
const SC_THEME_COOKIE = 'sc_theme';

/**
 * Which console theme this request should be painted in.
 *
 * The console ships dark; "light" is the opt-out the toggle stores. The cookie
 * is read here rather than by a script so the attribute is already on <body> in
 * the first byte of HTML: no flash of the other theme, and no pre-paint inline
 * script to allow through the nonce-based CSP. The value is compared against a
 * fixed list, so nothing from the request is ever echoed back.
 *
 * @return string "dark" or "light"
 */
function console_theme(): string
{
    $value = $_COOKIE[SC_THEME_COOKIE] ?? '';
    return is_string($value) && $value === 'light' ? 'light' : 'dark';
}

/**
 * Once-per-request nonce for the console's inline script.
 *
 * A nonce lets the console run its own <script> while a Content-Security-Policy
 * that omits 'unsafe-inline' still blocks any script an attacker manages to
 * inject into the page.
 */
function csp_nonce(): string
{
    static $nonce = null;
    if ($nonce === null) {
        $nonce = bin2hex(random_bytes(16));
    }
    return $nonce;
}

/**
 * Baseline security headers. Call before anything is echoed.
 *
 * @param string $frameAncestors Who may frame this response ("'none'" or
 *        "'self'"). Pages meant to be embedded elsewhere should pass 'self'.
 * @param array<string,string> $csp Extra Content-Security-Policy directives.
 *        Values are emitted verbatim, so quote keywords: "'self'".
 */
function security_headers(string $frameAncestors = "'none'", array $csp = []): void
{
    if (headers_sent()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('X-Frame-Options: ' . ($frameAncestors === "'none'" ? 'DENY' : 'SAMEORIGIN'));
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Every response gets a CSP; object/base are refused unconditionally so an
    // injected <object> or <base> cannot be used either.
    $csp['frame-ancestors'] = $frameAncestors;
    $csp['object-src'] = "'none'";
    $csp['base-uri'] = "'none'";
    $directives = [];
    foreach ($csp as $name => $value) {
        // A valueless directive (sandbox) must not be emitted with a stray space.
        $directives[] = trim($name . ' ' . $value);
    }
    header('Content-Security-Policy: ' . implode('; ', $directives));
}

/** @param array<string,mixed> $payload */
function json_out(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $message, int $status = 400): never
{
    json_out(['ok' => false, 'error' => $message], $status);
}

/** Escape for HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read a trimmed, length-capped string from $_POST (or $_GET when $fromGet). */
function param(string $key, int $max = 2000, bool $fromGet = false): string
{
    $source = $fromGet ? $_GET : $_POST;
    $value = $source[$key] ?? '';
    if (!is_string($value)) {
        return '';
    }
    $value = str_replace("\0", '', $value);
    return trim(mb_substr($value, 0, $max));
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/**
 * The host this install is being reached on.
 *
 * $_SERVER['HTTP_HOST'] is taken straight from the client's Host header, so it
 * is never used verbatim. It is accepted only when it looks like a hostname or
 * an IP with an optional port; anything carrying quotes, angle brackets,
 * slashes or whitespace falls back to a safe default. That keeps a crafted Host
 * header from breaking out of an HTML attribute or a <script> block, and from
 * being baked into absolute URLs the page then hands to the browser.
 */
function safe_host(string $fallback = 'localhost'): string
{
    $host = str_replace("\0", '', (string) ($_SERVER['HTTP_HOST'] ?? ''));
    $isIpv6 = preg_match('/^\[[0-9a-fA-F:.]+\](?::\d{1,5})?$/', $host) === 1;
    $isName = preg_match('/^[A-Za-z0-9](?:[A-Za-z0-9.\-]{0,252})?(?::\d{1,5})?$/', $host) === 1;
    return $isIpv6 || $isName ? $host : $fallback;
}

/**
 * Raw User-Agent header, trimmed of control characters and length-capped.
 *
 * Stored with a new conversation so the agent console can show which browser
 * and platform the visitor is on. It is never echoed back to the browser.
 */
function client_user_agent(): string
{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (!is_string($ua)) {
        return '';
    }
    $ua = str_replace(["\0", "\r", "\n"], ' ', $ua);
    return mb_substr(trim($ua), 0, 255);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Abort the request unless a valid CSRF token accompanies the POST. */
function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    $known = $_SESSION['csrf'] ?? '';
    if (!is_string($sent) || $known === '' || !hash_equals($known, $sent)) {
        http_response_code(419);
        header('Content-Type: text/plain; charset=utf-8');
        exit('Invalid or expired security token. Reload the page and try again.');
    }
}

/** Human-friendly byte size, e.g. "1.4 MB". */
function format_bytes(int $bytes): string
{
    $bytes = max(0, $bytes);
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    $value = $bytes / 1024;
    foreach (['KB', 'MB', 'GB'] as $unit) {
        if ($value < 1024) {
            return ($value < 10 ? number_format($value, 1) : (string) round($value)) . ' ' . $unit;
        }
        $value /= 1024;
    }
    return round($value) . ' TB';
}

/** Human-friendly relative time, e.g. "3 min ago". */
function time_ago(int $timestamp): string
{
    $delta = time() - $timestamp;
    if ($delta < 60) {
        return 'just now';
    }
    if ($delta < 3600) {
        return (int) floor($delta / 60) . ' min ago';
    }
    if ($delta < 86400) {
        return (int) floor($delta / 3600) . ' h ago';
    }
    if ($delta < 604800) {
        return (int) floor($delta / 86400) . ' d ago';
    }
    return date('M j, Y', $timestamp);
}
