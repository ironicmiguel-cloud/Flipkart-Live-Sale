<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Oops! Something went wrong – Flipkart</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Noto Sans',sans-serif;background:#f1f3f6;min-height:100vh;display:flex;flex-direction:column}
.header{background:#2874f0;padding:0 16px;height:56px;display:flex;align-items:center;gap:10px}
.header-logo{color:white;font-size:20px;font-weight:800;font-style:italic;letter-spacing:-.5px}
.header-logo span{color:#f0a500}
.wrap{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 24px;text-align:center}

/* Animated illustration */
.illus{position:relative;width:220px;height:220px;margin:0 auto 32px}
.phone{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:80px;height:130px;background:white;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.15);display:flex;align-items:center;justify-content:center}
.phone-alert{width:44px;height:44px;border-radius:50%;background:#fff3f3;border:3px solid #e53935;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:#e53935;animation:pulse 1.8s ease-in-out infinite}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.12)}}
.gear-wrap{position:absolute;left:18px;top:55px;animation:spin 4s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.crane-arm{position:absolute;right:20px;top:30px}
.bounce-box{animation:bounce 2s ease-in-out infinite}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
.tool-box{position:absolute;left:22px;bottom:40px}
.spark{position:absolute;top:38px;left:56px;animation:spark 1.2s ease-in-out infinite}
@keyframes spark{0%,100%{opacity:0;transform:scale(0.5)}50%{opacity:1;transform:scale(1)}}

h1{font-size:22px;font-weight:700;color:#212121;margin-bottom:10px}
p{font-size:14px;color:#717171;line-height:1.6;max-width:280px;margin:0 auto 28px}
.btn-row{display:flex;flex-direction:column;gap:12px;width:100%;max-width:280px}
.btn-primary{background:#2874f0;color:white;border:none;border-radius:4px;padding:14px;font-size:15px;font-weight:600;cursor:pointer;text-decoration:none;display:block}
.btn-secondary{background:white;color:#2874f0;border:1.5px solid #2874f0;border-radius:4px;padding:13px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:block}
.error-code{font-size:11px;color:#bbb;margin-top:24px}

/* ══ MOBILE RESPONSIVE FIX ══ */
html, body {
    max-width: 100% !important;
    overflow-x: hidden !important;
    width: 100% !important;
}
*, *::before, *::after {
    box-sizing: border-box !important;
    max-width: 100% !important;
}
img, video, iframe, table {
    max-width: 100% !important;
    height: auto;
}
input, select, textarea, button {
    max-width: 100% !important;
}
</style>
    <link rel="stylesheet" href="assets/shared.css?v=20260320">
</head>
<body data-fk-sync="auth">

<div class="header">
    <div class="header-logo">flip<span>kart</span></div>
</div>

<div class="wrap">
    <!-- Illustration -->
    <div class="illus">
        <!-- Yellow toolbox -->
        <div class="tool-box">
            <svg width="52" height="52" viewBox="0 0 52 52" fill="none">
                <rect x="4" y="22" width="44" height="28" rx="4" fill="#f5c518"/>
                <rect x="4" y="22" width="44" height="8" rx="4" fill="#e0a800"/>
                <rect x="20" y="14" width="12" height="12" rx="2" fill="none" stroke="#e0a800" stroke-width="3"/>
                <!-- gear icon on box -->
                <circle cx="26" cy="34" r="6" fill="#e0a800"/>
                <circle cx="26" cy="34" r="3" fill="#f5c518"/>
            </svg>
        </div>
        <!-- Tools (wrench + screwdriver) -->
        <svg style="position:absolute;left:28px;top:42px" width="28" height="36" viewBox="0 0 28 36" fill="none">
            <!-- wrench -->
            <rect x="5" y="4" width="5" height="22" rx="2.5" fill="#e53935"/>
            <rect x="3" y="4" width="9" height="5" rx="2" fill="#e53935"/>
            <!-- screwdriver -->
            <rect x="16" y="2" width="4" height="26" rx="2" fill="#2874f0"/>
            <rect x="15" y="24" width="6" height="4" rx="1" fill="#1a5fcc"/>
            <polygon points="16,28 20,28 18,34" fill="#888"/>
        </svg>
        <!-- Phone -->
        <div class="phone">
            <div class="phone-alert">!</div>
        </div>
        <!-- Crane arm (right) -->
        <div class="crane-arm">
            <svg width="50" height="80" viewBox="0 0 50 80" fill="none">
                <div class="bounce-box">
                    <rect x="30" y="0" width="18" height="18" rx="3" fill="#f5c518"/>
                    <rect x="36" y="18" width="6" height="2" fill="#e0a800"/>
                </div>
                <rect x="38" y="20" width="4" height="50" rx="2" fill="#f5c518"/>
                <rect x="0" y="26" width="42" height="4" rx="2" fill="#e53935"/>
                <circle cx="6" cy="28" r="5" fill="none" stroke="#2874f0" stroke-width="3"/>
            </svg>
        </div>
        <!-- Spark -->
        <div class="spark">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <line x1="9" y1="0" x2="9" y2="6" stroke="#f5c518" stroke-width="2" stroke-linecap="round"/>
                <line x1="9" y1="12" x2="9" y2="18" stroke="#f5c518" stroke-width="2" stroke-linecap="round"/>
                <line x1="0" y1="9" x2="6" y2="9" stroke="#f5c518" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="9" x2="18" y2="9" stroke="#f5c518" stroke-width="2" stroke-linecap="round"/>
                <line x1="2" y1="2" x2="6" y2="6" stroke="#f5c518" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="12" x2="16" y2="16" stroke="#f5c518" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <!-- Gear (spinning) -->
        <div class="gear-wrap">
            <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <path d="M18 10a8 8 0 1 0 0 16 8 8 0 0 0 0-16z" fill="none" stroke="#2874f0" stroke-width="2.5"/>
                <circle cx="18" cy="18" r="3.5" fill="#2874f0"/>
                <rect x="16" y="3" width="4" height="6" rx="2" fill="#2874f0"/>
                <rect x="16" y="27" width="4" height="6" rx="2" fill="#2874f0"/>
                <rect x="3" y="16" width="6" height="4" rx="2" fill="#2874f0"/>
                <rect x="27" y="16" width="6" height="4" rx="2" fill="#2874f0"/>
            </svg>
        </div>
        <!-- Red/blue triangles bottom -->
        <svg style="position:absolute;bottom:20px;right:14px" width="28" height="20" viewBox="0 0 28 20" fill="none">
            <polygon points="0,20 14,0 28,20" fill="#e53935" opacity="0.7"/>
            <polygon points="0,20 10,8 20,20" fill="#2874f0" opacity="0.6"/>
        </svg>
    </div>

    <h1>Oops! Something went wrong</h1>
    <p>We are fixing it. Please come back soon.</p>

    <div class="btn-row">
        <a href="index.php" class="btn-primary">Go to Homepage</a>
        <a href="javascript:goBackSmart('index.php')" class="btn-secondary"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Go Back</a>
    </div>
    <div class="error-code">Error 500 · Internal Server Error</div>
</div>

<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
