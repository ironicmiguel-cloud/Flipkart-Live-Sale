<?php
// CSRF token helpers — used by state_api and orders_api
function fk_csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    if (empty($_SESSION['fk_csrf'])) {
        $_SESSION['fk_csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['fk_csrf'];
}
function fk_csrf_verify(string $token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    $expected = (string)($_SESSION['fk_csrf'] ?? '');
    return $expected !== '' && hash_equals($expected, $token);
}

function fk_boot_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    $secure = false; // InfinityFree: SSL terminates at proxy
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
    }
    session_start();
}

function fk_json_read(string $file, $fallback) {
    if (!file_exists($file)) return $fallback;
    $raw = @file_get_contents($file);
    $data = json_decode($raw ?: '', true);
    return is_array($data) ? $data : $fallback;
}

function fk_json_write(string $file, $data): bool {
    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded === false) {
        error_log('fk_json_write: json_encode failed for ' . $file . ': ' . json_last_error_msg());
        return false;
    }
    $result = @file_put_contents($file, $encoded, LOCK_EX);
    if ($result === false) {
        error_log('fk_json_write: file_put_contents failed for ' . $file . ' (check permissions)');
        return false;
    }
    return true;
}

function fk_users_file(): string { return __DIR__ . '/users.json'; }
function fk_state_file(): string { return __DIR__ . '/state.json'; }
function fk_orders_file(): string { return __DIR__ . '/orders.json'; }
function fk_db_config_file(): string { return __DIR__ . '/db_config.php'; }
function fk_db_schema_file(): string { return __DIR__ . '/db_schema.sql'; }

function fk_db_config(): ?array {
    static $config = false;
    if ($config !== false) return $config;
    $file = fk_db_config_file();
    if (!file_exists($file)) return $config = null;
    $data = require $file;
    if (!is_array($data)) return $config = null;
    foreach (['host', 'database', 'username', 'password'] as $key) {
        if (!array_key_exists($key, $data)) return $config = null;
    }
    $data['port'] = (int)($data['port'] ?? 3306);
    $data['charset'] = trim((string)($data['charset'] ?? 'utf8mb4')) ?: 'utf8mb4';
    return $config = $data;
}

function fk_db(): ?PDO {
    static $pdo = false;
    static $schemaReady = false;
    if ($pdo !== false) return $pdo;
    $cfg = fk_db_config();
    if (!$cfg || !extension_loaded('pdo_mysql')) return $pdo = null;
    try {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']);
        $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        if (!$schemaReady) {
            // Use a file-flag so schema init only runs when schema file changes.
            // This avoids running CREATE TABLE on every request.
            $schemaFile  = fk_db_schema_file();
            $flagFile    = sys_get_temp_dir() . '/fk_schema_' . md5($schemaFile . (string)@filemtime($schemaFile)) . '.flag';
            if (!file_exists($flagFile)) {
                $schema = @file_get_contents($schemaFile) ?: '';
                foreach (array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $schema))) as $statement) {
                    if ($statement !== '') {
                        try { $pdo->exec($statement); } catch (Throwable $e) {
                            // Ignore "table already exists" errors only
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                error_log('FK schema error: ' . $e->getMessage());
                            }
                        }
                    }
                }
                @file_put_contents($flagFile, '1');
            }
            $schemaReady = true;
        }
        return $pdo;
    } catch (Throwable $e) {
        return $pdo = null;
    }
}

function fk_using_db(): bool { return fk_db() instanceof PDO; }

function fk_default_state(): array { return ['users' => [], 'guests' => []]; }

function fk_db_load_users(): array {
    $pdo = fk_db();
    if (!$pdo) return [];
    $rows = $pdo->query('SELECT id, name, mobile, email, joined, gender, dob, password_hash FROM fk_users ORDER BY created_at ASC')->fetchAll();
    return is_array($rows) ? array_values($rows) : [];
}

