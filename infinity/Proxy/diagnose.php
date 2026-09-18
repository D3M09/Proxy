<?php
$file = __DIR__ . '/cache/mobile/mc/memberCenter.38cabcf7.js';
header('Content-Type: text/plain; charset=UTF-8');
echo "File: $file\n";
echo "Exists: " . (is_file($file) ? 'yes' : 'no') . "\n";
echo "Size: " . filesize($file) . "\n";
$data = @file_get_contents($file);
if ($data === false) {
    echo "file_get_contents FAILED\n";
    echo "Error: " . error_get_last()['message'] ?? 'unknown' . "\n";
} else {
    echo "Read: " . strlen($data) . " bytes\n";
    echo "First 100: " . substr($data, 0, 100) . "\n";
    
    // Test brandReplace
    $pattern = '/(?<![\w.\/-])' . preg_quote('1333bet', '/') . '(?!\.[a-z])/i';
    $result = @preg_replace($pattern, 'LottoGames', $data);
    $err = preg_last_error();
    echo "preg_replace result: " . ($result === null ? 'NULL' : strlen($result)) . " bytes\n";
    echo "preg_last_error: $err\n";
}
