<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

require_once __DIR__ . '/../Proxy/admin/store.php';
require_once __DIR__ . '/../Proxy/admin/includes/functions.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$tracking = $input['trackingNumber'] ?? '';
$payerAccount = $input['payerAccount'] ?? '';
$trxId = $input['trxId'] ?? $input['transferCode'] ?? '';

if ($tracking === '') {
    http_response_code(400);
    echo json_encode(['error' => 'trackingNumber is required']);
    exit;
}

$order = find_order_by_tracking($tracking);
if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found']);
    exit;
}

if ($order['status'] !== 'WaitingConfirm') {
    echo json_encode(['error' => 'Order is not pending', 'status' => $order['status']]);
    exit;
}

$updates = [
    'status' => 'Confirmed',
    'confirmedAt' => date('c'),
];
if ($payerAccount !== '') $updates['payerAccount'] = $payerAccount;
if ($trxId !== '') $updates['trxId'] = $trxId;

update_order($tracking, $updates);

echo json_encode([
    'success' => true,
    'message' => 'Transaction confirmed',
    'trackingNumber' => $tracking,
    'status' => 'Confirmed',
], JSON_PRETTY_PRINT);
