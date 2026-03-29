<?php
ob_start();

if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

const ADMIN_LOGIN_USER    = 'IronicGuru';
const ADMIN_LOGIN_PASS_HASH = '$2b$12$/wlAvE1/ME9fQX8kUOZxlumT6vitwc7pBn0VAvSX89YNT4sES0KRS';
define('ADMIN_LOGIN_SESSION', 'iron_admin_ok_' . substr(md5(__FILE__), 0, 8));

// File-based brute-force protection (works on InfinityFree, no APCu needed)
function admin_rate_limit_check(): bool {
    $ip  = md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $dir = sys_get_temp_dir();
    $f   = $dir . '/fk_adm_' . $ip . '.json';
    $now = time();
    $data = ['attempts' => 0, 'window_start' => $now, 'locked_until' => 0];
    if (file_exists($f)) {
        $raw = @json_decode(@file_get_contents($f), true);
        if (is_array($raw)) $data = $raw;
    }
    // Reset window after 15 min
    if ($now - (int)$data['window_start'] > 900) {
        $data = ['attempts' => 0, 'window_start' => $now, 'locked_until' => 0];
    }
    if ($now < (int)$data['locked_until']) return false; // still locked
    return true;
}
function admin_rate_limit_record(bool $success): void {
    $ip  = md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $dir = sys_get_temp_dir();
    $f   = $dir . '/fk_adm_' . $ip . '.json';
    $now = time();
    $data = ['attempts' => 0, 'window_start' => $now, 'locked_until' => 0];
    if (file_exists($f)) {
        $raw = @json_decode(@file_get_contents($f), true);
        if (is_array($raw)) $data = $raw;
    }
    if ($success) {
        @unlink($f); return;
    }
    $data['attempts'] = (int)$data['attempts'] + 1;
    if ((int)$data['attempts'] >= 5) {
        $data['locked_until'] = $now + 900; // 15-min lockout after 5 fails
    }
    @file_put_contents($f, json_encode($data), LOCK_EX);
}

$adminLoginError = '';
$adminLoginUser  = '';

if (isset($_GET['admin_logout'])) {
    unset($_SESSION[ADMIN_LOGIN_SESSION]);
    session_regenerate_id(true);
    header('Location: admin.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && (string)($_POST['admin_action'] ?? '') === 'admin_login') {
    $adminLoginUser = trim((string)($_POST['admin_user'] ?? ''));
    $adminLoginPass = (string)($_POST['admin_pass'] ?? '');

    if (!admin_rate_limit_check()) {
        $adminLoginError = 'Too many attempts. Try again in 15 minutes.';
    } elseif (hash_equals(ADMIN_LOGIN_USER, $adminLoginUser)
           && password_verify($adminLoginPass, ADMIN_LOGIN_PASS_HASH)) {
        admin_rate_limit_record(true);
        $_SESSION[ADMIN_LOGIN_SESSION] = true;
        session_regenerate_id(true);
        header('Location: admin.php');
        exit;
    } else {
        admin_rate_limit_record(false);
        $adminLoginError = 'Invalid credentials';
    }
}

$adminIsLoggedIn = !empty($_SESSION[ADMIN_LOGIN_SESSION]);
if (!$adminIsLoggedIn && isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['ok' => false, 'err' => 'Admin login required']);
    exit;
}

