<?php
// Get fresh challenge, compute cookie, then test broken files
$ch = curl_init('http://lottogamez.gamer.free/m/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);
curl_close($ch);

if (strpos($r, 'slowAES') === false) {
    echo "No challenge detected\n";
    exit;
}

preg_match_all('/toNumbers\s*\(\s*"([0-9a-f]+)"\s*\)/', $r, $m);
$c_val = $m[1][2]; // third match is 'c'

$tmpJs = tempnam(sys_get_temp_dir(), 'ck') . '.js';
file_put_contents($tmpJs, 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("f655ba9d09a112d4968c63579db590b4"),b=toNumbers("98344c2eee86c3994890592585b49f80"),c=toNumbers("' . $c_val . '");console.log(toHex(s.decrypt(c,2,a,b)));');
$cookie = trim(shell_exec('node "' . $tmpJs . '" 2>&1'));
unlink($tmpJs);
echo "Cookie: $cookie\n\n";

$urls = [
    'app.73f3eb85.js (1.5MB - BROKEN)',
    'memberCenter.38cabcf7.js (986KB - BROKEN)',
    'chunk-common.916936e4.js (195KB - OK)',
    'vendor.encrypt.v2.dll.js (881KB - OK)',
    'domainCheck.js (16KB - OK)',
];

foreach ($urls as $label) {
    $name = explode(' ', $label)[0];
    $url = 'http://lottogamez.gamer.free/m/' . $name;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIE, '__test=' . $cookie);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($r, 0, $hs);
    $body = substr($r, $hs);
    
    $doubleCC = substr_count($headers, 'Cache-Control:');
    echo "$label\n";
    echo "  Status: {$info['http_code']}  Body: " . strlen($body) . " bytes  CacheControl-count: $doubleCC\n";
    if (strlen($body) > 0) echo "  First 100: " . substr($body, 0, 100) . "\n";
    echo "\n";
    curl_close($ch);
}
