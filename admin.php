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
            github_sync_file($mapFile, 'assets/cloudinary_map.json', 'Admin: delete image [auto]');
        }
        // Also clean up local file if exists (legacy)
        [$destDir, $destFile] = getDestPath($cfg, $slot, $imgnum);
        if ($destDir) {
            $base = pathinfo($destFile ?? '', PATHINFO_FILENAME);
            foreach (['avif','png','jpg','jpeg','webp'] as $ext) { $p = "$destDir/$base.$ext"; if (file_exists($p)) @unlink($p); }
        }
        echo json_encode(['ok'=>true,'deleted'=>1]);
        exit;
    }


    // ── ZIP UPLOAD (direct, no chunking) ─────────────────────
    if ($act === 'zip_upload') {
        $key = $_GET['section'] ?? '';
        $cfg = $SECTIONS[$key] ?? null;
        if (!$cfg) { echo json_encode(['ok'=>false,'err'=>'Unknown section']); exit; }
        if (empty($_FILES['zipfile']['tmp_name'])) { echo json_encode(['ok'=>false,'err'=>'No file received']); exit; }
        if (!class_exists('ZipArchive')) { echo json_encode(['ok'=>false,'err'=>'ZipArchive not available on server']); exit; }

        $zip = new ZipArchive();
        if ($zip->open($_FILES['zipfile']['tmp_name']) !== true) {
            echo json_encode(['ok'=>false,'err'=>'Cannot open ZIP — file may be corrupt']); exit;
        }

        $imageExts = ['jpg','jpeg','png','webp','avif'];
        // slot passed from JS (for toppicks: the product number e.g. 11)
        $forceSlot = (int)($_GET['slot'] ?? 0);
        $count = 0; $errors = []; $debug = [];
        $imgCounter = 0; // counts only valid image files for fallback imgnum

        for ($zi = 0; $zi < $zip->numFiles; $zi++) {
            $zname = $zip->getNameIndex($zi);
            $bname = basename($zname);
            if (substr($zname,-1)==='/' || $bname==='' || $bname[0]==='.') continue;
            $ext  = strtolower(pathinfo($bname, PATHINFO_EXTENSION));
            if (!in_array($ext, $imageExts)) continue;

            $imgCounter++; // increment only for valid image files
            $parts = array_values(array_filter(explode('/', $zname), fn($p)=>$p!==''));
            $stem  = pathinfo($bname, PATHINFO_FILENAME);
            $slot   = 0;
            $imgnum = 1;

            if ($forceSlot > 0) {
                // Slot fixed — file name (numeric) = imgnum; or sequential order
                if (is_numeric($stem)) { $imgnum = (int)$stem; }
                else { $imgnum = $imgCounter; } // use image-only counter, not zip entry index
                $slot = $forceSlot;
            } elseif (count($parts) >= 2 && is_numeric($parts[count($parts)-2])) {
                // subfolder structure: 11/1.avif → slot=11, imgnum=1
                $slot = (int)$parts[count($parts)-2];
                $imgnum = is_numeric($stem) ? (int)$stem : 1;
            } elseif (preg_match('/^p?(\d+)$/', pathinfo(($_FILES['zipfile']['name']??''), PATHINFO_FILENAME), $zm)) {
                // ZIP filename is p11.zip or 11.zip → slot from filename
                $slot = (int)$zm[1];
                $imgnum = is_numeric($stem) ? (int)$stem : ($zi + 1);
            } elseif (preg_match('/^(\d+)_(\d+)$/', $stem, $m)) {
                $slot = (int)$m[1]; $imgnum = (int)$m[2];
            } elseif (is_numeric($stem)) {
                $slot = (int)$stem; $imgnum = 1;
            } else {
                $errors[] = "Skipped '$bname' — cannot determine slot"; continue;
            }

            [$destDir, $destFile] = getDestPath($cfg, $slot, $imgnum, $ext);
            if (!$destDir) { $errors[] = "Slot $slot out of range (max {$cfg['count']})"; continue; }
            if (!is_dir($destDir) && !@mkdir($destDir, 0777, true)) { $errors[] = "Cannot create dir for slot $slot"; continue; }
            @chmod($destDir, 0777);
            $base = pathinfo($destFile, PATHINFO_FILENAME);
            foreach (glob("$destDir/$base.*") ?: [] as $old) @unlink($old);
            $dest = "$destDir/$destFile";
            $contents = $zip->getFromIndex($zi);
            if ($contents === false) { $errors[] = "Cannot read '$bname' from ZIP"; continue; }
            if (file_put_contents($dest, $contents) !== false) { @chmod($dest, 0644); $count++; $debug[] = "$bname → slot $slot img $imgnum"; }
            else $errors[] = "Write failed: $dest";
        }
        $zip->close();
        echo json_encode(['ok'=>true,'count'=>$count,'errors'=>$errors,'debug'=>$debug]);
        exit;
    }

    // Import CSV — accepts confirmed column mapping from client
    if ($act === 'import_csv') {
        if (empty($_FILES['csvfile']['tmp_name'])) { echo json_encode(['ok'=>false,'err'=>'No CSV file received']); exit; }
        $mapping = json_decode($_POST['mapping'] ?? '{}', true);
        if (!is_array($mapping) || !isset($mapping['id']) || (int)$mapping['id'] < 0) {
            echo json_encode(['ok'=>false,'err'=>'Product ID column is required — check mapping']); exit;
        }
        $idIdx = (int)$mapping['id'];
        $nmIdx = isset($mapping['name'])  && $mapping['name']  >= 0 ? (int)$mapping['name']  : -1;
        $brIdx = isset($mapping['brand']) && $mapping['brand'] >= 0 ? (int)$mapping['brand'] : -1;
        $prIdx = isset($mapping['price']) && $mapping['price'] >= 0 ? (int)$mapping['price'] : -1;
        $mrIdx = isset($mapping['mrp'])   && $mapping['mrp']   >= 0 ? (int)$mapping['mrp']   : -1;
        $dsIdx = isset($mapping['desc'])  && $mapping['desc']  >= 0 ? (int)$mapping['desc']  : -1;

        $tmp    = $_FILES['csvfile']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if (!$handle) { echo json_encode(['ok'=>false,'err'=>'Cannot read uploaded file']); exit; }
        fgetcsv($handle); // skip header row — mapping already resolved client-side

        // Helper: normalise ID value from any format → p1, p2 …
        $normId = function(string $raw): string {
            $raw = trim($raw);
            if ($raw === '') return '';
            $low = strtolower($raw);
            // Strip currency symbols, spaces, common prefixes
            $low = preg_replace('/[^a-z0-9]/', '', $low);
            if ($low === '') return '';
            // If purely numeric → prefix p
            if (ctype_digit($low)) return 'p'.$low;
            // Already like p1, prod1, product1 → keep only trailing number with p prefix
            if (preg_match('/^[a-z]*(\d+)$/', $low, $m)) return 'p'.$m[1];
            return $low;
        };

        // Helper: clean numeric value — strip ₹, $, commas, spaces
        $cleanNum = function(string $v): int {
            return (int)preg_replace('/[^0-9]/', '', $v);
        };

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $pid = $normId((string)($row[$idIdx] ?? ''));
            if (!$pid) continue;
            $rows[$pid] = [
                'name'  => $nmIdx >= 0 ? trim($row[$nmIdx] ?? '') : null,
                'brand' => $brIdx >= 0 ? trim($row[$brIdx] ?? '') : null,
                'price' => $prIdx >= 0 ? $cleanNum((string)($row[$prIdx] ?? '0')) : null,
                'mrp'   => $mrIdx >= 0 ? $cleanNum((string)($row[$mrIdx] ?? '0')) : null,
                'desc'  => $dsIdx >= 0 ? trim(str_replace(["\r\n","\r","\n"], ' ', $row[$dsIdx] ?? '')) : null,
            ];
        }
        fclose($handle);
        if (empty($rows)) { echo json_encode(['ok'=>false,'err'=>'No valid product rows found — check your ID column values']); exit; }

        // ── Update or Add in products.json ───────────────────
        $updated  = 0;
        $added    = 0;
        $jsonFile = BASE_DIR.'/assets/products.json';
        $j = [];
        if (file_exists($jsonFile)) {
            $decoded = json_decode(file_get_contents($jsonFile), true);
            if (is_array($decoded)) $j = $decoded;
        }

        // Build index map for fast lookup
        $existingIds = [];
        foreach ($j as $idx => $prod) {
            if (!empty($prod['id'])) $existingIds[$prod['id']] = $idx;
        }

        foreach ($rows as $pid => $r) {
            $price = $r['price'] ?? 0;
            $mrp   = $r['mrp']   ?? 0;
            $off   = ($mrp > $price && $price > 0) ? (int)round((1 - $price/$mrp)*100) : 0;

            if (isset($existingIds[$pid])) {
                // ── UPDATE existing product ──
                $idx = $existingIds[$pid];
                if ($r['name']  !== null && $r['name']  !== '') $j[$idx]['name']        = $r['name'];
                if ($r['brand'] !== null && $r['brand'] !== '') $j[$idx]['brand']       = $r['brand'];
                if ($r['price'] !== null && $r['price']  >  0) $j[$idx]['price']       = $r['price'];
                if ($r['mrp']   !== null && $r['mrp']    >  0) $j[$idx]['mrp']         = $r['mrp'];
                $p2 = (int)($j[$idx]['price'] ?? 0); $m2 = (int)($j[$idx]['mrp'] ?? 0);
                if ($m2 > $p2 && $p2 > 0) $j[$idx]['off'] = (int)round((1 - $p2/$m2) * 100);
                if ($r['desc']  !== null && $r['desc']  !== '') $j[$idx]['description'] = $r['desc'];
                // Ensure variants key exists
                if (!isset($j[$idx]['variants'])) $j[$idx]['variants'] = [];
                $updated++;
            } else {
                // ── ADD new product ──
                $pidNum = (int)preg_replace('/[^0-9]/', '', $pid);
                $name   = $r['name']  ?? 'Product '.$pidNum;
                $brand  = $r['brand'] ?? '';
                $desc   = $r['desc']  ?? '';
                $j[] = [
                    'id'          => $pid,
                    'name'        => $name !== '' ? $name : 'Product '.$pidNum,
                    'brand'       => $brand,
                    'price'       => $price > 0 ? $price : 0,
                    'mrp'         => $mrp > 0 ? $mrp : ($price > 0 ? $price : 0),
                    'off'         => $off,
                    'badge'       => '',
                    'stock'       => 100,
                    'rating'      => 4.0,
                    'rCount'      => 0,
                    'description' => $desc,
                    'images'      => ['Images/TopPicksForYou/'.$pidNum.'/1'],
                    'variants'    => [],
                    'sections'    => [],
                ];
                $existingIds[$pid] = count($j) - 1;
                $added++;
            }
        }

        // Sort by numeric product ID
        usort($j, function($a, $b) {
            $na = (int)preg_replace('/[^0-9]/', '', $a['id'] ?? '');
            $nb = (int)preg_replace('/[^0-9]/', '', $b['id'] ?? '');
            return $na - $nb;
        });

        file_put_contents($jsonFile, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        // ── Update products-data.js ───────────────────────────
        if (file_exists(PRODUCTS_JS)) {
            $js = file_get_contents(PRODUCTS_JS);
            foreach ($rows as $pid => $r) {
                $name  = $r['name']  ?? ''; $brand = $r['brand'] ?? '';
                $price = $r['price'] ?? 0;  $mrp   = $r['mrp']   ?? 0;
                $desc  = $r['desc']  ?? '';
                $off   = ($mrp > $price && $price > 0) ? (int)round((1 - $price/$mrp)*100) : 0;
                $js = preg_replace_callback(
                    '/(\{"id"\s*:\s*"'.preg_quote($pid,'/').'"\s*,(?:[^{}]|\{[^{}]*\})*\})/s',
                    function($m) use ($name,$brand,$price,$mrp,$off) {
                        $o = $m[1];
                        if ($name  !== '') $o = preg_replace('/"name"\s*:\s*"[^"]*"/',  '"name": "' .addslashes($name). '"', $o);
                        if ($brand !== '') $o = preg_replace('/"brand"\s*:\s*"[^"]*"/', '"brand": "'.addslashes($brand).'"', $o);
                        if ($price  >  0) { $o = preg_replace('/"price"\s*:\s*\d+/', '"price": '.$price, $o); $o = preg_replace('/"off"\s*:\s*\d+/', '"off": '.$off, $o); }
                        if ($mrp    >  0)   $o = preg_replace('/"mrp"\s*:\s*\d+/',   '"mrp": '.$mrp, $o);
                        return $o;
                    }, $js
                );
                if ($desc !== '') {
                    $js = preg_replace_callback(
                        '/(\/\/\s*'.preg_quote($pid,'/').'[^\n]*\n\s*\')((?:[^\'\\\\]|\\\\.)*)(\',.)/',
                        function($m) use ($desc) { return $m[1].addslashes($desc).$m[3]; }, $js
                    );
                }
            }
            file_put_contents(PRODUCTS_JS, $js);
        }
        echo json_encode(['ok'=>true,'updated'=>$updated,'added'=>$added,'total'=>count($rows)]);
        exit;
    }

    // Add new product
    if ($act === 'add_product') {
        $body  = json_decode(file_get_contents('php://input'), true) ?? [];
        $name  = trim($body['name']  ?? '');
        $brand = trim($body['brand'] ?? '');
        $price = (int)($body['price'] ?? 0);
        $mrp   = (int)($body['mrp']   ?? 0);
        $desc  = trim(str_replace(["\r\n","\r","\n"], ' ', $body['desc'] ?? ''));
        $cat   = trim($body['category'] ?? '');
        $subcat= trim($body['subcategory'] ?? '');
        $badge = trim($body['badge'] ?? '');
        $stock = max(0, (int)($body['stock'] ?? 100));
        $rating= min(5.0, max(0, (float)($body['rating'] ?? 4.0)));
        if (!$name) { echo json_encode(['ok'=>false,'err'=>'Product name is required']); exit; }
        if ($price <= 0) { echo json_encode(['ok'=>false,'err'=>'Price must be greater than 0']); exit; }
        $off = ($mrp > $price && $price > 0) ? (int)round((1 - $price/$mrp)*100) : 0;
        $jsonFile = BASE_DIR.'/assets/products.json';
        $j = [];
        if (file_exists($jsonFile)) { $decoded = json_decode(file_get_contents($jsonFile), true); if (is_array($decoded)) $j = $decoded; }
        $maxNum = 0;
        foreach ($j as $p) { $n = (int)preg_replace('/[^0-9]/', '', $p['id'] ?? ''); if ($n > $maxNum) $maxNum = $n; }
        $newNum = $maxNum + 1; $newId = 'p'.$newNum;
        $j[] = [
            'id'=>$newId,'name'=>$name,'brand'=>$brand,'price'=>$price,
            'mrp'=>$mrp>0?$mrp:$price,'off'=>$off,'badge'=>$badge,
            'stock'=>$stock,'rating'=>$rating,'rCount'=>0,
            'description'=>$desc,'category'=>$cat,'subcategory'=>$subcat,
            'images'=>['Images/TopPicksForYou/'.$newNum.'/1'],'variants'=>[],'sections'=>[],
        ];
        file_put_contents($jsonFile, json_encode($j, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        $ghSync = github_sync_products($jsonFile);
        echo json_encode(['ok'=>true,'id'=>$newId,'num'=>$newNum,'gh'=>$ghSync]); exit;
    }


    // Delete product
    if ($act === 'delete_product') {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid  = preg_replace('/[^a-z0-9]/', '', strtolower($body['pid'] ?? ''));
        if (!$pid) { echo json_encode(['ok'=>false,'err'=>'Invalid PID']); exit; }
        $jsonFile = BASE_DIR.'/assets/products.json';
        if (!file_exists($jsonFile)) { echo json_encode(['ok'=>false,'err'=>'products.json not found']); exit; }
        $j = json_decode(file_get_contents($jsonFile), true) ?? [];
        $before = count($j);
        $j = array_values(array_filter($j, function($p) use ($pid) { return ($p['id'] ?? '') !== $pid; }));
        if (count($j) === $before) { echo json_encode(['ok'=>false,'err'=>'Product not found']); exit; }
        file_put_contents($jsonFile, json_encode($j, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        $ghSync = github_sync_products($jsonFile);
        echo json_encode(['ok'=>true,'deleted'=>$pid,'gh'=>$ghSync]); exit;
    }

    // Save product
    if ($act === 'save_product') {
        $body  = json_decode(file_get_contents('php://input'), true) ?? [];
        $pid   = preg_replace('/[^a-z0-9]/', '', strtolower($body['pid'] ?? ''));
        if (!$pid) { echo json_encode(['ok'=>false,'err'=>'Invalid PID']); exit; }
        $name  = trim($body['name']  ?? '');
        $brand = trim($body['brand'] ?? '');
        $price = (int)($body['price'] ?? 0);
        $mrp   = (int)($body['mrp']   ?? 0);
        $off   = ($mrp > $price && $price > 0) ? (int)round((1 - $price/$mrp)*100) : 0;
        $desc  = trim(str_replace(["\r\n","\r","\n"], ' ', $body['desc'] ?? ''));

        // ── Always update products.json ──────────────────────────
        $jsonPath = BASE_DIR.'/assets/products.json';
        $jsonUpdated = false;
        if (file_exists($jsonPath)) {
            $prods = json_decode(file_get_contents($jsonPath), true) ?? [];
            foreach ($prods as &$p) {
                if (($p['id'] ?? '') === $pid) {
                    if ($name)  $p['name']        = $name;
                    if ($brand) $p['brand']        = $brand;
                    if ($price) $p['price']        = $price;
                    if ($mrp)   $p['mrp']          = $mrp;
                    $p['off']         = $off;
                    if ($desc)  $p['description']  = $desc;
                    // Save variants (always overwrite — empty array = no variants)
                    $rawVariants = $body['variants'] ?? [];
                    $p['variants'] = is_array($rawVariants) ? $rawVariants : [];
                    $jsonUpdated = true;
                    break;
                }
            }
            unset($p);
            if ($jsonUpdated) {
                file_put_contents($jsonPath, json_encode($prods, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            }
        }

        // ── Also update products-data.js for p1–p99 IDs ─────────
        if (!file_exists(PRODUCTS_JS)) {
            echo json_encode(['ok'=> $jsonUpdated, 'err'=> $jsonUpdated ? '' : 'Product not found']);
            exit;
        }
        $js = file_get_contents(PRODUCTS_JS);

        $js = preg_replace_callback(
            '/(\{"id"\s*:\s*"'.preg_quote($pid,'/').'"\s*,(?:[^{}]|\{[^{}]*\})*\})/s',
            function($m) use ($name,$brand,$price,$mrp,$off) {
                $o = $m[1];
                $o = preg_replace('/"name"\s*:\s*"[^"]*"/',  '"name": "' .addslashes($name) .'"', $o);
                $o = preg_replace('/"brand"\s*:\s*"[^"]*"/', '"brand": "'.addslashes($brand).'"', $o);
                $o = preg_replace('/"price"\s*:\s*\d+/',     '"price": '.$price,  $o);
                $o = preg_replace('/"mrp"\s*:\s*\d+/',       '"mrp": '  .$mrp,    $o);
                $o = preg_replace('/"off"\s*:\s*\d+/',       '"off": '  .$off,    $o);
                return $o;
            }, $js
        );
        $js = preg_replace_callback(
            '/(\/\/\s*'.preg_quote($pid,'/').'[^\n]*\n\s*\')((?:[^\'\\\\]|\\\\.)*)(\',.)/s',
            function($m) use ($desc) { return $m[1].addslashes($desc).$m[3]; }, $js
        );

        $pidNum = (int)preg_replace('/[^0-9]/', '', $pid);
        if ($pidNum >= 1 && $pidNum <= 99) {
            $idx = $pidNum - 1;
            $js = preg_replace_callback('/const priceList\s*=\s*\[([^\]]+)\]/s', function($m) use ($idx,$price) {
                $items = preg_split('/,(?![^\[]*\])/', $m[1]);
                if (isset($items[$idx])) $items[$idx] = preg_replace('/\s*\d+\s*/', $price, $items[$idx], 1);
                return 'const priceList=['.implode(',', $items).']';
            }, $js);
            $js = preg_replace_callback('/(\/\/\s*p'.$pidNum.'\s*\n\s*\')((?:[^\'\\\\]|\\\\.)*)(\')/', function($m) use ($name) { return $m[1].addslashes($name).$m[3]; }, $js);
            $js = preg_replace_callback("/('[^']*')(,?\s*\/\/\s*p".$pidNum."\b)/", function($m) use ($brand) { return "'".addslashes($brand)."'".$m[2]; }, $js);
        }

        if (file_put_contents(PRODUCTS_JS, $js) === false) {
            $ghSync = github_sync_products($jsonPath);
            echo json_encode(['ok'=>true,'off'=>$off,'warn'=>'products-data.js write failed — serving from products.json','gh'=>$ghSync]); exit;
        }

        $ghSync = github_sync_products($jsonPath);
        echo json_encode(['ok'=>true,'off'=>$off,'gh'=>$ghSync]); exit;
    }

    // Get payment
    if ($act === 'get_payment') {
        $defaults = ['upi_id'=>'','merchant_name'=>'Flipkart','mcc'=>'5262','tr_id'=>'','cod_amount'=>99,'cod_threshold'=>200,'cod_low'=>49,'platform_fee'=>7,'cod_note'=>'Flipkart COD Security','pay_note'=>'Flipkart','currency'=>'INR'];
        if (file_exists(PAYMENT_FILE)) { $s = json_decode(file_get_contents(PAYMENT_FILE), true); if (is_array($s)) $defaults = array_merge($defaults, $s); }
        echo json_encode(['ok'=>true,'data'=>$defaults]); exit;
    }

    // Save payment
    if ($act === 'save_payment') {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        // Preserve UPI fields from existing file — not editable via admin
        $existing = [];
        if (file_exists(PAYMENT_FILE)) { $s = json_decode(file_get_contents(PAYMENT_FILE), true); if (is_array($s)) $existing = $s; }
        $data = [
            'upi_id'        => $existing['upi_id']        ?? '',
            'merchant_name' => $existing['merchant_name'] ?? 'Flipkart',
            'mcc'           => $existing['mcc']           ?? '5262',
            'tr_id'         => $existing['tr_id']         ?? '',
            'platform_fee'  => $existing['platform_fee']  ?? 7,
            'cod_amount'    => max(0, (int)($body['cod_amount']    ?? 99)),
            'cod_threshold' => max(0, (int)($body['cod_threshold'] ?? 200)),
            'cod_low'       => max(0, (int)($body['cod_low']       ?? 49)),
            'cod_note'      => trim($body['cod_note'] ?? 'Flipkart COD Security'),
            'pay_note'      => $existing['pay_note'] ?? 'Flipkart',
            'currency'      => 'INR',
        ];
        $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false || file_put_contents(PAYMENT_FILE, $encoded, LOCK_EX) === false) {
            http_response_code(500);
            echo json_encode(['ok'=>false,'err'=>'Failed to save payment settings — check file permissions.']); exit;
        }
        echo json_encode(['ok'=>true,'msg'=>'Payment settings saved. payment.php reads these settings live.']); exit;
    }


    // Clear analytics
    if ($act === 'clear_analytics') {
        if (file_exists(ANALYTICS_FILE)) @unlink(ANALYTICS_FILE);
        echo json_encode(['ok'=>true]); exit;
    }

    // ── IMPORT ALL PRODUCT IMAGES FROM ZIP ───────────────────
    if ($act === 'import_product_images') {
        if (empty($_FILES['zipfile']['tmp_name'])) { echo json_encode(['ok'=>false,'err'=>'No ZIP file received']); exit; }
        if (!class_exists('ZipArchive')) { echo json_encode(['ok'=>false,'err'=>'ZipArchive not available on server']); exit; }

        $zip = new ZipArchive();
        if ($zip->open($_FILES['zipfile']['tmp_name']) !== true) {
            echo json_encode(['ok'=>false,'err'=>'Cannot open ZIP — file may be corrupt']); exit;
        }

        $imageExts  = ['jpg','jpeg','png','webp','avif'];
        $baseDir    = BASE_DIR . '/Images/TopPicksForYou';
        $extracted  = []; // [prodNum => [imgNum => relPath]]
        $errors     = [];
        $skipped    = 0;

        for ($zi = 0; $zi < $zip->numFiles; $zi++) {
            $zname = $zip->getNameIndex($zi);
            $bname = basename($zname);
            // Skip directories and hidden files
            if (substr($zname,-1)==='/' || $bname==='' || $bname[0]==='.') continue;
            $ext = strtolower(pathinfo($bname, PATHINFO_EXTENSION));
            if (!in_array($ext, $imageExts)) { $skipped++; continue; }

            $stem  = pathinfo($bname, PATHINFO_FILENAME);
            $parts = array_values(array_filter(explode('/', trim($zname,'/')), fn($p)=>$p!==''));
            $prodNum = null; $imgNum = null;

            // Pattern 1: TopPicksForYou/N/M.ext  OR  anything/N/M.ext  (subfolder structure)
            if (count($parts) >= 2) {
                $parentDir = $parts[count($parts)-2];
                // Strip any p prefix from folder name
                $parentClean = preg_replace('/^p/i', '', $parentDir);
                if (is_numeric($parentClean) && is_numeric($stem)) {
                    $prodNum = (int)$parentClean;
                    $imgNum  = (int)$stem;
                } elseif (is_numeric($parentClean) && !is_numeric($stem)) {
                    // Folder = product num, file name is not numeric (e.g. front.jpg) → sequential
                    $prodNum = (int)$parentClean;
                    $imgNum  = null; // assign later sequentially
                }
            }
            // Pattern 2: N_M.ext (flat: 3_2.jpg)
            if ($prodNum === null && preg_match('/^p?(\d+)[_\-](\d+)$/', $stem, $m)) {
                $prodNum = (int)$m[1]; $imgNum = (int)$m[2];
            }
            // Pattern 3: flat file, numeric name (N.jpg) → prodNum=N, imgNum=1
            if ($prodNum === null && is_numeric(preg_replace('/^p/i','',$stem))) {
                $prodNum = (int)preg_replace('/^p/i','',$stem);
                $imgNum  = 1;
            }

            if ($prodNum === null || $prodNum < 1) { $errors[] = "Skipped '$bname' — cannot detect product number"; continue; }

            // Handle sequential imgNum when not determinable from filename
            if ($imgNum === null) {
                $imgNum = count($extracted[$prodNum] ?? []) + 1;
            }
            if ($imgNum < 1) $imgNum = 1;

            // Ensure destination directory exists
            $destFolder = "$baseDir/$prodNum";
            if (!is_dir($destFolder)) {
                if (!@mkdir($destFolder, 0777, true)) { $errors[] = "Cannot create folder: $destFolder"; continue; }
                @chmod($destFolder, 0777);
            }

            // Remove any existing file with same base name (different extension)
            foreach (glob("$destFolder/$imgNum.*") ?: [] as $old) @unlink($old);

            $destFile = "$destFolder/$imgNum.$ext";
            $contents = $zip->getFromIndex($zi);
            if ($contents === false) { $errors[] = "Cannot read '$bname' from ZIP"; continue; }
            if (file_put_contents($destFile, $contents) !== false) {
                @chmod($destFile, 0644);
                $extracted[$prodNum][$imgNum] = "Images/TopPicksForYou/$prodNum/$imgNum.$ext";
            } else {
                $errors[] = "Write failed: $destFile — set folder permissions to 777";
            }
        }
        $zip->close();

        // ── Now update products.json with fresh local paths ──────
        $jsonFile    = BASE_DIR . '/assets/products.json';
        $updatedProds = 0;
        $jsonMsg     = '';
        if (!empty($extracted) && file_exists($jsonFile)) {
            $prods = json_decode(file_get_contents($jsonFile), true) ?? [];
            foreach ($prods as &$prod) {
                $pidNum = (int)preg_replace('/[^0-9]/', '', $prod['id'] ?? '');
                if (!isset($extracted[$pidNum])) continue;
                // Sort images by imgNum, rebuild array
                $imgs = $extracted[$pidNum];
                ksort($imgs);
                $prod['images'] = array_values($imgs);
                $updatedProds++;
            }
            unset($prod);
            file_put_contents($jsonFile, json_encode($prods, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            $jsonMsg = "Updated $updatedProds product(s) in products.json";
        }

        // Also scan folder for products NOT in extracted (already had images)
        // and refresh any product whose folder now has more images than recorded
        if (file_exists($jsonFile)) {
            $prods = json_decode(file_get_contents($jsonFile), true) ?? [];
            $changed = false;
            foreach ($prods as &$prod) {
                $pidNum = (int)preg_replace('/[^0-9]/', '', $prod['id'] ?? '');
                $folder = "$baseDir/$pidNum";
                if (!is_dir($folder)) continue;
                // Collect all image files in folder
                $imgs = [];
                foreach (glob("$folder/*") ?: [] as $f) {
                    $fext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                    if (!in_array($fext, $imageExts)) continue;
                    $fbase = pathinfo($f, PATHINFO_FILENAME);
                    if (is_numeric($fbase)) $imgs[(int)$fbase] = "Images/TopPicksForYou/$pidNum/$fbase.$fext";
                }
                if (empty($imgs)) continue;
                ksort($imgs);
                $newImgs = array_values($imgs);
                // Only update if different from current
                if (($prod['images'] ?? []) !== $newImgs) {
                    $prod['images'] = $newImgs;
                    $changed = true;
                }
            }
            unset($prod);
            if ($changed) file_put_contents($jsonFile, json_encode($prods, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        $totalFiles = array_sum(array_map('count', $extracted));
        echo json_encode([
            'ok'      => true,
            'files'   => $totalFiles,
            'products'=> count($extracted),
            'updated' => $updatedProds,
            'skipped' => $skipped,
            'errors'  => $errors,
            'msg'     => $jsonMsg,
            'debug'   => array_map(fn($num,$imgs)=>"p$num: ".count($imgs)." image(s)", array_keys($extracted), $extracted),
        ]);
        exit;
    }

    echo json_encode(['ok'=>false,'err'=>'Unknown action']); exit;
}

// ── HELPERS ──────────────────────────────────────────────────
function getDestPath(array $cfg, int $slot, int $imgnum = 1, string $srcExt = 'avif'): array {
    $folder = BASE_DIR . '/' . $cfg['folder'];
    $type   = $cfg['type'];
    $e      = strtolower($srcExt ?: 'avif');
    if ($type === 'single') {
        $baseName = 'banner';
        if (!empty($cfg['filename'])) {
            $baseName = pathinfo($cfg['filename'], PATHINFO_FILENAME) ?: 'banner';
        }
        return [$folder, $baseName.'.'.$e];
    }
    if ($type === 'direct')    { if ($slot < 1 || $slot > $cfg['count']) return [null,null]; return [$folder, "$slot.$e"]; }
    if ($type === 'subfolder') { if ($slot < 1 || $slot > $cfg['count']) return [null,null]; return ["$folder/$slot", '1.'.$e]; }
    if ($type === 'toppicks')  { if ($slot < 1 || $slot > $cfg['count']) return [null,null]; return ["$folder/$slot", max(1,$imgnum).'.'.$e]; }
    return [null, null];
}

function getCurrentImg(array $cfg, int $slot, int $imgnum = 1): string {
    // Check Cloudinary map first (Render-safe persistent storage)
    static $map = null;
    if ($map === null) {
        $f = BASE_DIR.'/assets/cloudinary_map.json';
        $map = file_exists($f) ? (json_decode(file_get_contents($f), true) ?: []) : [];
    }
    // Find section key for this cfg
    global $SECTIONS;
    $key = '';
    foreach ($SECTIONS as $k => $s) { if ($s === $cfg) { $key = $k; break; } }
    if ($key && isset($map[$key][$slot][$imgnum])) return $map[$key][$slot][$imgnum];

    // Fallback to local disk (for legacy/existing images)
    [$dir, $file] = getDestPath($cfg, $slot, $imgnum);
    if (!$dir) return '';
    $base = pathinfo($file, PATHINFO_FILENAME);
    foreach (['avif','png','jpg','jpeg','webp'] as $ext) {
        $p = "$dir/$base.$ext";
        if (file_exists($p)) return str_replace(BASE_DIR.'/', '', $p).'?t='.filemtime($p);
    }
    return '';
}

function getProductData(string $pid): array {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $f = BASE_DIR.'/assets/products.json';
        if (file_exists($f)) { $a = json_decode(file_get_contents($f), true); if (is_array($a)) foreach ($a as $p) if (!empty($p['id'])) $cache[$p['id']] = $p; }
    }
    $out = ['name'=>'','brand'=>'','price'=>'','mrp'=>'','off'=>'','desc'=>'','variants'=>[]];
    if (!isset($cache[$pid])) return $out;
    $p = $cache[$pid];
    return ['name'=>$p['name']??'','brand'=>$p['brand']??'','price'=>$p['price']??'','mrp'=>$p['mrp']??'','off'=>$p['off']??'','desc'=>$p['description']??'','variants'=>$p['variants']??[]];
}

// ── LOGIN PAGE ────────────────────────────────────────────────

// ── PAGE SETUP ────────────────────────────────────────────────
$tab = $_GET['tab'] ?? 'images';
// Include "bot" tab into allowed list for Telegram bot settings
if (!in_array($tab, ['images','products','shoes','mens','electronics','gadgets','payment','bot'])) {
    $tab = 'images';
}
$imgSection = $_GET['section'] ?? 'banners';
if (!isset($SECTIONS[$imgSection])) $imgSection = 'banners';
$page = max(1, (int)($_GET['page'] ?? 1));
// === Telegram bot settings processing ===
// Path to bot configuration file
$tgConfigPath = BASE_DIR . '/assets/tg_config.php';
// On bot tab and POST request, update the config file with new values
if ($tab === 'bot' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $newToken = trim($_POST['tg_token'] ?? '');
    $newChat  = trim($_POST['tg_chat_id'] ?? '');
    // Build PHP array content
    $conf  = "<?php\n";
    $conf .= "// Auto-generated Telegram bot configuration\n";
    $conf .= "return [\n";
    $conf .= "    'token' => '" . addslashes($newToken) . "',\n";
    $conf .= "    'chat_id' => '" . addslashes($newChat) . "',\n";
    $conf .= "];\n";
    @file_put_contents($tgConfigPath, $conf);
    $botSaved = true;
}
// Load current bot configuration or defaults
if (file_exists($tgConfigPath)) {
    $tg_config = include $tgConfigPath;
    if (!is_array($tg_config)) $tg_config = ['token' => '', 'chat_id' => ''];
} else {
    $tg_config = ['token' => '', 'chat_id' => ''];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ShopAdmin</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --accent:#3b82f6;--accent-d:#2563eb;
  --green:#10b981;--red:#ef4444;--amber:#f59e0b;--purple:#8b5cf6;--blue:#2874f0;
  --bg:#0f1117;--surface:#161b2e;--surface2:#1e2336;--border:rgba(255,255,255,.07);
  --txt:#e8eaf0;--mut:rgba(255,255,255,.38);--radius:14px;
  --shadow:0 8px 32px rgba(0,0,0,.5);
}
body{font-family:'Outfit',sans-serif;background:var(--bg);color:var(--txt);min-height:100vh;font-size:14px}

/* ── GLOBAL HEADER ─────────────────────────────────────────── */
.g-header{display:flex;align-items:center;justify-content:space-between;background:var(--surface);border-bottom:1px solid var(--border);padding:10px 20px;position:sticky;top:0;z-index:200;gap:12px;flex-wrap:wrap}
.g-header-logo{display:flex;align-items:center;gap:10px}
.g-header-logo .nm{font-size:15px;font-weight:700;color:var(--txt)}
.g-header-logo .sm{font-size:10px;color:var(--mut)}
.g-header-logo .logo-ic{width:36px;height:36px;background:linear-gradient(135deg,var(--accent) 0%,var(--purple) 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;box-shadow:0 4px 12px rgba(59,130,246,.4)}
.g-header-actions{display:none}
.btn-hdr{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font:700 12px 'Outfit',sans-serif;cursor:pointer;border:1.5px solid;transition:all .18s;white-space:nowrap}
.btn-hdr-zip{background:rgba(59,130,246,.12);color:var(--accent);border-color:rgba(59,130,246,.28)}
.btn-hdr-zip:hover{background:var(--accent);color:#fff;border-color:var(--accent)}
.btn-hdr-csv{background:rgba(245,158,11,.12);color:var(--amber);border-color:rgba(245,158,11,.28)}
.btn-hdr-csv:hover{background:var(--amber);color:#fff;border-color:var(--amber)}
.hamburger{display:none;background:none;border:1.5px solid var(--border);cursor:pointer;padding:7px 10px;color:var(--txt);font-size:18px;border-radius:8px;line-height:1;transition:all .18s}
.hamburger:hover{background:rgba(255,255,255,.07)}

/* ── LAYOUT ────────────────────────────────────────────────── */
.wrap{display:flex;min-height:calc(100vh - 57px)}
.sidebar{width:240px;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0;position:sticky;top:57px;height:calc(100vh - 57px);overflow-y:auto}
.main{flex:1;padding:28px 36px;overflow-x:hidden;min-width:0}

/* ── SIDEBAR ───────────────────────────────────────────────── */
.sb-logo{padding:20px 18px 16px;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border)}
.sb-logo .logo-ic{width:40px;height:40px;background:linear-gradient(135deg,var(--accent) 0%,var(--purple) 100%);border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;box-shadow:0 4px 12px rgba(59,130,246,.4)}
.sb-logo .nm{font-size:15px;font-weight:700;color:var(--txt);letter-spacing:-.3px}
.sb-logo .sm{font-size:11px;color:var(--mut);margin-top:1px}
.sb-section-lbl{font-size:10px;font-weight:700;color:var(--mut);letter-spacing:1px;text-transform:uppercase;padding:16px 18px 6px}
.sb-nav{padding:6px 10px;flex:1}
.sb-nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:var(--mut);text-decoration:none;font-size:13px;font-weight:500;margin-bottom:2px;transition:all .18s;position:relative;overflow:hidden}
.sb-nav a:hover{background:rgba(255,255,255,.05);color:var(--txt)}
.sb-nav a.on{background:rgba(59,130,246,.15);color:var(--accent);font-weight:600}
.sb-nav a.on::before{content:'';position:absolute;left:0;top:22%;bottom:22%;width:3px;background:var(--accent);border-radius:0 3px 3px 0}
.sb-nav a .ic{font-size:16px;width:22px;text-align:center;flex-shrink:0}
.sb-foot{padding:14px;border-top:1px solid var(--border);display:grid;gap:8px;margin-top:auto;position:sticky;bottom:0;background:linear-gradient(180deg, rgba(22,27,46,0.82) 0%, var(--surface) 22%)}
.sb-action-btn{width:100%;display:flex;align-items:center;gap:10px;padding:11px 12px;border-radius:11px;border:1px solid var(--border);background:rgba(255,255,255,.04);color:var(--txt);text-decoration:none;font:600 12px 'Outfit',sans-serif;cursor:pointer;transition:all .18s}
.sb-action-btn:hover{background:rgba(255,255,255,.07);border-color:rgba(255,255,255,.16)}
.sb-action-btn .ic{font-size:15px;width:18px;text-align:center;flex-shrink:0}
.sb-action-btn.logout{background:rgba(239,68,68,.10);color:#ff9a9a;border-color:rgba(239,68,68,.22)}
.sb-action-btn.logout:hover{background:rgba(239,68,68,.16);border-color:rgba(239,68,68,.34)}

/* ── TOPBAR ────────────────────────────────────────────────── */
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap}
.topbar h1{font-size:22px;font-weight:800;color:var(--txt);letter-spacing:-.5px;line-height:1.1}
.topbar .sub{font-size:13px;color:var(--mut);margin-top:4px}
.topbar-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}

/* ── CARDS ─────────────────────────────────────────────────── */
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px}
.card+.card{margin-top:16px}

/* ── SECTION PICKER ────────────────────────────────────────── */
.sec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:8px;margin-bottom:20px}
.sec-btn{background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;padding:14px 10px;text-align:center;cursor:pointer;transition:all .18s;text-decoration:none;display:block}
.sec-btn:hover{border-color:var(--accent);background:rgba(59,130,246,.08)}
.sec-btn.on{border-color:var(--accent);background:rgba(59,130,246,.12)}
.sec-btn .sic{font-size:22px;display:block;margin-bottom:5px}
.sec-btn .slb{font-size:11px;font-weight:600;color:var(--txt);display:block}
.sec-btn .sct{font-size:10px;color:var(--mut);display:block;margin-top:2px}

/* ── IMAGE GRID ────────────────────────────────────────────── */
.igrid{display:grid;gap:12px}
.igrid.g2{grid-template-columns:repeat(2,1fr)}.igrid.g3{grid-template-columns:repeat(3,1fr)}
.igrid.g4{grid-template-columns:repeat(4,1fr)}.igrid.g5{grid-template-columns:repeat(5,1fr)}
.igrid.g6{grid-template-columns:repeat(6,1fr)}
@media(max-width:900px){.igrid.g5,.igrid.g6{grid-template-columns:repeat(3,1fr)}.igrid.g4{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.igrid{grid-template-columns:repeat(2,1fr)!important}}
.icard{background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .18s,transform .18s}
.icard:hover{border-color:rgba(59,130,246,.35);transform:translateY(-1px)}
.ithumb{height:120px;background:rgba(255,255,255,.03);display:flex;align-items:center;justify-content:center;overflow:hidden}
.ithumb img{width:100%;height:100%;object-fit:cover}
.ithumb .ph{font-size:32px;opacity:.18}
.ifoot{padding:10px}
.ilabel{font-size:11px;font-weight:600;color:var(--txt);margin-bottom:2px}
.ipath{font-size:10px;color:var(--mut);margin-bottom:8px;font-family:monospace;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.istatus{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;display:inline-block;margin-bottom:6px}
.istatus.has{background:rgba(16,185,129,.15);color:var(--green)}
.istatus.nil{background:rgba(239,68,68,.12);color:var(--red)}
.upbtn{width:100%;padding:8px;background:var(--accent);color:#fff;border:none;border-radius:8px;font:600 12px 'Outfit',sans-serif;cursor:pointer;transition:background .18s}
.upbtn:hover{background:var(--accent-d)}

/* ── PRODUCT GRID ──────────────────────────────────────────── */
.pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px}
.pcard{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--radius);padding:16px;transition:all .18s}
.pcard:hover{border-color:rgba(59,130,246,.3);box-shadow:0 4px 20px rgba(0,0,0,.3)}
.pcard-top{display:flex;gap:12px;margin-bottom:14px}
.pthumb{width:60px;height:60px;border-radius:10px;object-fit:cover;flex-shrink:0;background:var(--surface2)}
.pph{width:60px;height:60px;border-radius:10px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0}
.pmeta{flex:1;min-width:0}
.pid{font-size:11px;font-weight:700;color:var(--accent);font-family:monospace;letter-spacing:.5px}
.pname{font-size:13px;font-weight:600;color:var(--txt);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:2px 0}
.pbrand{font-size:11px;color:var(--mut)}
.pprice{font-size:14px;font-weight:700;color:var(--txt)}
.pmrp{font-size:11px;color:var(--mut);text-decoration:line-through;margin-left:5px}
.poff{font-size:11px;color:var(--green);margin-left:4px;font-weight:700}
.btn-edit{width:100%;padding:8px;background:rgba(59,130,246,.1);color:var(--accent);border:1.5px solid rgba(59,130,246,.2);border-radius:8px;font:600 12px 'Outfit',sans-serif;cursor:pointer;transition:all .18s}
.btn-edit:hover{background:var(--accent);color:#fff;border-color:var(--accent)}

/*
 * Buttons row for product cards (toppicks actions)
 * The grid displays four actions: Edit, Images, ZIP and Delete.
 * On larger screens the first button takes all remaining space and the
 * subsequent three occupy only as much width as needed.  On small
 * screens (under 768px) the buttons wrap into two columns for better
 * touch accessibility.
 */
.btn-row{
  display:grid;
  grid-template-columns:1fr auto auto auto;
  gap:6px;
}
@media(max-width:768px){
  .btn-row{
    grid-template-columns:1fr 1fr;
    /* stack rows when there are more than two buttons */
    grid-auto-rows:1fr;
  }
}

/* ── MOBILE ADMIN RESPONSIVE FIXES ─────────────────────────── */
@media(max-width:600px){
  /* Product cards — full width list layout */
  .pgrid{grid-template-columns:1fr!important;gap:10px}
  .pcard{padding:12px}
  .pcard-top{gap:10px;margin-bottom:10px}
  .pthumb{width:52px;height:52px;border-radius:8px}
  .pph{width:52px;height:52px;font-size:20px}
  .pname{font-size:12px;white-space:normal;line-height:1.3}
  .pbrand{font-size:10px}
  .pprice{font-size:13px}
  .pmrp,.poff{font-size:10px}
  /* Buttons — 2x2 grid on mobile */
  .btn-row{grid-template-columns:1fr 1fr!important;grid-auto-rows:auto;gap:6px}
  .btn-edit{padding:7px 6px;font-size:11px}
  /* Stats row — 2 columns */
  .stats-row{grid-template-columns:repeat(2,1fr)!important;gap:8px}
  .stats-row .card{padding:12px 8px}
  /* Section buttons grid */
  .sec-grid{grid-template-columns:repeat(2,1fr)!important}
  .sec-btn{padding:12px 8px;min-height:80px}
  /* Tab bar & topbar */
  .topbar{gap:8px;margin-bottom:12px}
  .topbar h1{font-size:17px!important}
  .topbar-actions{flex-wrap:wrap;gap:6px}
  .btn-hdr{padding:7px 10px;font-size:11px}
  /* Image grid */
  .igrid{grid-template-columns:repeat(2,1fr)!important}
  .ithumb{height:90px}
  /* Search bar */
  .srchwrap input{font-size:13px;padding:9px 12px 9px 36px}
  /* Modal full screen on mobile */
  .mo{padding:0;align-items:flex-end}
  .mbox{max-width:100%!important;border-radius:18px 18px 0 0;max-height:92vh}
  .mhead{border-radius:18px 18px 0 0;padding:14px 16px}
  .mhead h3{font-size:14px}
  .mbody{padding:14px 16px}
  .mfoot{padding:12px 16px;gap:8px}
  .fr2{grid-template-columns:1fr!important}
  /* Pagination */
  .pag{gap:4px}
  .pbtn{padding:6px 10px;font-size:12px}
  /* Variant builder */
  .vgrp{padding:10px 12px}
  .vgrp-head{flex-wrap:wrap;gap:6px}
  /* Analytics grid */
  .an-grid{grid-template-columns:repeat(2,1fr)!important;gap:8px}
  /* Filter pills — scroll horizontally */
  .mens-pills{overflow-x:auto;flex-wrap:nowrap;-webkit-overflow-scrolling:touch;padding-bottom:4px}
  .mens-pill{white-space:nowrap;flex-shrink:0}
  /* Cards general */
  .card{padding:14px}
}

/*
 * Wider thumbnail variant used for image management sections
 * When class 'wide' is applied on .ithumb the height is reduced so
 * thumbnails appear wider than tall.  Without this the thumbnails
 * default to a square (120px) height defined above.
 */
.ithumb.wide{
  height:80px;
}

/* ── SEARCH ────────────────────────────────────────────────── */
.srchwrap{position:relative;margin-bottom:16px}
.srchwrap input{width:100%;padding:11px 14px 11px 40px;border:1.5px solid var(--border);border-radius:10px;font:14px 'Outfit',sans-serif;outline:none;background:var(--surface2);color:var(--txt);transition:border .18s}
.srchwrap input::placeholder{color:var(--mut)}
.srchwrap input:focus{border-color:var(--accent);background:var(--surface)}
.srchwrap .si{position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:16px;color:var(--mut)}

/* ── PAGINATION ────────────────────────────────────────────── */
.pag{display:flex;gap:6px;align-items:center;margin-top:20px;flex-wrap:wrap}
.pbtn{padding:7px 13px;border:1.5px solid var(--border);background:var(--surface2);border-radius:8px;font:500 13px 'Outfit',sans-serif;cursor:pointer;color:var(--txt);transition:all .18s}
.pbtn:hover{border-color:var(--accent);color:var(--accent)}
.pbtn.cur{background:var(--accent);color:#fff;border-color:var(--accent)}
.pinfo{margin-left:auto;font-size:12px;color:var(--mut)}

/* ── PAYMENT FORM ──────────────────────────────────────────── */
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--txt);margin-bottom:6px}
.form-group label .hint{font-weight:400;color:var(--mut);margin-left:6px;font-size:11px}
.form-group input{width:100%;padding:11px 13px;border:1.5px solid var(--border);border-radius:9px;font:14px 'Outfit',sans-serif;outline:none;background:var(--surface2);color:var(--txt);transition:border .18s}
.form-group input:focus{border-color:var(--accent)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.btn-save{padding:12px 28px;background:var(--accent);color:#fff;border:none;border-radius:10px;font:700 14px 'Outfit',sans-serif;cursor:pointer;transition:background .18s}
.btn-save:hover{background:var(--accent-d)}
.result{padding:11px 14px;border-radius:9px;font-size:13px;font-weight:600;margin-top:14px;display:none}
.result.ok{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.3)}
.result.err{background:rgba(239,68,68,.1);color:var(--red);border:1px solid rgba(239,68,68,.25)}

/* ── MODAL ─────────────────────────────────────────────────── */
.mo{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);backdrop-filter:blur(6px);z-index:1000;align-items:center;justify-content:center;padding:16px}
.mo.open{display:flex}
.mbox{background:var(--surface);border:1px solid var(--border);border-radius:18px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow)}
.mhead{display:flex;align-items:center;justify-content:space-between;padding:20px 22px;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--surface);z-index:1;border-radius:18px 18px 0 0}
.mhead h3{font-size:16px;font-weight:700;color:var(--txt)}
.mclose{background:rgba(255,255,255,.07);border:none;font-size:16px;cursor:pointer;color:var(--mut);width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:all .18s}
.mclose:hover{background:rgba(255,255,255,.12);color:var(--txt)}
.mbody{padding:22px}
.mfoot{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;position:sticky;bottom:0;background:var(--surface);z-index:1;border-radius:0 0 18px 18px}
.btn-cancel{padding:9px 18px;background:rgba(255,255,255,.06);color:var(--txt);border:1.5px solid var(--border);border-radius:9px;font:500 13px 'Outfit',sans-serif;cursor:pointer;transition:all .18s}
.btn-cancel:hover{background:rgba(255,255,255,.1)}

/* ── DROPZONE ──────────────────────────────────────────────── */
.dropzone{border:2px dashed rgba(59,130,246,.3);border-radius:12px;padding:32px 16px;text-align:center;cursor:pointer;transition:all .18s;background:rgba(59,130,246,.04)}
.dropzone:hover,.dropzone.drag{border-color:var(--accent);background:rgba(59,130,246,.1)}
.dz-ic{font-size:36px;margin-bottom:10px}
.dropzone p{font-size:13px;font-weight:600;color:var(--accent)}
.dropzone small{font-size:11px;color:var(--mut)}
.progress{height:4px;background:rgba(255,255,255,.08);border-radius:4px;overflow:hidden;margin-top:12px;display:none}
.pbar{height:100%;width:0;background:var(--accent);transition:width .3s}
.prev-wrap{margin-top:12px;display:none;text-align:center}
.prev-wrap p{font-size:12px;color:var(--mut);margin-bottom:6px}
.prev-wrap img{max-width:100%;max-height:180px;border-radius:10px;border:1px solid var(--border)}
.cur-img{margin-bottom:14px;display:none}
.cur-img p{font-size:12px;color:var(--mut);margin-bottom:6px}
.cur-img img{max-width:100%;max-height:120px;border-radius:10px;border:1px solid var(--border)}
.no-img{font-size:12px;color:var(--mut);margin-bottom:14px}

/* ── EDIT MODAL ────────────────────────────────────────────── */
.fr{margin-bottom:14px}
.fr label{display:block;font-size:12px;font-weight:600;color:var(--txt);margin-bottom:5px}
.fr input,.fr textarea{width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font:14px 'Outfit',sans-serif;outline:none;background:var(--surface2);color:var(--txt);transition:border .18s}
.fr input:focus,.fr textarea:focus{border-color:var(--accent)}
.fr textarea{min-height:80px;resize:vertical}
.fr2{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px}
.offbar{padding:9px 13px;background:rgba(16,185,129,.1);border-radius:8px;font-size:12px;color:var(--green);font-weight:600;margin-bottom:14px;display:none;border:1px solid rgba(16,185,129,.2)}

/* ── VARIANTS ──────────────────────────────────────────────── */
.vgrp{background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:12px}
.vgrp-head{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.vgrp-head input{flex:1;padding:6px 10px;border:1.5px solid var(--border);border-radius:7px;font:13px 'Outfit',sans-serif;outline:none;background:var(--surface);color:var(--txt)}
.vgrp-head input:focus{border-color:var(--accent)}
.vgrp-type{padding:6px 8px;border:1.5px solid var(--border);border-radius:7px;font:13px 'Outfit',sans-serif;outline:none;background:var(--surface);color:var(--txt);cursor:pointer}
.vgrp-del{background:none;border:none;color:var(--red);font-size:18px;cursor:pointer;opacity:.7;padding:0 4px;flex-shrink:0}
.vgrp-del:hover{opacity:1}
.vopts{display:flex;flex-direction:column;gap:6px;margin-bottom:8px}
.vopt-row{display:flex;align-items:center;gap:6px}
.vopt-row input[type=text]{flex:1;padding:5px 9px;border:1.5px solid var(--border);border-radius:7px;font:12.5px 'Outfit',sans-serif;outline:none;background:var(--surface);color:var(--txt)}
.vopt-row input[type=text]:focus{border-color:var(--accent)}
.vopt-row input[type=color]{width:32px;height:30px;border:1.5px solid var(--border);border-radius:7px;padding:1px;cursor:pointer;flex-shrink:0}
.vopt-row input[type=checkbox]{width:16px;height:16px;cursor:pointer;flex-shrink:0;accent-color:var(--green)}
.vopt-check-lbl{font-size:11px;color:var(--mut);white-space:nowrap}
.vopt-del{background:none;border:none;color:rgba(255,255,255,.2);font-size:16px;cursor:pointer;padding:0 2px;flex-shrink:0}
.vopt-del:hover{color:var(--red)}
.vadd-opt{background:none;border:1.5px dashed rgba(255,255,255,.1);color:var(--mut);border-radius:7px;padding:5px 10px;font-size:12px;cursor:pointer;width:100%;margin-top:2px;transition:all .18s}
.vadd-opt:hover{border-color:var(--accent);color:var(--accent)}

/* ── ZIP/CSV BUTTONS ───────────────────────────────────────── */
.btn-zip{padding:10px 18px;background:rgba(59,130,246,.1);color:var(--accent);border:1.5px solid rgba(59,130,246,.25);border-radius:10px;font:600 13px 'Outfit',sans-serif;cursor:pointer;transition:all .18s;white-space:nowrap}
.btn-zip:hover{background:var(--accent);color:#fff;border-color:var(--accent)}

/* ── TOAST ─────────────────────────────────────────────────── */
.toast{position:fixed;bottom:24px;right:24px;background:var(--surface2);color:var(--txt);border:1px solid var(--border);padding:13px 20px;border-radius:12px;font-size:13px;font-weight:600;opacity:0;transform:translateY(10px);transition:all .3s;z-index:9999;max-width:320px;box-shadow:var(--shadow)}
.toast.show{opacity:1;transform:translateY(0)}

/* ── SECTION HEADING ───────────────────────────────────────── */
.sh{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px}
.sh h2{font-size:17px;font-weight:700;color:var(--txt)}
.sh p{font-size:12px;color:var(--mut);margin-top:3px}
.badge{font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;white-space:nowrap;flex-shrink:0}
.badge-b{background:rgba(59,130,246,.15);color:var(--accent)}
.badge-g{background:rgba(16,185,129,.12);color:var(--green)}

/* ── ANALYTICS ─────────────────────────────────────────────── */
.an-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.an-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 16px;text-align:center}
.an-num{font-size:30px;font-weight:800;line-height:1;color:var(--txt)}
.an-lbl{font-size:11px;color:var(--mut);margin-top:5px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.an-sub{font-size:10px;color:var(--mut);margin-top:2px}
.chart-wrap{position:relative;height:180px;display:flex;align-items:flex-end;gap:3px;padding:0 2px}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;cursor:default}
.bar-fill{width:100%;border-radius:4px 4px 0 0;transition:height .4s ease;min-height:2px;position:relative}
.bar-fill:hover::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 4px);left:50%;transform:translateX(-50%);background:var(--surface2);color:var(--txt);padding:3px 7px;border-radius:5px;font-size:10px;white-space:nowrap;z-index:10;pointer-events:none;border:1px solid var(--border)}
.bar-lbl{font-size:9px;color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;text-align:center}
.chart-title{font-size:13px;font-weight:700;margin-bottom:10px;color:var(--txt)}
.chart-y{position:absolute;left:0;top:0;height:100%;display:flex;flex-direction:column;justify-content:space-between;pointer-events:none}
.chart-y span{font-size:9px;color:rgba(255,255,255,.2)}
.legend-row{display:flex;gap:12px;flex-wrap:wrap;margin-top:10px}
.legend-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:2px}
.legend-item{display:flex;align-items:flex-start;gap:6px;font-size:12px;color:var(--txt)}
.tbl{width:100%;border-collapse:collapse;font-size:12px}
.tbl th{padding:10px 12px;background:rgba(255,255,255,.03);color:var(--mut);font-weight:700;text-align:left;border-bottom:1px solid var(--border);font-size:11px;text-transform:uppercase;letter-spacing:.5px}
.tbl td{padding:10px 12px;border-bottom:1px solid var(--border);vertical-align:middle;color:var(--txt)}
.tbl tr:last-child td{border-bottom:none}
.tbl tr:hover td{background:rgba(255,255,255,.02)}
.device-bar{height:7px;border-radius:4px;background:rgba(255,255,255,.07);overflow:hidden;margin-top:4px}
.device-fill{height:100%;border-radius:4px}
.hour-grid{display:grid;grid-template-columns:repeat(24,1fr);gap:2px;margin-top:8px}
.hour-cell{height:28px;border-radius:3px;cursor:default;position:relative}
.hour-cell:hover::after{content:attr(data-tip);position:absolute;bottom:calc(100% + 4px);left:50%;transform:translateX(-50%);background:var(--surface2);color:var(--txt);padding:3px 7px;border-radius:5px;font-size:10px;white-space:nowrap;z-index:10;border:1px solid var(--border)}
.src-row{display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid var(--border)}
.src-row:last-child{border-bottom:none}
.src-bar-wrap{flex:1;height:7px;background:rgba(255,255,255,.07);border-radius:4px;overflow:hidden}
.src-bar-fill{height:100%;border-radius:4px}
.src-name{width:90px;font-size:12px;font-weight:600;flex-shrink:0;color:var(--txt)}
.src-ct{width:44px;font-size:12px;color:var(--mut);text-align:right;flex-shrink:0}

/* ── BOTTOM NAV (mobile) ───────────────────────────────────── */
.bottom-nav{display:none!important}
.bottom-nav-inner{display:flex;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none}
.bottom-nav-inner::-webkit-scrollbar{display:none}
.bottom-nav a{display:flex;flex-direction:column;align-items:center;gap:3px;padding:8px 12px;text-decoration:none;color:var(--mut);font-size:10px;font-weight:600;white-space:nowrap;min-width:56px;transition:all .18s;border-top:2px solid transparent}
.bottom-nav a.on{color:var(--accent);border-top-color:var(--accent)}
.bottom-nav a .ic{font-size:18px}

/* ── MOBILE SIDEBAR OVERLAY ────────────────────────────────── */
.sb-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:250;backdrop-filter:blur(2px)}
.sb-overlay.open{display:block}
.sidebar.mobile-open{display:flex!important;position:fixed;top:0;left:0;width:260px;height:100vh;z-index:260;box-shadow:4px 0 24px rgba(0,0,0,.5)}

/* ── RESPONSIVE ────────────────────────────────────────────── */
@media(max-width:768px){
  body{padding-bottom:0}
  .g-header{padding:8px 14px;top:0}
  .hamburger{display:flex;align-items:center;justify-content:center}
  .wrap{display:block;min-height:0}
  .sidebar{display:none}
  .bottom-nav{display:none!important}
  .main{padding:14px 14px 20px}
  .topbar{flex-direction:column;align-items:flex-start;gap:10px;margin-bottom:16px}
  .topbar h1{font-size:20px}
  .topbar-actions{width:100%;justify-content:flex-start}
  .btn-hdr .btn-text{display:none}
  .btn-hdr{padding:8px 12px;font-size:11px}
  .form-row{grid-template-columns:1fr!important}
  .fr2{grid-template-columns:1fr!important}
  .an-grid{grid-template-columns:repeat(2,1fr)!important}
  .sec-grid{grid-template-columns:repeat(3,1fr)}
  .pgrid{grid-template-columns:1fr!important}
  .mo{padding:0;align-items:flex-end}
  .mbox{max-width:100%!important;border-radius:18px 18px 0 0;max-height:94vh}
  .mhead{border-radius:18px 18px 0 0}
  .mfoot{border-radius:0}
  .toast{bottom:78px;right:12px;left:12px;max-width:100%}
}
@media(max-width:480px){
  .sec-grid{grid-template-columns:repeat(2,1fr)}
  .igrid{grid-template-columns:repeat(2,1fr)!important}
  .g-header-actions{gap:6px}
}
@media(min-width:1400px){
  .main{padding:32px 48px}
  .pgrid{grid-template-columns:repeat(auto-fill,minmax(280px,1fr))}
  .an-grid{grid-template-columns:repeat(4,1fr)}
}
@media(min-width:1800px){
  .sidebar{width:260px}
  .main{padding:36px 60px}
  .pgrid{grid-template-columns:repeat(auto-fill,minmax(300px,1fr))}
}

/* Stats grid responsive */
@media(max-width:600px){
  .stats-row{grid-template-columns:repeat(2,1fr)!important}
  .an-grid{grid-template-columns:repeat(2,1fr)!important}
  .hour-grid{grid-template-columns:repeat(12,1fr)!important}
}
/* ===== Advanced UI Upgrade Pack ===== */

/* smoother rendering */
html, body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
* { scroll-behavior: smooth; }

/* elevate design language */
:root{
  --shadowSoft: 0 10px 30px rgba(0,0,0,.35);
  --shadowHover: 0 16px 40px rgba(0,0,0,.45);
  --glass: rgba(255,255,255,.04);
  --glass2: rgba(255,255,255,.06);
  --ring: 0 0 0 4px rgba(59,130,246,.18);
}

/* Light theme (optional) */
body[data-theme="light"]{
  --bg:#f6f7fb;
  --surface:#ffffff;
  --surface2:#f0f2f8;
  --border:rgba(0,0,0,.08);
  --txt:#0b1220;
  --mut:rgba(0,0,0,.45);
  --shadow:0 10px 30px rgba(0,0,0,.08);
}

/* header polish */
.g-header{
  backdrop-filter: blur(10px);
  background: linear-gradient(180deg, var(--surface) 0%, color-mix(in srgb, var(--surface) 92%, transparent) 100%);
  box-shadow: 0 6px 22px rgba(0,0,0,.18);
}

/* buttons feel premium */
.btn-hdr, .btn-save, .btn-zip, .pbtn, .btn-edit{
  transform: translateZ(0);
}
.btn-hdr:active, .btn-save:active, .btn-zip:active, .pbtn:active, .btn-edit:active{
  transform: scale(.98);
}

/* cards: consistent elevation + hover */
.card{
  box-shadow: var(--shadowSoft);
  border: 1px solid var(--border);
}
.card:hover{
  box-shadow: var(--shadowHover);
}

/* sidebar collapse */
.sidebar{
  transition: width .18s ease, transform .18s ease;
}
body.sb-collapsed .sidebar{ width: 76px; }
body.sb-collapsed .sidebar .sb-logo .nm,
body.sb-collapsed .sidebar .sb-logo .sm,
body.sb-collapsed .sidebar .sb-section-lbl,
body.sb-collapsed .sidebar .sb-nav a span:not(.ic){
  display:none !important;
}
body.sb-collapsed .sidebar .sb-nav a{
  justify-content:center;
  padding:12px 10px;
}
body.sb-collapsed .sidebar .sb-nav a .ic{
  margin:0;
  font-size:18px;
}

/* main padding adapts */
.main{
  transition: padding .18s ease;
}
@media(min-width:980px){
  body.sb-collapsed .main{ padding-left: 22px; padding-right: 26px; }
}

/* inputs: clean focus */
input, select, textarea{
  transition: border .15s ease, box-shadow .15s ease, background .15s ease;
}
input:focus, select:focus, textarea:focus{
  box-shadow: var(--ring);
}

/* modals: glass + blur */
.mo{
  backdrop-filter: blur(8px);
}
.mbox{
  box-shadow: var(--shadowHover);
}
@media(max-width:640px){
  .mbox{
    border-radius: 18px 18px 0 0 !important;
    margin-top: auto !important;
  }
}

/* tables */
table{
  border-collapse: collapse;
}
th{
  position: sticky;
  top: 0;
  background: var(--surface);
  z-index: 1;
}

/* tiny animations */
@keyframes popIn{ from{transform:translateY(8px);opacity:0} to{transform:translateY(0);opacity:1} }
.card, .pcard, .mbox{ animation: popIn .18s ease both; }
</style>
</head>
<body>
<!-- GLOBAL TOP HEADER -->
<header class="g-header">
  <div style="display:flex;align-items:center;gap:12px">
    <button class="hamburger" id="sideToggle" onclick="toggleSidebar()" aria-label="Menu">☰</button>
    <div class="g-header-logo">
      <div class="logo-ic">🛒</div>
      <div><div class="nm">ShopAdmin</div><div class="sm">Website Manager</div></div>
    </div>
  </div>
  <div class="g-header-actions"></div>
</header>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<div class="wrap">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sb-logo">
    <div class="logo-ic">🛒</div>
    <div><div class="nm">ShopAdmin</div><div class="sm">Website Manager</div></div>
    <button onclick="closeSidebar()" style="display:none;margin-left:auto;background:rgba(255,255,255,.07);border:none;color:var(--mut);width:28px;height:28px;border-radius:7px;cursor:pointer;font-size:14px;align-items:center;justify-content:center" id="sb-close-btn">✕</button>
  </div>
  <div class="sb-section-lbl">Navigation</div>
  <nav class="sb-nav">
    <a href="?tab=images" class="<?=$tab==='images'?'on':''?>"><span class="ic">🖼️</span>Images</a>
    <a href="?tab=products" class="<?=$tab==='products'?'on':''?>"><span class="ic">📦</span>Products</a>
    <a href="?tab=shoes" class="<?=$tab==='shoes'?'on':''?>"><span class="ic">👟</span>Shoes</a>
    <a href="?tab=mens" class="<?=$tab==='mens'?'on':''?>"><span class="ic">👔</span>Men's</a>
    <a href="?tab=electronics" class="<?=$tab==='electronics'?'on':''?>"><span class="ic">📺</span>Electronics</a>
    <a href="?tab=gadgets" class="<?=$tab==='gadgets'?'on':''?>"><span class="ic">🎮</span>Gadgets</a>
    <a href="?tab=payment" class="<?=$tab==='payment'?'on':''?>"><span class="ic">💳</span>Payment</a>
    <a href="?tab=bot" class="<?=$tab==='bot'?'on':''?>"><span class="ic">🤖</span>Bot</a>
  </nav>
  <div class="sb-foot">
    <button class="sb-action-btn" type="button" onclick="openImportImagesMo()"><span class="ic">🗜️</span><span>Bulk Import ZIP</span></button>
    <button class="sb-action-btn" type="button" onclick="openCsvMo()"><span class="ic">📊</span><span>Import CSV</span></button>
    <button class="sb-action-btn" type="button" onclick="toggleTheme()"><span class="ic">🌓</span><span>Toggle Theme</span></button>
    <a class="sb-action-btn logout" href="?admin_logout=1"><span class="ic">🚪</span><span>Logout</span></a>
  </div>
</aside>

<!-- MAIN -->
<div class="main">

<?php if ($tab === 'images'): ?>
<!-- ══ IMAGES ══════════════════════════════════════════════ -->
<div class="topbar">
  <div>
    <h1>🖼️ Images</h1>
    <div class="sub">Upload images for every section of your website</div>
  </div>
  <div class="topbar-actions">
    <button class="btn-zip" onclick="openImportImagesMo()" style="background:var(--accent);color:#fff;border-color:var(--accent)">🗜️ Bulk Import ZIP</button>
    <button class="btn-zip" onclick="openZip('<?=$imgSection?>')">📦 Section ZIP</button>
    <button class="btn-zip" onclick="openCsvMo()" style="background:rgba(245,158,11,.1);color:var(--amber);border-color:rgba(245,158,11,.25)">📊 Import CSV</button>
  </div>
</div>

<?php $homeBannerKeys = ['banners','sponsored_banner','supercoin','adbanner']; ?>
<div class="card" style="margin-bottom:20px">
  <div style="font-size:10px;font-weight:700;color:var(--mut);margin-bottom:12px;text-transform:uppercase;letter-spacing:1px">Homepage Banner Shortcuts</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px">
    <?php foreach ($homeBannerKeys as $hbKey): $hb = $SECTIONS[$hbKey]; ?>
    <a href="?tab=images&section=<?=$hbKey?>" class="sec-btn <?=$imgSection===$hbKey?'on':''?>" style="min-height:92px;justify-content:flex-start">
      <span class="sic"><?=$hb['icon']?></span>
      <span class="slb"><?=htmlspecialchars($hb['label'])?></span>
      <span class="sct"><?=htmlspecialchars($hb['note'])?></span>
    </a>
    <?php endforeach ?>
  </div>
</div>

<!-- Section picker -->
<div class="card" style="margin-bottom:20px">
  <div style="font-size:10px;font-weight:700;color:var(--mut);margin-bottom:12px;text-transform:uppercase;letter-spacing:1px">Choose Section</div>
  <div class="sec-grid">
    <?php foreach ($SECTIONS as $k => $s): ?>
    <a href="?tab=images&section=<?=$k?>" class="sec-btn <?=$imgSection===$k?'on':''?>">
      <span class="sic"><?=$s['icon']?></span>
      <span class="slb"><?=htmlspecialchars($s['label'])?></span>
      <span class="sct"><?=$s['count']?> slot<?=$s['count']>1?'s':''?></span>
    </a>
    <?php endforeach ?>
  </div>
</div>

<!-- Image grid for selected section -->
<?php
$cfg  = $SECTIONS[$imgSection];
$type = $cfg['type'];
$count = $cfg['count'];
$names = $cfg['names'] ?? null;
$cols = $count === 1 ? 2 : ($count <= 3 ? 3 : ($count <= 4 ? 4 : ($count <= 6 ? 3 : 5)));
// For toppicks, show paginated
if ($type === 'toppicks') {
    $per = 20; $pages = (int)ceil($count/$per);
    $start = ($page-1)*$per+1; $end = min($page*$per,$count);
}
?>
<div class="card">
  <div class="sh">
    <div>
      <h2><?=$cfg['icon']?> <?=htmlspecialchars($cfg['label'])?></h2>
      <p><?=htmlspecialchars($cfg['note'])?></p>
    </div>
    <span class="badge badge-g"><?=htmlspecialchars($cfg['folder'])?>/</span>
  </div>

  <div class="igrid g<?=$cols?>">
  <?php
  $loopFrom = ($type === 'toppicks') ? $start : 1;
  $loopTo   = ($type === 'toppicks') ? $end   : ($type === 'single' ? 1 : $count);
  for ($i = $loopFrom; $i <= $loopTo; $i++):
      $imgUrl = getCurrentImg($cfg, $i, 1);
      $label  = $names[$i-1] ?? ($type === 'single' ? 'Main Banner' : ($type === 'toppicks' ? 'p'.$i : "Slot $i"));
      if ($type === 'direct')        $path = "{$cfg['folder']}/$i.avif";
      elseif ($type === 'single')    $path = "{$cfg['folder']}/".($cfg['filename']??'banner.avif');
      elseif ($type === 'toppicks')  $path = "{$cfg['folder']}/$i/1.avif";
      else                           $path = "{$cfg['folder']}/$i/1.avif";
      $imgCount = 0;
      if ($type === 'toppicks') {
          for ($n=1;$n<=10;$n++) { if (getCurrentImg($cfg,$i,$n)!=='') $imgCount++; }
      }
  ?>
    <div class="icard">
      <div class="ithumb <?=$type==='toppicks'?'':'wide'?>">
        <?php if($imgUrl):?><img id="img-<?=$imgSection?>-<?=$i?>" src="<?=htmlspecialchars($imgUrl)?>" alt="<?=htmlspecialchars($label)?>"><?php else:?><span class="ph" id="ph-<?=$imgSection?>-<?=$i?>"><?=$cfg['icon']?></span><?php endif?>
      </div>
      <div class="ifoot">
        <div class="istatus <?=$imgUrl?'has':'nil'?>" id="st-<?=$imgSection?>-<?=$i?>"><?=$imgUrl?($type==='toppicks'?'✓ '.$imgCount.' img'.($imgCount!=1?'s':''):'✓ Uploaded'):'✗ Empty'?></div>
        <div class="ilabel"><?=htmlspecialchars($label)?></div>
        <div class="ipath"><?=htmlspecialchars($path)?></div>
        <?php if ($type==='toppicks'): ?>
        <button class="upbtn" style="background:#1558d6" onclick="openProdImgs('<?=$imgSection?>',<?=$i?>,'<?=addslashes($label)?>')">
          📸 Images (<?=$imgCount?>)
        </button>
        <?php else: ?>
        <button class="upbtn" onclick="openUp('<?=$imgSection?>',<?=$i?>,1,'<?=addslashes($label)?>','<?=addslashes($imgUrl)?>')">
          <?=$imgUrl?'🔄 Replace':'📤 Upload'?>
        </button>
        <?php endif ?>
      </div>
    </div>
  <?php endfor ?>
  </div>

  <?php if ($type === 'toppicks'): ?>
  <div class="pag">
    <?php if($page>1):?><button class="pbtn" onclick="go(<?=$page-1?>)">← Prev</button><?php endif?>
    <?php for($p=1;$p<=$pages;$p++):?><button class="pbtn <?=$p===$page?'cur':''?>" onclick="go(<?=$p?>)"><?=$p?></button><?php endfor?>
    <?php if($page<$pages):?><button class="pbtn" onclick="go(<?=$page+1?>)">Next →</button><?php endif?>
    <span class="pinfo">Slot <?=$start?> – <?=$end?> of <?=$count?></span>
  </div>
  <?php endif ?>
</div>

<?php elseif ($tab === 'products'): ?>
<!-- ══ PRODUCTS ════════════════════════════════════════════ -->
<?php
$allProds = []; $jsonF = BASE_DIR.'/assets/products.json';
if (file_exists($jsonF)) { $tmp2 = json_decode(file_get_contents($jsonF),true); if(is_array($tmp2)) $allProds = $tmp2; }
$totalProds = count($allProds);
$withImgs   = 0; $withData = 0; $missingImgs = 0; $cfg2 = $SECTIONS['toppicks'];
foreach ($allProds as $ap) {
  $pn = preg_replace('/[^0-9]/','',$ap['id']??'');
  if (!$pn) continue;
  $hasImg  = getCurrentImg($cfg2,(int)$pn,1) !== '';
  $hasData = !empty($ap['name']) && !empty($ap['price']);
  if ($hasImg)  $withImgs++;
  if ($hasData) $withData++;
  if (!$hasImg) $missingImgs++;
}
?>
<div class="topbar">
  <div>
    <h1>📦 Products</h1>
    <div class="sub">Edit name, price, MRP, description and manage images</div>
  </div>
  <div class="topbar-actions">
    <button onclick="openAddProduct()" style="padding:10px 18px;background:var(--accent);color:#fff;border:none;border-radius:10px;font:700 13px 'Outfit',sans-serif;cursor:pointer;display:flex;align-items:center;gap:7px">
      ➕ Add Product
    </button>
    <button class="btn-zip" onclick="openCsvMo()" style="background:rgba(245,158,11,.1);color:var(--amber);border-color:rgba(245,158,11,.25)">
      📊 CSV Import
    </button>
    <button class="btn-zip" onclick="openImportImagesMo()">
      🗜️ Bulk ZIP
    </button>
  </div>
</div>

<!-- Stats Row -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px" class="stats-row">
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--accent)"><?=$totalProds?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:5px;font-weight:500;text-transform:uppercase;letter-spacing:.5px">Total Products</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--green)"><?=$withData?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:5px;font-weight:500;text-transform:uppercase;letter-spacing:.5px">Have Data</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--purple)"><?=$withImgs?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:5px;font-weight:500;text-transform:uppercase;letter-spacing:.5px">Have Images</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--red)"><?=$missingImgs?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:5px;font-weight:500;text-transform:uppercase;letter-spacing:.5px">Missing Images</div>
  </div>
