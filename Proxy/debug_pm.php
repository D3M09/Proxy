<?php
require __DIR__ . '/admin/store.php';
header('Content-Type: application/json');
echo json_encode(payment_methods_data_read(), JSON_PRETTY_PRINT);
