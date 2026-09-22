<?php
require __DIR__ . '/Proxy/admin/store.php';
$pm = payment_methods_data_read();
if (!isset($pm['methods']['BKASH']['accounts'])) { echo "no bkash\n"; exit; }
$exists = false; foreach ($pm['methods']['BKASH']['accounts'] as $a) if (($a['number']??'')==='01800000001') $exists=true;
if (!$exists) {
    $pm['methods']['BKASH']['accounts'][] = ['number'=>'01800000001','name'=>'Test Rotate','enabled'=>true,'channels'=>[['name'=>'Personal','enabled'=>true,'min'=>100,'max'=>30000]]];
    payment_methods_data_write($pm);
    echo "added\n";
} else echo "already\n";
echo json_encode(array_column($pm['methods']['BKASH']['accounts'],'number'), JSON_PRETTY_PRINT);