</div>

<div class="srchwrap">
  <span class="si">🔍</span>
  <input type="text" id="srch" placeholder="Search by product ID, name or brand..." oninput="filterProds(this.value)">
</div>
<?php
$cfg  = $SECTIONS['toppicks'];
$per  = 20; $total = $cfg['count'];
$pages = (int)ceil($total/$per);
$start = ($page-1)*$per+1; $end = min($page*$per,$total);
?>
<div class="pgrid" id="pgrid">
<?php for ($i = $start; $i <= $end; $i++):
    $pid  = 'p'.$i;
    $data = getProductData($pid);
    $img1 = getCurrentImg($cfg, $i, 1);
    $off  = ($data['mrp']>0 && $data['price']>0) ? (int)round((1-(float)$data['price']/(float)$data['mrp'])*100) : 0;
    $dj   = htmlspecialchars(json_encode($data, JSON_HEX_APOS|JSON_HEX_QUOT));
    $imgCount = 0; for ($n=1;$n<=10;$n++) { if (getCurrentImg($cfg,$i,$n)!=='') $imgCount++; }
?>
  <div class="pcard" data-id="<?=$pid?>" data-nm="<?=htmlspecialchars(strtolower($data['name']))?>" data-br="<?=htmlspecialchars(strtolower($data['brand']))?>">
    <div class="pcard-top">
      <?php if($img1):?><img src="<?=htmlspecialchars($img1)?>" class="pthumb" alt="<?=$pid?>"><?php else:?><div class="pph">📦</div><?php endif?>
      <div class="pmeta">
        <div class="pid"><?=strtoupper($pid)?></div>
        <div class="pname"><?=$data['name']?:'-'?></div>
        <div class="pbrand"><?=$data['brand']?:'-'?></div>
        <div style="margin-top:4px">
          <?php if($data['price']):?><span class="pprice">₹<?=number_format((int)$data['price'])?></span><?php endif?>
          <?php if($data['mrp'] && $data['mrp']>$data['price']):?><span class="pmrp">₹<?=number_format((int)$data['mrp'])?></span><span class="poff"><?=$off?>% off</span><?php endif?>
        </div>
      </div>
    </div>
    <div class="btn-row">
      <button class="btn-edit" onclick='openEdit("<?=$pid?>",<?=json_encode($data, JSON_HEX_QUOT|JSON_HEX_APOS)?>)'>✏️ Edit</button>
      <button class="btn-edit" style="background:rgba(59,130,246,.08);color:var(--accent);border-color:rgba(59,130,246,.2)" onclick="openProdImgs('toppicks',<?=$i?>,'p<?=$i?>')">📸 Images<?php if($imgCount): ?> <span style="font-size:10px;background:rgba(59,130,246,.2);padding:1px 5px;border-radius:8px;margin-left:2px"><?=$imgCount?></span><?php endif ?></button>
      <button class="btn-edit" style="background:rgba(16,185,129,.08);color:var(--green);border-color:rgba(16,185,129,.2)" onclick="openZip('toppicks',<?=$i?>)">📦 ZIP</button>
      <button class="btn-edit" style="background:rgba(239,68,68,.08);color:var(--red);border-color:rgba(239,68,68,.2)" onclick="confirmDelete('<?=$pid?>')">🗑️ Delete</button>
    </div>
  </div>
