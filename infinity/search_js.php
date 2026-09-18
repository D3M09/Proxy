<?php
$c = file_get_contents('C:/xampp/htdocs/infinity/Proxy/VoucherCenter/loadMemberCenter.js');
// Find domain/check related code
$terms = ['domain', 'check', 'location', 'host', 'origin', 'hostname', 'window.location'];
foreach ($terms as $t) {
    $pos = 0;
    $count = 0;
    while (($pos = stripos($c, $t, $pos)) !== false && $count < 3) {
        $start = max(0, $pos - 100);
        $end = min(strlen($c), $pos + 100);
        echo "[$t at $pos] ...".substr($c, $start, $end - $start)."...".PHP_EOL.PHP_EOL;
        $pos += strlen($t);
        $count++;
    }
}
