<?php
$node = 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("f655ba9d09a112d4968c63579db590b4"),b=toNumbers("98344c2eee86c3994890592585b49f80"),c=toNumbers("10a73bde9f2b07b0ea3a643ac0a0d636");console.log(toHex(s.decrypt(c,2,a,b)));';
$cookie = trim(shell_exec('node -e "' . addslashes($node) . '"'));
echo "Cookie: __test=$cookie\n";

// Test 1: Fetch /m/ with cookie
$ch = curl_init('http://lottogamez.gamer.free/m/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, "__test=$cookie");
$r = curl_exec($ch);
echo "Main page: " . strlen($r) . " bytes\n";
echo "First 200: " . substr($r, 0, 200) . "\n\n";

// Test 2: Fetch JS with cookie
$ch2 = curl_init('http://lottogamez.gamer.free/mobile/mc/memberCenter.38cabcf7.js');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_COOKIE, "__test=$cookie");
$r2 = curl_exec($ch2);
echo "JS file: " . strlen($r2) . " bytes\n";
echo "First 200: " . substr($r2, 0, 200) . "\n\n";

// Test 3: Fetch JS via /Proxy/ path with cookie
$ch3 = curl_init('http://lottogamez.gamer.free/Proxy/mobile/mc/memberCenter.38cabcf7.js');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_COOKIE, "__test=$cookie");
$r3 = curl_exec($ch3);
echo "JS via Proxy: " . strlen($r3) . " bytes\n";
echo "First 200: " . substr($r3, 0, 200) . "\n\n";
