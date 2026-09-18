<?php
$ftp = @ftp_connect('ftpupload.net', 21, 10);
$login = @ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

function ftpRead($ftp, $path) {
    $tmp = tempnam(sys_get_temp_dir(), 'ftp');
    if (@ftp_get($ftp, $tmp, $path, FTP_BINARY)) {
        $c = file_get_contents($tmp);
        @unlink($tmp);
        return $c;
    }
    @unlink($tmp);
    return false;
}

// Check cache/mobile/mc/
echo "=== /htdocs/Proxy/cache/mobile/mc/ ===\n";
$files = @ftp_nlist($ftp, '/htdocs/Proxy/cache/mobile/mc');
if ($files) { foreach ($files as $f) echo "  " . basename($f) . "\n"; } else echo "  (empty)\n";

// Check the specific file that's failing
echo "\n=== /htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js ===\n";
$tmp = tempnam(sys_get_temp_dir(), 'ftp');
if (@ftp_get($ftp, $tmp, '/htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js', FTP_BINARY)) {
    $c = file_get_contents($tmp);
    echo "  SIZE: " . strlen($c) . " bytes\n";
    echo "  FIRST 200: " . substr($c, 0, 200) . "\n";
} else {
    echo "  NOT CACHED\n";
}
@unlink($tmp);

// Check the domainCheck.js in cache
echo "\n=== Check domainCheck.js in cache ===\n";
$tmp2 = tempnam(sys_get_temp_dir(), 'ftp');
if (@ftp_get($ftp, $tmp2, '/htdocs/Proxy/cache/mobile/mc/domainCheck.js', FTP_BINARY)) {
    $c = file_get_contents($tmp2);
    echo "  SIZE: " . strlen($c) . " bytes\n";
} else {
    echo "  NOT CACHED\n";
}
@unlink($tmp2);

// Also check the /m/ path cache
echo "\n=== /htdocs/Proxy/cache/m/ ===\n";
$files = @ftp_nlist($ftp, '/htdocs/Proxy/cache/m');
if ($files) { foreach ($files as $f) { $bn = basename($f); if ($bn[0] != '.') echo "  $bn\n"; } } else echo "  (empty)\n";

ftp_close($ftp);
