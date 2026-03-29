<?php
header('Content-Type: application/json');
header('Cache-Control: public, max-age=300, must-revalidate');
header('Access-Control-Allow-Origin: *');

$allowed = ['dotd_deals', 'ticktock_deals', 'payment_settings'];
$f = preg_replace('/[^a-z0-9_]/', '', strtolower($_GET['f'] ?? ''));
if (!in_array($f, $allowed, true)) { http_response_code(400); echo json_encode(['error'=>'Invalid file']); exit; }

$path = __DIR__ . '/' . $f . '.json';
if (!file_exists($path)) { echo '[]'; exit; }

$etag = '"' . md5_file($path) . '"';
$lastModified = gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT';
header('ETag: ' . $etag);
header('Last-Modified: ' . $lastModified);
if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $lastModified)) {
    http_response_code(304);
    exit;
}
echo file_get_contents($path);
