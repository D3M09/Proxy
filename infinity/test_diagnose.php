<?php
// Step 1: Get challenge
$ch = curl_init('http://lottogamez.gamer.free/Proxy/diagnose.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);
preg_match('/c=toNumbers\("([0-9a-f]+)"\)/', $r, $m);
$c_val = $m[1];
echo "c=$c_val\n";

// Step 2: Compute cookie via node
$node = 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("f655ba9d09a112d4968c63579db590b4"),b=toNumbers("98344c2eee86c3994890592585b49f80"),c=toNumbers("' . $c_val . '");console.log(toHex(s.decrypt(c,2,a,b)));';
$cookie = trim(shell_exec('node -e "' . addslashes($node) . '"'));
echo "cookie=$cookie\n";

// Step 3: Fetch diagnose with cookie
$ch2 = curl_init('http://lottogamez.gamer.free/Proxy/diagnose.php');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_COOKIE, "__test=$cookie");
$r2 = curl_exec($ch2);
echo $r2;
