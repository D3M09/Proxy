<?php
// Test just the problematic JS file
$ch = curl_init('http://lottogamez.gamer.free/mobile/mc/memberCenter.38cabcf7.js');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$r = curl_exec($ch);
$info = curl_getinfo($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($r, 0, $headerSize);
$body = substr($r, $headerSize);
echo "Status: {$info['http_code']}\n";
echo "Body len: " . strlen($body) . "\n";
echo "Headers:\n$header\n";
if (strlen($body) > 0) echo "First 200: " . substr($body, 0, 200) . "\n";
