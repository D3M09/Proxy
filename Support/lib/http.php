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
    session_name($name);
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => is_https(),
        'path' => '/',
    ]);
    session_start();
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
