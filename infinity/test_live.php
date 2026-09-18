<?php
// Test: compute anti-bot cookie, then fetch JS files through the proxy
$cookie = trim(shell_exec('node -e "var vm=require(\"vm\"),fs=require(\"fs\");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync(\"C:/xampp/htdocs/infinity/aes.js\",\"utf8\"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f=\"\";for(var g=0;g<d.length;g++)f+=(16>d[g]?\"0\":\"\")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers(\"f655ba9d09a112d4968c63579db590b4\"),b=toNumbers(\"98344c2eee86c3994890592585b49f80\"),c=toNumbers(\"f7d32d39e99626946e7aa15a94e87637\");console.log(toHex(s.decrypt(c,2,a,b)));"' . "\n"));

$urls = [
    'Main HTML' => 'http://lottogamez.gamer.free/m/',
    'JS memberCenter' => 'http://lottogamez.gamer.free/mobile/mc/memberCenter.38cabcf7.js',
    'JS domainCheck' => 'http://lottogamez.gamer.free/mobile/mc/domainCheck.js?v=38cabcf7',
    'JS loadMemberCenter' => 'http://lottogamez.gamer.free/mobile/mc/loadMemberCenter.js',
    'CSS' => 'http://lottogamez.gamer.free/mobile/mc/memberCenter.17e24349.css',
];

foreach ($urls as $label => $url) {
    // Step 1: Get challenge if any
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $r = curl_exec($ch);
    $info = curl_getinfo($ch);
    
    // If it's the AES challenge, compute cookie and retry
    if (strpos($r, 'slowAES') !== false) {
        preg_match('/c=toNumbers\("([0-9a-f]+)"\)/', $r, $m);
        if ($m[1]) {
            $node = 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("f655ba9d09a112d4968c63579db590b4"),b=toNumbers("98344c2eee86c3994890592585b49f80"),c=toNumbers("' . $m[1] . '");console.log(toHex(s.decrypt(c,2,a,b)));';
            $ck = trim(shell_exec('node -e "' . addslashes($node) . '" 2>/dev/null'));
            curl_close($ch);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIE, '__test=' . $ck);
            $r = curl_exec($ch);
            $info = curl_getinfo($ch);
        }
    }
    curl_close($ch);
    
    echo "$label: status={$info['http_code']} len=" . strlen($r) . " ct={$info['content_type']}\n";
    if (strlen($r) > 0) echo "  First 100: " . substr($r, 0, 100) . "\n";
    echo "\n";
}