<?php endfor ?>
</div>
<div class="pag">
  <?php if($page>1):?><button class="pbtn" onclick="goProd(<?=$page-1?>)">← Prev</button><?php endif?>
  <?php for($p=1;$p<=$pages;$p++):?><button class="pbtn <?=$p===$page?'cur':''?>" onclick="goProd(<?=$p?>)"><?=$p?></button><?php endfor?>
  <?php if($page<$pages):?><button class="pbtn" onclick="goProd(<?=$page+1?>)">Next →</button><?php endif?>
  <span class="pinfo">p<?=$start?> – p<?=$end?> of <?=$total?></span>
</div>


<?php elseif ($tab === 'shoes'): ?>
<!-- ══ SHOES PRODUCTS ══════════════════════════════════════════ -->
<?php
$allProdsS = []; $jsonFS = BASE_DIR.'/assets/products.json';
if (file_exists($jsonFS)) { $tmpS = json_decode(file_get_contents($jsonFS),true); if(is_array($tmpS)) $allProdsS = $tmpS; }
$shoesProds = array_values(array_filter($allProdsS, function($p){ return ($p['category']??'') === 'Footwear'; }));
$totalShoes = count($shoesProds);
$shoesWithData = count(array_filter($shoesProds, function($p){ return !empty($p['name']) && !empty($p['price']); }));
?>
<div class="topbar" style="margin-bottom:18px">
  <div>
    <h1>👟 Shoes Products</h1>
    <div class="sub">Manage all footwear products — edit price, name, description</div>
  </div>
