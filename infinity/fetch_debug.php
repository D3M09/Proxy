<?php
// Get challenge + cookie
$ch = curl_init('http://lottogamez.gamer.free/m/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);
curl_close($ch);
if (strpos($r, 'slowAES') === false) { echo "No challenge\n"; exit; }
preg_match_all('/toNumbers\s*\(\s*"([0-9a-f]+)"\s*\)/', $r, $m);
$a_val = $m[1][0]; $b_val = $m[1][1]; $c_val = $m[1][2];
$tmpJs = tempnam(sys_get_temp_dir(), 'ck') . '.js';
file_put_contents($tmpJs, 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("' . $a_val . '"),b=toNumbers("' . $b_val . '"),c=toNumbers("' . $c_val . '");console.log(toHex(s.decrypt(c,2,a,b)));');
$cookie = trim(shell_exec('node "' . $tmpJs . '"'));
unlink($tmpJs);

// Fetch debug_servefile.php with cookie
$ch = curl_init('http://lottogamez.gamer.free/debug_servefile.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, '__test=' . $cookie);
echo curl_exec($ch);
curl_close($ch);
