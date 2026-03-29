<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
require_once __DIR__ . '/_store_lib.php';
fk_boot_session();

// Parse body once — needed for type detection on non-GET and for CSRF
$bodyRaw = file_get_contents('php://input') ?: '[]';
$body    = json_decode($bodyRaw, true);
if (!is_array($body)) $body = [];

$type = strtolower(trim((string)($_GET['type'] ?? '')));
if (!in_array($type, ['cart', 'wishlist'], true)) {
    $type = strtolower(trim((string)($body['type'] ?? '')));
}
if (!in_array($type, ['cart', 'wishlist'], true)) fk_respond(['ok' => false, 'error' => 'Invalid state type'], 400);

$state = fk_load_state();
$owner = fk_state_owner_ref();
fk_ensure_bucket($state, $owner['scope'], $owner['key']);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    // Return items + CSRF token for frontend to store
    $items = fk_get_state_items($state, $owner['scope'], $owner['key'], $type);
    fk_respond([
        'ok'      => true,
        'loggedIn'=> fk_current_user_email() !== '',
        'type'    => $type,
        'items'   => $items,
        'csrf'    => fk_csrf_token(),
    ]);
}

// SECURITY: CSRF check on all mutating requests (POST/DELETE/etc.)
$csrfToken = (string)($body['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
if (!fk_csrf_verify($csrfToken)) {
    fk_respond(['ok' => false, 'error' => 'Invalid CSRF token'], 403);
}

$action  = strtolower(trim((string)($body['action'] ?? 'replace')));
$current = fk_get_state_items($state, $owner['scope'], $owner['key'], $type);

if ($action === 'replace') {
    $items = fk_set_state_items($state, $owner['scope'], $owner['key'], $type, is_array($body['items'] ?? null) ? $body['items'] : []);
    if (!fk_save_state($state)) fk_respond(['ok' => false, 'error' => 'State save failed'], 500);
    fk_respond(['ok' => true, 'items' => $items]);
}

if ($action === 'clear') {
    $items = fk_set_state_items($state, $owner['scope'], $owner['key'], $type, []);
    if (!fk_save_state($state)) fk_respond(['ok' => false, 'error' => 'State save failed'], 500);
    fk_respond(['ok' => true, 'items' => $items]);
}

if ($action === 'toggle' && $type === 'wishlist') {
    $incoming = fk_normalize_wishlist_item(is_array($body['item'] ?? null) ? $body['item'] : []);
    if (!$incoming) fk_respond(['ok' => false, 'error' => 'Invalid wishlist item'], 400);
    $exists = false;
    $next = [];
    foreach ($current as $item) {
        if (($item['id'] ?? '') === $incoming['id']) { $exists = true; continue; }
        $next[] = $item;
    }
    if (!$exists) $next[] = $incoming;
    $items = fk_set_state_items($state, $owner['scope'], $owner['key'], 'wishlist', $next);
    if (!fk_save_state($state)) fk_respond(['ok' => false, 'error' => 'State save failed'], 500);
    fk_respond(['ok' => true, 'items' => $items, 'active' => !$exists]);
}

if (($action === 'add' || $action === 'upsert') && $type === 'cart') {
    $incoming = fk_normalize_cart_item(is_array($body['item'] ?? null) ? $body['item'] : []);
    if (!$incoming) fk_respond(['ok' => false, 'error' => 'Invalid cart item'], 400);
    $next = [];
    $found = false;
    foreach ($current as $item) {
        if (($item['id'] ?? '') === $incoming['id']) {
            $item['qty'] = max(1, min(10, (int)$item['qty'] + (int)$incoming['qty']));
            $found = true;
        }
        $next[] = $item;
    }
    if (!$found) $next[] = $incoming;
    $items = fk_set_state_items($state, $owner['scope'], $owner['key'], 'cart', $next);
    if (!fk_save_state($state)) fk_respond(['ok' => false, 'error' => 'State save failed'], 500);
    fk_respond(['ok' => true, 'items' => $items]);
}

if ($action === 'remove') {
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($body['id'] ?? ''));
    $next = array_values(array_filter($current, function($item) use ($id) { return ($item['id'] ?? '') !== $id; }));
    $items = fk_set_state_items($state, $owner['scope'], $owner['key'], $type, $next);
    if (!fk_save_state($state)) fk_respond(['ok' => false, 'error' => 'State save failed'], 500);
    fk_respond(['ok' => true, 'items' => $items]);
}

if ($action === 'set_qty' && $type === 'cart') {
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($body['id'] ?? ''));
    $qty = max(1, min(10, (int)($body['qty'] ?? 1)));
    $next = [];
    foreach ($current as $item) {
        if (($item['id'] ?? '') === $id) $item['qty'] = $qty;
        $next[] = $item;
    }
    $items = fk_set_state_items($state, $owner['scope'], $owner['key'], 'cart', $next);
    if (!fk_save_state($state)) fk_respond(['ok' => false, 'error' => 'State save failed'], 500);
    fk_respond(['ok' => true, 'items' => $items]);
}

fk_respond(['ok' => false, 'error' => 'Unsupported action'], 400);
