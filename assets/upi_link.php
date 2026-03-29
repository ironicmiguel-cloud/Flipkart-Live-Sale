<?php
require_once __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$ps_file = __DIR__ . '/payment_settings.json';
if (!file_exists($ps_file)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Payment config missing']);
    exit;
}
$ps = json_decode(file_get_contents($ps_file), true) ?? [];
$upi_id       = trim((string)($ps['upi_id'] ?? ''));
$merchant     = trim((string)($ps['merchant_name'] ?? 'Store'));
$mcc          = trim((string)($ps['mcc'] ?? '5262'));
$tr_prefix    = trim((string)($ps['tr_id'] ?? ''));
$currency     = trim((string)($ps['currency'] ?? 'INR')) ?: 'INR';
if ($upi_id === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'UPI not configured']);
    exit;
}

$body = json_decode(file_get_contents('php://input') ?: '[]', true);
if (!is_array($body)) $body = [];
$amount = round((float)($body['amount'] ?? 0), 2);
$note   = preg_replace('/[^a-zA-Z0-9 _\-]/', '', (string)($body['note'] ?? $merchant));
if ($amount <= 0 || $amount > 1000000) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid amount']);
    exit;
}
$tr = $tr_prefix . bin2hex(random_bytes(4));
$link = sprintf(
    'upi://pay?pa=%s&pn=%s&mc=%s&tr=%s&am=%.2f&cu=%s&tn=%s',
    urlencode($upi_id),
    urlencode($merchant),
    urlencode($mcc),
    urlencode($tr),
    $amount,
    urlencode($currency),
    urlencode($note)
);
$qs = ['pa'=>$upi_id,'pn'=>$merchant,'mc'=>$mcc,'tr'=>$tr,'am'=>number_format($amount,2,'.',''),'cu'=>$currency,'tn'=>$note];
echo json_encode([
    'ok'      => true,
    'link'    => $link,
    'gpay'    => 'tez://upi/pay?' . http_build_query($qs),
    'phonepe' => 'phonepe://pay?' . http_build_query($qs),
    'paytm'   => 'paytmmp://pay?' . http_build_query($qs),
    'tr'      => $tr,
    'amount'  => $amount,
]);
