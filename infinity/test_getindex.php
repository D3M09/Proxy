<?php
$ftp = ftp_connect('ftpupload.net', 21);
$login = ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

$tmpFile = tempnam(sys_get_temp_dir(), 'ftp_');
$fp = fopen($tmpFile, 'w');
ftp_fget($ftp, $fp, '/htdocs/Proxy/index.php', FTP_ASCII);
fclose($fp);
$code = file_get_contents($tmpFile);
echo $code;
unlink($tmpFile);
ftp_close($ftp);
