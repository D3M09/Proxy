<?php
// Debug endpoint - tests what the proxy does with memberCenter.38cabcf7.js
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/Proxy/admin/store.php';

$upstream = 'https://www.1333bet.ai';
$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';

header('Content-Type: text/plain; charset=UTF-8');

// Test 1: Can PHP read the cache file?
$file = __DIR__ . '/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js';
echo "=== Cache file check ===\n";
echo "Path: $file\n";
echo "is_file: " . (is_file($file) ? 'YES' : 'NO') . "\n";
clearstatcache();
echo "filesize: " . @filesize($file) . "\n";
echo "is_readable: " . (is_readable($file) ? 'YES' : 'NO') . "\n\n";

// Test 2: fetchUpstream directly
echo "=== fetchUpstream test ===\n";
$path = '/mobile/mc/memberCenter.38cabcf7.js';
$url = $upstream . $path;
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_USERAGENT => $ua,
    CURLOPT_HTTPHEADER => ['Accept: */*', 'Referer: ' . $upstream . '/'],
]);
$body = curl_exec($ch);
$err = curl_error($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$ct = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "Status: $status\n";
echo "Content-Type: $ct\n";
echo "Body length: " . strlen($body) . "\n";
echo "Error: " . ($err ?: 'none') . "\n";
echo "First 100: " . substr($body, 0, 100) . "\n\n";

// Test 3: readfile on cache
if (is_file($file)) {
    echo "=== readfile test (output buffering) ===\n";
    ob_start();
    $read = readfile($file);
    $buf = ob_get_clean();
    echo "readfile returned: $read\n";
    echo "buffer length: " . strlen($buf) . "\n\n";
}

// Test 4: PHP environment
echo "=== PHP environment ===\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "output_buffering: " . ini_get('output_buffering') . "\n";
echo "output_handler: " . (ini_get('output_handler') ?: 'none') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
