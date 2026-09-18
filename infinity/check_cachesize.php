<?php
$ftp = ftp_connect('ftpupload.net', 21);
ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

$files = [
    '/htdocs/Proxy/cache/m/app.73f3eb85.js',
    '/htdocs/Proxy/cache/m/chunk-common.916936e4.js',
    '/htdocs/Proxy/cache/m/vendor.encrypt.v2.dll.js',
    '/htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js',
    '/htdocs/Proxy/cache/mobile/mc/domainCheck.js',
    '/htdocs/Proxy/cache/mobile/mc/loadMemberCenter.js',
];

foreach ($files as $f) {
    $size = ftp_size($ftp, $f);
    echo "$f: $size bytes\n";
}

// List m/ directory to see what's there
echo "\n=== m/ directory ===\n";
$raw = ftp_rawlist($ftp, '/htdocs/Proxy/cache/m/');
if ($raw) foreach ($raw as $l) echo "  $l\n";

ftp_close($ftp);
