<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
require_once __DIR__ . '/_store_lib.php';
fk_boot_session();

$store = fk_orders_file();
if (!file_exists($store)) {
    @file_put_contents($store, '[]');
}

function read_orders(string $store): array {
    if (fk_using_db()) {
        $orders = fk_db_read_orders_for_current();
        usort($orders, function($a, $b) {
            return strcmp((string)($b['createdAt'] ?? ''), (string)($a['createdAt'] ?? ''));
        });
        return array_values($orders);
    }
    $raw = @file_get_contents($store);
    $data = json_decode($raw ?: '[]', true);
    $data = is_array($data) ? $data : [];
    $email = fk_current_user_email();
    $sessionId = session_id();
    $filtered = array_values(array_filter($data, function($order) use ($email, $sessionId) {
        if (!is_array($order)) return false;
        $ownerEmail = strtolower(trim((string)($order['user']['email'] ?? '')));
        $guestSession = trim((string)($order['guestSession'] ?? ''));
        if ($email !== '') return $ownerEmail === $email;
        return $ownerEmail === '' && $guestSession === $sessionId;
    }));
    usort($filtered, function($a, $b) {
        return strcmp((string)($b['createdAt'] ?? ''), (string)($a['createdAt'] ?? ''));
    });
    return $filtered;
}
function write_orders(string $store, array $orders): bool {
    if (fk_using_db()) {
        return fk_db_replace_orders_for_current($orders);
    }
    return false !== @file_put_contents($store, json_encode(array_values($orders), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function respond(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
// Server-side product catalog cache (loaded once per request)
function fk_get_server_product(string $id): ?array {
    static $catalog = null;
    if ($catalog === null) {
        $f = __DIR__ . '/products.json';
        $raw = @file_get_contents($f);
        $decoded = $raw ? json_decode($raw, true) : null;
        $catalog = is_array($decoded) ? $decoded : [];
        // Index by id for O(1) lookup
        $indexed = [];
        foreach ($catalog as $p) {
            if (isset($p['id'])) $indexed[$p['id']] = $p;
        }
        $catalog = $indexed;
    }
    return $catalog[$id] ?? null;
}

function normalize_order(array $order): array {
    $order['id'] = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($order['id'] ?? '')));
    $order['checkoutToken'] = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($order['checkoutToken'] ?? '')));
    $order['createdAt'] = (string)($order['createdAt'] ?? gmdate('c'));
    $order['date'] = (string)($order['date'] ?? '');
    // SECURITY: Never trust client-provided 'paid' status. 
    // Only server-verified statuses are allowed. Client can set processing/payment_pending/cod_pending.
    $allowedStatuses = ['processing', 'payment_pending', 'cod_pending', 'shipped', 'delivered', 'cancelled'];
    $rawStatus = strtolower(trim((string)($order['status'] ?? 'processing')));
    $order['status'] = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'payment_pending';
    $order['deliveryMsg'] = trim((string)($order['deliveryMsg'] ?? ''));
    $order['deliveryDate'] = trim((string)($order['deliveryDate'] ?? ''));
    $order['paymentMode'] = preg_replace('/[^a-z]/', '', strtolower((string)($order['paymentMode'] ?? 'upi')));
    $order['total'] = (float)($order['total'] ?? 0);
    $order['items'] = (int)($order['items'] ?? 1);
    $order['progress'] = max(0, min(100, (int)($order['progress'] ?? 25)));

    $user = is_array($order['user'] ?? null) ? $order['user'] : [];
    $currentEmail = fk_current_user_email();
    $order['user'] = [
        'email' => strtolower(trim((string)($user['email'] ?? $currentEmail))),
        'mobile' => trim((string)($user['mobile'] ?? '')),
        'name' => trim((string)($user['name'] ?? '')),
    ];

    $product   = is_array($order['product'] ?? null) ? $order['product'] : [];
    $productId = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($product['id'] ?? ''));
    // SECURITY: look up real price from server catalog — never trust client
    $serverProduct = $productId !== '' ? fk_get_server_product($productId) : null;
    $serverPrice   = $serverProduct ? (float)$serverProduct['price'] : 0.0;
    $serverMrp     = $serverProduct ? (float)$serverProduct['mrp']   : $serverPrice;
    $order['product'] = [
        'id'    => $productId,
        'brand' => trim((string)($product['brand'] ?? ($serverProduct['brand'] ?? ''))),
        'name'  => trim((string)($product['name']  ?? ($serverProduct['name']  ?? 'Product'))),
        'price' => $serverPrice > 0 ? $serverPrice : (float)($product['price'] ?? 0),
        'img'   => trim((string)($product['img']   ?? '')),
    ];

    $address = is_array($order['address'] ?? null) ? $order['address'] : [];
    $order['address'] = [
        'name' => trim((string)($address['name'] ?? '')),
        'phone' => trim((string)($address['phone'] ?? '')),
        'line' => trim((string)($address['line'] ?? ''))
    ];

    $pricing = is_array($order['pricing'] ?? null) ? $order['pricing'] : [];
    $delivery  = (float)($pricing['delivery'] ?? 0);
    $donation  = (float)($pricing['donation'] ?? 0);
    $qty       = (int)($order['items'] ?? 1);
    // SECURITY: if server price known, recalculate subtotal server-side
    $lockedSubtotal = $serverPrice > 0
        ? round($serverPrice * max(1, $qty), 2)
        : (float)($pricing['subtotal'] ?? $order['total']);
    $lockedTotal = round($lockedSubtotal + $delivery + $donation, 2);
    // Sync top-level total to locked value
    $order['total'] = $lockedTotal;
    $order['pricing'] = [
        'subtotal' => $lockedSubtotal,
        'mrp'      => $serverMrp > 0 ? round($serverMrp * max(1, $qty), 2) : (float)($pricing['mrp'] ?? $lockedSubtotal),
        'delivery' => $delivery,
        'donation' => $donation,
        'total'    => $lockedTotal,
    ];

    $order['cart'] = is_array($order['cart'] ?? null) ? array_values($order['cart']) : [];
    $order['guestSession'] = $order['user']['email'] === '' ? session_id() : '';
    return $order;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'GET') {
    $orders = read_orders($store);
    respond(['ok' => true, 'orders' => $orders]);
}

