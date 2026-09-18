<?php
// One-shot: get challenge, compute cookie, fetch debug page
$ch = curl_init('http://lottogamez.gamer.free/debug_proxy.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$r = curl_exec($ch);

if (strpos($r, 'slowAES') === false) {
    echo $r;
    exit;
}

preg_match('/c=toNumbers\("([0-9a-f]+)"\)/', $r, $m);
$c_val = $m[1];

// Write temp node script
$tmpJs = tempnam(sys_get_temp_dir(), 'ck') . '.js';
file_put_contents($tmpJs, 'var vm=require("vm"),fs=require("fs");var ctx={};vm.createContext(ctx);vm.runInContext(fs.readFileSync("C:/xampp/htdocs/infinity/aes.js","utf8"),ctx);var s=ctx.slowAES;function toNumbers(d){var e=[];d.replace(/(..)/g,function(d){e.push(parseInt(d,16))});return e}function toHex(d){var f="";for(var g=0;g<d.length;g++)f+=(16>d[g]?"0":"")+d[g].toString(16);return f.toLowerCase()}var a=toNumbers("f655ba9d09a112d4968c63579db590b4"),b=toNumbers("98344c2eee86c3994890592585b49f80"),c=toNumbers("' . $c_val . '");console.log(toHex(s.decrypt(c,2,a,b)));');

$cookie = trim(shell_exec('node "' . $tmpJs . '" 2>&1'));
unlink($tmpJs);

echo "Cookie: __test=$cookie\n\n";

$ch2 = curl_init('http://lottogamez.gamer.free/debug_proxy.php');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_COOKIE, '__test=' . $cookie);
echo curl_exec($ch2);
