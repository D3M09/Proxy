<?php
/**
 * Test if the anti-bot strips responses based on size.
 * Create test files of various sizes and try to serve them through the proxy.
 */

// The key question: does the anti-bot only intercept HTML requests?
// Test by requesting different Accept headers for the SAME file
$urls = [
    '/m/chunk-common.916936e4.js',   // Works (195KB)
    '/m/app.73f3eb85.js',             // Broken (1.5MB)
];

$accepts = [
    'text/html,application/xhtml+xml',   // Browser HTML request
    '*/*',                                // Wildcard
    'application/javascript',             // JS request
    'text/plain',                         // Plain text
];

$tmpCookie = tempnam(sys_get_temp_dir(), 'ckjar');

// Get a valid cookie by getting challenge and solving it
$ch = curl_init('http://lottogamez.gamer.free/m/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $tmpCookie,
    CURLOPT_COOKIEFILE => $tmpCookie,
]);
$html = curl_exec($ch);
curl_close($ch);

if (strpos($html, 'slowAES') !== false) {
    preg_match_all('/toNumbers\s*\(\s*"([0-9a-f]+)"\s*\)/', $html, $m);
    $tmpJs = tempnam(sys_get_temp_dir(), 'ck') . '.js';
    file_put_contents($tmpJs, 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("' . $m[1][0] . '"),b=toNumbers("' . $m[1][1] . '"),c=toNumbers("' . $m[1][2] . '");console.log(toHex(s.decrypt(c,2,a,b)));');
    $cookie_val = trim(shell_exec('node "' . $tmpJs . '"'));
    unlink($tmpJs);
    file_put_contents($tmpCookie, "__test\tTRUE\t/\tTRUE\t" . (time()+21600) . "\t__test\t$cookie_val\n", FILE_APPEND);
}

echo "=== Test: Same file, different Accept headers ===\n\n";
foreach ($urls as $url) {
    foreach ($accepts as $accept) {
        $ch = curl_init('http://lottogamez.gamer.free' . $url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $tmpCookie,
            CURLOPT_HTTPHEADER => ['Accept: ' . $accept],
            CURLOPT_HEADER => true,
        ]);
        $r = curl_exec($ch);
        $info = curl_getinfo($ch);
        $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($r, $hs);
        $headers = substr($r, 0, $hs);
        
        $hit = (strpos($headers, 'X-Proxy-Cache: HIT') !== false) ? 'HIT' : 'MISS';
        echo "$url Accept=$accept → {$info['http_code']} body=" . strlen($body) . " [$hit]\n";
        curl_close($ch);
    }
    echo "\n";
}

// Also test: request app.js as a Range request
echo "=== Test: Range request for app.js ===\n";
$ch = curl_init('http://lottogamez.gamer.free/m/app.73f3eb85.js');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $tmpCookie,
    CURLOPT_RANGE => '0-1023',
    CURLOPT_HEADER => true,
]);
$r = curl_exec($ch);
$hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($r, $hs);
$headers = substr($r, 0, $hs);
echo "Range request: " . strlen($body) . " bytes\n";
echo "Headers: $headers\n";
curl_close($ch);

// Test: directly curl the UPSTREAM for app.js (no anti-bot)
echo "\n=== Direct upstream (no anti-bot) ===\n";
$ch = curl_init('https://www.1333bet.ai/m/app.73f3eb85.js');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
]);
$r = curl_exec($ch);
$hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body = substr($r, $hs);
$headers = substr($r, 0, $hs);
echo "Upstream: " . strlen($body) . " bytes\n";
if (strlen($body) > 0) echo "First 100: " . substr($body, 0, 100) . "\n";
curl_close($ch);

unlink($tmpCookie);