function fk_db_save_users(array $users): bool {
    $pdo = fk_db();
    if (!$pdo) return false;
    $sql = 'INSERT INTO fk_users (id, name, mobile, email, joined, gender, dob, password_hash)
            VALUES (:id, :name, :mobile, :email, :joined, :gender, :dob, :password_hash)
            ON DUPLICATE KEY UPDATE name=VALUES(name), mobile=VALUES(mobile), email=VALUES(email), joined=VALUES(joined), gender=VALUES(gender), dob=VALUES(dob), password_hash=VALUES(password_hash)';
    $stmt = $pdo->prepare($sql);
    foreach (array_values($users) as $user) {
        $stmt->execute([
            ':id' => (string)($user['id'] ?? ''),
            ':name' => trim((string)($user['name'] ?? '')),
            ':mobile' => preg_replace('/\D+/', '', (string)($user['mobile'] ?? '')),
            ':email' => strtolower(trim((string)($user['email'] ?? ''))),
            ':joined' => trim((string)($user['joined'] ?? '')),
            ':gender' => trim((string)($user['gender'] ?? '')),
            ':dob' => trim((string)($user['dob'] ?? '')),
            ':password_hash' => (string)($user['password_hash'] ?? ''),
        ]);
    }
    return true;
}

function fk_db_load_state(): array {
    $pdo = fk_db();
    if (!$pdo) return fk_default_state();
    $state = fk_default_state();
    $rows = $pdo->query('SELECT owner_type, owner_key, cart_json, wishlist_json FROM fk_state')->fetchAll();
    foreach ($rows as $row) {
        $scope = (($row['owner_type'] ?? '') === 'users') ? 'users' : 'guests';
        $key = (string)($row['owner_key'] ?? '');
        if ($key === '') continue;
        $state[$scope][$key] = [
            'cart' => fk_normalize_state_items('cart', json_decode((string)($row['cart_json'] ?? '[]'), true) ?: []),
            'wishlist' => fk_normalize_state_items('wishlist', json_decode((string)($row['wishlist_json'] ?? '[]'), true) ?: []),
        ];
    }
    return $state;
}

