<?php
$ftp = ftp_connect('ftpupload.net', 21);
$login = ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);

// Download the full JS file to local temp
$tmpFile = tempnam(sys_get_temp_dir(), 'ftp_');
$fp = fopen($tmpFile, 'w');
ftp_fget($ftp, $fp, '/htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js', FTP_BINARY);
fclose($fp);
$size = filesize($tmpFile);
echo "Downloaded: $size bytes\n";

// Check if applyBrand corrupts it
// Simulate what serveFile does
$data = file_get_contents($tmpFile);
echo "file_get_contents: " . strlen($data) . " bytes\n";

// Test brandReplaceText
$pattern = '/(?<![\w.\/-])' . preg_quote('1333bet', '/') . '(?!\.[a-z])/i';
$result = @preg_replace($pattern, 'LottoGames', $data);
if ($result === null) {
    echo "preg_replace RETURNED NULL! Error: " . preg_last_error() . "\n";
} else {
    echo "After brandReplace: " . strlen($result) . " bytes\n";
}

// Check if there's any issue with the content type
echo "First 100: " . substr($data, 0, 100) . "\n";

unlink($tmpFile);
ftp_close($ftp);