$body = json_decode(file_get_contents('php://input') ?: '[]', true);
if (!is_array($body)) $body = [];
$action = strtolower((string)($body['action'] ?? ''));

if ($action === 'create') {
    $order = normalize_order(is_array($body['order'] ?? null) ? $body['order'] : []);
    if ($order['id'] === '' || $order['checkoutToken'] === '') {
        respond(['ok' => false, 'error' => 'Missing order id or checkout token'], 400);
    }
    if (fk_using_db()) {
        fk_db_upsert_order($order);
        respond(['ok' => true, 'order' => $order]);
    }
    $orders = read_orders($store);
    $replaced = false;
    foreach ($orders as $idx => $existing) {
        if (($existing['checkoutToken'] ?? '') === $order['checkoutToken'] || ($existing['id'] ?? '') === $order['id']) {
            $orders[$idx] = $order;
            $replaced = true;
            break;
        }
    }
    if (!$replaced) array_unshift($orders, $order);
    if (!write_orders($store, $orders)) {
        respond(['ok' => false, 'error' => 'Order save failed — storage error'], 500);
    }
    respond(['ok' => true, 'order' => $order]);
}

if ($action === 'replace') {
    $orders = is_array($body['orders'] ?? null) ? $body['orders'] : [];
    $normalized = array_map('normalize_order', $orders);
    usort($normalized, function($a, $b) {
        return strcmp((string)($b['createdAt'] ?? ''), (string)($a['createdAt'] ?? ''));
    });
    if (!write_orders($store, $normalized)) {
        respond(['ok' => false, 'error' => 'Orders save failed — storage error'], 500);
    }
    respond(['ok' => true, 'count' => count($normalized)]);
}

if ($action === 'update') {
    $id = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($body['id'] ?? '')));
    $patch = is_array($body['patch'] ?? null) ? $body['patch'] : [];
    if ($id === '') respond(['ok' => false, 'error' => 'Missing order id'], 400);
    if (fk_using_db()) {
        $updated = fk_db_update_order($id, $patch);
        if ($updated) respond(['ok' => true, 'order' => normalize_order($updated)]);
        respond(['ok' => false, 'error' => 'Order not found'], 404);
    }
    $orders = read_orders($store);
    foreach ($orders as $idx => $existing) {
        if (($existing['id'] ?? '') === $id) {
            $orders[$idx] = normalize_order(array_replace_recursive($existing, $patch));
            if (!write_orders($store, $orders)) {
                respond(['ok' => false, 'error' => 'Order update failed — storage error'], 500);
            }
            respond(['ok' => true, 'order' => $orders[$idx]]);
        }
    }
    respond(['ok' => false, 'error' => 'Order not found'], 404);
}

respond(['ok' => false, 'error' => 'Unsupported action'], 400);
