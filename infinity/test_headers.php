<?php
// Test: does domainCheck.js have Content-Length?
$urls = [
    'memberCenter' => 'http://lottogamez.gamer.free/mobile/mc/memberCenter.38cabcf7.js',
    'domainCheck' => 'http://lottogamez.gamer.free/mobile/mc/domainCheck.js?v=38cabcf7',
    'loadMemberCenter' => 'http://lottogamez.gamer.free/mobile/mc/loadMemberCenter.js',
];

foreach ($urls as $label => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($r, 0, $headerSize);
    $body = substr($r, $headerSize);
    echo "$label:\n";
    echo "  Status: {$info['http_code']}\n";
    echo "  Body: " . strlen($body) . " bytes\n";
    // Extract Content-Length
    if (preg_match('/Content-Length:\s*(\d+)/i', $headers, $m)) {
        echo "  Content-Length header: {$m[1]}\n";
    } else {
        echo "  Content-Length header: NOT SET\n";
    }
    // Check for Transfer-Encoding
    if (preg_match('/Transfer-Encoding:/i', $headers)) {
        echo "  Transfer-Encoding: chunked\n";
    }
    // Check X-Proxy-Cache
    if (preg_match('/X-Proxy-Cache:\s*(\S+)/i', $headers, $m)) {
        echo "  X-Proxy-Cache: {$m[1]}\n";
    }
    echo "\n";
    curl_close($ch);
}
