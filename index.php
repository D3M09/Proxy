<?php
header('X-Debug-Root: 1');
header('X-Docroot: ' . ($_SERVER['DOCUMENT_ROOT'] ?? ''));
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($uri === false || $uri === null || $uri === '') $uri = '/';
$local = __DIR__ . $uri;
if ($uri !== '/' && is_file($local)) {
    $ext = strtolower(pathinfo($local, PATHINFO_EXTENSION));
    $mimes = ['css'=>'text/css','js'=>'application/javascript','json'=>'application/json','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','gif'=>'image/gif','svg'=>'image/svg+xml','webp'=>'image/webp','ico'=>'image/x-icon','woff'=>'font/woff','woff2'=>'font/woff2','ttf'=>'font/ttf','html'=>'text/html; charset=UTF-8','htm'=>'text/html; charset=UTF-8'];
    if (isset($mimes[$ext])) header('Content-Type: '.$mimes[$ext]);
    header('X-Served-By: root-index');
    readfile($local);
    exit;
}
if ($uri === '/admin' || strpos($uri, '/admin/') === 0) {
    require __DIR__ . '/Proxy/admin/index.php';
    exit;
}
if ($uri === '/setup' || $uri === '/setup/' || $uri === '/setup.php') {
    require __DIR__ . '/Proxy/setup.php';
    exit;
}
if ($uri === '/api' || strpos($uri, '/api/') === 0) {
    $apiFile = __DIR__ . $uri;
    if (is_file($apiFile) && pathinfo($apiFile, PATHINFO_EXTENSION) === 'php') {
        require $apiFile;
        exit;
    }
}
require __DIR__ . '/Proxy/index.php';
