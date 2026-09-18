<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    $ext = pathinfo($uri, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'html' => 'text/html; charset=utf-8',
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile(__DIR__ . $uri);
    return true;
}

// Admin panel - serve locally
if ($uri === '/admin' || strpos($uri, '/admin/') === 0) {
    require __DIR__ . '/admin/index.php';
    return true;
}

// Setup installer - serve locally
if ($uri === '/setup' || $uri === '/setup/' || $uri === '/setup.php') {
    require __DIR__ . '/setup.php';
    return true;
}

// Route everything else through index.php
require __DIR__ . '/index.php';
