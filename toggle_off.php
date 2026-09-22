<?php
require __DIR__ . '/Proxy/admin/store.php';
$c = content_load();
$c['voucher']['enabled'] = false;
content_save($c);
echo json_encode(['enabled'=>$c['voucher']['enabled']]);
