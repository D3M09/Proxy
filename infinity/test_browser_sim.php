<?php
/**
 * Complete browser simulation: get challenge, solve it, follow redirect with cookie,
 * then request JS files with the SAME cookie jar.
 * Uses curl cookie jar to simulate browser cookie persistence.
 */

$tmpCookie = tempnam(sys_get_temp_dir(), 'ckjar');

// Step 1: Get challenge page (like a browser first visit)
$ch = curl_init('http://lottogamez.gamer.free/m/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR       => $tmpCookie,
    CURLOPT_COOKIEFILE      => $tmpCookie,
    CURLOPT_FOLLOWLOCATION  => false,
]);
$html = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "Step 1 - Challenge page: HTTP {$info['http_code']}, " . strlen($html) . " bytes\n";

if (strpos($html, 'slowAES') === false) {
    echo "No challenge found. Maybe cookie already works?\n";
    echo substr($html, 0, 300) . "\n";
    unlink($tmpCookie);
    exit;
}

// Parse challenge params
preg_match_all('/toNumbers\s*\(\s*"([0-9a-f]+)"\s*\)/', $html, $m);
$a_val = $m[1][0]; $b_val = $m[1][1]; $c_val = $m[1][2];
echo "Challenge c=$c_val\n";

// Compute cookie using node
$tmpJs = tempnam(sys_get_temp_dir(), 'ck') . '.js';
file_put_contents($tmpJs, 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("' . $a_val . '"),b=toNumbers("' . $b_val . '"),c=toNumbers("' . $c_val . '");console.log(toHex(s.decrypt(c,2,a,b)));');
$cookie_val = trim(shell_exec('node "' . $tmpJs . '"'));
unlink($tmpJs);
echo "Cookie: __test=$cookie_val\n";

// Write cookie to jar file (simulate what browser does with Set-Cookie)
// The challenge page doesn't set a cookie via HTTP, it sets it via JS document.cookie
// So we need to manually add it to the cookie jar
$domain = 'lottogamez.gamer.free';
$expiry = time() + 21600;
// Netscape cookie format
$cookieLine = "$domain\tTRUE\t/\tTRUE\t$expiry\t__test\t$cookie_val\n";
file_put_contents($tmpCookie, "# Netscape HTTP Cookie File\n" . $cookieLine, FILE_APPEND);

echo "\nStep 2 - Following redirect with cookie...\n";
// Step 2: Follow the redirect (like browser after challenge solves)
$ch = curl_init('http://lottogamez.gamer.free/m/?i=1');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE     => $tmpCookie,
    CURLOPT_COOKIEJAR      => $tmpCookie,
]);
$html2 = curl_exec($ch);
$info2 = curl_getinfo($ch);
curl_close($ch);
echo "Step 2 - Redirect: HTTP {$info2['http_code']}, " . strlen($html2) . " bytes\n";
echo "Has base tag: " . (strpos($html2, '<base') !== false ? 'YES' : 'no') . "\n";
echo "Has script src: " . (preg_match_all('/<script[^>]+src=/i', $html2, $sm) ? count($sm[0]) : 0) . " scripts\n";

// Show the script sources
preg_match_all('/<script[^>]+src=["\']([^"\']+)["\']/i', $html2, $sm);
foreach ($sm[1] as $src) {
    echo "  src: $src\n";
}

echo "\nStep 3 - Testing JS file loads with session cookie...\n";
// Step 3: Request each JS file with the same session
$urls = [
    '/m/app.73f3eb85.js',
    '/m/chunk-common.916936e4.js',
    '/m/vendor.encrypt.v2.dll.js',
    '/mobile/mc/memberCenter.38cabcf7.js',
    '/mobile/mc/domainCheck.js?v=38cabcf7',
    '/mobile/mc/loadMemberCenter.js',
];

foreach ($urls as $path) {
    $url = 'http://lottogamez.gamer.free' . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE     => $tmpCookie,
        CURLOPT_HEADER         => true,
    ]);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($r, $hs);
    $headers = substr($r, 0, $hs);
    
    $hit = (strpos($headers, 'X-Proxy-Cache: HIT') !== false) ? 'HIT' : 'MISS';
    $cl = '';
    if (preg_match('/Content-Length:\s*(\d+)/i', $headers, $clm)) $cl = 'CL=' . $clm[1];
    $cc = '';
    if (preg_match('/Cache-Control:.*max-age=(\d+)/i', $headers, $ccm)) $cc = 'MA=' . $ccm[1];
    
    echo "$path → {$info['http_code']} body=" . strlen($body) . " [$hit] [$cl] [$cc]\n";
    if (strlen($body) < 200 && strlen($body) > 0) echo "  Body: " . substr($body, 0, 200) . "\n";
    curl_close($ch);
}

unlink($tmpCookie);
