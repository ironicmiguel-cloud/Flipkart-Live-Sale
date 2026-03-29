<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Page Not Found – Flipkart</title>
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

/* Big 404 */
.num{font-size:96px;font-weight:800;color:#2874f0;line-height:1;letter-spacing:-4px;position:relative;display:inline-block;margin-bottom:8px}
.num .zero{position:relative;display:inline-block}
.num .zero svg{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%)}

/* Cart illustration */
.cart-wrap{margin:0 auto 28px;position:relative;width:120px;height:90px}
.cart-shake{animation:shake 2.5s ease-in-out infinite}
@keyframes shake{0%,100%{transform:rotate(0)}10%{transform:rotate(-4deg)}20%{transform:rotate(4deg)}30%{transform:rotate(-3deg)}40%{transform:rotate(3deg)}50%{transform:rotate(0)}}
.question{position:absolute;top:-8px;right:-4px;width:28px;height:28px;background:#f0a500;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;color:white;animation:bob 1.5s ease-in-out infinite}
@keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}

h1{font-size:20px;font-weight:700;color:#212121;margin-bottom:8px}
p{font-size:14px;color:#717171;line-height:1.6;max-width:280px;margin:0 auto 28px}
.btn-row{display:flex;flex-direction:column;gap:12px;width:100%;max-width:280px}
.btn-primary{background:#2874f0;color:white;border:none;border-radius:4px;padding:14px;font-size:15px;font-weight:600;cursor:pointer;text-decoration:none;display:block}
.btn-secondary{background:white;color:#2874f0;border:1.5px solid #2874f0;border-radius:4px;padding:13px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;display:block}
.links{margin-top:28px;display:flex;flex-wrap:wrap;gap:10px;justify-content:center}
.chip{background:white;border:1.5px solid #e0e0e0;border-radius:20px;padding:7px 16px;font-size:12px;color:#2874f0;text-decoration:none;font-weight:600}
.chip:hover{background:#e8f0fe}
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

    <!-- Cart illustration -->
    <div class="cart-wrap">
        <div class="cart-shake">
            <svg width="120" height="90" viewBox="0 0 120 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- cart body -->
                <path d="M10 20h8l14 40h56l10-32H30" stroke="#2874f0" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                <!-- wheels -->
                <circle cx="46" cy="68" r="8" fill="#2874f0"/>
                <circle cx="46" cy="68" r="4" fill="white"/>
                <circle cx="78" cy="68" r="8" fill="#2874f0"/>
                <circle cx="78" cy="68" r="4" fill="white"/>
                <!-- X mark on cart body -->
                <line x1="52" y1="36" x2="66" y2="50" stroke="#e53935" stroke-width="3" stroke-linecap="round"/>
                <line x1="66" y1="36" x2="52" y2="50" stroke="#e53935" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="question">?</div>
    </div>

    <!-- 404 number -->
    <div class="num">404</div>

    <h1>Page Not Found</h1>
    <p>The page you're looking for doesn't exist or has been moved.</p>

    <div class="btn-row">
        <a href="index.php" class="btn-primary"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Go to Homepage</a>
        <a href="javascript:goBackSmart('index.php')" class="btn-secondary"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg> Go Back</a>
    </div>

    <div class="links">
        <a href="search.php" class="chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg> Search Products</a>
        <a href="cart.php" class="chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 16.1 6.9 18 9 18h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 23.4 5H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg> My Cart</a>
        <a href="orders.php" class="chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27z"/></svg> My Orders</a>
        <a href="wishlist.php" class="chip"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Wishlist</a>
    </div>

    <div class="error-code">Error 404 · Page Not Found</div>
</div>

<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
