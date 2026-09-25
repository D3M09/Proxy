<?php

declare(strict_types=1);

/**
 * Front controller for PHP's built-in web server.
 *
 *     php -S 127.0.0.1:8000 router.php
 *
 * `data/.htaccess` protects the conversation store on Apache, but .htaccess is
 * an Apache-only mechanism: the built-in server (and nginx, and Apache with
 * AllowOverride None) ignores it completely and would happily hand out
 * data/admin.json, data/agents.json and every visitor transcript as plain text.
 *
 * This router closes that hole for the built-in server. Anything else — assets,
 * admin/, api.php — is served normally by returning false.
 *
 * Returning false lets the built-in server handle the request itself, including
 * executing .php files. Denied paths exit here instead.
 */

// Read by the console's security self-check: proof that the built-in server is
// running through this router, so data/ is not being served as plain text.
define('SC_ROUTER', true);

$requested = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$requested = is_string($requested) ? $requested : '/';
// Decode first, then normalise, so "%2e%2e/data/x" and "/a/../../data/x"
// cannot slip past the prefix checks below.
$requested = rawurldecode($requested);

$segments = [];
foreach (explode('/', str_replace('\\', '/', $requested)) as $segment) {
    if ($segment === '' || $segment === '.') {
        continue;
    }
    if ($segment === '..') {
        array_pop($segments);
        continue;
    }
    $segments[] = $segment;
}
$path = '/' . implode('/', $segments);

/** Answer as if the file simply is not there — do not confirm what exists. */
$deny = static function (): never {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    exit("Not found.\n");
};

// Runtime data and shared libraries: never fetchable, not even to PHP.
foreach (['/data', '/lib'] as $prefix) {
    if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
        $deny();
    }
}

// Any dotfile or dot-directory (.git, .env, .htaccess, .freebuff, …).
if (str_starts_with($path, '/.') || str_contains($path, '/.')) {
    $deny();
}

// config.php holds hashes and settings: if PHP ever stopped handling .php this
// would be served as source, so refuse it at the web layer too.
if ($path === '/config.php') {
    $deny();
}

return false;
