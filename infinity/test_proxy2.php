<?php
$cookie = 'a659ca047c396b85433fabfb94a9edfc';

$urls = [
    'http://lottogamez.gamer.free/mobile/mc/memberCenter.38cabcf7.js',
    'http://lottogamez.gamer.free/Proxy/mobile/mc/memberCenter.38cabcf7.js',
    'http://lottogamez.gamer.free/mobile/mc/domainCheck.js?v=38cabcf7',
    'http://lottogamez.gamer.free/res/common/common Chunk.js',
];

foreach ($urls as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, "__test=$cookie");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($r, 0, $headerSize);
    $body = substr($r, $headerSize);
    echo "URL: $url\n";
    echo "  Status: {$info['http_code']}\n";
    echo "  CT: {$info['content_type']}\n";
    echo "  Body len: " . strlen($body) . "\n";
    echo "  Redirect: {$info['redirect_url']}\n";
    // Show first few headers
    $lines = explode("\r\n", $header);
    foreach (array_slice($lines, 0, 10) as $line) {
        if ($line !== '') echo "  H: $line\n";
    }
    echo "\n";
    curl_close($ch);
}
