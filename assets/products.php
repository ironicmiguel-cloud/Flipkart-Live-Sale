<?php
header('Content-Type: application/json; charset=utf-8');
$file = __DIR__ . '/products.json';
if (!file_exists($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'products.json not found']);
    exit;
}
$etag = '"' . md5_file($file) . '"';
$lastModified = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';
// No long cache — always revalidate with ETag so admin edits show immediately
header('Cache-Control: no-cache, must-revalidate');
header('ETag: ' . $etag);
header('Last-Modified: ' . $lastModified);
if (
    (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && trim($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $lastModified)
) {
    http_response_code(304);
    exit;
}
echo file_get_contents($file);
