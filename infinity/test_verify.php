<?php
// Quick check: did the upload work? Download a snippet of the server's index.php
$ftp = ftp_connect('ftpupload.net', 21);
ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

$tmpFile = tempnam(sys_get_temp_dir(), 'ftp_');
$fp = fopen($tmpFile, 'w');
ftp_fget($ftp, $fp, '/htdocs/Proxy/index.php', FTP_ASCII);
fclose($fp);
$code = file_get_contents($tmpFile);
unlink($tmpFile);

// Check if readfile is in serveFile
if (strpos($code, 'readfile($file)') !== false) {
    echo "PASS: readfile() found in serveFile\n";
} else {
    echo "FAIL: readfile() NOT found in serveFile\n";
}

// Check if <base href is gone
if (strpos($code, '<base href') !== false) {
    echo "WARN: <base href still present\n";
} else {
    echo "PASS: <base href removed\n";
}

// Check serveFile function
preg_match('/function serveFile.*?\n\}/s', $code, $m);
echo "\nserveFile function:\n" . ($m[0] ?? 'NOT FOUND') . "\n";

ftp_close($ftp);
