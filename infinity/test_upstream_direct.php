<?php
// Check what UPSTREAM returns for these paths directly
$urls = [
    'upstream /mobile/mc/memberCenter.38cabcf7.js' => 'https://www.1333bet.ai/mobile/mc/memberCenter.38cabcf7.js',
    'upstream /mobile/mc/domainCheck.js' => 'https://www.1333bet.ai/mobile/mc/domainCheck.js',
    'upstream /m/app.73f3eb85.js' => 'https://www.1333bet.ai/m/app.73f3eb85.js',
    'upstream /m/chunk-common.916936e4.js' => 'https://www.1333bet.ai/m/chunk-common.916936e4.js',
];

foreach ($urls as $label => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_COOKIE, '__test=a659ca047c396b85433fabfb94a9edfc');
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($r, $hs);
    
    echo "$label\n";
    echo "  Status: {$info['http_code']}  Size: " . strlen($body) . "  Final URL: {$info['url']}\n";
    echo "  First 100: " . substr($body, 0, 100) . "\n\n";
    curl_close($ch);
}
