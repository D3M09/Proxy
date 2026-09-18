<?php
$ftp = ftp_connect('ftpupload.net', 21);
$login = ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

$tmpFile = tempnam(sys_get_temp_dir(), 'ftp_');
$fp = fopen($tmpFile, 'w');
ftp_fget($ftp, $fp, '/htdocs/Proxy/config.php', FTP_ASCII);
fclose($fp);
echo file_get_contents($tmpFile);
unlink($tmpFile);
ftp_close($ftp);
