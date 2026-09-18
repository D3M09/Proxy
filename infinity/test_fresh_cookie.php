<?php
// Get a FRESH challenge and immediately use the cookie for the SAME domain
// Step 1: Get challenge from the domain itself (not via debug_proxy)
$ch = curl_init('http://lottogamez.gamer.free/m/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);
curl_close($ch);

if (strpos($r, 'slowAES') === false) {
    echo "No challenge - page loaded directly\n";
    echo substr($r, 0, 200) . "\n";
    exit;
}

// Parse the challenge
preg_match_all('/toNumbers\s*\(\s*"([0-9a-f]+)"\s*\)/', $r, $m);
$a_val = $m[1][0];
$b_val = $m[1][1];
$c_val = $m[1][2];
echo "Challenge: a=$a_val b=$b_val c=$c_val\n";

// Compute cookie with node
$tmpJs = tempnam(sys_get_temp_dir(), 'ck') . '.js';
file_put_contents($tmpJs, 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("' . $a_val . '"),b=toNumbers("' . $b_val . '"),c=toNumbers("' . $c_val . '");console.log(toHex(s.decrypt(c,2,a,b)));');
$cookie = trim(shell_exec('node "' . $tmpJs . '"'));
unlink($tmpJs);
echo "Cookie: __test=$cookie\n\n";

// Step 2: Now immediately request JS files with this cookie
$urls = [
    '/m/app.73f3eb85.js',
    '/mobile/mc/memberCenter.38cabcf7.js',
    '/mobile/mc/domainCheck.js?v=38cabcf7',
    '/mobile/mc/loadMemberCenter.js',
    '/m/chunk-common.916936e4.js',
];

foreach ($urls as $path) {
    $url = 'http://lottogamez.gamer.free' . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, '__test=' . $cookie);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($r, $hs);
    $headers = substr($r, 0, $hs);
    
    $hit = (strpos($headers, 'X-Proxy-Cache: HIT') !== false) ? 'HIT' : 'MISS';
    $isJS = (strpos($body, 'function') !== false || strpos($body, 'var ') !== false) ? 'JS' : 'HTML';
    echo "$path → {$info['http_code']} " . strlen($body) . " bytes [$hit] [$isJS]\n";
    if (strlen($body) > 0 && strlen($body) < 200) echo "  Body: $body\n";
    curl_close($ch);
}