</div>

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--blue)"><?=$totalShoes?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Total Shoes</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--green)"><?=$shoesWithData?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Have Data</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:#7c3aed"><?=count(array_unique(array_column($shoesProds,'brand')))?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Brands</div>
  </div>
</div>

<div class="card">
  <div class="srchwrap" style="margin-bottom:14px">
    <span class="si">🔍</span>
    <input type="text" id="srchShoes" placeholder="Search by ID, name or brand..." oninput="filterCatProds('pgridShoes',this.value)">
  </div>
  <div class="pgrid" id="pgridShoes">
  <?php foreach($shoesProds as $sp):
    $spData = ['name'=>$sp['name']??'','brand'=>$sp['brand']??'','price'=>$sp['price']??0,'mrp'=>$sp['mrp']??0,'desc'=>$sp['description']??'','variants'=>$sp['variants']??[]];
    $spImg   = $sp['img1'] ?? ($sp['images'][0] ?? '');
    $spOff   = ($spData['mrp']>0&&$spData['price']>0)?(int)round((1-(float)$spData['price']/(float)$spData['mrp'])*100):0;
    $spId    = $sp['id']??'';
  ?>
    <div class="pcard" data-id-c="<?=htmlspecialchars(strtolower($spId))?>" data-nm-c="<?=htmlspecialchars(strtolower($spData['name']))?>" data-br-c="<?=htmlspecialchars(strtolower($spData['brand']))?>">
      <div class="pcard-top">
        <?php if($spImg):?><img src="<?=htmlspecialchars($spImg)?>" class="pthumb" alt="" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"><?php endif?>
        <div class="pph" style="<?=$spImg?'display:none':'display:flex'?>">👟</div>
        <div class="pmeta">
          <div class="pid"><?=strtoupper($spId)?></div>
          <div class="pname"><?=htmlspecialchars($spData['name'])?:'-'?></div>
          <div class="pbrand"><?=htmlspecialchars($spData['brand'])?:'-'?></div>
          <div style="margin-top:4px">
            <?php if($spData['price']):?><span class="pprice">₹<?=number_format((int)$spData['price'])?></span><?php endif?>
            <?php if($spData['mrp']&&$spData['mrp']>$spData['price']):?><span class="pmrp">₹<?=number_format((int)$spData['mrp'])?></span><span class="poff"><?=$spOff?>% off</span><?php endif?>
          </div>
          <div style="margin-top:3px"><span style="font-size:10px;background:#fff3e0;color:#e65100;padding:2px 7px;border-radius:8px;font-weight:600"><?=htmlspecialchars($sp['subcategory']??'Footwear')?></span></div>
        </div>
      </div>
      <button class="btn-edit" style="width:100%" onclick='openEdit("<?=$spId?>",<?=json_encode($spData,JSON_HEX_QUOT|JSON_HEX_APOS)?>)'>✏️ Edit Product</button>
    </div>
  <?php endforeach ?>
  </div>
</div>


<?php elseif ($tab === 'mens'): ?>
<!-- ══ MENS CLOTHING PRODUCTS ════════════════════════════════ -->
<?php
$allProdsM2 = []; $jsonFM2 = BASE_DIR.'/assets/products.json';
if (file_exists($jsonFM2)) { $tmpM2 = json_decode(file_get_contents($jsonFM2),true); if(is_array($tmpM2)) $allProdsM2 = $tmpM2; }
$mensProds = array_values(array_filter($allProdsM2, function($p){ return ($p['category']??'') === 'Mens Clothing'; }));
$totalMens = count($mensProds);
$mensWithData = count(array_filter($mensProds, function($p){ return !empty($p['name']) && !empty($p['price']); }));
$mensSubcats = array_unique(array_column($mensProds,'subcategory'));
?>
<div class="topbar" style="margin-bottom:18px">
  <div>
    <h1>👔 Men's Clothing</h1>
    <div class="sub">Manage all men's fashion products — edit price, name, description</div>
  </div>
</div>

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--blue)"><?=$totalMens?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Total Items</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--green)"><?=$mensWithData?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Have Data</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:#7c3aed"><?=count($mensSubcats)?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Subcategories</div>
  </div>
</div>

<!-- Subcategory filter pills -->
<div class="pill-wrap">
  <button class="mens-pill on" data-sub="all" onclick="filterMensSub(this)">All</button>
  <?php foreach($mensSubcats as $msc): ?>
  <button class="mens-pill" data-sub="<?=htmlspecialchars($msc)?>" onclick="filterMensSub(this)"><?=htmlspecialchars($msc)?></button>
  <?php endforeach ?>
</div>
<style>
.mens-pill{padding:7px 14px;border-radius:20px;border:1.5px solid var(--border);background:var(--surface2);font-size:12px;font-weight:600;color:var(--mut);cursor:pointer;transition:all .18s;white-space:nowrap}
.mens-pill:hover{border-color:var(--accent);color:var(--accent)}
.mens-pill.on{background:rgba(59,130,246,.12);border-color:var(--accent);color:var(--accent)}
/* Category pills container */
.pill-wrap{display:flex;gap:8px;flex-wrap:nowrap;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding-bottom:4px;margin-bottom:16px}
.pill-wrap::-webkit-scrollbar{display:none}
</style>

<div class="card">
  <div class="srchwrap" style="margin-bottom:14px">
    <span class="si">🔍</span>
    <input type="text" id="srchMens" placeholder="Search by ID, name or brand..." oninput="filterCatProds('pgridMens',this.value)">
  </div>
  <div class="pgrid" id="pgridMens">
  <?php foreach($mensProds as $mp):
    $mpData = ['name'=>$mp['name']??'','brand'=>$mp['brand']??'','price'=>$mp['price']??0,'mrp'=>$mp['mrp']??0,'desc'=>$mp['description']??'','variants'=>$mp['variants']??[]];
    $mpImg   = $mp['img1'] ?? ($mp['images'][0] ?? '');
    $mpOff   = ($mpData['mrp']>0&&$mpData['price']>0)?(int)round((1-(float)$mpData['price']/(float)$mpData['mrp'])*100):0;
    $mpId    = $mp['id']??'';
    $mpSub   = $mp['subcategory']??'Clothing';
  ?>
    <div class="pcard" data-id-c="<?=htmlspecialchars(strtolower($mpId))?>" data-nm-c="<?=htmlspecialchars(strtolower($mpData['name']))?>" data-br-c="<?=htmlspecialchars(strtolower($mpData['brand']))?>" data-sub-c="<?=htmlspecialchars(strtolower($mpSub))?>">
      <div class="pcard-top">
        <?php if($mpImg):?><img src="<?=htmlspecialchars($mpImg)?>" class="pthumb" alt="" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"><?php endif?>
        <div class="pph" style="<?=$mpImg?'display:none':'display:flex'?>">👔</div>
        <div class="pmeta">
          <div class="pid"><?=strtoupper($mpId)?></div>
          <div class="pname"><?=htmlspecialchars($mpData['name'])?:'-'?></div>
          <div class="pbrand"><?=htmlspecialchars($mpData['brand'])?:'-'?></div>
          <div style="margin-top:4px">
            <?php if($mpData['price']):?><span class="pprice">₹<?=number_format((int)$mpData['price'])?></span><?php endif?>
            <?php if($mpData['mrp']&&$mpData['mrp']>$mpData['price']):?><span class="pmrp">₹<?=number_format((int)$mpData['mrp'])?></span><span class="poff"><?=$mpOff?>% off</span><?php endif?>
          </div>
          <div style="margin-top:3px"><span style="font-size:10px;background:#e8f5e9;color:#2e7d32;padding:2px 7px;border-radius:8px;font-weight:600"><?=htmlspecialchars($mpSub)?></span></div>
        </div>
      </div>
      <button class="btn-edit" style="width:100%" onclick='openEdit("<?=$mpId?>",<?=json_encode($mpData,JSON_HEX_QUOT|JSON_HEX_APOS)?>)'>✏️ Edit Product</button>
    </div>
  <?php endforeach ?>
  </div>
</div>


<?php elseif ($tab === 'electronics'): ?>
<!-- ══ ELECTRONICS ════════════════════════════════════════════ -->
<?php
$allProdsE = []; $jsonFE = BASE_DIR.'/assets/products.json';
if (file_exists($jsonFE)) { $tmpE = json_decode(file_get_contents($jsonFE),true); if(is_array($tmpE)) $allProdsE = $tmpE; }
$elecCats = ['Electronics'];
$elecProds = array_values(array_filter($allProdsE, function($p) use($elecCats){ return in_array($p['category']??'', $elecCats); }));
$totalElec = count($elecProds);
$elecWithData = count(array_filter($elecProds, function($p){ return !empty($p['name']) && !empty($p['price']); }));
$elecSubcats = array_unique(array_column($elecProds,'subcategory'));
?>
<div class="topbar" style="margin-bottom:18px">
  <div>
    <h1>📺 Electronics</h1>
    <div class="sub">Manage all electronics products — edit price, name, description, variants</div>
  </div>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--blue)"><?=$totalElec?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Total Products</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--green)"><?=$elecWithData?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Have Data</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:#7c3aed"><?=count($elecSubcats)?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Subcategories</div>
  </div>
