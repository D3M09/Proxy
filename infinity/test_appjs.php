<?php
$urls = [
    'app.73f3eb85.js' => 'http://lottogamez.gamer.free/m/app.73f3eb85.js',
    'chunk-common.916936e4.js' => 'http://lottogamez.gamer.free/m/chunk-common.916936e4.js',
    'vendor.encrypt.v2.dll.js' => 'http://lottogamez.gamer.free/m/vendor.encrypt.v2.dll.js',
];

foreach ($urls as $label => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($r, 0, $hs);
    $body = substr($r, $hs);
    
    echo "=== $label ===\n";
    echo "  Status: {$info['http_code']}\n";
    echo "  Body: " . strlen($body) . " bytes\n";
    echo "  CT: {$info['content_type']}\n";
    if (preg_match('/Content-Length:\s*(\d+)/i', $headers, $m)) echo "  Content-Length: {$m[1]}\n";
    if (preg_match('/X-Proxy-Cache:\s*(\S+)/i', $headers, $m)) echo "  X-Proxy-Cache: {$m[1]}\n";
    if (strlen($body) > 0) echo "  First 200: " . substr($body, 0, 200) . "\n";
    echo "\n";
    curl_close($ch);
}
