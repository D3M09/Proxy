<?php
/**
 * Test different output methods for large files to see which ones
 * the anti-bot strips.
 */
$file = __DIR__ . '/Proxy/cache/m/app.73f3eb85.js';
$size = filesize($file);
echo "File size: $size\n";

// Test 1: readfile with Content-Length
header('Content-Type: application/javascript; charset=UTF-8');
echo "TEST1_START\n";
ob_start();
header('X-Test: readfile-with-cl');
clearstatcache();
$s = filesize($file);
header('Content-Length: ' . $s);
readfile($file);
$buf = ob_get_clean();
echo "\nTEST1_END len=" . strlen($buf) . "\n";

// Test 2: readfile WITHOUT Content-Length
ob_start();
header_remove('Content-Length');
clearstatcache();
readfile($file);
$buf2 = ob_get_clean();
echo "\nTEST2_NO_CL len=" . strlen($buf2) . "\n";

// Test 3: file_get_contents + echo
ob_start();
$data = file_get_contents($file);
echo $data;
$buf3 = ob_get_clean();
echo "\nTEST3_FGC len=" . strlen($buf3) . "\n";

// Test 4: fpassthru
ob_start();
$fp = fopen($file, 'rb');
fpassthru($fp);
fclose($fp);
$buf4 = ob_get_clean();
echo "\nTEST4_FPT len=" . strlen($buf4) . "\n";

echo "\nBuffer sizes: readfile_with_cl=" . strlen($buf) 
    . " readfile_no_cl=" . strlen($buf2) 
    . " fgc=" . strlen($buf3)
    . " fpassthru=" . strlen($buf4) . "\n";
