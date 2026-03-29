<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>My Profile – Flipkart</title>
    <meta name="description" content="Flipkart – profile page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --blue:#2874f0;--orange:#fb641b;--green:#388e3c;
  --red:#d32f2f;--bg:#f1f3f6;--card:#fff;
  --border:#e0e0e0;--text:#212121;--muted:#878787;--radius:10px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Noto Sans',sans-serif;background:var(--bg);color:var(--text);font-size:14px;padding-bottom:40px;}

/* HEADER */
header{
  background:var(--blue);height:56px;
  display:flex;align-items:center;padding:0 16px;gap:12px;
  position:sticky;top:0;z-index:200;
  box-shadow:0 2px 8px rgba(0,0,0,.15);
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

.h-title{color:#fff;font-size:17px;font-weight:600;flex:1;}
.h-edit{background:none;border:none;color:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;padding:6px 12px;border:1.5px solid rgba(255,255,255,.5);border-radius:6px;}
.h-edit:hover{background:rgba(255,255,255,.15);}

/* HERO BANNER */
.profile-hero{
  background:linear-gradient(160deg,#1a56db,#2874f0 50%,#1565c0);
  padding:24px 16px 60px;
  position:relative;overflow:hidden;
}
.profile-hero::before{content:'';position:absolute;top:-80px;right:-80px;width:220px;height:220px;background:rgba(255,255,255,.06);border-radius:50%;}
.profile-hero::after{content:'';position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;background:rgba(255,255,255,.04);border-radius:50%;}

/* AVATAR */
.avatar-wrap{position:relative;display:inline-block;margin-bottom:12px;}
.avatar{
  width:80px;height:80px;border-radius:50%;
  background:linear-gradient(135deg,#ff9800,#fb641b);
  display:flex;align-items:center;justify-content:center;
  font-size:32px;font-weight:700;color:#fff;
  border:3px solid rgba(255,255,255,.4);
  box-shadow:0 4px 16px rgba(0,0,0,.2);
  cursor:pointer;overflow:hidden;
}
.avatar img{width:100%;height:100%;object-fit:cover;}
.avatar-edit{
  position:absolute;bottom:2px;right:2px;
  background:#fff;border-radius:50%;
  width:24px;height:24px;display:flex;align-items:center;justify-content:center;
  font-size:12px;cursor:pointer;border:none;
  box-shadow:0 1px 4px rgba(0,0,0,.2);
}
.hero-name{color:#fff;font-size:20px;font-weight:700;margin-bottom:3px;}
.hero-email{color:rgba(255,255,255,.8);font-size:13px;margin-bottom:4px;}
.hero-joined{color:rgba(255,255,255,.6);font-size:12px;}
.plus-badge{
  display:inline-flex;align-items:center;gap:5px;
  background:linear-gradient(90deg,#ff9800,#fb641b);
  color:#fff;font-size:11px;font-weight:700;
  padding:3px 10px;border-radius:20px;margin-top:8px;
  letter-spacing:.04em;
}

/* STATS CARDS */
.stats-row{
  display:flex;gap:10px;
  margin:-36px 12px 0;
  position:relative;z-index:10;
}
.stat-card{
  flex:1;background:var(--card);border-radius:var(--radius);
  border:1px solid var(--border);padding:14px 8px;
  text-align:center;cursor:pointer;transition:.2s;
  box-shadow:0 2px 10px rgba(0,0,0,.08);
  animation:fadeUp .3s ease both;
}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.12);}
.stat-num{font-size:22px;font-weight:800;color:var(--blue);margin-bottom:3px;}
.stat-lbl{font-size:11px;color:var(--muted);font-weight:500;}

/* SECTION */
.section{
  background:var(--card);margin:12px 12px 0;
  border-radius:var(--radius);border:1px solid var(--border);
  overflow:hidden;animation:fadeUp .3s .05s ease both;
}
.sec-head{
  padding:12px 16px;font-size:12px;font-weight:700;
  color:var(--muted);text-transform:uppercase;letter-spacing:.07em;
  border-bottom:1px solid var(--border);background:#fafafa;
}

/* MENU ITEMS */
.menu-item{
  display:flex;align-items:center;gap:12px;
  padding:14px 16px;border-bottom:1px solid #f5f5f5;
  cursor:pointer;transition:.15s;text-decoration:none;
}
.menu-item:last-child{border-bottom:none;}
.menu-item:hover{background:#f8fbff;}
.mi-icon{
  width:38px;height:38px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;flex-shrink:0;
}
.mi-text{flex:1;}
.mi-label{font-size:14px;font-weight:600;color:var(--text);}
.mi-sub{font-size:12px;color:var(--muted);margin-top:2px;}
.mi-arrow{color:#ccc;font-size:16px;}
.mi-badge{
  background:var(--orange);color:#fff;
  font-size:10px;font-weight:700;
  padding:2px 7px;border-radius:20px;
  margin-right:6px;
}

/* EDIT FORM */
.edit-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.5);z-index:500;
  align-items:flex-end;justify-content:center;
}
.edit-overlay.show{display:flex;}
.edit-sheet{
  background:#fff;border-radius:18px 18px 0 0;
  width:100%;max-width:480px;padding:20px 16px 36px;
  animation:slideUp .3s ease;
}
@keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.es-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.es-title{font-size:17px;font-weight:700;}
.es-close{background:none;border:none;font-size:22px;cursor:pointer;color:var(--muted);}
.edit-field{margin-bottom:14px;}
.edit-field label{display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em;}
.edit-field input,.edit-field select{
  width:100%;border:1.5px solid var(--border);border-radius:8px;
  padding:11px 14px;font-size:14px;font-family:inherit;outline:none;transition:.2s;
}
.edit-field input:focus,.edit-field select:focus{border-color:var(--blue);}
.save-btn{
  width:100%;padding:14px;background:linear-gradient(135deg,#ff9800,#fb641b);
  color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;
  cursor:pointer;font-family:inherit;margin-top:6px;
  box-shadow:0 3px 10px rgba(251,100,27,.3);transition:.2s;
}
.save-btn:hover{transform:translateY(-1px);}

/* LOGOUT */
.logout-btn{
  margin:12px;width:calc(100% - 24px);padding:14px;
  background:#fff;color:var(--red);
  border:2px solid var(--red);border-radius:var(--radius);
  font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;
  transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;
  animation:fadeUp .3s .15s ease both;
}
.logout-btn:hover{background:var(--red);color:#fff;}

/* TOAST */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:10px 20px;border-radius:24px;font-size:13px;z-index:9999;opacity:0;transition:.3s;pointer-events:none;white-space:nowrap;}
.toast.show{opacity:1;}

@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

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
<body data-fk-sync="auth,cart,wishlist">

<header>
  <button class="h-back" onclick="goBackSmart('index.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <span class="h-title">My Profile</span>
  <button class="h-edit" onclick="openEdit()"><svg viewBox="0 0 24 24" width="15" height="15" fill="white"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg> Edit</button>
</header>

<!-- HERO -->
<div class="profile-hero">
  <div class="avatar-wrap">
    <div class="avatar" id="avatarEl">
      <span id="avatarInitials">U</span>
    </div>
    <button class="avatar-edit" onclick="openEdit()"><svg viewBox="0 0 24 24" width="15" height="15" fill="white"><path d="M12 15.2A3.2 3.2 0 0 1 8.8 12 3.2 3.2 0 0 1 12 8.8 3.2 3.2 0 0 1 15.2 12 3.2 3.2 0 0 1 12 15.2M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9z"/></svg></button>
  </div>
  <div class="hero-name" id="heroName">Guest User</div>
  <div class="hero-email" id="heroEmail">Not logged in</div>
  <div class="hero-joined" id="heroJoined"></div>
  <div class="plus-badge"><svg viewBox="0 0 24 24" width="14" height="14" fill="#ffe500" style="vertical-align:-2px;margin-right:3px"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg> Flipkart Plus Member</div>
</div>

<!-- STATS -->
<div class="stats-row">
  <div class="stat-card" onclick="window.location.href='orders.php'">
    <div class="stat-num" id="statOrders">0</div>
    <div class="stat-lbl">Orders</div>
  </div>
  <div class="stat-card" onclick="window.location.href='wishlist.php'">
    <div class="stat-num" id="statWish">0</div>
    <div class="stat-lbl">Wishlist</div>
  </div>
  <div class="stat-card" onclick="window.location.href='cart.php'">
    <div class="stat-num" id="statCart">0</div>
    <div class="stat-lbl">Cart</div>
  </div>
  <div class="stat-card">
    <div class="stat-num" id="statCoins"><svg viewBox="0 0 24 24" width="14" height="14" fill="#f9a825" style="vertical-align:-2px;margin-right:2px"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/></svg> <span id="statCoinsVal">0</span></div>
    <div class="stat-lbl">FK Coins</div>
  </div>
</div>

<!-- MY ACTIVITY -->
<div class="section" style="margin-top:22px;">
  <div class="sec-head">My Activity</div>
  <a class="menu-item" href="orders.php">
    <div class="mi-icon" style="background:#e3f2fd;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/></svg></div>
    <div class="mi-text"><div class="mi-label">My Orders</div><div class="mi-sub">Track, return or buy again</div></div>
    <span class="mi-arrow">›</span>
  </a>
  <a class="menu-item" href="wishlist.php">
    <div class="mi-icon" style="background:#fce4ec;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
    <div class="mi-text"><div class="mi-label">My Wishlist</div><div class="mi-sub">Items you saved for later</div></div>
    <span class="mi-arrow">›</span>
  </a>
  <a class="menu-item" href="cart.php">
    <div class="mi-icon" style="background:#fff3e0;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 16.1 6.9 18 9 18h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 23.4 5H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg></div>
    <div class="mi-text"><div class="mi-label">My Cart</div><div class="mi-sub">Items ready to checkout</div></div>
    <span class="mi-badge" id="cartBadge" style="display:none;">0</span>
    <span class="mi-arrow">›</span>
  </a>
</div>

<!-- ACCOUNT -->
<div class="section">
  <div class="sec-head">Account Settings</div>
  <div class="menu-item" onclick="openEdit()">
    <div class="mi-icon" style="background:#e8f5e9;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
    <div class="mi-text"><div class="mi-label">Personal Information</div><div class="mi-sub" id="menuNameSub">Update your name & contact</div></div>
    <span class="mi-arrow">›</span>
  </div>
  <a class="menu-item" href="address.php">
    <div class="mi-icon" style="background:#f3e5f5;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></div>
    <div class="mi-text"><div class="mi-label">Manage Addresses</div><div class="mi-sub">Add, edit or delete addresses</div></div>
    <span class="mi-arrow">›</span>
  </a>
  <div class="menu-item" onclick="showToast('💳 Payment methods coming soon!')">
    <div class="mi-icon" style="background:#e1f5fe;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg></div>
    <div class="mi-text"><div class="mi-label">Payment Methods</div><div class="mi-sub">Saved cards, UPI & wallets</div></div>
    <span class="mi-arrow">›</span>
  </div>
  <div class="menu-item" onclick="showToast('🔔 Notification settings coming soon!')">
    <div class="mi-icon" style="background:#fff8e1;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg></div>
    <div class="mi-text"><div class="mi-label">Notifications</div><div class="mi-sub">Manage your alerts</div></div>
    <span class="mi-arrow">›</span>
  </div>
</div>

<!-- SUPPORT -->
<div class="section">
  <div class="sec-head">Help & Support</div>
  <div class="menu-item" onclick="showToast('📞 Connecting to support...')">
    <div class="mi-icon" style="background:#e8f5e9;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg></div>
    <div class="mi-text"><div class="mi-label">Help Center</div><div class="mi-sub">FAQs and support options</div></div>
    <span class="mi-arrow">›</span>
  </div>
  <div class="menu-item" onclick="showToast('⭐ Thanks for rating us!')">
    <div class="mi-icon" style="background:#fff3e0;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg></div>
    <div class="mi-text"><div class="mi-label">Rate the App</div><div class="mi-sub">Share your feedback</div></div>
    <span class="mi-arrow">›</span>
  </div>
  <div class="menu-item" onclick="showToast('ℹ️ Version 2.0.1')">
    <div class="mi-icon" style="background:#f5f5f5;"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"/></svg></div>
    <div class="mi-text"><div class="mi-label">About</div><div class="mi-sub">Version 2.0.1</div></div>
    <span class="mi-arrow">›</span>
  </div>
</div>

<!-- LOGOUT -->
<button class="logout-btn" onclick="doLogout()">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
  Logout
</button>

<!-- EDIT BOTTOM SHEET -->
<div class="edit-overlay" id="editOverlay" onclick="closeEdit(event)">
  <div class="edit-sheet">
    <div class="es-head">
      <span class="es-title">Edit Profile</span>
      <button class="es-close" onclick="closeEdit()"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
    </div>
    <div class="edit-field">
      <label>Full Name</label>
      <input type="text" id="editName" placeholder="Your full name">
    </div>
    <div class="edit-field">
      <label>Mobile Number</label>
      <input type="tel" id="editMobile" placeholder="10-digit mobile">
    </div>
    <div class="edit-field">
      <label>Email Address</label>
      <input type="email" id="editEmail" placeholder="your@email.com">
    </div>
    <div class="edit-field">
      <label>Gender</label>
      <select id="editGender">
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
        <option value="Prefer not to say">Prefer not to say</option>
      </select>
    </div>
    <div class="edit-field">
      <label>Date of Birth</label>
      <input type="date" id="editDob">
    </div>
    <button class="save-btn" onclick="saveProfile()">SAVE CHANGES</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════
//  LOAD USER DATA
// ══════════════════════════════════════
async function loadProfile(skipSync) {
  if (!skipSync && window.FK && FK.syncAuthFromServer) {
    await Promise.allSettled([FK.syncAuthFromServer(), FK.syncStateFromServer('cart'), FK.syncStateFromServer('wishlist')]);
  }
  const user = (window.FK && FK.getCurrentUser) ? FK.getCurrentUser() : JSON.parse(localStorage.getItem('fk_user') || 'null');
  const extra = JSON.parse(localStorage.getItem('fk_profile_extra') || '{}');

  if (!user) {
    window.location.href = 'login.php?return=' + encodeURIComponent('profile.php');
    return;
  }

  const name = user.name || 'Guest User';
  const initials = name.split(' ').map(n=>n[0]).join('').toUpperCase().slice(0,2);
  document.getElementById('avatarInitials').textContent = initials;
  document.getElementById('heroName').textContent = name;
  document.getElementById('heroEmail').textContent = user.email || user.mobile || '';
  document.getElementById('heroJoined').textContent = user.joined ? 'Member since ' + user.joined : '';
  document.getElementById('menuNameSub').textContent = user.email || 'Update your name & contact';

  document.getElementById('editName').value   = name;
  document.getElementById('editMobile').value = user.mobile || '';
  document.getElementById('editEmail').value  = user.email  || '';
  document.getElementById('editGender').value = user.gender || extra.gender || '';
  document.getElementById('editDob').value    = user.dob || extra.dob || '';

  const orders   = (window.FK && FK.getOrders) ? FK.getOrders() : JSON.parse(localStorage.getItem('fk_orders') || '[]');
  const wishlist = (window.FK && FK.getWishlist) ? FK.getWishlist() : JSON.parse(localStorage.getItem('fk_wishlist') || '[]');
  const cart     = (window.FK && FK.getCart) ? FK.getCart() : JSON.parse(localStorage.getItem('flipkart_cart') || '[]');
  const cartQty  = cart.reduce((s,i)=>s+(i.qty||0),0);

  document.getElementById('statOrders').textContent = orders.length;
  document.getElementById('statWish').textContent   = wishlist.length;
  document.getElementById('statCart').textContent   = cartQty;
  document.getElementById('statCoinsVal').textContent = (orders.length * 25);

  if (cartQty > 0) {
    document.getElementById('cartBadge').style.display = 'inline';
    document.getElementById('cartBadge').textContent   = cartQty > 99 ? '99+' : cartQty;
  } else {
    document.getElementById('cartBadge').style.display = 'none';
  }
}

// ══════════════════════════════════════
//  EDIT PROFILE
// ══════════════════════════════════════
function openEdit() {
  document.getElementById('editOverlay').classList.add('show');
}
function closeEdit(e) {
  if (!e || e.target === document.getElementById('editOverlay')) {
    document.getElementById('editOverlay').classList.remove('show');
  }
}
async function saveProfile() {
  const name   = document.getElementById('editName').value.trim();
  const mobile = document.getElementById('editMobile').value.trim();
  const email  = document.getElementById('editEmail').value.trim();
  const gender = document.getElementById('editGender').value;
  const dob    = document.getElementById('editDob').value;

  if (!name) { showToast('⚠️ Please enter your name'); return; }

  try {
    if (window.FK && FK.updateProfile) {
      await FK.updateProfile({ name, mobile, email, gender, dob });
    } else {
      const user = JSON.parse(localStorage.getItem('fk_user') || '{}');
      user.name = name; user.mobile = mobile; user.email = email; user.gender = gender; user.dob = dob;
      localStorage.setItem('fk_user', JSON.stringify(user));
      localStorage.setItem('fk_profile_extra', JSON.stringify({ gender, dob }));
    }
    closeEdit();
    await loadProfile();
    showToast('✅ Profile updated successfully!');
  } catch (err) {
    showToast('⚠️ ' + ((err && err.message) || 'Profile update failed'));
  }
}

// ══════════════════════════════════════
//  LOGOUT
// ══════════════════════════════════════
async function doLogout() {
  if (window.FK && FK.logout) {
    await FK.logout();
  } else {
    localStorage.removeItem('fk_user');
    localStorage.removeItem('isLoggedIn');
  }
  showToast('👋 Logged out successfully!');
  setTimeout(() => { window.location.href = 'login.php'; }, 700);
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

document.addEventListener('fk:orders-sync', loadProfile);
document.addEventListener('fk:cart-sync', loadProfile);
document.addEventListener('fk:wishlist-sync', loadProfile);
document.addEventListener('fk:auth-sync', loadProfile);
loadProfile();
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
