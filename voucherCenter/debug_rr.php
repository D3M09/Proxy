<?php
require __DIR__ . '/../Proxy/admin/store.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? 'add';
if ($action === 'add') {
    $pm = payment_methods_data_read();
    $exists = false; foreach (($pm['methods']['BKASH']['accounts'] ?? []) as $a) if (($a['number'] ?? '') === '01800000001') $exists = true;
    if (!$exists) {
        $pm['methods']['BKASH']['accounts'][] = ['number' => '01800000001', 'name' => 'Test Rotate', 'enabled' => true, 'channels' => [['name' => 'Personal', 'enabled' => true, 'min' => 100, 'max' => 30000]]];
        payment_methods_data_write($pm);
        echo json_encode(['added' => true, 'accounts' => array_column($pm['methods']['BKASH']['accounts'], 'number')]);
    } else echo json_encode(['already' => true, 'accounts' => array_column($pm['methods']['BKASH']['accounts'], 'number')]);
} elseif ($action === 'orders') {
    $tracks = $_GET['tracks'] ?? '';
    $list = array_filter(array_map('trim', explode(',', $tracks)));
    $orders = orders_read();
    $out = [];
    foreach ($list as $t) foreach ($orders as $o) if (($o['trackingNumber'] ?? '') === $t) $out[] = ['tracking' => substr($t,0,8), 'account' => $o['accountNumber'], 'method' => $o['paymentMethod'], 'channel' => $o['paymentChannel']];
    if (!$list) {
        $out = array_slice(array_reverse($orders), 0, 10);
        $out = array_map(fn($o)=>['tracking'=>substr($o['trackingNumber']??'',0,8),'account'=>$o['accountNumber']??'','method'=>$o['paymentMethod']??'','channel'=>$o['paymentChannel']??''], $out);
    }
    echo json_encode($out, JSON_PRETTY_PRINT);
} elseif ($action === 'rotation') {
    $pm = payment_methods_data_read();
    echo json_encode(['bkash' => $pm['methods']['BKASH']['accounts'] ?? [], 'rotation' => payment_rotation_read()], JSON_PRETTY_PRINT);
} elseif ($action === 'clean') {
    $pm = payment_methods_data_read();
    $pm['methods']['BKASH']['accounts'] = array_values(array_filter($pm['methods']['BKASH']['accounts'] ?? [], fn($a)=>($a['number']??'')!=='01800000001'));
    payment_methods_data_write($pm);
    $d = payment_rotation_read(); unset($d['BKASH:Personal']); payment_rotation_write($d);
    echo json_encode(['cleaned' => true, 'accounts' => array_column($pm['methods']['BKASH']['accounts'], 'number'), 'rotation' => $d]);
}