</div>
<!-- Subcategory pills -->
<div class="pill-wrap">
  <button class="mens-pill on" data-sub="all" onclick="filterMensSub(this)">All</button>
  <?php foreach($elecSubcats as $esc2): ?>
  <button class="mens-pill" data-sub="<?=htmlspecialchars($esc2)?>" onclick="filterMensSub(this)"><?=htmlspecialchars($esc2)?></button>
  <?php endforeach ?>
</div>
<div class="card">
  <div class="srchwrap" style="margin-bottom:14px">
    <span class="si">🔍</span>
    <input type="text" id="srchElec" placeholder="Search by ID, name or brand..." oninput="filterCatProds('pgridElec',this.value)">
  </div>
  <div class="pgrid" id="pgridElec">
  <?php foreach($elecProds as $ep):
    $epData = ['name'=>$ep['name']??'','brand'=>$ep['brand']??'','price'=>$ep['price']??0,'mrp'=>$ep['mrp']??0,'desc'=>$ep['description']??'','variants'=>$ep['variants']??[]];
    $epImg   = $ep['img1'] ?? ($ep['images'][0] ?? '');
    $epOff   = ($epData['mrp']>0&&$epData['price']>0)?(int)round((1-(float)$epData['price']/(float)$epData['mrp'])*100):0;
    $epId    = $ep['id']??'';
    $epSub   = $ep['subcategory']??'Electronics';
  ?>
    <div class="pcard" data-id-c="<?=htmlspecialchars(strtolower($epId))?>" data-nm-c="<?=htmlspecialchars(strtolower($epData['name']))?>" data-br-c="<?=htmlspecialchars(strtolower($epData['brand']))?>" data-sub-c="<?=htmlspecialchars(strtolower($epSub))?>">
      <div class="pcard-top">
        <?php if($epImg):?><img src="<?=htmlspecialchars($epImg)?>" class="pthumb" alt="" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"><?php endif?>
        <div class="pph" style="<?=$epImg?'display:none':'display:flex'?>">📺</div>
        <div class="pmeta">
          <div class="pid"><?=strtoupper($epId)?></div>
          <div class="pname"><?=htmlspecialchars($epData['name'])?:'-'?></div>
          <div class="pbrand"><?=htmlspecialchars($epData['brand'])?:'-'?></div>
          <div style="margin-top:4px">
            <?php if($epData['price']):?><span class="pprice">₹<?=number_format((int)$epData['price'])?></span><?php endif?>
            <?php if($epData['mrp']&&$epData['mrp']>$epData['price']):?><span class="pmrp">₹<?=number_format((int)$epData['mrp'])?></span><span class="poff"><?=$epOff?>% off</span><?php endif?>
          </div>
          <div style="margin-top:3px"><span style="font-size:10px;background:#e3f2fd;color:#1565c0;padding:2px 7px;border-radius:8px;font-weight:600"><?=htmlspecialchars($epSub)?></span></div>
        </div>
      </div>
      <button class="btn-edit" style="width:100%" onclick='openEdit("<?=$epId?>",<?=json_encode($epData,JSON_HEX_QUOT|JSON_HEX_APOS)?>)'>✏️ Edit Product</button>
    </div>
  <?php endforeach ?>
  </div>
</div>


<?php elseif ($tab === 'gadgets'): ?>
<!-- ══ GADGETS ════════════════════════════════════════════════ -->
<?php
$allProdsG = []; $jsonFG = BASE_DIR.'/assets/products.json';
if (file_exists($jsonFG)) { $tmpG = json_decode(file_get_contents($jsonFG),true); if(is_array($tmpG)) $allProdsG = $tmpG; }
$gadgetCats = ['Smart Gadgets','Gaming','Wearables','Smart Home','Computer Accessories','Mobile Accessories','Creator Tools','Camera'];
$gadgetProds = array_values(array_filter($allProdsG, function($p) use($gadgetCats){ return in_array($p['category']??'', $gadgetCats); }));
$totalGadgets = count($gadgetProds);
$gadgetsWithData = count(array_filter($gadgetProds, function($p){ return !empty($p['name']) && !empty($p['price']); }));
$gadgetCatsFound = array_unique(array_column($gadgetProds,'category'));
?>
<div class="topbar" style="margin-bottom:18px">
  <div>
    <h1>🎮 Gadgets & Accessories</h1>
    <div class="sub">Smart Gadgets · Gaming · Wearables · Smart Home · Accessories · Creator Tools</div>
  </div>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--blue)"><?=$totalGadgets?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Total Products</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:var(--green)"><?=$gadgetsWithData?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Have Data</div>
  </div>
  <div class="card" style="padding:18px;text-align:center;background:var(--surface);border:1px solid var(--border)">
    <div style="font-size:28px;font-weight:800;color:#7c3aed"><?=count($gadgetCatsFound)?></div>
    <div style="font-size:11px;color:var(--mut);margin-top:3px;font-weight:600">Categories</div>
  </div>
</div>
<!-- Category pills -->
<div class="pill-wrap">
  <button class="mens-pill on" data-sub="all" onclick="filterGadgetCat(this)">All</button>
  <?php foreach($gadgetCatsFound as $gc): ?>
  <button class="mens-pill" data-sub="<?=htmlspecialchars(strtolower($gc))?>" onclick="filterGadgetCat(this)"><?=htmlspecialchars($gc)?></button>
  <?php endforeach ?>
</div>
<div class="card">
  <div class="srchwrap" style="margin-bottom:14px">
    <span class="si">🔍</span>
    <input type="text" id="srchGadgets" placeholder="Search by ID, name or brand..." oninput="filterCatProds('pgridGadgets',this.value)">
  </div>
  <div class="pgrid" id="pgridGadgets">
  <?php foreach($gadgetProds as $gp):
    $gpData = ['name'=>$gp['name']??'','brand'=>$gp['brand']??'','price'=>$gp['price']??0,'mrp'=>$gp['mrp']??0,'desc'=>$gp['description']??'','variants'=>$gp['variants']??[]];
    $gpImg   = $gp['img1'] ?? ($gp['images'][0] ?? '');
    $gpOff   = ($gpData['mrp']>0&&$gpData['price']>0)?(int)round((1-(float)$gpData['price']/(float)$gpData['mrp'])*100):0;
    $gpId    = $gp['id']??'';
    $gpCat   = $gp['category']??'Gadgets';
    $gpSub   = $gp['subcategory']??'';
  ?>
    <div class="pcard" data-id-c="<?=htmlspecialchars(strtolower($gpId))?>" data-nm-c="<?=htmlspecialchars(strtolower($gpData['name']))?>" data-br-c="<?=htmlspecialchars(strtolower($gpData['brand']))?>" data-sub-c="<?=htmlspecialchars(strtolower($gpCat))?>">
      <div class="pcard-top">
        <?php if($gpImg):?><img src="<?=htmlspecialchars($gpImg)?>" class="pthumb" alt="" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"><?php endif?>
        <div class="pph" style="<?=$gpImg?'display:none':'display:flex'?>">🎮</div>
        <div class="pmeta">
          <div class="pid"><?=strtoupper($gpId)?></div>
          <div class="pname"><?=htmlspecialchars($gpData['name'])?:'-'?></div>
          <div class="pbrand"><?=htmlspecialchars($gpData['brand'])?:'-'?></div>
          <div style="margin-top:4px">
            <?php if($gpData['price']):?><span class="pprice">₹<?=number_format((int)$gpData['price'])?></span><?php endif?>
            <?php if($gpData['mrp']&&$gpData['mrp']>$gpData['price']):?><span class="pmrp">₹<?=number_format((int)$gpData['mrp'])?></span><span class="poff"><?=$gpOff?>% off</span><?php endif?>
          </div>
          <div style="margin-top:3px;display:flex;gap:4px;flex-wrap:wrap">
            <span style="font-size:10px;background:#f3e5f5;color:#4a148c;padding:2px 7px;border-radius:8px;font-weight:600"><?=htmlspecialchars($gpCat)?></span>
            <?php if($gpSub):?><span style="font-size:10px;background:#f5f5f5;color:#555;padding:2px 7px;border-radius:8px"><?=htmlspecialchars($gpSub)?></span><?php endif?>
          </div>
        </div>
      </div>
      <button class="btn-edit" style="width:100%" onclick='openEdit("<?=$gpId?>",<?=json_encode($gpData,JSON_HEX_QUOT|JSON_HEX_APOS)?>)'>✏️ Edit Product</button>
    </div>
  <?php endforeach ?>
  </div>
</div>


<?php elseif ($tab === 'analytics'): ?>
<!-- ══ ANALYTICS ════════════════════════════════════════════ -->
<?php
$an = ['days'=>[],'pages'=>[],'devices'=>[],'browsers'=>[],'sources'=>[],'recent'=>[],'total'=>0];
if (defined('ANALYTICS_FILE') && file_exists(ANALYTICS_FILE)) {
    $raw2 = @file_get_contents(ANALYTICS_FILE);
    if ($raw2) $an = array_merge($an, json_decode($raw2, true) ?? []);
}
// Sort days
ksort($an['days']);
$dayKeys = array_keys($an['days']);

// --- Compute summary stats ---
$today2   = date('Y-m-d');
$todayViews   = $an['days'][$today2]['views']  ?? 0;
$todayUnique  = count($an['days'][$today2]['unique'] ?? []);

// This week (Mon–today)
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekViews = 0; $weekUnique = [];
foreach ($an['days'] as $dk => $dv) {
    if ($dk >= $weekStart) {
        $weekViews += $dv['views'] ?? 0;
        foreach ($dv['unique'] ?? [] as $u) $weekUnique[$u] = 1;
    }
}

// This month
$monthStart = date('Y-m-01');
$monthViews = 0; $monthUnique = [];
foreach ($an['days'] as $dk => $dv) {
    if ($dk >= $monthStart) {
        $monthViews += $dv['views'] ?? 0;
        foreach ($dv['unique'] ?? [] as $u) $monthUnique[$u] = 1;
    }
}

$totalViews  = $an['total'] ?? 0;

// Last 30 days for chart
$last30 = []; $d30 = new DateTime(); $d30->modify('-29 days');
for ($di=0; $di<30; $di++) {
    $dk = $d30->format('Y-m-d');
    $last30[] = ['date'=>$dk,'label'=>$d30->format('d M'),'views'=>$an['days'][$dk]['views']??0,'unique'=>count($an['days'][$dk]['unique']??[])];
    $d30->modify('+1 day');
}
$maxBar = max(1, ...array_column($last30,'views'));

// Top pages
arsort($an['pages']);
$topPages = array_slice($an['pages'], 0, 10, true);

// Devices total
$totalDev = array_sum($an['devices']) ?: 1;

// Sources
arsort($an['sources']);
$totalSrc = array_sum($an['sources']) ?: 1;

// Today's hourly
$todayHours = $an['days'][$today2]['hours'] ?? array_fill(0,24,0);
$maxHour = max(1, ...array_values($todayHours));

// Recent visits (last 50)
$recentHits = array_slice($an['recent'] ?? [], 0, 50);
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
  <div>
    <h1 style="font-size:22px;font-weight:800">📊 Analytics</h1>
    <div style="font-size:13px;color:var(--mut);margin-top:2px">Website visitor statistics — real-time, no third-party</div>
  </div>
  <button onclick="clearAnalytics()" style="padding:8px 16px;background:var(--surface);border:1.5px solid var(--red);color:var(--red);border-radius:8px;font:600 12px 'Outfit',sans-serif;cursor:pointer">🗑️ Reset Data</button>
</div>

<!-- ─── 4 KPI Cards ──────────────────────────────────────────── -->
<div class="an-grid">
  <div class="an-card">
    <div class="an-num" style="color:var(--blue)"><?=number_format($todayViews)?></div>
    <div class="an-lbl">Views Today</div>
    <div class="an-sub"><?=number_format($todayUnique)?> unique visitors</div>
  </div>
  <div class="an-card">
    <div class="an-num" style="color:#7c3aed"><?=number_format($weekViews)?></div>
    <div class="an-lbl">This Week</div>
    <div class="an-sub"><?=number_format(count($weekUnique))?> unique visitors</div>
  </div>
  <div class="an-card">
    <div class="an-num" style="color:var(--green)"><?=number_format($monthViews)?></div>
    <div class="an-lbl">This Month</div>
    <div class="an-sub"><?=number_format(count($monthUnique))?> unique visitors</div>
  </div>
  <div class="an-card">
    <div class="an-num" style="color:#e67e22"><?=number_format($totalViews)?></div>
    <div class="an-lbl">All Time</div>
    <div class="an-sub"><?=count($an['days'])?> days recorded</div>
  </div>
</div>

<!-- ─── 30-Day Chart ─────────────────────────────────────────── -->
<div class="card" style="margin-bottom:18px">
  <div class="chart-title">📈 Last 30 Days — Page Views</div>
  <div class="chart-wrap" id="chart30">
    <?php foreach ($last30 as $row): ?>
    <div class="bar-col">
      <div class="bar-fill"
           style="height:<?=round($row['views']/$maxBar*160)?>px;background:<?=$row['date']===$today2?'var(--blue)':'#93bbfc'?>"
           data-tip="<?=htmlspecialchars($row['label'])?>: <?=$row['views']?> views, <?=$row['unique']?> unique">
      </div>
      <div class="bar-lbl"><?=substr($row['label'],0,5)?></div>
    </div>
    <?php endforeach ?>
  </div>
  <div style="display:flex;align-items:center;gap:14px;margin-top:12px;font-size:11px;color:var(--mut)">
    <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:12px;background:var(--blue);border-radius:2px;display:inline-block"></span>Today</span>
    <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:12px;background:#93bbfc;border-radius:2px;display:inline-block"></span>Past days</span>
  </div>
</div>

<!-- ─── Today's Hourly Heatmap ───────────────────────────────── -->
<div class="card" style="margin-bottom:18px">
  <div class="chart-title">🕐 Today's Traffic by Hour</div>
  <div style="display:flex;align-items:center;gap:4px;font-size:9px;color:var(--mut);margin-bottom:4px">
    <?php for($h=0;$h<24;$h++): ?><div style="flex:1;text-align:center"><?=$h?></div><?php endfor ?>
  </div>
  <div class="hour-grid">
    <?php foreach ($todayHours as $h => $hv):
      $alpha = $hv > 0 ? max(0.12, min(1.0, $hv/$maxHour)) : 0;
      $bg = $hv > 0 ? 'rgba(40,116,240,'.round($alpha,2).')' : '#f5f6fa';
    ?>
    <div class="hour-cell" style="background:<?=$bg?>" data-tip="<?=$h?>:00 — <?=$hv?> views"></div>
    <?php endforeach ?>
  </div>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;font-size:10px;color:var(--mut)">
    <span>Light = few visits</span><span>Dark blue = peak traffic</span>
  </div>
</div>

<!-- ─── Row: Devices + Sources ─────────────────────────────────── -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">

  <!-- Devices -->
  <div class="card">
    <div class="chart-title">📱 Device Breakdown</div>
    <?php
    $devColors = ['mobile'=>'#2874f0','desktop'=>'#7c3aed','tablet'=>'#e67e22'];
    $devIcons  = ['mobile'=>'📱','desktop'=>'🖥️','tablet'=>'📟'];
    $devOrder  = ['mobile','desktop','tablet'];
    foreach ($devOrder as $dv):
      $cnt = $an['devices'][$dv] ?? 0;
      $pct = round($cnt/$totalDev*100);
    ?>
    <div style="margin-bottom:12px">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
        <span><?=($devIcons[$dv]??'📦')?> <?=ucfirst($dv)?></span>
        <span style="font-weight:700"><?=number_format($cnt)?> <span style="color:var(--mut);font-weight:400">(<?=$pct?>%)</span></span>
      </div>
      <div class="device-bar">
        <div class="device-fill" style="width:<?=$pct?>%;background:<?=($devColors[$dv]??'#ccc')?>"></div>
      </div>
    </div>
    <?php endforeach ?>
  </div>

  <!-- Traffic Sources -->
  <div class="card">
    <div class="chart-title">🌐 Traffic Sources</div>
    <?php
    $srcColors=['Direct'=>'#2874f0','Google'=>'#34a853','Facebook'=>'#1877f2','Instagram'=>'#e1306c','WhatsApp'=>'#25d366','Twitter/X'=>'#1da1f2','YouTube'=>'#ff0000','Other'=>'#888'];
    foreach ($an['sources'] as $src => $scnt):
      $spct = round($scnt/$totalSrc*100);
    ?>
    <div class="src-row">
      <div class="src-name"><?=htmlspecialchars($src)?></div>
      <div class="src-bar-wrap">
        <div class="src-bar-fill" style="width:<?=$spct?>%;background:<?=($srcColors[$src]??'#888')?>"></div>
      </div>
      <div class="src-ct"><?=number_format($scnt)?></div>
    </div>
    <?php endforeach ?>
    <?php if(empty($an['sources'])): ?><div style="font-size:12px;color:var(--mut);padding:10px 0">No data yet</div><?php endif ?>
  </div>

</div>

<!-- ─── Row: Top Pages + Browsers ──────────────────────────────── -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">

  <!-- Top Pages -->
  <div class="card">
    <div class="chart-title">📄 Top Pages</div>
    <?php if(empty($topPages)): ?><div style="font-size:12px;color:var(--mut)">No data yet</div>
    <?php else:
      $maxPg = max(1, ...array_values($topPages));
    ?>
    <table class="tbl">
      <tr><th>Page</th><th style="text-align:right">Views</th><th style="text-align:right">Share</th></tr>
      <?php foreach ($topPages as $pg => $pv): ?>
      <tr>
        <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?=htmlspecialchars($pg)?>"><?=htmlspecialchars($pg)?></td>
        <td style="text-align:right;font-weight:700"><?=number_format($pv)?></td>
        <td style="text-align:right;color:var(--mut)"><?=round($pv/$totalViews*100,1)?>%</td>
      </tr>
      <?php endforeach ?>
    </table>
    <?php endif ?>
  </div>

  <!-- Browsers -->
  <div class="card">
    <div class="chart-title">🌍 Browsers</div>
    <?php
    $bColors=['Chrome'=>'#4285f4','Firefox'=>'#ff7139','Safari'=>'#34aadc','Edge'=>'#0078d7','Opera'=>'#ff1b2d','Other'=>'#888'];
    $totalBr = array_sum($an['browsers']) ?: 1;
    arsort($an['browsers']);
    ?>
    <?php foreach ($an['browsers'] as $br => $bv):
      $bpct = round($bv/$totalBr*100);
    ?>
    <div style="margin-bottom:10px">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
        <span><?=htmlspecialchars($br)?></span>
        <span style="font-weight:700"><?=number_format($bv)?> <span style="color:var(--mut);font-weight:400">(<?=$bpct?>%)</span></span>
      </div>
      <div style="height:6px;background:#f0f0f0;border-radius:4px;overflow:hidden">
        <div style="width:<?=$bpct?>%;height:100%;background:<?=($bColors[$br]??'#888')?>;border-radius:4px"></div>
      </div>
    </div>
    <?php endforeach ?>
    <?php if(empty($an['browsers'])): ?><div style="font-size:12px;color:var(--mut)">No data yet</div><?php endif ?>
  </div>

</div>

<!-- ─── Recent Visits ─────────────────────────────────────────── -->
<div class="card">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <div class="chart-title" style="margin-bottom:0">🕵️ Recent Visits</div>
    <span style="font-size:11px;color:var(--mut)">Last <?=count($recentHits)?> hits</span>
  </div>
  <?php if(empty($recentHits)): ?>
  <div style="font-size:13px;color:var(--mut);padding:20px 0;text-align:center">No visits recorded yet — add tracker.php to your site first</div>
  <?php else: ?>
  <div style="overflow-x:auto">
  <table class="tbl">
    <tr><th>Time</th><th>Page</th><th>Device</th><th>Browser</th><th>Source</th></tr>
    <?php foreach ($recentHits as $hit): ?>
    <tr>
      <td style="white-space:nowrap;color:var(--mut)"><?=date('d M, H:i', $hit['ts']??0)?></td>
      <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=htmlspecialchars($hit['page']??'-')?></td>
      <td><?=htmlspecialchars($hit['device']??'-')?></td>
      <td><?=htmlspecialchars($hit['browser']??'-')?></td>
      <td><?=htmlspecialchars($hit['source']??'-')?></td>
    </tr>
    <?php endforeach ?>
  </table>
  </div>
  <?php endif ?>
</div>

<?php elseif ($tab === 'bot'): ?>
<!-- ══ BOT SETTINGS ═════════════════════════════════════════ -->
<div class="topbar">
  <div>
    <h1>🤖 Telegram Bot</h1>
    <div class="sub">Yahan se Token aur Chat ID change karo — turant live hoga!</div>
  </div>
</div>

<?php if(isset($botSaved) && $botSaved): ?>
<div class="result ok" style="margin-bottom:16px;padding:12px 16px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:10px;color:#10b981;font-weight:600">
  ✅ Bot settings saved! Ab address.php automatically naya token use karega.
</div>
<?php endif ?>

<div class="card" style="max-width:640px;margin-bottom:20px">
  <div style="font-size:14px;font-weight:700;margin-bottom:4px">🔧 Bot Configuration</div>
  <div style="font-size:12px;color:var(--mut);margin-bottom:18px">Save karne ke baad Telegram pe address automatically aayega — koi redeploy nahi chahiye.</div>

  <form method="post" action="?tab=bot">
    <div class="form-group">
      <label>🔑 Bot API Token</label>
      <input type="text" name="tg_token"
             placeholder="e.g. 8785030547:AAEKd8kf..."
             value="<?=htmlspecialchars($tg_config['token'] ?? '')?>"
             style="font-family:monospace;font-size:12px">
      <div style="font-size:11px;color:var(--mut);margin-top:4px">@BotFather se milta hai → /mybots → API Token</div>
    </div>
    <div class="form-group">
      <label>💬 Chat ID</label>
      <input type="text" name="tg_chat_id"
             placeholder="e.g. 6624574992 ya -1001234567890"
             value="<?=htmlspecialchars($tg_config['chat_id'] ?? '')?>"
             style="font-family:monospace;font-size:12px">
      <div style="font-size:11px;color:var(--mut);margin-top:4px">Apna Telegram User ID ya Group ID</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;margin-top:6px">
      <button class="btn-save" type="submit">💾 Save Bot Settings</button>
      <?php if(!empty($tg_config['token'])): ?>
      <button type="button" onclick="testBot()" style="padding:10px 16px;background:var(--surface2);border:1.5px solid var(--border);border-radius:8px;font:600 13px 'Outfit',sans-serif;cursor:pointer">🧪 Test Bot</button>
      <?php endif ?>
    </div>
  </form>
