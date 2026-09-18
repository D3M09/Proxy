<?php
$ftp = ftp_connect('ftpupload.net', 21);
$login = ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

echo "Connected: " . ($login ? 'yes' : 'no') . "\n\n";

// Check the cached JS file
$files = [
    '/htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js',
    '/htdocs/Proxy/cache/mobile/mc/domainCheck.js',
];

foreach ($files as $f) {
    $size = ftp_size($ftp, $f);
    echo "$f: size=$size\n";
}

// List the cache/mobile/mc/ directory
echo "\nListing /htdocs/Proxy/cache/mobile/mc/:\n";
$lines = ftp_nlist($ftp, '/htdocs/Proxy/cache/mobile/mc/');
if ($lines) {
    foreach ($lines as $line) {
        echo "  $line\n";
    }
} else {
    echo "  (directory listing failed)\n";
    // Try rawlist
    $raw = ftp_rawlist($ftp, '/htdocs/Proxy/cache/mobile/mc/');
    if ($raw) foreach ($raw as $line) echo "  $line\n";
}

ftp_close($ftp);
