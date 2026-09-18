<?php
$ftp = ftp_connect('ftpupload.net', 21);
$login = ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

// Check file permissions and content
$f = '/htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js';
echo "File: $f\n";
echo "Size: " . ftp_size($ftp, $f) . "\n";

// Get rawlist for permissions
$raw = ftp_rawlist($ftp, '/htdocs/Proxy/cache/mobile/mc/');
foreach ($raw as $line) {
    if (strpos($line, 'memberCenter.38cabcf7') !== false) {
        echo "Raw: $line\n";
    }
}

// Download first 200 bytes to verify content
$tmpFile = tempnam(sys_get_temp_dir(), 'ftp_');
$fp = fopen($tmpFile, 'w');
ftp_fget($ftp, $fp, $f, FTP_BINARY, 0, 200);
fclose($fp);
echo "First 200 bytes: " . file_get_contents($tmpFile) . "\n";

// Check server config
echo "\nServer config.php:\n";
$tmpFile2 = tempnam(sys_get_temp_dir(), 'ftp_');
$fp2 = fopen($tmpFile2, 'w');
ftp_fget($ftp, $fp2, '/htdocs/Proxy/config.php', FTP_ASCII);
fclose($fp2);
echo file_get_contents($tmpFile2) . "\n";

// Check .htaccess in root
echo "\nRoot .htaccess:\n";
$tmpFile3 = tempnam(sys_get_temp_dir(), 'ftp_');
$fp3 = fopen($tmpFile3, 'w');
ftp_fget($ftp, $fp3, '/htdocs/.htaccess', FTP_ASCII);
fclose($fp3);
echo file_get_contents($tmpFile3) . "\n";

// Check Proxy .htaccess
echo "\nProxy .htaccess:\n";
$tmpFile4 = tempnam(sys_get_temp_dir(), 'ftp_');
$fp4 = fopen($tmpFile4, 'w');
ftp_fget($ftp, $fp4, '/htdocs/Proxy/.htaccess', FTP_ASCII);
fclose($fp4);
echo file_get_contents($fp4) . "\n";

ftp_close($ftp);
unlink($tmpFile);
unlink($tmpFile2);
unlink($tmpFile3);
unlink($tmpFile4);