</div>

<div class="card" style="max-width:640px">
  <div style="font-size:13px;font-weight:700;margin-bottom:12px">📊 Current Status</div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
    <div style="background:var(--surface2);border-radius:10px;padding:12px">
      <div style="font-size:11px;color:var(--mut);margin-bottom:4px">TOKEN</div>
      <div style="font-size:12px;font-weight:700;font-family:monospace;word-break:break-all">
        <?php
          $tok = $tg_config['token'] ?? '';
          echo $tok ? '✅ ' . substr($tok,0,8) . '...' . substr($tok,-6) : '❌ Not Set';
        ?>
      </div>
    </div>
    <div style="background:var(--surface2);border-radius:10px;padding:12px">
      <div style="font-size:11px;color:var(--mut);margin-bottom:4px">CHAT ID</div>
      <div style="font-size:12px;font-weight:700;font-family:monospace">
        <?= ($tg_config['chat_id'] ?? '') ? '✅ ' . htmlspecialchars($tg_config['chat_id']) : '❌ Not Set' ?>
      </div>
    </div>
  </div>
</div>

<script>
function testBot() {
  var token   = document.querySelector('[name=tg_token]').value.trim();
  var chat_id = document.querySelector('[name=tg_chat_id]').value.trim();
  if (!token || !chat_id) { alert('Pehle Token aur Chat ID bharo!'); return; }
  fetch('https://api.telegram.org/bot' + token + '/sendMessage', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({chat_id: chat_id, text: '✅ Test message from Admin Panel!\n\nBot sahi kaam kar raha hai 🎉', parse_mode: 'HTML'})
  }).then(r => r.json()).then(r => {
    if (r.ok) alert('✅ Message gaya! Telegram check karo.');
    else alert('❌ Error: ' + (r.description || JSON.stringify(r)));
  }).catch(e => alert('❌ Network error: ' + e));
}
</script>

<?php elseif ($tab === 'payment'): ?>
<!-- ══ PAYMENT ═════════════════════════════════════════════ -->
<div class="topbar">
  <div>
    <h1>💳 Payment</h1>
    <div class="sub">UPI and COD settings — saved to payment_settings.json and read live by payment.php</div>
  </div>
</div>
<div class="card" style="max-width:640px">
  <div style="font-size:14px;font-weight:700;margin-bottom:18px">📦 COD Settings</div>
  <div style="background:#e8f0fe;border:1px solid #c5d9ff;border-radius:8px;padding:10px 13px;font-size:12px;color:#1a3a7a;margin-bottom:14px;line-height:1.7">
    <strong>Tiered COD Charges:</strong> If cart total is below the threshold, the lower charge applies. Otherwise the standard charge applies.
  </div>
  <div class="form-row" style="margin-bottom:14px">
    <div class="form-group" style="margin-bottom:0">
      <label>Cart Threshold ₹ <span class="hint">Amount that splits the tiers</span></label>
      <input type="number" id="ps_cod_threshold" placeholder="200" min="0">
    </div>
    <div class="form-group" style="margin-bottom:0">
      <label>COD Charge — Below Threshold ₹</label>
      <input type="number" id="ps_cod_low" placeholder="49" min="0">
    </div>
  </div>
  <div class="form-row">
    <div class="form-group" style="margin-bottom:0">
      <label>COD Charge — Above Threshold ₹</label>
      <input type="number" id="ps_cod" placeholder="99" min="0">
    </div>
    <div class="form-group" style="margin-bottom:0">
      <label>COD Note</label>
      <input type="text" id="ps_codn" placeholder="Flipkart COD Security">
    </div>
  </div>

  <div style="margin-top:20px;display:flex;gap:10px;align-items:center">
    <button class="btn-save" onclick="psSave()" id="psSaveBtn">💾 Save Settings</button>
    <button onclick="psLoad()" style="padding:10px 16px;background:var(--surface2);border:1.5px solid var(--border);border-radius:8px;font:500 13px 'Outfit',sans-serif;cursor:pointer">🔄 Reload</button>
  </div>
  <div class="result" id="psResult"></div>
</div>
<?php endif ?>

</div><!-- /main -->
</div><!-- /wrap -->

<script>
const CSRF_TOKEN = '<?= htmlspecialchars($_SESSION['admin_csrf'] ?? '', ENT_QUOTES) ?>';

// ── Sidebar Toggle ───────────────────────────────────────────
function toggleSidebar(){
  const sb = document.getElementById('sidebar');
  const ov = document.getElementById('sbOverlay');
  const closeBtn = document.getElementById('sb-close-btn');
  // On small screens use overlay mode, on larger screens toggle collapsed layout
  if (window.innerWidth <= 768) {
    if (sb.classList.contains('mobile-open')) {
      sb.classList.remove('mobile-open');
      ov.classList.remove('open');
      if (closeBtn) closeBtn.style.display = 'none';
    } else {
      sb.classList.add('mobile-open');
      ov.classList.add('open');
      if (closeBtn) closeBtn.style.display = 'flex';
    }
  } else {
    // toggle collapsed state on desktop
    document.body.classList.toggle('sb-collapsed');
    try {
      localStorage.setItem('admin_sb', document.body.classList.contains('sb-collapsed') ? '1' : '0');
    } catch (e) {}
  }
}
function closeSidebar(){
  const sb=document.getElementById('sidebar');
  const ov=document.getElementById('sbOverlay');
  const closeBtn=document.getElementById('sb-close-btn');
  sb.classList.remove('mobile-open');
  ov.classList.remove('open');
  if(closeBtn) closeBtn.style.display='none';
}
// Close sidebar on nav link click (mobile)
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.sb-nav a').forEach(function(a){
    a.addEventListener('click',function(){
      if(window.innerWidth<=768) closeSidebar();
    });
  });
});

// ── Theme Toggle ────────────────────────────────────────────
// Switch between dark and light theme and persist choice in localStorage
function toggleTheme(){
  const cur = document.body.getAttribute('data-theme') || 'dark';
  const next = (cur === 'dark') ? 'light' : 'dark';
  document.body.setAttribute('data-theme', next);
  try {
    localStorage.setItem('admin_theme', next);
  } catch (e) {}
}

// ── Boot UI State ───────────────────────────────────────────
// Restore theme and sidebar collapsed state on load
(function(){
  try {
    const savedTheme = localStorage.getItem('admin_theme');
    if (savedTheme) {
      document.body.setAttribute('data-theme', savedTheme);
    }
    const sbCollapsed = localStorage.getItem('admin_sb');
    if (sbCollapsed === '1') {
      document.body.classList.add('sb-collapsed');
    }
  } catch (e) {}
})();

// ── State ────────────────────────────────────────────────────
let curSection='',curSlot=0,curFile=null,curPid='',curImgnum=1;
let piSection='',piSlot=0,piUrls=[];

// ── Toast ────────────────────────────────────────────────────
function toast(msg,type='ok'){
  const t=document.getElementById('toast');
  t.textContent=msg;
  t.style.background=type==='err'?'#c0392b':'#1a1a2e';
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),3200);
}

// ── Modal ────────────────────────────────────────────────────
function openMo(id){const el=document.getElementById(id); if(el) el.classList.add('open');}
function closeMo(id){
  const el=document.getElementById(id); if(el) el.classList.remove('open');
  if(id==='upMo'){curFile=null;document.getElementById('prevWrap').style.display='none';document.getElementById('prog').style.display='none';document.getElementById('pbar').style.width='0';}
}
document.querySelectorAll('.mo').forEach(m=>m.addEventListener('click',function(e){if(e.target===this)this.classList.remove('open')}));

// ── Upload Image ─────────────────────────────────────────────
function openUp(section,slot,imgnum,label,imgUrl){
  curSection=section; curSlot=slot; curImgnum=imgnum||1; curFile=null;
  document.getElementById('upTitle').textContent=label+(imgnum>1?' · Img '+imgnum:'');
  const cw=document.getElementById('curImgWrap');
  const ni=document.getElementById('noImg');
  if(imgUrl){cw.style.display='block';document.getElementById('curImg').src=imgUrl;ni.style.display='none';}
  else{cw.style.display='none';ni.style.display='block';}
  document.getElementById('prevWrap').style.display='none';
  document.getElementById('prog').style.display='none';
  openMo('upMo');
}
// ── Product Multi-Image Manager ───────────────────────────────
function renderPiCell(cell, n, url){
  cell.style.cursor='pointer';
  cell.innerHTML=
    '<div style="height:64px;background:var(--surface2);border-radius:8px;overflow:hidden;display:flex;align-items:center;justify-content:center;border:2px solid '+(url?'#2874f0':'#e0e0e0')+';margin-bottom:4px">'
    +(url?'<img src="'+url+'" style="width:100%;height:100%;object-fit:cover">'
         :'<span style="font-size:20px;opacity:.25">📷</span>')
    +'</div>'
    +'<div style="font-size:10px;font-weight:700;text-align:center;color:'+(url?'#1a7a3c':'#aaa')+'">'+(url?'✓ #'+n:'+ #'+n)+'</div>';
}
function openProdImgs(section,slot,label){
  piSection=section; piSlot=slot; piUrls=Array(10).fill('');
  document.getElementById('piTitle').textContent=label;
  document.getElementById('piMultiLabel').textContent='Choose multiple images at once';
  document.getElementById('piProgress').style.display='none';
  document.getElementById('piMultiFi').value='';
  const grid=document.getElementById('piGrid');
  grid.innerHTML='<div style="grid-column:1/-1;text-align:center;padding:20px;color:#aaa;font-size:12px">Loading…</div>';
  openMo('prodImgMo');
  Promise.all(Array.from({length:10},(_,i)=>i+1).map(n=>
    fetch('?ajax=get_img_url&section='+encodeURIComponent(section)+'&slot='+slot+'&imgnum='+n)
      .then(r=>r.json()).catch(()=>({ok:false,url:''}))
  )).then(results=>{
    grid.innerHTML='';
    results.forEach((d,i)=>{
      const n=i+1, url=(d.ok&&d.url)?d.url:'';
      piUrls[i]=url;
      const cell=document.createElement('div');
      cell.id='pi-cell-'+n;
      renderPiCell(cell,n,url);
      cell.onclick=()=>{
        closeMo('prodImgMo');
        openUp(piSection,piSlot,n,label,url);
      };
      grid.appendChild(cell);
    });
  });
}
// Multi-select: upload all chosen files to slots 1,2,3… sequentially
async function piMultiSelect(inp){
  const files=[...inp.files].slice(0,10);
  if(!files.length) return;
  document.getElementById('piMultiLabel').textContent=files.length+' image'+(files.length>1?'s':'')+' selected — uploading…';
  const prog=document.getElementById('piProgress');
  const bar=document.getElementById('piProgBar');
  const lbl=document.getElementById('piProgLabel');
  prog.style.display='block'; bar.style.width='0';
  let done=0;
  for(let i=0;i<files.length;i++){
    const n=i+1;
    lbl.textContent='Uploading image '+n+' of '+files.length+' …';
    const url=await uploadImgFile(files[i],piSection,piSlot,n);
    done++;
    bar.style.width=Math.round(done/files.length*100)+'%';
    piUrls[i]=url||'';
    const cell=document.getElementById('pi-cell-'+n);
    if(cell&&url) renderPiCell(cell,n,url);
    // Update main card status badge
    if(n===1){
      const stEl=document.getElementById('st-'+piSection+'-'+piSlot);
      if(stEl){stEl.textContent='✓ '+files.length+' imgs';stEl.className='istatus has';}
      const imgEl=document.getElementById('img-'+piSection+'-'+piSlot);
      const phEl=document.getElementById('ph-'+piSection+'-'+piSlot);
      if(imgEl&&url){imgEl.src=url;}
      else if(phEl&&url){const img=document.createElement('img');img.id='img-'+piSection+'-'+piSlot;img.src=url;phEl.parentNode.replaceChild(img,phEl);}
    }
  }
  lbl.textContent='✓ All '+files.length+' images uploaded!';
  bar.style.background='#1a7a3c';
  document.getElementById('piMultiLabel').textContent='Choose multiple images at once';
  inp.value='';
  toast('✓ '+files.length+' images uploaded for '+document.getElementById('piTitle').textContent);
}
function uploadImgFile(file,section,slot,imgnum){
  return new Promise(resolve=>{
    const fd=new FormData(); fd.append('img',file);
    const xhr=new XMLHttpRequest();
    xhr.onload=()=>{try{const d=JSON.parse(xhr.responseText);resolve(d.ok?d.url:'');}catch{resolve('');}};
    xhr.onerror=()=>resolve('');
    xhr.open('POST','?ajax=upload&section='+encodeURIComponent(section)+'&slot='+slot+'&imgnum='+imgnum);
    xhr.setRequestHeader('X-CSRF-Token', CSRF_TOKEN);
    xhr.send(fd);
  });
}

function handleDrop(e){e.preventDefault();document.getElementById('dz').classList.remove('drag');const f=e.dataTransfer.files[0];if(f){curFile=f;showPreview(f);}}
function onFile(inp){if(inp.files[0]){curFile=inp.files[0];showPreview(inp.files[0]);}}
function showPreview(f){
  const r=new FileReader();
  r.onload=e=>{document.getElementById('prevImg').src=e.target.result;document.getElementById('prevWrap').style.display='block';}
  r.readAsDataURL(f);
}
function doUpload(){
  if(!curFile){toast('Choose an image first','err');return;}
  const fd=new FormData();
  fd.append('img',curFile);
  const btn=document.getElementById('upBtn');
  btn.disabled=true;btn.textContent='Uploading…';
  const prog=document.getElementById('prog');
  const pbar=document.getElementById('pbar');
  prog.style.display='block';pbar.style.width='0';
  const xhr=new XMLHttpRequest();
  xhr.upload.onprogress=e=>{if(e.lengthComputable)pbar.style.width=Math.round(e.loaded/e.total*100)+'%';};
  xhr.onload=()=>{
    btn.disabled=false;btn.textContent='📤 Upload';
    try{
      const d=JSON.parse(xhr.responseText);
      if(d.ok){
        toast('Image uploaded!');
        closeMo('upMo');
        const imgEl=document.getElementById('img-'+curSection+'-'+curSlot);
        const phEl=document.getElementById('ph-'+curSection+'-'+curSlot);
        const stEl=document.getElementById('st-'+curSection+'-'+curSlot);
        if(imgEl){imgEl.src=d.url;}
        else if(phEl){const img=document.createElement('img');img.id='img-'+curSection+'-'+curSlot;img.src=d.url;phEl.parentNode.replaceChild(img,phEl);}
        if(stEl&&curImgnum===1){stEl.textContent='✓ Uploaded';stEl.className='istatus has';}
        piUrls[curImgnum-1]=d.url;
        const cell=document.getElementById('pi-cell-'+curImgnum);
        if(cell) renderPiCell(cell,curImgnum,d.url);
        if(piSection===curSection&&piSlot===curSlot) setTimeout(()=>openMo('prodImgMo'),150);
      }else{toast(d.err||'Upload failed','err');}
    }catch(e){toast('Server error','err');}
  };
  xhr.onerror=()=>{btn.disabled=false;btn.textContent='📤 Upload';toast('Network error','err');};
  xhr.open('POST','?ajax=upload&section='+encodeURIComponent(curSection)+'&slot='+curSlot+'&imgnum='+curImgnum);
    xhr.setRequestHeader('X-CSRF-Token', CSRF_TOKEN);
  xhr.send(fd);
}

// ── Product Edit ─────────────────────────────────────────────
function openEdit(pid,data){
  curPid=pid;
  document.getElementById('ePid').textContent=pid.toUpperCase();
  document.getElementById('eName').value=data.name||'';
  document.getElementById('eBrand').value=data.brand||'';
  document.getElementById('ePrice').value=data.price||'';
  document.getElementById('eMrp').value=data.mrp||'';
  document.getElementById('eDesc').value=data.description||'';
  document.getElementById('editResult').style.display='none';
  calcOff();
  // Load variants
  const vc = document.getElementById('eVariants');
  vc.innerHTML = '';
  (data.variants||[]).forEach(function(v){ appendVariantGroup(v); });
  openMo('editMo');
}

// ── VARIANT GROUP BUILDER ─────────────────────────────────────
function addVariantGroup(){
  appendVariantGroup({label:'',type:'text',options:[{value:'',inStock:true}]});
}
function appendVariantGroup(v){
  const vc = document.getElementById('eVariants');
  const grp = document.createElement('div');
  grp.className = 'vgrp';

  // Header: label + type + delete
  grp.innerHTML = `
    <div class="vgrp-head">
      <input type="text" placeholder="Group name (e.g. Colour, Size, Wattage)" value="${escH(v.label||'')}" class="vg-label">
      <select class="vgrp-type vg-type">
        <option value="color"${v.type==='color'?' selected':''}>🎨 Colour</option>
        <option value="size"${v.type==='size'?' selected':''}>📐 Size</option>
        <option value="text"${(v.type!=='color'&&v.type!=='size')?' selected':''}>✏️ Text</option>
      </select>
      <button type="button" class="vgrp-del" title="Remove group">✕</button>
    </div>
    <div class="vopts"></div>
    <button type="button" class="vadd-opt">+ Add Option</button>`;

  grp.querySelector('.vgrp-del').onclick = function(){ grp.remove(); };
  const typeEl = grp.querySelector('.vg-type');
  const optsEl = grp.querySelector('.vopts');
  const addBtn = grp.querySelector('.vadd-opt');

  function addOpt(opt){
    const row = document.createElement('div');
    row.className = 'vopt-row';
    const isColor = typeEl.value === 'color';
    row.innerHTML = `
      ${isColor ? `<input type="color" class="vo-hex" value="${opt.hex||'#000000'}" title="Pick colour">` : ''}
      <input type="text" class="vo-val" placeholder="Option value (e.g. Red, 8GB, 1200W)" value="${escH(opt.value||'')}">
      <input type="checkbox" class="vo-stock" title="In Stock" ${opt.inStock!==false?'checked':''}>
      <span class="vopt-check-lbl">In stock</span>
      <button type="button" class="vopt-del" title="Remove">✕</button>`;
    row.querySelector('.vopt-del').onclick = function(){ row.remove(); };
    optsEl.appendChild(row);
  }

  // Re-render options when type changes
  typeEl.onchange = function(){
    const cur = collectOpts();
    optsEl.innerHTML = '';
    cur.forEach(addOpt);
  };

  function collectOpts(){
    return Array.from(optsEl.querySelectorAll('.vopt-row')).map(function(r){
      const o = {value:r.querySelector('.vo-val').value, inStock:r.querySelector('.vo-stock').checked};
      const hx = r.querySelector('.vo-hex');
      if(hx) o.hex = hx.value;
      return o;
    });
  }

  (v.options||[{value:'',inStock:true}]).forEach(addOpt);
  addBtn.onclick = function(){ addOpt({value:'',inStock:true}); };
  vc.appendChild(grp);
}

function escH(s){ return String(s||'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

function collectVariants(){
  return Array.from(document.getElementById('eVariants').querySelectorAll('.vgrp')).map(function(grp){
    const type = grp.querySelector('.vg-type').value;
    const opts = Array.from(grp.querySelectorAll('.vopt-row')).map(function(r){
      const o = {value:r.querySelector('.vo-val').value.trim(), inStock:r.querySelector('.vo-stock').checked};
      const hx = r.querySelector('.vo-hex');
      if(hx) o.hex = hx.value;
      return o;
    }).filter(function(o){ return o.value !== ''; });
    // Auto-generate label if user left it empty
    let label = grp.querySelector('.vg-label').value.trim();
    if(!label){ label = type === 'color' ? 'Colour' : (type === 'size' ? 'Size' : 'Variant'); }
    return {label:label, type:type, options:opts};
  }).filter(function(v){ return v.options.length > 0; }); // only drop if no options at all
}

function calcOff(){
  const p=parseInt(document.getElementById('ePrice').value)||0;
  const m=parseInt(document.getElementById('eMrp').value)||0;
  const bar=document.getElementById('offBar');
  if(m>p&&p>0){const off=Math.round((1-p/m)*100);bar.textContent='✓ '+off+'% discount';bar.style.display='block';}
  else bar.style.display='none';
}
function saveProduct(){
  const btn=document.getElementById('saveBtn');
  btn.disabled=true;btn.textContent='Saving…';
  const body={
    pid:curPid,
    name:document.getElementById('eName').value,
    brand:document.getElementById('eBrand').value,
    price:parseInt(document.getElementById('ePrice').value)||0,
    mrp:parseInt(document.getElementById('eMrp').value)||0,
    desc:document.getElementById('eDesc').value,
    variants:collectVariants()
  };
  fetch('?ajax=save_product',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN},body:JSON.stringify(body)})
  .then(r=>r.json()).then(d=>{
    btn.disabled=false;btn.textContent='💾 Save';
    const res=document.getElementById('editResult');
    if(d.ok){
      res.textContent='✓ Saved!';res.className='result ok';res.style.display='block';
      toast('Product '+curPid.toUpperCase()+' saved!');
      setTimeout(()=>closeMo('editMo'),1200);
    }else{res.textContent='✗ '+(d.err||'Error');res.className='result err';res.style.display='block';}
  }).catch(()=>{btn.disabled=false;btn.textContent='💾 Save';toast('Network error','err');});
}

// ── Product Search ───────────────────────────────────────────
function filterCatProds(gridId, q){
  q=q.toLowerCase().trim();
  document.querySelectorAll('#'+gridId+' .pcard').forEach(c=>{
    const match=!q||c.dataset.idC.includes(q)||c.dataset.nmC.includes(q)||c.dataset.brC.includes(q);
    c.style.display=match?'':'none';
  });
}
function filterMensSub(btn){
  document.querySelectorAll('.mens-pill').forEach(b=>b.classList.remove('on'));
  btn.classList.add('on');
  const sub=btn.dataset.sub.toLowerCase();
  document.querySelectorAll('#pgridMens .pcard, #pgridElec .pcard').forEach(c=>{
    c.style.display=(sub==='all'||c.dataset.subC===sub)?'':'none';
  });
}
function filterGadgetCat(btn){
  document.querySelectorAll('.mens-pill').forEach(b=>b.classList.remove('on'));
  btn.classList.add('on');
  const sub=btn.dataset.sub.toLowerCase();
  document.querySelectorAll('#pgridGadgets .pcard').forEach(c=>{
    c.style.display=(sub==='all'||c.dataset.subC===sub)?'':'none';
  });
}
function filterProds(q){
  q=q.toLowerCase().trim();
  document.querySelectorAll('#pgrid .pcard').forEach(c=>{
    const match=!q||c.dataset.id.includes(q)||c.dataset.nm.includes(q)||c.dataset.br.includes(q);
    c.style.display=match?'':'none';
  });
}

