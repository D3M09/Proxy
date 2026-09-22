<?php
require __DIR__ . '/Proxy/admin/store.php';
$pm = payment_methods_data_read();
if (empty($pm['methods'])) {
    echo "empty\n";
    var_dump($pm);
    exit;
}
foreach ($pm['methods'] as $k => &$m) {
    if (isset($m['enabled'])) $m['enabled'] = true;
    foreach (($m['accounts'] ?? []) as &$acc) {
        $acc['enabled'] = true;
        foreach (($acc['channels'] ?? []) as &$ch) $ch['enabled'] = true;
    }
}
payment_methods_data_write($pm);
header('Content-Type: application/json');
echo json_encode($pm, JSON_PRETTY_PRINT);
