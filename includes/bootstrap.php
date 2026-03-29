<?php
/**
 * includes/bootstrap.php
 * Loaded at top of every PHP page.
 * Provides: $db, get_products(), get_product($id), session fix.
 */

// ── Session — InfinityFree SSL terminates at proxy, secure=true breaks cookies ──
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (int)($_SERVER['SERVER_PORT'] ?? 80) === 443;
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
    }
    ini_set('session.use_strict_mode', '1');
    session_start();
}

// ── Security headers ─────────────────────────────────────────
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://checkout.razorpay.com https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-src https://checkout.razorpay.com; object-src 'none'; base-uri 'self';");
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// ── DB connection ────────────────────────────────────────────
function fk_get_db(): ?PDO {
    static $pdo = false;
    if ($pdo !== false) return $pdo;
    $cfg = @(require __DIR__ . '/../assets/db_config.php');
    if (!is_array($cfg)) return $pdo = null;
    try {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'], $cfg['port'] ?? 3306, $cfg['database'], $cfg['charset'] ?? 'utf8mb4');
        $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (Exception $e) {
        error_log('FK DB error: ' . $e->getMessage());
        return $pdo = null;
    }
}

// ── Product loading — DB first, JSON fallback ────────────────
function get_products(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    // APCu cross-request cache (60 seconds) — available on many shared hosts
    if (function_exists('apcu_fetch')) {
        $apc = apcu_fetch('fk_products', $found);
        if ($found && is_array($apc)) { return $cache = $apc; }
    }

    // Try DB
    $db = fk_get_db();
    if ($db) {
        try {
            $rows = $db->query("
                SELECT id, brand, name, price, mrp, `off`, badge, stock,
                       rating, rcount, category, subcategory, description,
                       images, model
                FROM fk_products WHERE is_active = 1
                ORDER BY num ASC
            ")->fetchAll();
            if ($rows) {
                foreach ($rows as &$r) {
                    $r['images']  = json_decode($r['images'] ?? '[]', true) ?: [];
                    $r['rCount']  = (int)($r['rcount'] ?? 0);
                    $r['price']   = (float)$r['price'];
                    $r['mrp']     = (float)$r['mrp'];
                    $r['off']     = (int)$r['off'];
                    $r['stock']   = (int)$r['stock'];
                    $r['rating']  = (float)$r['rating'];
                    $r['features']= [];
                }
                unset($r);
                return $cache = array_values($rows);
            }
        } catch (Exception $e) {
            error_log('get_products DB error: ' . $e->getMessage());
        }
    }

    // JSON fallback
    $file = __DIR__ . '/../assets/products.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) {
            $cache = $data;
            if (function_exists('apcu_store')) apcu_store('fk_products', $cache, 60);
            return $cache;
        }
    }
    return $cache = [];
}

function get_product(string $id): ?array {
    foreach (get_products() as $p) {
        if (($p['id'] ?? '') === $id) return $p;
    }
    return null;
}

function get_logged_in_user(): ?array {
    $email = $_SESSION['fk_user_email'] ?? '';
    if (!$email) return null;
    // Check localStorage mirror — return minimal session user
    return ['email' => $email, 'name' => $_SESSION['fk_user_name'] ?? ''];
}

// build_fk_products_js — kept as stub; pages now use assets/products-data.js
function build_fk_products_js(array $products): string {
    // Inline injection removed for performance — external cacheable script used instead
    return '';
}
