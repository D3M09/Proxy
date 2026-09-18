<?php
// Direct test of readfile on server files
$base = __DIR__ . '/Proxy/cache';
$files = [
    '/m/app.73f3eb85.js',
    '/mobile/mc/memberCenter.38cabcf7.js',
    '/mobile/mc/domainCheck.js',
    '/mobile/mc/loadMemberCenter.js',
    '/m/chunk-common.916936e4.js',
    '/m/vendor.encrypt.v2.dll.js',
];

header('Content-Type: text/plain; charset=UTF-8');

foreach ($files as $f) {
    $full = $base . $f;
    $exists = is_file($full);
    $size = $exists ? filesize($full) : -1;
    echo "$f exists=$exists size=$size\n";

    if ($exists && $size > 0) {
        $fp = fopen($full, 'rb');
        $chunk = fread($fp, 100);
        $first100 = bin2hex(substr($chunk, 0, 40));
        fclose($fp);
        echo "  first40hex: $first100\n";
        echo "  first40txt: " . substr($chunk, 0, 40) . "\n";
    }
}

// Now test readfile output size
echo "\n--- readfile output test ---\n";
foreach ($files as $f) {
    $full = $base . $f;
    if (!is_file($full)) continue;
    $size = filesize($full);
    ob_start();
    readfile($full);
    $output = ob_get_clean();
    echo "$f expected=$size got=" . strlen($output) . "\n";
}