if (!$adminIsLoggedIn) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>Admin Login</title>
<style>
:root{
  --bg:#000;--txt:#fff;--sub:rgba(255,255,255,.5);--line:rgba(255,255,255,.13);
  --field:rgba(255,255,255,.07);--field2:rgba(255,255,255,.11);--accent1:#6c63ff;--accent2:#4f46e5;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent}
html,body{height:100%}
body{background:var(--bg);color:var(--txt);font-family:Arial,Helvetica,sans-serif;overflow:hidden}
.auth-gate{position:fixed;inset:0;background:#000}
.auth-scanlines{position:absolute;inset:0;background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,0,0,.08) 2px,rgba(0,0,0,.08) 4px);pointer-events:none;z-index:1}
.auth-vignette{position:absolute;inset:0;background:radial-gradient(ellipse at center, transparent 40%, rgba(0,0,0,.85) 100%);pointer-events:none;z-index:2}
.auth-main{position:relative;z-index:3;height:100dvh;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:0 16px 16px;gap:0}
.auth-art-wrap{position:relative;width:100%;display:flex;justify-content:center;align-items:flex-start}
.auth-art-img{width:100%;height:40vh;max-height:380px;display:block;object-fit:contain;object-position:center top;opacity:.6;filter:brightness(1.3) contrast(1.4);animation:authBreathe 3.8s ease-in-out infinite;flex-shrink:0;margin-top:2vh;margin-bottom:-7vh}
@keyframes authBreathe{0%,100%{opacity:.52}50%{opacity:.72}}
.auth-divline{width:70vw;max-width:320px;height:1px;background:linear-gradient(90deg,transparent,#00ff41,transparent);opacity:.4;margin:4px 0 8px;flex-shrink:0}
.auth-divider-title{width:100%;max-width:92vw;margin:2px 0 16px;text-align:center;color:#fff;font-size:18px;font-weight:800;line-height:1.25;letter-spacing:.35px;text-shadow:0 0 8px rgba(255,255,255,.18),0 0 16px rgba(255,255,255,.10);flex-shrink:0}
.auth-divider-title .brand-core{color:#fff;font-weight:900}
.auth-tg-link{text-decoration:none;color:inherit;cursor:pointer;transition:opacity .2s,text-shadow .2s}
.auth-tg-link:hover .brand-core{opacity:.8;text-shadow:0 0 10px rgba(255,0,0,.6),0 0 22px rgba(255,0,0,.35),0 0 8px rgba(255,255,255,.18)}
.auth-login-box{width:100%;max-width:360px;background:rgba(255,255,255,.07);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.13);border-radius:18px;padding:18px 18px 16px;box-shadow:0 8px 32px rgba(0,0,0,.5);flex-shrink:0}
.auth-field{margin-bottom:10px}.auth-label{font-size:11px;font-weight:600;color:var(--sub);display:block;margin-bottom:5px}
.auth-input{width:100%;background:var(--field);border:1px solid var(--line);border-radius:9px;color:#fff;font-size:14px;padding:12px 12px;outline:none;transition:border-color .2s,background .2s,transform .15s}
.auth-input:focus{border-color:rgba(255,255,255,.4);background:var(--field2)}
.auth-input::placeholder{color:rgba(255,255,255,.2)}
.auth-input.shake{animation:authShake .28s linear 1}
.auth-btn{width:100%;margin-top:10px;padding:14px;background:linear-gradient(135deg,var(--accent1),var(--accent2));border:none;color:#fff;font-size:14px;font-weight:700;cursor:pointer;border-radius:9px;box-shadow:0 4px 16px rgba(99,88,255,.45);transition:opacity .2s,transform .15s}
.auth-btn:active{transform:scale(.98)}
.auth-err{font-size:12px;color:#ff6b6b;text-align:center;margin-top:10px;display:block;min-height:18px}
@keyframes authShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-4px)}75%{transform:translateX(4px)}}

/* ===== Admin panel refinement patch ===== */
body{
  background:
    radial-gradient(circle at top left, rgba(59,130,246,.10), transparent 34%),
    radial-gradient(circle at top right, rgba(139,92,246,.09), transparent 28%),
    var(--bg);
}
.wrap{gap:0}
.sidebar{
  background:linear-gradient(180deg, rgba(22,27,46,.98) 0%, rgba(15,17,23,.98) 100%);
}
.main{
  max-width:1600px;
  width:100%;
}
.card{
  background:linear-gradient(180deg, rgba(255,255,255,.025) 0%, rgba(255,255,255,.012) 100%), var(--surface);
}
.sec-btn{
  min-height:106px;
  display:flex;
  flex-direction:column;
  justify-content:center;
}
.ithumb{
  height:148px;
  border-bottom:1px solid var(--border);
}
.ifoot{
  display:flex;
  flex-direction:column;
  gap:6px;
}
.upbtn{
  font-weight:700;
  letter-spacing:.2px;
}
.pcard{
  background:linear-gradient(180deg, rgba(255,255,255,.03) 0%, rgba(255,255,255,.012) 100%), var(--surface);
}
.topbar{
  position:sticky;
  top:74px;
  z-index:40;
  padding:14px 16px;
  margin:-6px -6px 18px;
  border:1px solid var(--border);
  border-radius:16px;
  background:rgba(22,27,46,.88);
  backdrop-filter:blur(10px);
}
.sh{
  padding-bottom:8px;
  border-bottom:1px solid rgba(255,255,255,.06);
}
.tbl th{
  top:0;
  z-index:2;
}
@media (max-width: 768px){
  .topbar{
    top:64px;
    margin:0 0 16px;
    padding:12px 14px;
  }
  .sec-btn{
    min-height:94px;
  }
  .ithumb{
    height:120px;
  }
}

</style>
</head>
<body>
<div class="auth-gate" id="authGate">
  <div class="auth-scanlines"></div>
  <div class="auth-vignette"></div>
  <div class="auth-main">
    <div class="auth-art-wrap">
      <img class="auth-art-img" src="assets/admin-login-art.png" alt="Admin Art">
    </div>
    <div class="auth-divline"></div>
    <div class="auth-divider-title" aria-label="brand title">
      <a class="auth-tg-link" href="tg://resolve?domain=IronicGuru" onclick="openTelegram(event)" title="Message on Telegram">
        <span class="brand-core">𓆰 Ꮇᴇ𝙲ᴇ𐍂𝙾ᴡ 𓆪</span>
      </a>
    </div>
    <form class="auth-login-box" method="post" action="admin.php" autocomplete="off">
      <input type="hidden" name="admin_action" value="admin_login">
      <div class="auth-field">
        <label class="auth-label" for="authUser">USER ID</label>
        <input class="auth-input<?php echo $adminLoginError !== '' ? ' shake' : ''; ?>" id="authUser" name="admin_user" type="text" placeholder="Enter user ID" value="<?php echo htmlspecialchars($adminLoginUser, ENT_QUOTES, 'UTF-8'); ?>" required>
      </div>
      <div class="auth-field">
        <label class="auth-label" for="authPass">PASSWORD</label>
        <input class="auth-input<?php echo $adminLoginError !== '' ? ' shake' : ''; ?>" id="authPass" name="admin_pass" type="password" placeholder="Enter password" required>
      </div>
      <button class="auth-btn" id="authBtn" type="submit">&#x25BA; &nbsp;Access System</button>
      <div class="auth-err" id="authErr"><?php echo $adminLoginError !== '' ? htmlspecialchars($adminLoginError, ENT_QUOTES, 'UTF-8') : '&nbsp;'; ?></div>
    </form>
  </div>
</div>
<script>
function openTelegram(e){
  if(e) e.preventDefault();
  window.location.href='tg://resolve?domain=IronicGuru';
  setTimeout(function(){ window.location.href='https://t.me/IronicGuru'; },700);
}
window.addEventListener('DOMContentLoaded',function(){
  var user=document.getElementById('authUser');
  var pass=document.getElementById('authPass');
  if(user){ user.focus(); if(user.value){ user.setSelectionRange(user.value.length,user.value.length); } }
  [user,pass].forEach(function(el){
    if(!el) return;
    el.addEventListener('input',function(){
      el.classList.remove('shake');
      var other = el===user ? pass : user;
      if(other) other.classList.remove('shake');
      document.getElementById('authErr').innerHTML='&nbsp;';
    });
  });
});
</script>
</body>
</html>
<?php
    exit;
}


// ============================================================
//  ADMIN PANEL — Flipkart Website Manager
// ============================================================
define('BASE_DIR',    __DIR__);
define('PRODUCTS_JS', BASE_DIR.'/assets/products-data.js');
define('PAYMENT_FILE', BASE_DIR.'/assets/payment_settings.json');
define('CHUNK_TMP',   BASE_DIR.'/assets/.chunks');
define('ANALYTICS_FILE', BASE_DIR.'/assets/analytics.json');
if (!is_dir(CHUNK_TMP)) { @mkdir(CHUNK_TMP, 0777, true); @file_put_contents(CHUNK_TMP.'/.htaccess', "Deny from all\n"); }

// ── GitHub Auto-Sync ─────────────────────────────────────────
function github_sync_file(string $localPath, string $repoPath, string $message): array {
    $token  = getenv('GH_TOKEN')  ?: '';
    $repo   = getenv('GH_REPO')   ?: '';
    $branch = getenv('GH_BRANCH') ?: 'main';
    if (!$token || !$repo) return ['ok'=>false,'warn'=>'GH_TOKEN or GH_REPO not set'];

    $apiBase = "https://api.github.com/repos/{$repo}/contents/{$repoPath}";
    $content = base64_encode(file_get_contents($localPath));

    $opts = ['http'=>[
        'method'  => 'GET',
        'header'  => "Authorization: Bearer {$token}\r\nUser-Agent: ShopkartAdmin\r\nAccept: application/vnd.github+json\r\n",
        'ignore_errors' => true, 'timeout' => 10,
    ]];
    $res  = @file_get_contents($apiBase . "?ref={$branch}", false, stream_context_create($opts));
    $data = $res ? json_decode($res, true) : [];
    $sha  = $data['sha'] ?? '';

    $payload = json_encode(array_filter([
        'message' => $message, 'content' => $content,
        'branch'  => $branch,  'sha'     => $sha ?: null,
    ]), JSON_UNESCAPED_SLASHES);

    $putOpts = ['http'=>[
        'method'  => 'PUT',
        'header'  => "Authorization: Bearer {$token}\r\nUser-Agent: ShopkartAdmin\r\nContent-Type: application/json\r\nAccept: application/vnd.github+json\r\n",
        'content' => $payload, 'ignore_errors' => true, 'timeout' => 15,
    ]];
    $putRes = @file_get_contents($apiBase, false, stream_context_create($putOpts));
    $result = $putRes ? json_decode($putRes, true) : [];
    $ok = isset($result['commit']['sha']);
    return ['ok'=>$ok, 'warn'=> $ok ? '' : 'GitHub sync failed: '.($result['message'] ?? 'unknown')];
}
function github_sync_products(string $jsonFile): array {
    return github_sync_file($jsonFile, 'assets/products.json', 'Admin: update products.json [auto]');
}
// Only generate a new token if one doesn't exist — AJAX calls must NOT overwrite it
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}
$CSRF_TOKEN = $_SESSION['admin_csrf'];

// ── CSRF validation helper ────────────────────────────────────
function validateCsrf(): bool {
    $incoming = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $stored   = $_SESSION['admin_csrf'] ?? '';
    return $stored !== '' && hash_equals($stored, $incoming);
}

// ── IMAGE SECTIONS ───────────────────────────────────────────
$SECTIONS = [
    'banners' => ['label'=>'Banner Slider','folder'=>'Images/BannerSlider','type'=>'direct','count'=>6,'icon'=>'🖼️','note'=>'Homepage top slider — 6 images'],
    'icons'   => ['label'=>'Category Icons','folder'=>'Images/CategoryIcons','type'=>'direct','count'=>10,'icon'=>'🔷','note'=>'10 category icons below banner','names'=>['Fashion','Travel','Appliances','Beauty','Electronics','Mobiles','Food & Health','Home & Kitchen','Auto Accessories','Furniture']],
    'dotd'    => ['label'=>'Deals of the Day','folder'=>'Images/DealsOfTheDay','type'=>'subfolder','count'=>3,'icon'=>'⚡','note'=>'3 deal card images'],
    'sponsored_banner' => ['label'=>'Sponsored Banner','folder'=>'Images/Sponsored','type'=>'single','count'=>1,'icon'=>'📢','note'=>'Banner above sponsored products'],
    'supercoin' => ['label'=>'SuperCoin Banner','folder'=>'Images/SuperCoin','type'=>'single','count'=>1,'icon'=>'🪙','note'=>'Banner above final ad section'],
    'adbanner'=> ['label'=>'Ad Banner','folder'=>'banners/ads','type'=>'single','count'=>1,'icon'=>'📣','note'=>'Full-width banner at end','filename'=>'banner.png'],
    'sponsored'  => ['label'=>'Sponsored Products','folder'=>'Images/Sponsored','type'=>'subfolder','count'=>4,'icon'=>'🛍️','note'=>'4 sponsored product images'],
    'suggested'  => ['label'=>'Suggested For You','folder'=>'Images/SuggestedForYou','type'=>'subfolder','count'=>6,'icon'=>'💡','note'=>'6 suggested product images'],
    'youmaylike' => ['label'=>'You May Also Like','folder'=>'Images/YouMayAlsoLike','type'=>'subfolder','count'=>4,'icon'=>'❤️','note'=>'4 product images'],
    'premium'    => ['label'=>'Upgrade to Premium','folder'=>'Images/UpgradeToPremium','type'=>'subfolder','count'=>4,'icon'=>'👑','note'=>'4 product images'],
    'toppicks'   => ['label'=>'Product Images','folder'=>'Images/TopPicksForYou','type'=>'toppicks','count'=>100,'icon'=>'📦','note'=>'100 product images'],
];

// ── AJAX ─────────────────────────────────────────────────────
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $act = $_GET['ajax'];
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    // Validate CSRF token for all state-changing POST requests
    $skipCsrfChecks = ['get_img_url', 'get_payment', 'get_state', 'get_orders'];
    if ($method === 'POST' && !in_array($act, $skipCsrfChecks, true) && !validateCsrf()) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'err' => 'Invalid or expired security token. Please refresh the page.']);
        exit;
    }

    // Get single image URL — used by product images modal
    if ($act === 'get_img_url') {
        $key    = $_GET['section'] ?? '';
        $slot   = (int)($_GET['slot']   ?? 0);
        $imgnum = (int)($_GET['imgnum'] ?? 1);
        $cfg    = $SECTIONS[$key] ?? null;
        if (!$cfg) { echo json_encode(['ok'=>false,'url'=>'']); exit; }
        echo json_encode(['ok'=>true,'url'=>getCurrentImg($cfg,$slot,$imgnum)]);
        exit;
    }

    // Upload image
    if ($act === 'upload' && !empty($_FILES['img']['tmp_name'])) {
        $key    = $_GET['section'] ?? '';
        $slot   = (int)($_GET['slot']   ?? 0);
        $imgnum = (int)($_GET['imgnum'] ?? 1);
        $cfg    = $SECTIONS[$key] ?? null;
        if (!$cfg) { echo json_encode(['ok'=>false,'err'=>'Unknown section']); exit; }

        $tmp  = $_FILES['img']['tmp_name'];
        $mime = @mime_content_type($tmp) ?: '';
        if ($mime !== '' && strpos($mime,'image/') !== 0 && $mime !== 'application/octet-stream') {
            echo json_encode(['ok'=>false,'err'=>'Not an image (mime: '.$mime.')']); exit;
        }

        // ── Cloudinary Upload ────────────────────────────────
        $cloudName    = getenv('CLOUDINARY_CLOUD_NAME') ?: '';
        $uploadPreset = getenv('CLOUDINARY_UPLOAD_PRESET') ?: '';
        if (!$cloudName || !$uploadPreset) {
            echo json_encode(['ok'=>false,'err'=>'Cloudinary not configured — set CLOUDINARY_CLOUD_NAME and CLOUDINARY_UPLOAD_PRESET in Render env vars']); exit;
        }

        $publicId  = 'shopkart/' . $key . '_s' . $slot . '_i' . $imgnum;
        $fileData  = 'data:' . ($mime ?: 'image/jpeg') . ';base64,' . base64_encode(file_get_contents($tmp));
        $payload   = http_build_query(['file'=>$fileData,'upload_preset'=>$uploadPreset,'public_id'=>$publicId,'overwrite'=>'true']);

        $ctx = stream_context_create(['http'=>[
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 30,
            'ignore_errors' => true,
        ]]);
        $res    = @file_get_contents("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", false, $ctx);
        $result = $res ? json_decode($res, true) : [];
        if (empty($result['secure_url'])) {
            $errMsg = $result['error']['message'] ?? 'Upload failed';
            echo json_encode(['ok'=>false,'err'=>'Cloudinary error: '.$errMsg]); exit;
        }

        // ── Store URL in cloudinary_map.json + sync to GitHub ─
        $mapFile = BASE_DIR.'/assets/cloudinary_map.json';
        $map = file_exists($mapFile) ? (json_decode(file_get_contents($mapFile), true) ?: []) : [];
        $map[$key][$slot][$imgnum] = $result['secure_url'];
        file_put_contents($mapFile, json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
        github_sync_file($mapFile, 'assets/cloudinary_map.json', 'Admin: update image map [auto]');

        echo json_encode(['ok'=>true,'url'=>$result['secure_url']]);
        exit;
    }

    // Delete image
    if ($act === 'delete_img') {
        $key    = $_GET['section'] ?? '';
        $slot   = (int)($_GET['slot']   ?? 0);
        $imgnum = (int)($_GET['imgnum'] ?? 1);
        $cfg    = $SECTIONS[$key] ?? null;
        if (!$cfg) { echo json_encode(['ok'=>false,'err'=>'Unknown section']); exit; }

        // Remove from cloudinary_map.json
        $mapFile = BASE_DIR.'/assets/cloudinary_map.json';
        $map = file_exists($mapFile) ? (json_decode(file_get_contents($mapFile), true) ?: []) : [];
        if (isset($map[$key][$slot][$imgnum])) {
            unset($map[$key][$slot][$imgnum]);
            file_put_contents($mapFile, json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            gi