function fk_db_save_state(array $state): bool {
    $pdo = fk_db();
    if (!$pdo) return false;
    if (!isset($state['users']) || !is_array($state['users'])) $state['users'] = [];
    if (!isset($state['guests']) || !is_array($state['guests'])) $state['guests'] = [];
    $pdo->beginTransaction();
    try {
        $pdo->exec('DELETE FROM fk_state');
        $stmt = $pdo->prepare('INSERT INTO fk_state (owner_type, owner_key, cart_json, wishlist_json) VALUES (:owner_type, :owner_key, :cart_json, :wishlist_json)');
        foreach (['users', 'guests'] as $scope) {
            foreach ($state[$scope] as $key => $bucket) {
                if (!is_array($bucket)) continue;
                $stmt->execute([
                    ':owner_type' => $scope,
                    ':owner_key' => (string)$key,
                    ':cart_json' => json_encode(fk_normalize_state_items('cart', $bucket['cart'] ?? []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    ':wishlist_json' => json_encode(fk_normalize_state_items('wishlist', $bucket['wishlist'] ?? []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return false;
    }
}

function fk_db_read_orders_for_current(): array {
    $pdo = fk_db();
    if (!$pdo) return [];
    $email = fk_current_user_email();
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT order_json FROM fk_orders WHERE owner_email = :owner_email ORDER BY created_at DESC, updated_at DESC');
        $stmt->execute([':owner_email' => $email]);
    } else {
        $stmt = $pdo->prepare('SELECT order_json FROM fk_orders WHERE guest_session = :guest_session ORDER BY created_at DESC, updated_at DESC');
        $stmt->execute([':guest_session' => session_id()]);
    }
    $orders = [];
    foreach ($stmt->fetchAll() as $row) {
        $order = json_decode((string)($row['order_json'] ?? '{}'), true);
        if (is_array($order)) $orders[] = $order;
    }
    return array_values($orders);
}

function fk_db_upsert_order(array $order): bool {
    $pdo = fk_db();
    if (!$pdo) return false;
    $stmt = $pdo->prepare('INSERT INTO fk_orders (id, checkout_token, owner_email, guest_session, created_at, order_json)
        VALUES (:id, :checkout_token, :owner_email, :guest_session, :created_at, :order_json)
        ON DUPLICATE KEY UPDATE owner_email=VALUES(owner_email), guest_session=VALUES(guest_session), created_at=VALUES(created_at), order_json=VALUES(order_json)');
    return $stmt->execute([
        ':id' => (string)($order['id'] ?? ''),
        ':checkout_token' => (string)($order['checkoutToken'] ?? ''),
        ':owner_email' => strtolower(trim((string)($order['user']['email'] ?? fk_current_user_email()))),
        ':guest_session' => fk_current_user_email() === '' ? session_id() : '',
        ':created_at' => (string)($order['createdAt'] ?? gmdate('c')),
        ':order_json' => json_encode($order, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    ]);
}

function fk_db_replace_orders_for_current(array $orders): bool {
    $pdo = fk_db();
    if (!$pdo) return false;
    $email = fk_current_user_email();
    $guest = $email === '' ? session_id() : '';
    $pdo->beginTransaction();
    try {
        if ($email !== '') {
            $del = $pdo->prepare('DELETE FROM fk_orders WHERE owner_email = :owner_email');
            $del->execute([':owner_email' => $email]);
        } else {
            $del = $pdo->prepare('DELETE FROM fk_orders WHERE guest_session = :guest_session');
            $del->execute([':guest_session' => $guest]);
        }
        foreach ($orders as $order) {
            fk_db_upsert_order($order);
        }
        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return false;
    }
}

function fk_db_update_order(string $id, array $patch): ?array {
    $pdo = fk_db();
    if (!$pdo) return null;
    $email = fk_current_user_email();
    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT order_json FROM fk_orders WHERE id = :id AND owner_email = :owner_email LIMIT 1');
        $stmt->execute([':id' => $id, ':owner_email' => $email]);
    } else {
        $stmt = $pdo->prepare('SELECT order_json FROM fk_orders WHERE id = :id AND guest_session = :guest_session LIMIT 1');
        $stmt->execute([':id' => $id, ':guest_session' => session_id()]);
    }
    $row = $stmt->fetch();
    if (!$row) return null;
    $existing = json_decode((string)($row['order_json'] ?? '{}'), true);
    if (!is_array($existing)) return null;
    $updated = array_replace_recursive($existing, $patch);
    $stmt = $pdo->prepare('UPDATE fk_orders SET order_json = :order_json, created_at = :created_at WHERE id = :id');
    $stmt->execute([
        ':order_json' => json_encode($updated, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ':created_at' => (string)($updated['createdAt'] ?? ($existing['createdAt'] ?? gmdate('c'))),
        ':id' => $id,
    ]);
    return $updated;
}

function fk_orders_migrate_owner_email(string $oldEmail, string $newEmail): void {
    $oldEmail = strtolower(trim($oldEmail));
    $newEmail = strtolower(trim($newEmail));
    if ($oldEmail === '' || $newEmail === '' || $oldEmail === $newEmail) return;
    $pdo = fk_db();
    if ($pdo) {
        $stmt = $pdo->prepare('UPDATE fk_orders SET owner_email = :new_email WHERE owner_email = :old_email');
        $stmt->execute([':new_email' => $newEmail, ':old_email' => $oldEmail]);
        return;
    }
    $file = fk_orders_file();
    $orders = fk_json_read($file, []);
    foreach ($orders as &$order) {
        if (strtolower(trim((string)($order['user']['email'] ?? ''))) === $oldEmail) {
            $order['user']['email'] = $newEmail;
        }
    }
    unset($order);
    fk_json_write($file, $orders);
}

function fk_load_users(): array {
    if (fk_using_db()) return fk_db_load_users();
    $file = fk_users_file();
    if (!file_exists($file)) fk_json_write($file, []);
    $users = fk_json_read($file, []);
    return is_array($users) ? array_values($users) : [];
}

function fk_save_users(array $users): bool {
    if (fk_using_db()) return fk_db_save_users(array_values($users));
    return fk_json_write(fk_users_file(), array_values($users));
}

function fk_load_state(): array {
    static $stateCache = null;
    if ($stateCache !== null) return $stateCache;
    if (fk_using_db()) { $stateCache = fk_db_load_state(); return $stateCache; }
    $file = fk_state_file();
    if (!file_exists($file)) fk_json_write($file, fk_default_state());
    $state = fk_json_read($file, fk_default_state());
    if (!isset($state['users']) || !is_array($state['users'])) $state['users'] = [];
    if (!isset($state['guests']) || !is_array($state['guests'])) $state['guests'] = [];
    $stateCache = $state;
    return $stateCache;
}

function fk_save_state(array $state): bool {
    // Bust per-request cache so subsequent reads in same request see updated state
    static $stateCache;
    $stateCache = $state;
    if (fk_using_db()) return fk_db_save_state($state);
    if (!isset($state['users']) || !is_array($state['users'])) $state['users'] = [];
    if (!isset($state['guests']) || !is_array($state['guests'])) $state['guests'] = [];
    return fk_json_write(fk_state_file(), $state);
}

function fk_user_public(array $user): array {
    return [
        'id' => (string)($user['id'] ?? ''),
        'name' => trim((string)($user['name'] ?? '')),
        'mobile' => preg_replace('/\D+/', '', (string)($user['mobile'] ?? '')),
        'email' => trim((string)($user['email'] ?? '')),
        'joined' => trim((string)($user['joined'] ?? '')),
        'gender' => trim((string)($user['gender'] ?? '')),
        'dob' => trim((string)($user['dob'] ?? '')),
    ];
}

function fk_find_user_index(array $users, string $identifier): int {
    $identifier = trim(strtolower($identifier));
    foreach ($users as $idx => $user) {
        $email = trim(strtolower((string)($user['email'] ?? '')));
        $mobile = preg_replace('/\D+/', '', (string)($user['mobile'] ?? ''));
        if ($email === $identifier || $mobile === preg_replace('/\D+/', '', $identifier)) return $idx;
    }
    return -1;
}

function fk_current_user_email(): string {
    return strtolower(trim((string)($_SESSION['fk_user_email'] ?? '')));
}

function fk_state_owner_ref(): array {
    $email = fk_current_user_email();
    if ($email !== '') return ['scope' => 'users', 'key' => $email];
    return ['scope' => 'guests', 'key' => session_id()];
}

function fk_ensure_bucket(array &$state, string $scope, string $key): array {
    if (!isset($state[$scope][$key]) || !is_array($state[$scope][$key])) {
        $state[$scope][$key] = ['cart' => [], 'wishlist' => []];
    }
    if (!isset($state[$scope][$key]['cart']) || !is_array($state[$scope][$key]['cart'])) $state[$scope][$key]['cart'] = [];
    if (!isset($state[$scope][$key]['wishlist']) || !is_array($state[$scope][$key]['wishlist'])) $state[$scope][$key]['wishlist'] = [];
    return $state[$scope][$key];
}

function fk_normalize_scalar($value): string {
    return trim(preg_replace('/\s+/', ' ', (string)$value));
}

// Server-side product price lookup — prevents client price injection in cart
function fk_server_product_price(string $id): ?array {
    static $catalog = null;
    if ($catalog === null) {
        $f = __DIR__ . '/products.json';
        $raw = @file_get_contents($f);
        $decoded = $raw ? json_decode($raw, true) : null;
        $list = is_array($decoded) ? $decoded : [];
        $catalog = [];
        foreach ($list as $p) {
            if (isset($p['id'])) $catalog[$p['id']] = ['price' => (float)$p['price'], 'mrp' => (float)$p['mrp']];
        }
    }
    return $catalog[$id] ?? null;
}

function fk_normalize_cart_item($item): ?array {
    if (!is_array($item)) return null;
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($item['id'] ?? ''));
    if ($id === '') return null;
    $qty = max(1, min(10, (int)($item['qty'] ?? 1)));
    // SECURITY: lock price to server catalog — ignore client-supplied value
    $serverPrices = fk_server_product_price($id);
    $price = $serverPrices ? $serverPrices['price'] : (float)($item['price'] ?? 0);
    $mrp   = $serverPrices ? $serverPrices['mrp']   : (float)($item['mrp'] ?? $price);
    return [
        'id'    => $id,
        'name'  => fk_normalize_scalar($item['name'] ?? 'Product'),
        'brand' => fk_normalize_scalar($item['brand'] ?? ''),
        'price' => $price,
        'mrp'   => $mrp,
        'off'   => fk_normalize_scalar($item['off'] ?? ''),
        'img'   => fk_normalize_scalar($item['img'] ?? ''),
        'qty'   => $qty,
    ];
}

function fk_normalize_wishlist_item($item): ?array {
    if (!is_array($item)) return null;
    $id = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)($item['id'] ?? ''));
    if ($id === '') return null;
    $serverPrices = fk_server_product_price($id);
    $price = $serverPrices ? $serverPrices['price'] : (float)($item['price'] ?? 0);
    $mrp   = $serverPrices ? $serverPrices['mrp']   : (float)($item['mrp'] ?? $price);
    return [
        'id'    => $id,
        'name'  => fk_normalize_scalar($item['name'] ?? 'Product'),
        'brand' => fk_normalize_scalar($item['brand'] ?? ''),
        'price' => $price,
        'mrp'   => $mrp,
        'img'   => fk_normalize_scalar($item['img'] ?? ''),
        'added' => (int)($item['added'] ?? round(microtime(true) * 1000)),
    ];
}

function fk_normalize_state_items(string $type, $items): array {
    $out = [];
    if (!is_array($items)) return $out;
    foreach ($items as $item) {
        $norm = $type === 'cart' ? fk_normalize_cart_item($item) : fk_normalize_wishlist_item($item);
        if (!$norm) continue;
        if ($type === 'cart') {
            $found = false;
            foreach ($out as &$existing) {
                if (($existing['id'] ?? '') === $norm['id']) {
                    $existing['qty'] = max(1, min(10, (int)$existing['qty'] + (int)$norm['qty']));
                    $found = true;
                    break;
                }
            }
            unset($existing);
            if (!$found) $out[] = $norm;
        } else {
            $exists = false;
            foreach ($out as $existing) {
                if (($existing['id'] ?? '') === $norm['id']) { $exists = true; break; }
            }
            if (!$exists) $out[] = $norm;
        }
    }
    return array_values($out);
}

function fk_get_state_items(array $state, string $scope, string $key, string $type): array {
    return array_values((array)($state[$scope][$key][$type] ?? []));
}

function fk_set_state_items(array &$state, string $scope, string $key, string $type, array $items): array {
    fk_ensure_bucket($state, $scope, $key);
    $state[$scope][$key][$type] = fk_normalize_state_items($type, $items);
    return $state[$scope][$key][$type];
}

function fk_merge_state_lists(string $type, array $a, array $b): array {
    return fk_normalize_state_items($type, array_merge($a, $b));
}

function fk_merge_guest_to_user(array &$state, string $userEmail, array $guestCart = [], array $guestWishlist = []): void {
    $guestId = session_id();
    fk_ensure_bucket($state, 'users', $userEmail);
    $userCart = fk_get_state_items($state, 'users', $userEmail, 'cart');
    $userWish = fk_get_state_items($state, 'users', $userEmail, 'wishlist');
    $guestStoredCart = fk_get_state_items($state, 'guests', $guestId, 'cart');
    $guestStoredWish = fk_get_state_items($state, 'guests', $guestId, 'wishlist');

    $state['users'][$userEmail]['cart'] = fk_merge_state_lists('cart', $userCart, array_merge($guestStoredCart, fk_normalize_state_items('cart', $guestCart)));
    $state['users'][$userEmail]['wishlist'] = fk_merge_state_lists('wishlist', $userWish, array_merge($guestStoredWish, fk_normalize_state_items('wishlist', $guestWishlist)));
    unset($state['guests'][$guestId]);
}

function fk_clear_guest_state(array &$state): void {
    unset($state['guests'][session_id()]);
}

function fk_respond(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}
