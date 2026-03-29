<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>My Cart – Flipkart</title>
    <meta name="description" content="Flipkart – cart page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --blue: #2874f0;
  --blue-light: #e8f0fe;
  --orange: #fb641b;
  --bg: #f1f3f6;
  --card: #fff;
  --border: #e0e0e0;
  --text: #212121;
  --muted: #878787;
  --green: #388e3c;
  --red: #d32f2f;
  --radius: 8px;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Noto Sans', sans-serif;
  background: var(--bg);
  color: var(--text);
  font-size: 14px;
  padding-bottom: 90px;
}

/* HEADER */
header {
  background: var(--blue);
  height: 56px;
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 12px;
  position: sticky;
  top: 0;
  z-index: 200;
  box-shadow: 0 2px 8px rgba(0,0,0,.15);
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

.h-title { color: #fff; font-size: 17px; font-weight: 600; flex: 1; }
.h-count {
  background: #fff;
  color: var(--blue);
  font-size: 12px;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 20px;
}

/* EMPTY CART */
.empty-cart {
  display: none;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}
.empty-cart .ec-icon { font-size: 80px; margin-bottom: 20px; animation: bounce 2s infinite; }
.empty-cart h2 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.empty-cart p { color: var(--muted); font-size: 14px; margin-bottom: 24px; }
.empty-cart button {
  background: var(--blue); color: #fff; border: none;
  border-radius: 6px; padding: 13px 32px;
  font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit;
  transition: .2s;
}
.empty-cart button:hover { background: #1a5fd0; }

@keyframes bounce {
  0%,100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* CART ITEMS */
.cart-item {
  background: var(--card);
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 14px;
  display: flex;
  gap: 12px;
  animation: fadeUp .3s ease both;
  position: relative;
}
.ci-thumb-wrap {
  width: 90px; height: 90px; flex-shrink: 0;
  background: #f8f8f8;
  border-radius: 8px;
  border: 1px solid #eee;
  display: flex; align-items: center; justify-content: center;
  font-size: 36px;
  overflow: hidden;
}
.ci-thumb-wrap img {
  width: 100%; height: 100%; object-fit: contain; padding: 4px;
}
.ci-details { flex: 1; min-width: 0; }
.ci-brand { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
.ci-name {
  font-size: 13.5px; font-weight: 600; color: var(--text);
  margin: 3px 0 6px;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.ci-price-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
.ci-price { font-size: 16px; font-weight: 700; }
.ci-mrp { font-size: 12px; color: var(--muted); text-decoration: line-through; }
.ci-off { font-size: 12px; color: var(--green); font-weight: 600; }

/* QTY CONTROLS */
.qty-row { display: flex; align-items: center; gap: 0; }
.qty-btn {
  width: 30px; height: 30px;
  border: 1.5px solid var(--border);
  background: #fafafa;
  font-size: 18px; font-weight: 600;
  cursor: pointer; color: var(--text);
  display: flex; align-items: center; justify-content: center;
  transition: .15s;
  border-radius: 0;
  font-family: inherit;
}
.qty-btn:first-child { border-radius: 6px 0 0 6px; }
.qty-btn:last-child { border-radius: 0 6px 6px 0; }
.qty-btn:hover { background: var(--blue-light); border-color: var(--blue); color: var(--blue); }
.qty-num {
  width: 36px; height: 30px;
  border-top: 1.5px solid var(--border);
  border-bottom: 1.5px solid var(--border);
  border-left: none; border-right: none;
  text-align: center; font-size: 14px; font-weight: 600;
  display: flex; align-items: center; justify-content: center;
  background: #fff;
}
.ci-remove {
  position: absolute; top: 10px; right: 12px;
  background: none; border: none;
  color: var(--muted); font-size: 18px; cursor: pointer;
  padding: 4px; border-radius: 50%; transition: .15s;
}
.ci-remove:hover { background: #fef2f2; color: var(--red); }

/* DELIVERY BADGE */
.ci-delivery {
  font-size: 11.5px; color: var(--green); margin-top: 8px;
  display: flex; align-items: center; gap: 4px;
}

/* PRICE SUMMARY */
.summary-card {
  background: var(--card);
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  overflow: hidden;
  animation: fadeUp .3s .1s ease both;
}
.summary-head {
  padding: 13px 16px 10px;
  font-size: 12px; font-weight: 700;
  color: var(--muted); text-transform: uppercase; letter-spacing: .07em;
  border-bottom: 1px solid var(--border);
  background: #fafafa;
}
.s-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 16px;
}
.s-row .s-lbl { color: var(--muted); }
.s-row .s-val { font-weight: 500; }
.s-row.s-saving .s-val { color: var(--green); }
.s-divider { border: none; border-top: 1px dashed #e0e0e0; margin: 4px 16px; }
.s-row.s-total {
  padding: 14px 16px; background: #fafafa; border-top: 1px solid var(--border);
}
.s-row.s-total .s-lbl { font-weight: 700; font-size: 15px; }
.s-row.s-total .s-val { font-weight: 800; font-size: 16px; }
.s-saving-msg {
  padding: 10px 16px;
  background: #f1fdf2;
  border-top: 1px solid #c8e6c9;
  font-size: 13px; color: var(--green); font-weight: 600;
  display: flex; align-items: center; gap: 6px;
}

/* COUPONS */
.coupon-card {
  background: var(--card);
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 14px 16px;
  display: flex; align-items: center; gap: 10px;
  cursor: pointer;
  transition: .15s;
  animation: fadeUp .3s .15s ease both;
}
.coupon-card:hover { background: var(--blue-light); }
.coupon-icon { font-size: 24px; }
.coupon-text { flex: 1; }
.coupon-text b { font-size: 13.5px; color: var(--text); display: block; }
.coupon-text span { font-size: 12px; color: var(--muted); }
.coupon-arrow { color: var(--muted); font-size: 18px; }

/* SAFE SHOPPING */
.safe-strip {
  background: var(--card);
  margin: 10px 12px 0;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  padding: 12px 16px;
  display: flex; align-items: center; gap: 10px;
  font-size: 12.5px; color: var(--muted);
  animation: fadeUp .3s .2s ease both;
}
.safe-strip .s-icons { display: flex; gap: 14px; margin-left: auto; font-size: 20px; }

/* FOOTER */
.footer {
  position: fixed; bottom: 0; width: 100%;
  background: #fff;
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 16px;
  border-top: 1px solid var(--border);
  box-shadow: 0 -2px 10px rgba(0,0,0,.07);
  z-index: 100;
}
.footer-total-lbl { font-size: 11px; color: var(--muted); }
.footer-total-val { font-size: 17px; font-weight: 800; color: var(--text); }
.checkout-btn {
  background: linear-gradient(135deg,#ff9800,#fb641b);
  border: none; padding: 13px 28px;
  font-weight: 700; font-size: 15px; color: #fff;
  border-radius: var(--radius); cursor: pointer;
  font-family: inherit; letter-spacing: .03em;
  box-shadow: 0 3px 8px rgba(251,100,27,.35);
  transition: .2s;
}
.checkout-btn:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(251,100,27,.4); }

/* TOAST */
.toast {
  position: fixed; bottom: 90px; left: 50%;
  transform: translateX(-50%);
  background: #323232; color: white;
  padding: 10px 20px; border-radius: 24px;
  font-size: 13px; z-index: 9999;
  opacity: 0; transition: opacity .3s;
  pointer-events: none; white-space: nowrap;
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

<header>
  <button class="h-back" onclick="goBackSmart('index.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <span class="h-title">My Cart</span>
  <span class="h-count" id="headerCount">0 items</span>
</header>

<!-- EMPTY STATE -->
<div class="empty-cart" id="emptyCart">
  <div class="ec-icon"><svg viewBox="0 0 24 24" width="64" height="64" fill="#bdbdbd"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 16.1 6.9 18 9 18h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 23.4 5H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg></div>
  <h2>Your cart is empty!</h2>
  <p>Add items to get started. Explore thousands<br>of products on Flipkart.</p>
  <button onclick="window.location.href='index.php'">Shop Now</button>
</div>

<!-- CART ITEMS CONTAINER -->
<div id="cartItemsContainer"></div>

<!-- COUPONS -->
<div class="coupon-card" id="couponCard" style="display:none;" onclick="applyCoupon()">
  <span class="coupon-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="#2874f0"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg></span>
  <div class="coupon-text">
    <b>Apply Coupons</b>
    <span id="couponLabel">Save extra with a coupon</span>
  </div>
  <span class="coupon-arrow">›</span>
</div>

<!-- PRICE SUMMARY -->
<div class="summary-card" id="summaryCard" style="display:none;">
  <div class="summary-head">Price Details</div>
  <div class="s-row">
    <span class="s-lbl">Price (<span id="sItemCount">0</span> items)</span>
    <span class="s-val" id="sMrpTotal">₹0</span>
  </div>
  <div class="s-row s-saving">
    <span class="s-lbl">Discount</span>
    <span class="s-val" id="sDiscount">-₹0</span>
  </div>
  <div class="s-row s-saving">
    <span class="s-lbl">Delivery Charges</span>
    <span class="s-val">FREE <s style="color:#bbb;font-weight:400;font-size:12px;">₹40</s></span>
  </div>
  <hr class="s-divider">
  <div class="s-row s-total">
    <span class="s-lbl">Total Amount</span>
    <span class="s-val" id="sTotal">₹0</span>
  </div>
  <div class="s-saving-msg" id="sSavingMsg">
    <svg viewBox="0 0 24 24" width="14" height="14" fill="#388e3c" style="vertical-align:-2px;margin-right:3px"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z"/></svg> You are saving <span id="sSavingAmt">₹0</span> on this order!
  </div>
</div>

<!-- SAFE SHOPPING -->
<div class="safe-strip" id="safeStrip" style="display:none;">
  <span><svg viewBox="0 0 24 24" width="13" height="13" fill="#666" style="vertical-align:-2px;margin-right:3px"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>Safe and Secure Payments. Easy returns. 100% Authentic products.</span>
  <div class="s-icons"><svg viewBox="0 0 24 24" width="22" height="22" fill="#666" style="margin:0 3px"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg><svg viewBox="0 0 24 24" width="22" height="22" fill="#666" style="margin:0 3px"><path d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zm-3 13h10l1-2H2l1 2zm13-13v7h3v-7h-3zm-4.5-9L2 6v2h20V6L12.5 1z"/></svg><svg viewBox="0 0 24 24" width="22" height="22" fill="#666" style="margin:0 3px"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg></div>
</div>

<!-- FOOTER -->
<div class="footer" id="cartFooter" style="display:none;">
  <div>
    <div class="footer-total-lbl">Total Payable</div>
    <div class="footer-total-val" id="footerTotal">₹0</div>
  </div>
  <button class="checkout-btn" onclick="checkout()">PLACE ORDER</button>
</div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════════════
//  CART DATA  (stored in localStorage)
// ══════════════════════════════════════════════
function normalizeCart(items) {
  return (Array.isArray(items) ? items : []).map(function(item){
    if (!item || !item.id) return null;
    var price = parseFloat(item.price || 0) || 0;
    var mrp = parseFloat(item.mrp || item.price || 0) || price;
    var qty = Math.max(1, Math.min(10, parseInt(item.qty || 1, 10) || 1));
    return Object.assign({}, item, { price: price, mrp: mrp, qty: qty });
  }).filter(Boolean);
}
function getCart() {
  try {
    const raw = (window.FK && FK.getCart) ? FK.getCart() : JSON.parse(localStorage.getItem('flipkart_cart') || '[]');
    return normalizeCart(raw);
  } catch(e) { return []; }
}
function saveCart(cart) {
  cart = normalizeCart(cart);
  if (window.FK && FK._mirrorState && FK.replaceState) { FK._mirrorState('cart', cart); FK.replaceState('cart', cart).catch(function(){}); }
  else { localStorage.setItem('flipkart_cart', JSON.stringify(cart)); }
}

// ══════════════════════════════════════════════
//  RENDER
// ══════════════════════════════════════════════
function render() {
  const cart = getCart();
  const persistedRaw = (window.FK && FK.getCart) ? FK.getCart() : (function(){ try { return JSON.parse(localStorage.getItem('flipkart_cart') || '[]'); } catch(e){ return []; } })();
  if (Array.isArray(persistedRaw) && JSON.stringify(normalizeCart(persistedRaw)) !== JSON.stringify(cart)) {
    saveCart(cart);
  }
  const container = document.getElementById('cartItemsContainer');
  const emptyCart = document.getElementById('emptyCart');
  const summaryCard = document.getElementById('summaryCard');
  const couponCard = document.getElementById('couponCard');
  const safeStrip = document.getElementById('safeStrip');
  const cartFooter = document.getElementById('cartFooter');

  container.innerHTML = '';

  if (cart.length === 0) {
    emptyCart.style.display = 'flex';
    summaryCard.style.display = 'none';
    couponCard.style.display = 'none';
    safeStrip.style.display = 'none';
    cartFooter.style.display = 'none';
    document.getElementById('headerCount').textContent = '0 items';
    return;
  }

  emptyCart.style.display = 'none';
  summaryCard.style.display = 'block';
  couponCard.style.display = 'flex';
  safeStrip.style.display = 'flex';
  cartFooter.style.display = 'flex';

  let totalQty = 0, totalMrp = 0, totalPrice = 0;

  cart.forEach((item, idx) => {
    totalQty += item.qty;
    totalMrp   += (item.mrp   || item.price) * item.qty;
    totalPrice += item.price * item.qty;

    const div = document.createElement('div');
    div.className = 'cart-item';
    div.style.animationDelay = (idx * 0.05) + 's';
    const offPct = item.mrp > item.price ? Math.round((1 - item.price/item.mrp)*100) : 0;

    div.innerHTML = `
      <div class="ci-thumb-wrap" id="thumb-${idx}">
        ${item.img
          ? `<img src="${item.img}" alt="${item.name}" onerror="this.parentElement.innerHTML='<svg viewBox=\"0 0 24 24\" width=\"40\" height=\"40\" fill=\"#e0e0e0\"><path d=\"M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z\"/></svg>'">`
          : '<svg viewBox="0 0 24 24" width="40" height="40" fill="#e0e0e0"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>'}
      </div>
      <div class="ci-details">
        <div class="ci-brand">${esc(item.brand || '')}</div>
        <div class="ci-name">${esc(item.name)}</div>
        <div class="ci-price-row">
          <span class="ci-price">₹${item.price.toLocaleString('en-IN')}</span>
          ${item.mrp > item.price ? `<span class="ci-mrp">₹${item.mrp.toLocaleString('en-IN')}</span>` : ''}
          ${offPct > 0 ? `<span class="ci-off">${offPct}% off</span>` : ''}
        </div>
        <div class="qty-row">
          <button class="qty-btn" onclick="changeQty(${idx}, -1)">−</button>
          <div class="qty-num">${item.qty}</div>
          <button class="qty-btn" onclick="changeQty(${idx}, 1)">+</button>
        </div>
        <div class="ci-delivery"><svg viewBox="0 0 24 24" width="14" height="14" fill="#388e3c" style="vertical-align:-2px;margin-right:3px"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z"/></svg> Free delivery by Tomorrow</div>
      </div>
      <button class="ci-remove" onclick="removeItem(${idx})" title="Remove"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
    `;
    container.appendChild(div);
  });

  // Summary
  const discount = totalMrp - totalPrice;
  document.getElementById('headerCount').textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
  document.getElementById('sItemCount').textContent = totalQty;
  document.getElementById('sMrpTotal').textContent = '₹' + totalMrp.toLocaleString('en-IN');
  document.getElementById('sDiscount').textContent = '-₹' + discount.toLocaleString('en-IN');
  document.getElementById('sTotal').textContent = '₹' + totalPrice.toLocaleString('en-IN');
  document.getElementById('sSavingAmt').textContent = '₹' + discount.toLocaleString('en-IN');
  document.getElementById('footerTotal').textContent = '₹' + totalPrice.toLocaleString('en-IN');

  if (discount <= 0) document.getElementById('sSavingMsg').style.display = 'none';
}

function changeQty(idx, delta) {
  const cart = getCart();
  const item = cart[idx];
  if (!item) return;
  item.qty = Math.max(1, Math.min(10, item.qty + delta));
  saveCart(cart);
  render();
}

function removeItem(idx) {
  const cart = getCart();
  const name = cart[idx].name.substring(0, 20);
  cart.splice(idx, 1);
  saveCart(cart);
  render();
  showToast('🗑️ Removed: ' + name + '...');
}

let couponApplied = false;
function applyCoupon() {
  if (couponApplied) { showToast('✅ Coupon already applied!'); return; }
  couponApplied = true;
  document.getElementById('couponLabel').textContent = '✓ SAVE10 applied — ₹10 off!';
  showToast('🎉 Coupon SAVE10 applied!');
}

// ══════════════════════════════════════════════
//  CHECKOUT  →  Save full cart total & go to payment
// ══════════════════════════════════════════════
function checkout() {
  const cart = getCart();
  const total = cart.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseInt(item.qty,10) || 1)), 0);
  if (!cart.length || total <= 0) {
    showToast('Cart is empty');
    render();
    return;
  }
  if (window.FK && FK.prepareCheckout) {
    FK.prepareCheckout(cart);
  }
  showToast('Proceeding to payment...');
  setTimeout(() => { window.location.href = 'address.php'; }, 600);
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

// ══════════════════════════════════════════════
//  ADD TO CART helper (called from product.php)
// ══════════════════════════════════════════════
window.addToCart = function(item) {
  if (window.FK && FK.addToCart) {
    FK.addToCart(item).then(function(){ render(); });
    return;
  }
  const cart = getCart();
  const existing = cart.find(c => c.id === item.id);
  if (existing) { existing.qty = Math.min(10, existing.qty + 1); }
  else { cart.push({ ...item, qty: 1 }); }
  saveCart(cart);
};

document.addEventListener('fk:cart-sync', render);
document.addEventListener('fk:ready', render);
window.addEventListener('pageshow', render);
render();
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
