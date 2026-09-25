<?php
/**
 * Front controller for the PHP built-in server (`php -S host:port router.php`).
 *
 * Apache deployments use the repo-root .htaccess with the *repo root* as the
 * document root, so real files there (/img/splash.png, /css/*.css, ...) are
 * served as-is. The built-in server runs with cwd = Proxy/, which would make
 * those files unreachable and send them to the upstream proxy instead — where
 * the SPA answers with its index.html and the asset guard turns it into a 404.
 * This router mirrors the Apache behaviour: local Proxy files first, then the
 * whitelisted repo-root assets, then the application.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Never expose internal stores or panel internals. Apache denies data/ via
// data/.htaccess; the built-in server ignores .htaccess, so deny here too.
if (preg_match('#^/(data|admin/includes)/#', $uri)
    || preg_match('#/(config|panel|panel_new|panel_final3)\.php$#', $uri)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    echo 'Forbidden';
    return true;
}

$mimeTypes = [
    'css' => 'text/css',
    'js' => 'application/javascript',
    'mjs' => 'application/javascript',
    'json' => 'application/json',
    'map' => 'application/json',
    'txt' => 'text/plain; charset=utf-8',
    'xml' => 'application/xml',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'svg' => 'image/svg+xml',
    'webp' => 'image/webp',
    'avif' => 'image/avif',
    'ico' => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf' => 'font/ttf',
    'otf' => 'font/otf',
    'eot' => 'application/vnd.ms-fontobject',
    'mp3' => 'audio/mpeg',
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'html' => 'text/html; charset=utf-8',
    'htm' => 'text/html; charset=utf-8',
];

/**
 * Stream a local file with the matching content type.
 */
$sendStatic = static function (string $file, array $mimeTypes): void {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    $size = @filesize($file);
    if ($size !== false) {
        header('Content-Length: ' . $size);
    }
    readfile($file);
};

// Serve Proxy-local static files directly (skip .php files — they must run).
$uriExt = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
if ($uri !== '/' && $uriExt !== 'php' && is_file(__DIR__ . $uri)) {
    $sendStatic(__DIR__ . $uri, $mimeTypes);
    return true;
}

// Repo-root assets that Apache serves because its document root is the repo
// root. Whitelisted on purpose: the built-in server must not expose the repo's
// dev/internal files (Support/, _verify/, userprompt.txt, archives, ...).
$repoRoot = dirname(__DIR__);
$rootDirWhitelist = ['img/', 'images/', 'css/', 'js/', 'fonts/', 'webfonts/', 'sounds/'];
$rootFileWhitelist = ['robots.txt', 'sitemap.xml', 'favicon.ico', 'Pay.html'];

$serveFromRoot = false;
foreach ($rootFileWhitelist as $name) {
    if ($uri === '/' . $name) {
        $serveFromRoot = true;
        break;
    }
}
if (!$serveFromRoot) {
    foreach ($rootDirWhitelist as $dir) {
        if (strpos($uri, '/' . $dir) === 0) {
            $serveFromRoot = true;
            break;
        }
    }
}

if ($serveFromRoot && strpos($uri, '..') === false) {
    // block /css/ style directory listings; only real files are served
    $candidate = $repoRoot . $uri;
    if (is_file($candidate) && $uriExt !== 'php') {
        $sendStatic($candidate, $mimeTypes);
        return true;
    }
}

// Admin panel - serve locally
if ($uri === '/admin' || strpos($uri, '/admin/') === 0) {
    require __DIR__ . '/admin/index.php';
    return true;
}

// One-time player impersonation (single-use token, admin session required)
if ($uri === '/player-login' || $uri === '/player-login/') {
    require __DIR__ . '/player-login.php';
    return true;
}

// Setup installer - serve locally
if ($uri === '/setup' || $uri === '/setup/' || $uri === '/setup.php') {
    require __DIR__ . '/setup.php';
    return true;
}

require_once __DIR__ . '/admin/store.php';
$voucher = content_load()['voucher'] ?? [];

// VoucherCenter custom only when ON — OFF = normal 404
if ($uri === '/voucherCenter' || $uri === '/voucherCenter/' || strpos($uri, '/voucherCenter/') === 0) {
    if (empty($voucher['enabled'])) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Voucher Center disabled';
        return true;
    }
    $vcFile = __DIR__ . '/..' . $uri;
    if ($uri === '/voucherCenter' || $uri === '/voucherCenter/' || $uri === '/voucherCenter/index.php') {
        require __DIR__ . '/../voucherCenter/index.php';
        return true;
    }
    if (is_file($vcFile)) {
        if (pathinfo($uri, PATHINFO_EXTENSION) === 'php') {
            require $vcFile;
            return true;
        }
        $sendStatic($vcFile, $mimeTypes);
        return true;
    }
    http_response_code(404);
    header('Content-Type: text/html; charset=UTF-8');
    echo '404 Not Found';
    return true;
}

// Payment API endpoints
if ($uri === '/api' || strpos($uri, '/api/') === 0) {
    $apiFile = __DIR__ . $uri;
    if (is_file($apiFile) && pathinfo($apiFile, PATHINFO_EXTENSION) === 'php') {
        require $apiFile;
        return true;
    }
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not found']);
    return true;
}

// Route everything else through index.php
require __DIR__ . '/index.php';
