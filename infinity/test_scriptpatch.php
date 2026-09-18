<?php
$ftp = ftp_connect('ftpupload.net', 21);
ftp_login($ftp, 'if0_42944398', 'dXxrecJDQMIYsC3');
ftp_pasv($ftp, true);
$tmpFile = tempnam(sys_get_temp_dir(), 'ftp_');
$fp = fopen($tmpFile, 'w');
ftp_fget($ftp, $fp, '/htdocs/Proxy/cache/mobile/mc/memberCenter.38cabcf7.js', FTP_BINARY);
fclose($fp);
$data = file_get_contents($tmpFile);
echo 'Original size:' . strlen($data) . "\n";

// Test applyScriptPatches
DISABLE_AFFILIATE_REDIRECT: true; // won't work as constant but let's inline
$body = $data;
if (strpos($body, 'affiliateRedirect') === false && strpos($body, '3===') === false) {
    echo "SKIPPED (not merchant script)\n";
} elseif (strpos($body, 'affiliateRedirect:!0') !== false) {
    $body = str_replace('affiliateRedirect:!0', 'affiliateRedirect:!1', $body);
    echo "Replaced affiliateRedirect:!0\n";
} else {
    echo "Trying regex...\n";
    $patched = preg_replace(
        '/3===([A-Za-z_$][A-Za-z0-9_$]*)\.length&&!\/\^\(www\|preview\|sit\)(?:\\$)?\/\.test\(\1\[0\]\)/',
        '!1',
        $body,
        1
    );
    $err = preg_last_error();
    echo "preg_replace error: $err\n";
    if ($patched === null) {
        echo "preg_replace returned NULL! Using original\n";
    } else {
        echo "After patch: " . strlen($patched) . " bytes\n";
        $body = $patched;
    }
}

echo "Final size: " . strlen($body) . "\n";
unlink($tmpFile);
ftp_close($ftp);
