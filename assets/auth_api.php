<?php
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
require_once __DIR__ . '/_store_lib.php';
fk_boot_session();

// ── CSRF token for state-changing actions ────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['fk_csrf'] = $_SESSION['fk_csrf'] ?? bin2hex(random_bytes(16));
}

// ── Rate limiting — max 10 attempts per IP per 15 min ────────
function fk_check_rate_limit(): void {
    $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key      = 'fk_rl_' . md5($ip);
    $window   = 900;  // 15 minutes
    $max      = 10;

    $now      = time();
    $data     = isset($_SESSION[$key]) ? $_SESSION[$key] : ['count' => 0, 'start' => $now];

    // Reset window if expired
    if (($now - $data['start']) > $window) {
        $data = ['count' => 0, 'start' => $now];
    }

    $data['count']++;
    $_SESSION[$key] = $data;

    if ($data['count'] > $max) {
        http_response_code(429);
        echo json_encode(['ok' => false, 'error' => 'Too many requests. Try again in 15 minutes.']);
        exit;
    }
}


$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$users = fk_load_users();

if ($method === 'GET') {
    $email = fk_current_user_email();
    if ($email === '') fk_respond(['ok' => true, 'loggedIn' => false, 'user' => null]);
    $idx = fk_find_user_index($users, $email);
    if ($idx < 0) {
        unset($_SESSION['fk_user_email']);
        fk_respond(['ok' => true, 'loggedIn' => false, 'user' => null]);
    }
    fk_respond(['ok' => true, 'loggedIn' => true, 'user' => fk_user_public($users[$idx])]);
}

$body = json_decode(file_get_contents('php://input') ?: '[]', true);
if (!is_array($body)) $body = [];
$action = strtolower(trim((string)($body['action'] ?? '')));
if (in_array($action, ['login', 'signup'])) fk_check_rate_limit();

if ($action === 'signup') {
    $name = fk_normalize_scalar($body['name'] ?? '');
    $mobile = preg_replace('/\D+/', '', (string)($body['mobile'] ?? ''));
    $email = strtolower(trim((string)($body['email'] ?? '')));
    $password = (string)($body['password'] ?? '');
    if ($name === '' || !preg_match('/^\d{10}$/', $mobile) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        fk_respond(['ok' => false, 'error' => 'Invalid signup data'], 400);
    }
    if (fk_find_user_index($users, $email) >= 0 || fk_find_user_index($users, $mobile) >= 0) {
        fk_respond(['ok' => false, 'error' => 'Account already exists'], 409);
    }
    $user = [
        'id' => 'USR' . strtoupper(substr(md5($email . microtime(true)), 0, 10)),
        'name' => $name,
        'mobile' => $mobile,
        'email' => $email,
        'joined' => date('d M Y'),
        'gender' => '',
        'dob' => '',
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ];
    $users[] = $user;
    if (!fk_save_users($users)) {
        fk_respond(['ok' => false, 'error' => 'Account creation failed — server storage error. Please try again.'], 500);
    }
    session_regenerate_id(true);
    $_SESSION['fk_user_email'] = $email;

    $state = fk_load_state();
    fk_merge_guest_to_user($state, $email, $body['guest_cart'] ?? [], $body['guest_wishlist'] ?? []);
    fk_save_state($state);

    fk_respond(['ok' => true, 'user' => fk_user_public($user)]);
}

if ($action === 'login') {
    $identifier = trim((string)($body['identifier'] ?? ''));
    $password = (string)($body['password'] ?? '');
    if ($identifier === '' || strlen($password) < 6) {
        fk_respond(['ok' => false, 'error' => 'Invalid login data'], 400);
    }
    $idx = fk_find_user_index($users, $identifier);
    if ($idx < 0) fk_respond(['ok' => false, 'error' => 'Account not found'], 404);
    $user = $users[$idx];
    if (!password_verify($password, (string)($user['password_hash'] ?? ''))) {
        fk_respond(['ok' => false, 'error' => 'Incorrect password'], 401);
    }
    session_regenerate_id(true);
    $_SESSION['fk_user_email'] = strtolower(trim((string)$user['email']));
    $state = fk_load_state();
    fk_merge_guest_to_user($state, strtolower(trim((string)$user['email'])), $body['guest_cart'] ?? [], $body['guest_wishlist'] ?? []);
    fk_save_state($state);
    fk_respond(['ok' => true, 'user' => fk_user_public($user)]);
}

if ($action === 'logout') {
    unset($_SESSION['fk_user_email']);
    session_regenerate_id(true);
    fk_respond(['ok' => true]);
}

if ($action === 'update_profile') {
    $email = fk_current_user_email();
    if ($email === '') fk_respond(['ok' => false, 'error' => 'Not logged in'], 401);
    $idx = fk_find_user_index($users, $email);
    if ($idx < 0) fk_respond(['ok' => false, 'error' => 'User not found'], 404);
    $name = fk_normalize_scalar($body['name'] ?? $users[$idx]['name']);
    $mobile = preg_replace('/\D+/', '', (string)($body['mobile'] ?? $users[$idx]['mobile']));
    $newEmail = strtolower(trim((string)($body['email'] ?? $users[$idx]['email'])));
    $gender = fk_normalize_scalar($body['gender'] ?? $users[$idx]['gender']);
    $dob = trim((string)($body['dob'] ?? $users[$idx]['dob']));
    if ($name === '' || !preg_match('/^\d{10}$/', $mobile) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        fk_respond(['ok' => false, 'error' => 'Invalid profile data'], 400);
    }
    foreach ($users as $uIdx => $u) {
        if ($uIdx === $idx) continue;
        if (strtolower(trim((string)($u['email'] ?? ''))) === $newEmail || preg_replace('/\D+/', '', (string)($u['mobile'] ?? '')) === $mobile) {
            fk_respond(['ok' => false, 'error' => 'Email or mobile already in use'], 409);
        }
    }
    $oldEmail = strtolower(trim((string)$users[$idx]['email']));
    $users[$idx]['name'] = $name;
    $users[$idx]['mobile'] = $mobile;
    $users[$idx]['email'] = $newEmail;
    $users[$idx]['gender'] = $gender;
    $users[$idx]['dob'] = $dob;
    if (!fk_save_users($users)) {
        fk_respond(['ok' => false, 'error' => 'Profile save failed — server storage error'], 500);
    }
    if ($oldEmail !== $newEmail) {
        $state = fk_load_state();
        if (isset($state['users'][$oldEmail]) && !isset($state['users'][$newEmail])) {
            $state['users'][$newEmail] = $state['users'][$oldEmail];
            unset($state['users'][$oldEmail]);
            fk_save_state($state);
        }
        fk_orders_migrate_owner_email($oldEmail, $newEmail);
        $_SESSION['fk_user_email'] = $newEmail;
    }
    fk_respond(['ok' => true, 'user' => fk_user_public($users[$idx])]);
}

fk_respond(['ok' => false, 'error' => 'Unsupported action'], 400);