// ── Pagination ───────────────────────────────────────────────
function go(p){window.location='?tab=images&section=<?=$imgSection?>&page='+p;}
function goProd(p){window.location='?tab=products&page='+p;}

// ── Payment ──────────────────────────────────────────────────
function psLoad(){
  fetch('?ajax=get_payment').then(r=>r.json()).then(d=>{
    if(!d.ok)return;
    const ps=d.data;
    document.getElementById('ps_cod').value           = ps.cod_amount    ?? 99;
    document.getElementById('ps_cod_threshold').value = ps.cod_threshold ?? 200;
    document.getElementById('ps_cod_low').value       = ps.cod_low       ?? 49;
    document.getElementById('ps_codn').value          = ps.cod_note      || '';
  });
}
function psSave(){
  const btn=document.getElementById('psSaveBtn');
  btn.disabled=true;btn.textContent='Saving…';
  const body={
    cod_amount:    parseInt(document.getElementById('ps_cod').value)           || 99,
    cod_threshold: parseInt(document.getElementById('ps_cod_threshold').value) || 200,
    cod_low:       parseInt(document.getElementById('ps_cod_low').value)       || 49,
    cod_note:      document.getElementById('ps_codn').value,
  };
  fetch('?ajax=save_payment',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN},body:JSON.stringify(body)})
  .then(r=>r.json()).then(d=>{
    btn.disabled=false;btn.textContent='💾 Save Settings';
    const res=document.getElementById('psResult');
    if(d.ok){res.textContent='✓ '+d.msg;res.className='result ok';res.style.display='block';toast('Payment settings saved!');}
    else{res.textContent='✗ '+(d.err||'Error');res.className='result err';res.style.display='block';}
  }).catch(()=>{btn.disabled=false;btn.textContent='💾 Save Settings';toast('Network error','err');});
}

// ── Import All Product Images ─────────────────────────────────
let iiFile = null;

function openImportImagesMo() {
  iiFile = null;
  document.getElementById('iiFileName').textContent = 'No file selected';
  document.getElementById('iiResult').style.display = 'none';
  document.getElementById('iiProg').style.display   = 'none';
  document.getElementById('iiBar').style.width      = '0';
  document.getElementById('iiBar').style.background = 'var(--blue)';
  document.getElementById('iiLog').style.display    = 'none';
  document.getElementById('iiLog').innerHTML        = '';
  document.getElementById('iiStatus').style.display = 'none';
  document.getElementById('iiUpBtn').disabled       = false;
  document.getElementById('iiUpBtn').textContent    = '\u{1F5DC}\uFE0F Import & Update Paths';
  document.getElementById('iiFi').value             = '';
  openMo('importImagesMo');
}
function closeImportImagesMo() { closeMo('importImagesMo'); }
function handleIiDrop(e) {
  e.preventDefault();
  document.getElementById('iiDz').classList.remove('drag');
  const f = Array.from(e.dataTransfer.files).find(f => f.name.endsWith('.zip'));
  if (f) { iiFile = f; document.getElementById('iiFileName').textContent = f.name + ' (' + (f.size/1048576).toFixed(1) + ' MB)'; }
}
function onIiFile(inp) {
  if (inp.files[0]) {
    iiFile = inp.files[0];
    document.getElementById('iiFileName').textContent = inp.files[0].name + ' (' + (inp.files[0].size/1048576).toFixed(1) + ' MB)';
  }
}

function doImportImages() {
  if (!iiFile) { toast('Choose a ZIP file first', 'err'); return; }
  const btn    = document.getElementById('iiUpBtn');
  const prog   = document.getElementById('iiProg');
  const bar    = document.getElementById('iiBar');
  const log    = document.getElementById('iiLog');
  const status = document.getElementById('iiStatus');
  const res    = document.getElementById('iiResult');

  btn.disabled = true; btn.textContent = 'Uploading...';
  prog.style.display = 'block'; bar.style.width = '0'; bar.style.background = 'var(--blue)';
  log.style.display = 'block'; log.innerHTML = '';
  status.style.display = 'block'; status.textContent = 'Uploading ZIP to server...';
  res.style.display = 'none';

  const addLog = (msg, color) => {
    const d = document.createElement('div');
    d.style.color = color || '#444';
    d.textContent = msg;
    log.appendChild(d);
    log.scrollTop = log.scrollHeight;
  };

  const fd = new FormData();
  fd.append('zipfile', iiFile);

  const xhr = new XMLHttpRequest();
  xhr.upload.onprogress = e => {
    if (e.lengthComputable) {
      const pct = Math.round(e.loaded / e.total * 80);
      bar.style.width = pct + '%';
      status.textContent = 'Uploading... ' + pct + '%';
    }
  };
  xhr.onload = () => {
    bar.style.width = '100%';
    btn.disabled = false; btn.textContent = '\u{1F5DC}\uFE0F Import & Update Paths';
    let d;
    try { d = JSON.parse(xhr.responseText); } catch(e) { toast('Server error — invalid response', 'err'); return; }
    if (!d.ok) {
      status.textContent = 'Failed';
      bar.style.background = '#e74c3c';
      res.textContent = 'Error: ' + (d.err || 'Import failed');
      res.className = 'result err'; res.style.display = 'block';
      toast(d.err || 'Import failed', 'err');
      return;
    }
    status.textContent = 'Done!';
    bar.style.background = '#1a7a3c';
    addLog('Extracted ' + d.files + ' image(s) from ' + d.products + ' product folder(s)', '#1a7a3c');
    if (d.updated > 0) addLog(d.msg, '#1a7a3c');
    if (d.skipped > 0) addLog('Skipped ' + d.skipped + ' non-image file(s)', '#888');
    if (d.debug && d.debug.length) {
      addLog('--- Products updated ---', '#aaa');
      d.debug.forEach(x => addLog('  ' + x, '#2874f0'));
    }
    if (d.errors && d.errors.length) {
      addLog('--- Warnings ---', '#aaa');
      d.errors.slice(0, 5).forEach(x => addLog('  Warning: ' + x, '#c0392b'));
    }
    const summary = d.files + ' images imported, ' + d.products + ' products, paths updated in products.json';
    res.textContent = summary; res.className = 'result ok'; res.style.display = 'block';
    toast(summary);
    if (d.files > 0) setTimeout(() => { closeMo('importImagesMo'); location.reload(); }, 3000);
  };
  xhr.onerror = () => {
    btn.disabled = false; btn.textContent = '\u{1F5DC}\uFE0F Import & Update Paths';
    toast('Network error', 'err');
  };
  xhr.open('POST', '?ajax=import_product_images');
  xhr.setRequestHeader('X-CSRF-Token', CSRF_TOKEN);
  xhr.send(fd);
}

// ── ZIP Upload ───────────────────────────────────────────────
const ZIP_SEC_LABELS = {
  banners:'Banner Slider',icons:'Category Icons',dotd:'Deals of the Day',
  sponsored_banner:'Sponsored Banner',supercoin:'SuperCoin Banner',adbanner:'Ad Banner',sponsored:'Sponsored Products',
  suggested:'Suggested For You',youmaylike:'You May Also Like',
  premium:'Upgrade to Premium',toppicks:'Product Images'
};
let zipSection='', zipSlot=0, zipFiles=[];

function openZip(section, slot){
  zipSection=section; zipSlot=slot||0; zipFiles=[];
  document.getElementById('zipFileName').textContent='No files selected';
  document.getElementById('zipResult').style.display='none';
  document.getElementById('zipProg').style.display='none';
  document.getElementById('zipBar').style.width='0';
  document.getElementById('zipLog').style.display='none';
  document.getElementById('zipLog').innerHTML='';
  document.getElementById('zipUpBtn').disabled=false;
  document.getElementById('zipUpBtn').textContent='📦 Upload ZIP';
  document.getElementById('zipSecLabel').textContent=ZIP_SEC_LABELS[section]||section;
  document.getElementById('zipFi').value='';
  openMo('zipMo');
}
function closeZipMo(){ closeMo('zipMo'); }
function handleZipDrop(e){
  e.preventDefault();
  document.getElementById('zipDz').classList.remove('drag');
  setZipFiles(Array.from(e.dataTransfer.files).filter(f=>f.name.endsWith('.zip')));
}
function onZipFile(inp){
  if(inp.files.length) setZipFiles(Array.from(inp.files));
}
function setZipFiles(files){
  zipFiles=files;
  if(!files.length) return;
  const totalMB=(files.reduce((s,f)=>s+f.size,0)/1048576).toFixed(1);
  document.getElementById('zipFileName').textContent=
    files.length===1
      ? files[0].name+' ('+( files[0].size/1048576).toFixed(1)+' MB)'
      : files.length+' ZIP files selected · '+totalMB+' MB total';
}

async function doZipUpload(){
  if(!zipFiles.length){toast('Choose at least one ZIP file','err');return;}
  const btn=document.getElementById('zipUpBtn');
  btn.disabled=true; btn.textContent='Uploading…';
  document.getElementById('zipResult').style.display='none';
  document.getElementById('zipProg').style.display='block';
  const log=document.getElementById('zipLog');
  log.style.display='block'; log.innerHTML='';
  const addLog=(msg,c)=>{const d=document.createElement('div');d.style.color=c||'#444';d.textContent=msg;log.appendChild(d);log.scrollTop=log.scrollHeight;};

  let totalImported=0, totalErrors=0;

  for(let i=0; i<zipFiles.length; i++){
    const f=zipFiles[i];
    addLog('['+(i+1)+'/'+zipFiles.length+'] '+f.name+' ('+(f.size/1048576).toFixed(1)+' MB)…');
    document.getElementById('zipBar').style.width=Math.round(i/zipFiles.length*100)+'%';

    const result = await uploadOneZip(f, (pct)=>{
      const overall=((i + pct/100)/zipFiles.length)*100;
      document.getElementById('zipBar').style.width=Math.round(overall)+'%';
    });

    if(result.ok){
      addLog('  ✓ '+result.count+' image'+(result.count!==1?'s':'')+' imported'+(result.errors&&result.errors.length?' ('+result.errors.length+' skipped)':''), '#1a7a3c');
      if(result.debug) result.debug.slice(0,3).forEach(x=>addLog('    '+x,'#555'));
      if(result.errors) result.errors.slice(0,2).forEach(x=>addLog('    ⚠ '+x,'#c0392b'));
      totalImported+=result.count;
    } else {
      addLog('  ✗ '+( result.err||'Failed'), '#c0392b');
      totalErrors++;
    }
  }

  document.getElementById('zipBar').style.width='100%';
  const summary='✓ Done — '+totalImported+' images imported from '+zipFiles.length+' ZIP'+(zipFiles.length>1?'s':'')+(totalErrors?' ('+totalErrors+' failed)':'');
  addLog(summary, '#1a7a3c');
  const res=document.getElementById('zipResult');
  res.textContent=summary; res.className='result ok'; res.style.display='block';
  toast(summary);
  btn.disabled=false; btn.textContent='📦 Upload ZIP';
  if(totalImported>0) setTimeout(()=>{closeMo('zipMo');location.reload();},2000);
}

function uploadOneZip(file, onProgress){
  return new Promise(resolve=>{
    const fd=new FormData();
    fd.append('zipfile', file);
    const xhr=new XMLHttpRequest();
    xhr.upload.onprogress=e=>{if(e.lengthComputable) onProgress(e.loaded/e.total*100);};
    xhr.onload=()=>{
      try{ resolve(JSON.parse(xhr.responseText)); }
      catch(e){ resolve({ok:false,err:'Invalid server response'}); }
    };
    xhr.onerror=()=>resolve({ok:false,err:'Network error'});
    xhr.open('POST','?ajax=zip_upload&section='+encodeURIComponent(zipSection)+(zipSlot>0?'&slot='+zipSlot:''));
    xhr.setRequestHeader('X-CSRF-Token', CSRF_TOKEN);
    xhr.send(fd);
  });
}

// ── CSV Smart Import ─────────────────────────────────────────

// Alias map: for each target field, all known header variants (normalized)
const CSV_ALIASES = {
  id:    ['id','pid','productid','itemid','sku','skuid','code','productcode','itemcode',
          'slno','srno','sno','serialno','serial','no','number','num','index','rowno',
          'row','#','sr','sl','productno','itemno','catalogid','asin','articleno'],
  name:  ['name','productname','itemname','title','producttitle','itemtitle','product',
          'item','label','heading','productlabel','goods','goodsname','productdescription',
          'itemdescription','article','articlename','listingtitle'],
  brand: ['brand','brandname','manufacturer','maker','company','vendor','make','mfr',
          'mfg','producedby','brandlabel','manufact','supplierbrands','vendorname'],
  price: ['price','sellingprice','saleprice','sp','offerprice','discountedprice',
          'currentprice','ourprice','cost','amount','finalprice','netprice','rate',
          'actualprice','salerate','discountprice','yourprice','nowprice','dealprice',
          'purchaseprice','listingprice','promoprice'],
  mrp:   ['mrp','originalprice','listprice','maxprice','retailprice','regularprice',
          'marketprice','fullprice','wasprice','maximumretailprice','baseprice','oldprice',
          'strikeprice','maxretailprice','undiscountedprice','standardprice','rrp',
          'recommendedretailprice','originaltag','compareatprice','beforeprice'],
  desc:  ['description','desc','details','about','info','summary','productdescription',
          'productdetails','overview','longdescription','specification','specifications',
          'spec','specs','content','body','productinfo','aboutproduct','features',
          'feature','productoverview','itemdescription','productdesc','productfeatures',
          'shortdescription','fulldescription','productbody','productcontent']
};

const CSV_LABELS = {
  id:'Product ID', name:'Name', brand:'Brand',
  price:'Price (₹)', mrp:'MRP (₹)', desc:'Description'
};

// Normalize header: lowercase, strip all non-alpha-numeric
function csvNorm(s){ return s.toLowerCase().replace(/[^a-z0-9]/g,''); }

// Score a single header against a target field
function csvScore(header, field){
  const h = csvNorm(header);
  if (!h) return 0;
  const aliases = CSV_ALIASES[field];
  // Exact match to field key
  if (h === field) return 100;
  // Exact alias match
  if (aliases.includes(h)) return 95;
  // Field key is the full header
  if (h.startsWith(field) || h.endsWith(field)) return 80;
  // Header is prefix of alias or alias is in header
  for (const a of aliases){
    if (h === a) return 95;
    if (h.startsWith(a) || h.endsWith(a)) return 75;
    if (a.startsWith(h) && h.length >= 3) return 65;
    if (h.includes(a) && a.length >= 3) return 60;
    if (a.includes(h) && h.length >= 3) return 55;
  }
  return 0;
}

// Greedy best-match assignment: each column used at most once
function csvDetect(headers){
  const fields = ['id','name','brand','price','mrp','desc'];
  const mapping = {};
  const used = new Set();
  // Score matrix
  const scores = {};
  for (const f of fields){
    scores[f] = headers.map((h,i) => ({i, score: csvScore(h, f)}));
    scores[f].sort((a,b) => b.score - a.score);
  }
  // Assign in priority order — id first
  for (const f of fields){
    for (const {i, score} of scores[f]){
      if (score > 0 && !used.has(i)){
        mapping[f] = i;
        used.add(i);
        break;
      }
    }
    if (!(f in mapping)) mapping[f] = -1;
  }
  return mapping;
}

// Confidence badge
function csvBadge(header, field){
  if (header === null) return '<span style="font-size:10px;color:#aaa">— not detected</span>';
  const score = csvScore(header, field);
  if (score >= 90) return '<span style="font-size:10px;font-weight:700;color:#1a7a3c;background:#e8f5e9;padding:2px 7px;border-radius:10px">✓ High confidence</span>';
  if (score >= 60) return '<span style="font-size:10px;font-weight:700;color:#7c5700;background:#fffbe6;padding:2px 7px;border-radius:10px">~ Guessed</span>';
  return '<span style="font-size:10px;font-weight:700;color:#c0392b;background:#fff0f0;padding:2px 7px;border-radius:10px">? Low confidence</span>';
}

let csvFile=null, csvHeaders=[], csvPreviewRows=[], csvMapping={};

function openCsvMo(){
  csvReset();
  openMo('csvMo');
}
function closeCsvMo(){
  closeMo('csvMo');
}
function csvReset(){
  csvFile=null; csvHeaders=[]; csvPreviewRows=[]; csvMapping={};
  document.getElementById('csvStep1').style.display='';
  document.getElementById('csvStep2').style.display='none';
  document.getElementById('csvImportBtn').style.display='none';
  document.getElementById('csvFileName').textContent='No file selected';
  document.getElementById('csvStep1Err').style.display='none';
  document.getElementById('csvResult').style.display='none';
  document.getElementById('csvProg').style.display='none';
  document.getElementById('csvBar').style.width='0';
  document.getElementById('csvFi').value='';
}

function handleCsvDrop(e){
  e.preventDefault();
  document.getElementById('csvDz').classList.remove('drag');
  const f = e.dataTransfer.files[0];
  if (f) processCsvFile(f);
}
function onCsvFile(inp){
  if (inp.files[0]) processCsvFile(inp.files[0]);
}

function processCsvFile(f){
  csvFile = f;
  document.getElementById('csvFileName').textContent = f.name;
  const reader = new FileReader();
  reader.onload = e => {
    const text = e.target.result;
    // Detect delimiter: comma vs tab vs semicolon
    const firstLine = text.split(/\r?\n/)[0] || '';
    let delim = ',';
    const tabs  = (firstLine.match(/\t/g)||[]).length;
    const semis = (firstLine.match(/;/g)||[]).length;
    const commas= (firstLine.match(/,/g)||[]).length;
    if (tabs > commas && tabs > semis) delim = '\t';
    else if (semis > commas) delim = ';';

    // Parse CSV properly (handles quoted fields)
    const rows = csvParse(text, delim);
    if (!rows || rows.length < 2){
      showCsvErr('CSV appears empty or unreadable — must have a header row + at least one data row.');
      return;
    }
    csvHeaders = rows[0].map(h => h.trim());
    if (csvHeaders.length < 2){
      showCsvErr('Only 1 column detected. Check the file delimiter (comma/tab/semicolon).');
      return;
    }
    csvPreviewRows = rows.slice(1,4);
    csvMapping = csvDetect(csvHeaders);
    renderMappingUI();
  };
  reader.onerror = () => showCsvErr('Could not read the file.');
  reader.readAsText(f, 'UTF-8');
}

// Minimal RFC-4180 CSV parser
function csvParse(text, delim=','){
  const rows = []; let row = []; let field = ''; let inQ = false;
  for (let i=0; i<text.length; i++){
    const c = text[i];
    if (inQ){
      if (c==='"'){
        if (text[i+1]==='"'){field+='"';i++;}
        else inQ=false;
      } else field+=c;
    } else {
      if (c==='"'){ inQ=true; }
      else if (c===delim){ row.push(field); field=''; }
      else if (c==='\n'||c==='\r'){
        row.push(field); field='';
        if (row.some(v=>v.trim()!=='')||row.length>1) rows.push(row);
        row=[];
        if (c==='\r'&&text[i+1]==='\n') i++;
      } else field+=c;
    }
  }
  if (field||row.length) { row.push(field); if(row.some(v=>v.trim()!=='')) rows.push(row); }
  return rows;
}

function showCsvErr(msg){
  const el = document.getElementById('csvStep1Err');
  el.textContent = '✗ '+msg; el.className='result err'; el.style.display='block';
}

function renderMappingUI(){
  document.getElementById('csvStep1').style.display='none';
  document.getElementById('csvStep2').style.display='';
  document.getElementById('csvImportBtn').style.display='';

  const fields = ['id','name','brand','price','mrp','desc'];
  const optionsHtml = ['<option value="-1">— skip / not in CSV —</option>',
    ...csvHeaders.map((h,i)=>`<option value="${i}">${h}</option>`)
  ].join('');

  let html = '<table style="width:100%;border-collapse:collapse;font-size:12px">';
  html += '<tr style="border-bottom:2px solid var(--border)">'
        + '<th style="padding:7px 8px;text-align:left;color:var(--mut)">Our Field</th>'
        + '<th style="padding:7px 8px;text-align:left;color:var(--mut)">Detected Column</th>'
        + '<th style="padding:7px 8px;text-align:left;color:var(--mut)">Confidence</th>'
        + '<th style="padding:7px 8px;text-align:left;color:var(--mut)">Override</th>'
        + '</tr>';
  for (const f of fields){
    const idx = csvMapping[f];
    const detectedHeader = idx >= 0 ? csvHeaders[idx] : null;
    const isRequired = f === 'id';
    const selOpts = optionsHtml.replace(
      `value="${idx}"`,`value="${idx}" selected`
    );
    html += `<tr style="border-bottom:1px solid var(--border)">
      <td style="padding:8px;font-weight:700;white-space:nowrap">
        ${CSV_LABELS[f]}${isRequired?' <span style="color:var(--red)">*</span>':''}
      </td>
      <td style="padding:8px;font-family:monospace;color:${detectedHeader?'var(--txt)':'#bbb'}">
        ${detectedHeader ? detectedHeader : '—'}
      </td>
      <td style="padding:8px">${csvBadge(detectedHeader, f)}</td>
      <td style="padding:8px">
        <select id="csvMap_${f}" onchange="csvMapChange('${f}',this.value)"
          style="width:100%;padding:5px 7px;border:1.5px solid var(--border);border-radius:6px;font:12px 'Outfit',sans-serif;outline:none">
          ${selOpts}
        </select>
      </td>
    </tr>`;
  }
  html += '</table>';
  document.getElementById('csvMapTable').innerHTML = html;

  // Preview table
  if (csvPreviewRows.length > 0){
    let pt = '<table style="border-collapse:collapse;min-width:100%">';
    pt += '<tr>'+csvHeaders.map(h=>`<th style="padding:5px 8px;background:var(--surface2);border:1px solid var(--border);white-space:nowrap">${escHtml(h)}</th>`).join('')+'</tr>';
    for (const r of csvPreviewRows){
      pt += '<tr>'+csvHeaders.map((_,i)=>`<td style="padding:4px 8px;border:1px solid var(--border);white-space:nowrap;max-width:140px;overflow:hidden;text-overflow:ellipsis">${escHtml(r[i]||'')}</td>`).join('')+'</tr>';
    }
    pt += '</table>';
    document.getElementById('csvPreview').innerHTML = pt;
    document.getElementById('csvPreviewWrap').style.display='';
  }
}

function csvMapChange(field, val){
  csvMapping[field] = parseInt(val);
}

function escHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function doCsvImport(){
  if (!csvFile){ toast('No file loaded','err'); return; }
  if (csvMapping['id'] === undefined || csvMapping['id'] < 0){
    toast('Product ID column is required — set it in the mapping','err'); return;
  }
  const btn = document.getElementById('csvImportBtn');
  btn.disabled=true; btn.textContent='Importing…';
  document.getElementById('csvResult').style.display='none';
  const prog=document.getElementById('csvProg');
  const bar=document.getElementById('csvBar');
  prog.style.display='block'; bar.style.width='0';

  const fd = new FormData();
  fd.append('csvfile', csvFile);
  fd.append('mapping', JSON.stringify(csvMapping));

  const xhr = new XMLHttpRequest();
  xhr.upload.onprogress = e => { if(e.lengthComputable) bar.style.width=Math.round(e.loaded/e.total*100)+'%'; };
  xhr.onload = () => {
    btn.disabled=false; btn.textContent='📥 Confirm & Import';
    const res = document.getElementById('csvResult');
    try {
      const d = JSON.parse(xhr.responseText);
      if (d.ok){
        let msg = '';
        if (d.added > 0 && d.updated > 0) msg = '✓ '+d.added+' products added, '+d.updated+' updated';
        else if (d.added > 0) msg = '✓ '+d.added+' new products added!';
        else msg = '✓ '+d.updated+' of '+d.total+' products updated';
        res.textContent=msg; res.className='result ok'; res.style.display='block';
        toast(msg);
        if (d.added > 0 || d.updated > 0) setTimeout(()=>{ closeCsvMo(); location.reload(); }, 1600);
      } else {
        res.textContent='✗ '+(d.err||'Import failed');
        res.className='result err'; res.style.display='block';
      }
    } catch(e){ res.textContent='✗ Server error'; res.className='result err'; res.style.display='block'; }
  };
  xhr.onerror = () => { btn.disabled=false; btn.textContent='📥 Confirm & Import'; toast('Network error','err'); };
  xhr.open('POST','?ajax=import_csv');
    xhr.setRequestHeader('X-CSRF-Token', CSRF_TOKEN);
  xhr.send(fd);
}


// ── Analytics ────────────────────────────────────────────────
function clearAnalytics(){
  if(!confirm('Reset ALL analytics data? This cannot be undone.')) return;
  fetch('?ajax=clear_analytics').then(r=>r.json()).then(d=>{
    if(d.ok){toast('Analytics data cleared');setTimeout(()=>location.reload(),800);}
    else toast('Error: '+(d.err||'unknown'),'err');
  });
}


// ── Add Product ───────────────────────────────────────────────
function openAddProduct(){
  document.getElementById('ap_name').value='';
  document.getElementById('ap_brand').value='';
  document.getElementById('ap_price').value='';
  document.getElementById('ap_mrp').value='';
  document.getElementById('ap_category').value='';
  document.getElementById('ap_subcategory').value='';
  document.getElementById('ap_badge').value='';
  document.getElementById('ap_stock').value='100';
  document.getElementById('ap_desc').value='';
  document.getElementById('apOffBar').style.display='none';
  document.getElementById('apResult').style.display='none';
  document.getElementById('apSaveBtn').disabled=false;
  document.getElementById('apSaveBtn').textContent='➕ Add Product';
  document.getElementById('addProdMo').classList.add('open');
  setTimeout(()=>document.getElementById('ap_name').focus(),100);
}
function closeAddProduct(){
  document.getElementById('addProdMo').classList.remove('open');
}
function apCalcOff(){
  const p=parseInt(document.getElementById('ap_price').value)||0;
  const m=parseInt(document.getElementById('ap_mrp').value)||0;
  const bar=document.getElementById('apOffBar');
  if(m>p&&p>0){const off=Math.round((1-p/m)*100);bar.textContent='🏷️ '+off+'% discount on this product';bar.style.display='block';}
  else bar.style.display='none';
}
function doAddProduct(){
  const name=document.getElementById('ap_name').value.trim();
  const price=parseInt(document.getElementById('ap_price').value)||0;
  if(!name){toast('Product name is required','err');document.getElementById('ap_name').focus();return;}
  if(price<=0){toast('Price must be greater than 0','err');document.getElementById('ap_price').focus();return;}
  const btn=document.getElementById('apSaveBtn');
  btn.disabled=true; btn.textContent='Saving…';
  const payload={
    name,
    brand:document.getElementById('ap_brand').value.trim(),
    price,
    mrp:parseInt(document.getElementById('ap_mrp').value)||0,
    desc:document.getElementById('ap_desc').value.trim(),
    category:document.getElementById('ap_category').value.trim(),
    subcategory:document.getElementById('ap_subcategory').value.trim(),
    badge:document.getElementById('ap_badge').value.trim(),
    stock:parseInt(document.getElementById('ap_stock').value)||100,
    rating:4.0
  };
  fetch('?ajax=add_product',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN},body:JSON.stringify(payload)})
    .then(r=>r.json()).then(d=>{
      btn.disabled=false; btn.textContent='➕ Add Product';
      const res=document.getElementById('apResult');
      if(d.ok){
        res.textContent='✓ Product '+d.id+' added successfully!';
        res.className='result ok'; res.style.display='block';
        toast('✓ Product '+d.id+' created!');
        setTimeout(()=>{closeAddProduct();location.reload();},1500);
      } else {
        res.textContent='✗ '+(d.err||'Failed to add product');
        res.className='result err'; res.style.display='block';
        toast(d.err||'Error','err');
      }
    }).catch(()=>{
      btn.disabled=false; btn.textContent='➕ Add Product';
      toast('Network error','err');
    });
}


// ── Delete Product ────────────────────────────────────────────
var _delPid = '';
function confirmDelete(pid){
  _delPid = pid;
  document.getElementById('delPidLabel').textContent = pid.toUpperCase();
  document.getElementById('delResult').style.display = 'none';
  document.getElementById('delConfirmBtn').disabled = false;
  document.getElementById('delConfirmBtn').textContent = '🗑️ Yes, Delete';
  document.getElementById('deleteProdMo').classList.add('open');
}
function doDeleteProduct(){
  const btn = document.getElementById('delConfirmBtn');
  btn.disabled = true; btn.textContent = 'Deleting…';
  fetch('?ajax=delete_product', {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN},
    body: JSON.stringify({pid: _delPid})
  }).then(r=>r.json()).then(d=>{
    const res = document.getElementById('delResult');
    if(d.ok){
      res.textContent = '✓ ' + _delPid.toUpperCase() + ' deleted!';
      res.className = 'result ok'; res.style.display = 'block';
      toast('Product ' + _delPid.toUpperCase() + ' deleted');
      setTimeout(()=>{ closeMo('deleteProdMo'); location.reload(); }, 1200);
    } else {
      res.textContent = '✗ ' + (d.err || 'Failed');
      res.className = 'result err'; res.style.display = 'block';
      btn.disabled = false; btn.textContent = '🗑️ Yes, Delete';
    }
  }).catch(()=>{ btn.disabled=false; btn.textContent='🗑️ Yes, Delete'; toast('Network error','err'); });
}

// ── Init ─────────────────────────────────────────────────────
<?php if($tab==='payment'):?>psLoad();<?php endif?>
</script>


<div id="toast" style="position:fixed;left:50%;bottom:22px;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 14px;border-radius:10px;font:600 13px 'Outfit',sans-serif;box-shadow:0 10px 24px rgba(0,0,0,.22);opacity:0;pointer-events:none;transition:all .22s ease;z-index:9999"></div>
<style>#toast.show{opacity:1;transform:translateX(-50%) translateY(0)} .mo .mbody{max-height:70vh;overflow:auto}</style>

<!-- ══ UPLOAD IMAGE MODAL ═══════════════════════════════════ -->
<div class="mo" id="upMo">
  <div class="mbox" style="max-width:560px">
    <div class="mhead">
      <h3 id="upTitle">Upload image</h3>
      <button class="mclose" onclick="closeMo('upMo')">✕</button>
    </div>
    <div class="mbody">
      <div id="curImgWrap" style="display:none;margin-bottom:14px">
        <div style="font-size:11px;color:var(--mut);font-weight:700;margin-bottom:8px">Current image</div>
        <img id="curImg" src="" alt="Current image" style="width:100%;max-height:220px;object-fit:contain;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:10px">
      </div>
      <div id="noImg" style="display:block;margin-bottom:14px;font-size:12px;color:var(--mut);background:var(--surface2);border:1px dashed var(--border);border-radius:12px;padding:14px;text-align:center">No image uploaded yet for this slot.</div>
      <div id="dz" class="dropzone" onclick="document.getElementById('upFi').click()" ondragover="event.preventDefault();this.classList.add('drag')" ondragleave="this.classList.remove('drag')" ondrop="handleDrop(event)">
        <div class="dz-ic">🖼️</div>
        <p>Tap to choose image</p>
        <small>JPG, PNG, WEBP, AVIF supported</small>
      </div>
      <input id="upFi" type="file" accept="image/*,.avif" onchange="onFile(this)" style="display:none">
      <div id="prevWrap" style="display:none;margin-top:14px">
        <div style="font-size:11px;color:var(--mut);font-weight:700;margin-bottom:8px">Preview</div>
        <img id="prevImg" src="" alt="Preview" style="width:100%;max-height:220px;object-fit:contain;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:10px">
      </div>
      <div id="prog" style="display:none;margin-top:14px">
        <div style="height:10px;background:var(--surface2);border-radius:999px;overflow:hidden"><div id="pbar" class="pbar"></div></div>
      </div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeMo('upMo')">Cancel</button>
      <button id="upBtn" onclick="doUpload()" style="padding:10px 18px;background:var(--accent);color:#fff;border:none;border-radius:10px;font:700 14px 'Outfit',sans-serif;cursor:pointer">📤 Upload</button>
    </div>
  </div>
</div>

<!-- ══ PRODUCT IMAGES MODAL ═════════════════════════════════ -->
<div class="mo" id="prodImgMo">
  <div class="mbox" style="max-width:720px">
    <div class="mhead">
      <h3>📸 <span id="piTitle">Product Images</span></h3>
      <button class="mclose" onclick="closeMo('prodImgMo')">✕</button>
    </div>
    <div class="mbody">
      <label for="piMultiFi" style="display:block;margin-bottom:14px;padding:14px;border:1px dashed var(--border);border-radius:12px;background:var(--surface2);cursor:pointer">
        <div style="font-weight:700;margin-bottom:4px">Select up to 10 images</div>
        <div id="piMultiLabel" style="font-size:12px;color:var(--mut)">Choose multiple images at once</div>
      </label>
      <input id="piMultiFi" type="file" accept="image/*,.avif" multiple onchange="piMultiSelect(this)" style="display:none">
      <div id="piProgress" style="display:none;margin-bottom:14px">
        <div style="font-size:12px;color:var(--mut);margin-bottom:6px" id="piProgLabel">Uploading…</div>
        <div style="height:10px;background:var(--surface2);border-radius:999px;overflow:hidden"><div id="piProgBar" class="pbar"></div></div>
      </div>
      <div id="piGrid" style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px"></div>
    </div>
  </div>
</div>

<!-- ══ ZIP SECTION MODAL ════════════════════════════════════ -->
<div class="mo" id="zipMo">
  <div class="mbox" style="max-width:560px">
    <div class="mhead">
      <h3>📦 Upload ZIP for <span id="zipSecLabel">Section</span></h3>
      <button class="mclose" onclick="closeZipMo()">✕</button>
    </div>
    <div class="mbody">
      <div id="zipDz" class="dropzone" onclick="document.getElementById('zipFi').click()" ondragover="event.preventDefault();this.classList.add('drag')" ondragleave="this.classList.remove('drag')" ondrop="handleZipDrop(event)">
        <div class="dz-ic">🗜️</div>
        <p>Tap to choose ZIP file</p>
        <small id="zipFileName">No files selected</small>
      </div>
      <input id="zipFi" type="file" accept=".zip,application/zip" multiple onchange="onZipFile(this)" style="display:none">
      <div id="zipProg" style="display:none;margin-top:14px"><div style="height:10px;background:var(--surface2);border-radius:999px;overflow:hidden"><div id="zipBar" class="pbar"></div></div></div>
      <div id="zipResult" class="result" style="display:none;margin-top:12px"></div>
      <div id="zipLog" style="display:none;margin-top:12px;max-height:190px;overflow:auto;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:12px;font-size:12px"></div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeZipMo()">Cancel</button>
      <button id="zipUpBtn" onclick="doZipUpload()" style="padding:10px 18px;background:var(--accent);color:#fff;border:none;border-radius:10px;font:700 14px 'Outfit',sans-serif;cursor:pointer">📦 Upload ZIP</button>
    </div>
  </div>
</div>

<!-- ══ IMPORT PRODUCT IMAGES MODAL ══════════════════════════ -->
<div class="mo" id="importImagesMo">
  <div class="mbox" style="max-width:560px">
    <div class="mhead">
      <h3>🗜️ Bulk Product Images ZIP</h3>
      <button class="mclose" onclick="closeImportImagesMo()">✕</button>
    </div>
    <div class="mbody">
      <div id="iiDz" class="dropzone" onclick="document.getElementById('iiFi').click()" ondragover="event.preventDefault();this.classList.add('drag')" ondragleave="this.classList.remove('drag')" ondrop="handleIiDrop(event)">
        <div class="dz-ic">🖼️</div>
        <p>Tap to choose products ZIP</p>
        <small id="iiFileName">No file selected</small>
      </div>
      <input id="iiFi" type="file" accept=".zip,application/zip" onchange="onIiFile(this)" style="display:none">
      <div id="iiProg" style="display:none;margin-top:14px"><div style="height:10px;background:var(--surface2);border-radius:999px;overflow:hidden"><div id="iiBar" class="pbar"></div></div></div>
      <div id="iiStatus" style="display:none;margin-top:10px;font-size:12px;color:var(--mut)"></div>
      <div id="iiResult" class="result" style="display:none;margin-top:12px"></div>
      <div id="iiLog" style="display:none;margin-top:12px;max-height:190px;overflow:auto;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:12px;font-size:12px"></div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeImportImagesMo()">Cancel</button>
      <button id="iiUpBtn" onclick="doImportImages()" style="padding:10px 18px;background:var(--accent);color:#fff;border:none;border-radius:10px;font:700 14px 'Outfit',sans-serif;cursor:pointer">🗜️ Import & Update Paths</button>
    </div>
  </div>
</div>

<!-- ══ CSV IMPORT MODAL ═════════════════════════════════════ -->
<div class="mo" id="csvMo">
  <div class="mbox" style="max-width:860px">
    <div class="mhead">
      <h3>📊 CSV Import</h3>
      <button class="mclose" onclick="closeCsvMo()">✕</button>
    </div>
    <div class="mbody">
      <div id="csvStep1">
        <div id="csvDz" class="dropzone" onclick="document.getElementById('csvFi').click()" ondragover="event.preventDefault();this.classList.add('drag')" ondragleave="this.classList.remove('drag')" ondrop="handleCsvDrop(event)">
          <div class="dz-ic">📄</div>
          <p>Tap to choose CSV file</p>
          <small id="csvFileName">No file selected</small>
        </div>
        <input id="csvFi" type="file" accept=".csv,text/csv,text/plain" onchange="onCsvFile(this)" style="display:none">
        <div id="csvStep1Err" class="result err" style="display:none;margin-top:12px"></div>
      </div>
      <div id="csvStep2" style="display:none">
        <div style="font-size:13px;font-weight:700;margin-bottom:10px">Map CSV columns to your product fields</div>
        <div id="csvMapTable" style="overflow:auto"></div>
        <div id="csvPreviewWrap" style="display:none;margin-top:14px">
          <div style="font-size:13px;font-weight:700;margin-bottom:8px">Preview</div>
          <div id="csvPreview" style="overflow:auto;border:1px solid var(--border);border-radius:12px"></div>
        </div>
      </div>
      <div id="csvProg" style="display:none;margin-top:14px"><div style="height:10px;background:var(--surface2);border-radius:999px;overflow:hidden"><div id="csvBar" class="pbar"></div></div></div>
      <div id="csvResult" class="result" style="display:none;margin-top:12px"></div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeCsvMo()">Cancel</button>
      <button id="csvImportBtn" onclick="doCsvImport()" style="display:none;padding:10px 18px;background:var(--accent);color:#fff;border:none;border-radius:10px;font:700 14px 'Outfit',sans-serif;cursor:pointer">📥 Confirm & Import</button>
    </div>
  </div>
</div>

<!-- ══ ADD PRODUCT MODAL ══════════════════════════════════════ -->
<div class="mo" id="addProdMo">
  <div class="mbox" style="max-width:580px">
    <div class="mhead">
      <h3>➕ Add New Product</h3>
      <button class="mclose" onclick="closeAddProduct()">✕</button>
    </div>
    <div class="mbody">
      <div class="fr">
        <label>Product Name <span style="color:var(--red)">*</span></label>
        <input type="text" id="ap_name" placeholder="e.g. Samsung Galaxy S24 Ultra">
      </div>
      <div class="fr">
        <label>Brand</label>
        <input type="text" id="ap_brand" placeholder="e.g. Samsung">
      </div>
      <div class="fr2">
        <div class="fr" style="margin-bottom:0">
          <label>Price (₹) <span style="color:var(--red)">*</span></label>
          <input type="number" id="ap_price" placeholder="e.g. 49999" min="0" oninput="apCalcOff()">
        </div>
        <div class="fr" style="margin-bottom:0">
          <label>MRP (₹)</label>
          <input type="number" id="ap_mrp" placeholder="e.g. 59999" min="0" oninput="apCalcOff()">
        </div>
      </div>
      <div class="offbar" id="apOffBar" style="margin-top:10px"></div>
      <div class="fr2">
        <div class="fr" style="margin-bottom:0">
          <label>Category</label>
          <input type="text" id="ap_category" placeholder="e.g. Electronics">
        </div>
        <div class="fr" style="margin-bottom:0">
          <label>Subcategory</label>
          <input type="text" id="ap_subcategory" placeholder="e.g. Smartphones">
        </div>
      </div>
      <div class="fr2" style="margin-top:14px">
        <div class="fr" style="margin-bottom:0">
          <label>Badge <span style="color:var(--mut);font-weight:400;font-size:11px">(optional)</span></label>
          <input type="text" id="ap_badge" placeholder="e.g. New, Hot, Sale">
        </div>
        <div class="fr" style="margin-bottom:0">
          <label>Stock</label>
          <input type="number" id="ap_stock" value="100" min="0">
        </div>
      </div>
      <div class="fr" style="margin-top:14px">
        <label>Description</label>
        <textarea id="ap_desc" placeholder="Product description..." style="min-height:80px;resize:vertical"></textarea>
      </div>
      <div id="apResult" class="result"></div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeAddProduct()">Cancel</button>
      <button id="apSaveBtn" onclick="doAddProduct()" style="padding:9px 22px;background:var(--blue);color:#fff;border:none;border-radius:8px;font:600 14px 'Outfit',sans-serif;cursor:pointer">➕ Add Product</button>
    </div>
  </div>
</div>

<!-- ══ EDIT PRODUCT MODAL ════════════════════════════════════ -->
<div class="mo" id="editMo">
  <div class="mbox" style="max-width:600px">
    <div class="mhead">
      <h3>✏️ Edit Product — <span id="ePid" style="color:var(--accent);font-size:14px"></span></h3>
      <button class="mclose" onclick="closeMo('editMo')">✕</button>
    </div>
    <div class="mbody">
      <div class="fr">
        <label>Product Name</label>
        <input type="text" id="eName" placeholder="e.g. boAt Rockerz 450">
      </div>
      <div class="fr">
        <label>Brand</label>
        <input type="text" id="eBrand" placeholder="e.g. boAt">
      </div>
      <div class="fr2">
        <div class="fr" style="margin-bottom:0">
          <label>Sale Price (₹)</label>
          <input type="number" id="ePrice" placeholder="e.g. 1299" min="0" oninput="calcOff()">
        </div>
        <div class="fr" style="margin-bottom:0">
          <label>MRP (₹)</label>
          <input type="number" id="eMrp" placeholder="e.g. 2999" min="0" oninput="calcOff()">
        </div>
      </div>
      <div class="offbar" id="offBar" style="margin-top:10px;margin-bottom:14px"></div>
      <div class="fr">
        <label>Description</label>
        <textarea id="eDesc" placeholder="Product description..." style="min-height:100px;resize:vertical"></textarea>
      </div>
      <div style="margin-bottom:12px">
        <div style="font-size:12px;font-weight:600;color:var(--txt);margin-bottom:8px">Variants <span style="color:var(--mut);font-weight:400">(optional)</span></div>
        <div id="eVariants"></div>
        <button type="button" onclick="addVariantGroup()" style="background:none;border:1.5px dashed rgba(255,255,255,.15);color:var(--mut);border-radius:8px;padding:7px 14px;font-size:13px;cursor:pointer;width:100%;transition:all .18s" onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='rgba(255,255,255,.15)';this.style.color='var(--mut)'">+ Add Variant Group</button>
      </div>
      <div id="editResult" class="result"></div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeMo('editMo')">Cancel</button>
      <button id="saveBtn" onclick="saveProduct()" style="padding:9px 22px;background:var(--accent);color:#fff;border:none;border-radius:8px;font:600 14px 'Outfit',sans-serif;cursor:pointer">💾 Save</button>
    </div>
  </div>
</div>

<!-- ══ DELETE PRODUCT MODAL ══════════════════════════════════ -->
<div class="mo" id="deleteProdMo">
  <div class="mbox" style="max-width:400px">
    <div class="mhead" style="border-bottom:2px solid #fff0f0">
      <h3 style="color:#c0392b">🗑️ Delete Product</h3>
      <button class="mclose" onclick="closeMo('deleteProdMo')">✕</button>
    </div>
    <div class="mbody" style="text-align:center;padding:28px 24px">
      <div style="font-size:44px;margin-bottom:12px">⚠️</div>
      <div style="font-size:15px;font-weight:700;margin-bottom:8px">Are you sure?</div>
      <div style="font-size:13px;color:var(--mut);margin-bottom:20px">
        Product <strong id="delPidLabel" style="color:var(--red)"></strong> will be permanently removed from products.json. This cannot be undone.
      </div>
      <div id="delResult" class="result"></div>
    </div>
    <div class="mfoot" style="justify-content:center;gap:12px">
      <button class="btn-cancel" onclick="closeMo('deleteProdMo')" style="min-width:100px">Cancel</button>
      <button id="delConfirmBtn" onclick="doDeleteProduct()" style="min-width:120px;padding:9px 20px;background:#e74c3c;color:#fff;border:none;border-radius:8px;font:600 14px 'Outfit',sans-serif;cursor:pointer">🗑️ Yes, Delete</button>
    </div>
  </div>
</div>
</body>
</html>
