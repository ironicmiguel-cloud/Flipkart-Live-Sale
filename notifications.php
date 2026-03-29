<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Notifications — Flipkart</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Noto Sans',sans-serif;background:#f1f3f6;min-height:100vh}

/* Header */
.header{background:#2874f0;padding:0 16px;height:56px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:100}

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
.header-title{color:white;font-size:17px;font-weight:700;flex:1}
.header-btn{background:none;border:none;color:white;font-size:12px;font-weight:600;cursor:pointer;padding:6px 10px;border-radius:4px;opacity:.9}
.header-btn:hover{background:rgba(255,255,255,.15)}

/* Filter Tabs */
.filter-tabs{background:white;display:flex;gap:0;overflow-x:auto;border-bottom:1px solid #e8eaed;padding:0 4px}
.filter-tabs::-webkit-scrollbar{display:none}
.ftab{padding:13px 16px;font-size:13px;font-weight:600;color:#717171;cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:all .2s}
.ftab.active{color:#2874f0;border-bottom-color:#2874f0}

/* Notification List */
.notif-list{padding:10px 12px;display:flex;flex-direction:column;gap:8px}

.notif-card{background:white;border-radius:10px;padding:14px 16px;display:flex;gap:13px;align-items:flex-start;cursor:pointer;transition:box-shadow .15s;position:relative;border:1.5px solid #f0f0f0}
.notif-card:hover{box-shadow:0 2px 12px rgba(0,0,0,.1)}
.notif-card.unread{border-left:3px solid #2874f0;background:#fafcff}
.notif-card.unread::after{content:'';position:absolute;top:14px;right:14px;width:8px;height:8px;background:#2874f0;border-radius:50%}

.notif-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.notif-icon.offer  {background:#fff3e0}
.notif-icon.order  {background:#e8f5e9}
.notif-icon.promo  {background:#fce4ec}
.notif-icon.price  {background:#e3f2fd}
.notif-icon.account{background:#f3e5f5}
.notif-icon.flash  {background:#fff8e1}

.notif-body{flex:1;min-width:0}
.notif-title{font-size:13px;font-weight:700;color:#212121;margin-bottom:3px;line-height:1.3}
.notif-desc{font-size:12px;color:#555;line-height:1.45;margin-bottom:6px}
.notif-time{font-size:11px;color:#9e9e9e;font-weight:500}
.notif-tag{display:inline-block;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;margin-right:6px;margin-bottom:4px}
.notif-tag.sale  {background:#fce4ec;color:#c62828}
.notif-tag.saved {background:#e8f5e9;color:#2e7d32}
.notif-tag.new   {background:#e3f2fd;color:#1565c0}

/* CTA button inside notif */
.notif-cta{display:inline-block;margin-top:6px;padding:5px 14px;background:#2874f0;color:white;border-radius:4px;font-size:11px;font-weight:700;text-decoration:none}
.notif-cta.outline{background:white;color:#2874f0;border:1.5px solid #2874f0}

/* Divider label */
.notif-date-label{font-size:11px;font-weight:700;color:#9e9e9e;padding:8px 4px 4px;text-transform:uppercase;letter-spacing:.5px}

/* Empty state */
.empty-state{display:none;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;text-align:center}
.empty-state.show{display:flex}
.empty-emoji{font-size:56px;margin-bottom:16px}
.empty-title{font-size:16px;font-weight:700;color:#212121;margin-bottom:8px}
.empty-desc{font-size:13px;color:#888;line-height:1.5;max-width:240px}

/* Swipe-to-dismiss hint */
.swipe-hint{text-align:center;font-size:11px;color:#bbb;padding:12px;margin-top:4px}

/* Bottom nav */
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:white;border-top:1px solid #e8eaed;display:flex;height:56px;z-index:100}
.bnav-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;text-decoration:none;color:#717171;font-size:10px;font-weight:500}
.bnav-item.active{color:#2874f0}
.bnav-item svg{width:22px;height:22px;fill:currentColor}
.page-pad{height:70px}

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

<!-- Header -->
<div class="header">
  <button class="header-back" onclick="goBackSmart('index.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <div class="header-title">Notifications</div>
  <button class="header-btn" onclick="markAllRead()">Mark all read</button>
  <button class="header-btn" onclick="clearAll()" style="opacity:.7">Clear</button>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
  <div class="ftab active" onclick="filterTab(this,'all')">All</div>
  <div class="ftab" onclick="filterTab(this,'orders')">Orders</div>
  <div class="ftab" onclick="filterTab(this,'offers')">Offers</div>
  <div class="ftab" onclick="filterTab(this,'price')">Price Drops</div>
  <div class="ftab" onclick="filterTab(this,'account')">Account</div>
</div>

<!-- Notification List -->
<div class="notif-list" id="notifList">

  <div class="notif-date-label">Today</div>

  <div class="notif-card unread" data-cat="offers" onclick="openNotif(this,'search.php')">
    <div class="notif-icon flash"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Flash Sale — Up to 80% Off Electronics!</div>
      <div class="notif-desc">Grab deals on mobiles, laptops, earbuds and more. Sale ends in <b>2 hours</b>. Don't miss out!</div>
      <span class="notif-tag sale">FLASH SALE</span>
      <div class="notif-time">Just now</div>
      <a class="notif-cta" href="search.php">Shop Now</a>
    </div>
  </div>

  <div class="notif-card unread" data-cat="price" onclick="openNotif(this,'search.php?q=mobiles')">
    <div class="notif-icon price"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Price Drop Alert! Mobiles from ₹6,999</div>
      <div class="notif-desc">Smartphones you wishlisted have dropped in price. Check them before stock runs out.</div>
      <span class="notif-tag saved">PRICE DROP</span>
      <div class="notif-time">15 min ago</div>
      <a class="notif-cta outline" href="search.php?q=mobiles">View Deals</a>
    </div>
  </div>

  <div class="notif-card unread" data-cat="offers" onclick="openNotif(this,'search.php')">
    <div class="notif-icon offer"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 6h-2.18c.07-.28.18-.51.18-.8C18 3.88 16.12 2 13.8 2c-1.13 0-2.08.49-2.77 1.26L10 4.54l-1.03-1.28C8.28 2.49 7.33 2 6.2 2 3.88 2 2 3.88 2 6.2c0 .3.11.52.18.8H0v14h20v-14zm-9.5 11H4V8h6.5v9zM6.2 4c1 0 1.9.68 2.2 1.69L9 7H6.2C5.11 7 4.2 6.09 4.2 5s.91-1 2-1zm7.6 0c1.1 0 2 .9 2 2S14.9 7 13.8 7H11l.6-1.31C11.9 4.68 12.8 4 13.8 4zM20 18h-6.5V8H20v10z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">New Deals Every Hour — Tick Tock!</div>
      <div class="notif-desc">New hourly deals are live. Each deal unlocks for just 60 minutes — be quick!</div>
      <span class="notif-tag new">NEW</span>
      <div class="notif-time">1 hour ago</div>
    </div>
  </div>

  <div class="notif-date-label" style="margin-top:8px">Yesterday</div>

  <div class="notif-card" data-cat="offers" onclick="openNotif(this,'search.php')">
    <div class="notif-icon promo"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Big Saving Days — Everything up to 90% Off</div>
      <div class="notif-desc">Appliances, Fashion, Electronics — massive discounts with GP Rewards + HDFC 10% off.</div>
      <span class="notif-tag sale">BIG SALE</span>
      <div class="notif-time">Yesterday, 10:30 AM</div>
      <a class="notif-cta" href="search.php">Explore</a>
    </div>
  </div>

  <div class="notif-card" data-cat="price" onclick="openNotif(this,'search.php?q=electronics')">
    <div class="notif-icon price"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Apple Watch Series 9 — Price Reduced!</div>
      <div class="notif-desc">Now at ₹26,999 (was ₹44,900). 40% savings on your shortlisted product.</div>
      <span class="notif-tag saved">40% OFF</span>
      <div class="notif-time">Yesterday, 8:15 AM</div>
    </div>
  </div>

  <div class="notif-card" data-cat="orders" onclick="openNotif(this,'orders.php')">
    <div class="notif-icon order"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Your order has been placed successfully!</div>
      <div class="notif-desc">Thank you for shopping with us. Your order will be processed and shipped shortly. Track your delivery in Orders.</div>
      <span class="notif-tag new">ORDER</span>
      <div class="notif-time">Yesterday, 2:00 PM</div>
      <a class="notif-cta outline" href="orders.php">Track Order</a>
    </div>
  </div>

  <div class="notif-date-label" style="margin-top:8px">Earlier</div>

  <div class="notif-card" data-cat="offers" onclick="openNotif(this,'search.php?q=fashion')">
    <div class="notif-icon offer"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16 2l-4.75 4.75L8 4 2 8l4 4-4 8h6v-4h4v4h6l-4-8 4-4-2.18-1.55zM12 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Fashion Sale — Tops, Kurtas, Jeans from ₹199</div>
      <div class="notif-desc">Style up without breaking the bank. Trending brands at incredible prices.</div>
      <div class="notif-time">2 days ago</div>
    </div>
  </div>

  <div class="notif-card" data-cat="account" onclick="openNotif(this,'login.php')">
    <div class="notif-icon account"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Account Security Tip</div>
      <div class="notif-desc">Keep your account safe — never share your OTP or password with anyone, including Flipkart agents.</div>
      <div class="notif-time">3 days ago</div>
    </div>
  </div>

  <div class="notif-card" data-cat="offers" onclick="openNotif(this,'search.php?q=beauty')">
    <div class="notif-icon promo"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M9.5 11L7 6 4.5 11v9.5C4.5 21.33 5.17 22 6 22h2c.83 0 1.5-.67 1.5-1.5V11zm5.5-9v3h-1v1.5c0 1.93 1.06 3.62 2.62 4.5L16 22h2l-.62-12C19 9.12 20 7.43 20 5.5V4h-1V1h-4z"/></svg></div>
    <div class="notif-body">
      <div class="notif-title">Beauty & Personal Care — Min 50% Off</div>
      <div class="notif-desc">Top brands in skincare, makeup and grooming on sale. Explore the beauty festival.</div>
      <div class="notif-time">4 days ago</div>
    </div>
  </div>

  <div class="swipe-hint">You're all caught up <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></div>
</div>

<!-- Empty state (shown when all cleared) -->
<div class="empty-state" id="emptyState">
  <div class="empty-emoji"><svg viewBox="0 0 24 24" width="48" height="48" fill="#bdbdbd"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg></div>
  <div class="empty-title">No Notifications</div>
  <div class="empty-desc">You're all caught up! New deals, order updates and offers will appear here.</div>
</div>

<div class="page-pad"></div>

<!-- Bottom Nav -->
<nav class="bottom-nav">
  <a href="index.php" class="bnav-item">
    <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>Home
  </a>
  <a href="search.php" class="bnav-item">
    <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>Search
  </a>
  <a href="wishlist.php" class="bnav-item">
    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>Wishlist
  </a>
  <a href="login.php" class="bnav-item">
    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>Account
  </a>
</nav>

<script>
// ── Notifications logic ──────────────────────────────────────

var currentFilter = 'all';

function filterTab(el, cat) {
  document.querySelectorAll('.ftab').forEach(function(t){ t.classList.remove('active'); });
  el.classList.add('active');
  currentFilter = cat;
  var cards = document.querySelectorAll('.notif-card');
  var visible = 0;
  var labels = document.querySelectorAll('.notif-date-label');
  // Hide all labels first
  labels.forEach(function(l){ l.style.display = 'none'; });
  cards.forEach(function(c) {
    var match = cat === 'all' || c.dataset.cat === cat;
    c.style.display = match ? 'flex' : 'none';
    if (match) visible++;
  });
  // Show date labels only if adjacent cards are visible
  labels.forEach(function(label) {
    // Check if any following sibling card is visible before next label
    var next = label.nextElementSibling;
    var hasVisible = false;
    while (next && !next.classList.contains('notif-date-label') && !next.classList.contains('swipe-hint')) {
      if (next.style.display !== 'none') { hasVisible = true; break; }
      next = next.nextElementSibling;
    }
    label.style.display = hasVisible ? 'block' : 'none';
  });
  document.getElementById('emptyState').className = visible === 0 ? 'empty-state show' : 'empty-state';
}

function openNotif(card, url) {
  card.classList.remove('unread');
  updateBadge();
  if (url) setTimeout(function(){ window.location.href = url; }, 120);
}

function markAllRead() {
  document.querySelectorAll('.notif-card.unread').forEach(function(c){ c.classList.remove('unread'); });
  updateBadge();
  showSnack('All notifications marked as read');
}

function clearAll() {
  var cards = document.querySelectorAll('.notif-card');
  cards.forEach(function(c){ c.style.display='none'; });
  document.querySelectorAll('.notif-date-label,.swipe-hint').forEach(function(e){ e.style.display='none'; });
  document.getElementById('emptyState').className = 'empty-state show';
  showSnack('Notifications cleared');
}

function updateBadge() {
  var unread = document.querySelectorAll('.notif-card.unread').length;
  // Store in localStorage so index.php can read it
  localStorage.setItem('fk_unread_notif', unread);
}

function showSnack(msg) {
  var s = document.createElement('div');
  s.textContent = msg;
  s.style.cssText = 'position:fixed;bottom:70px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:10px 20px;border-radius:6px;font-size:13px;z-index:9999;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,.3)';
  document.body.appendChild(s);
  setTimeout(function(){ s.remove(); }, 2200);
}

// Init badge count
updateBadge();
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
