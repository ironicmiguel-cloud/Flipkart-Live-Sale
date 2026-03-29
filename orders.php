<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>My Orders – Flipkart</title>
    <meta name="description" content="Flipkart – orders page">
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
body{font-family:'Noto Sans',sans-serif;background:var(--bg);color:var(--text);font-size:14px;padding-bottom:30px;}

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

/* FILTER TABS */
.status-tabs{
  background:var(--card);display:flex;
  border-bottom:1px solid var(--border);
  overflow-x:auto;padding:0 4px;
}
.status-tabs::-webkit-scrollbar{display:none;}
.stab{
  padding:13px 16px;font-size:13px;font-weight:600;
  color:var(--muted);cursor:pointer;white-space:nowrap;
  border-bottom:2.5px solid transparent;transition:.15s;
  background:none;border-top:none;border-left:none;border-right:none;
  font-family:inherit;
}
.stab.active{color:var(--blue);border-bottom-color:var(--blue);}

/* SEARCH BAR */
.order-search{
  background:var(--card);padding:10px 14px;
  border-bottom:1px solid var(--border);
}
.order-search input{
  width:100%;border:1.5px solid var(--border);border-radius:8px;
  padding:9px 14px;font-size:14px;font-family:inherit;outline:none;transition:.2s;
}
.order-search input:focus{border-color:var(--blue);}

/* EMPTY */
.empty{display:none;flex-direction:column;align-items:center;padding:70px 20px;text-align:center;}
.empty-icon{font-size:80px;margin-bottom:20px;animation:float 3s ease-in-out infinite;}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.empty h2{font-size:20px;font-weight:700;margin-bottom:8px;}
.empty p{color:var(--muted);font-size:14px;margin-bottom:24px;line-height:1.6;}
.empty-btn{background:var(--blue);color:#fff;border:none;border-radius:8px;padding:13px 32px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;}

/* ORDER CARDS */
.orders-list{padding:10px;}

.order-card{
  background:var(--card);border-radius:var(--radius);
  border:1px solid var(--border);margin-bottom:10px;
  overflow:hidden;animation:fadeUp .3s ease both;
}

.order-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 14px;border-bottom:1px solid #f5f5f5;
  background:#fafafa;
}
.oh-left{}
.oh-oid{font-size:11px;color:var(--muted);margin-bottom:2px;}
.oh-date{font-size:12.5px;font-weight:600;color:var(--text);}
.oh-right{text-align:right;}
.oh-total{font-size:14px;font-weight:700;}
.oh-items{font-size:11px;color:var(--muted);margin-top:2px;}

/* STATUS BADGE */
.status-badge{
  display:inline-flex;align-items:center;gap:5px;
  font-size:11.5px;font-weight:700;padding:3px 9px;border-radius:4px;
  text-transform:uppercase;letter-spacing:.04em;
}
.status-delivered{background:#e8f5e9;color:#2e7d32;}
.status-shipped{background:#e3f2fd;color:#1565c0;}
.status-processing{background:#fff3e0;color:#e65100;}
.status-cancelled{background:#fce4ec;color:#880e4f;}
.status-returned{background:#f3e5f5;color:#6a1b9a;}

/* PRODUCT ROW */
.order-product{
  display:flex;align-items:center;gap:12px;
  padding:12px 14px;border-bottom:1px solid #f8f8f8;cursor:pointer;
  transition:.15s;
}
.order-product:hover{background:#f8fbff;}
.op-thumb{
  width:64px;height:64px;flex-shrink:0;
  background:#f8f8f8;border-radius:8px;border:1px solid #eee;
  display:flex;align-items:center;justify-content:center;
  font-size:28px;overflow:hidden;
}
.op-thumb img{width:100%;height:100%;object-fit:contain;padding:4px;}
.op-info{flex:1;min-width:0;}
.op-brand{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;}
.op-name{font-size:13px;font-weight:600;margin:2px 0 4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.op-price{font-size:13px;font-weight:700;}
.op-status-wrap{flex-shrink:0;}

/* DELIVERY BAR */
.delivery-bar{
  padding:10px 14px;border-bottom:1px solid #f5f5f5;
  display:flex;align-items:center;gap:8px;
  font-size:12px;
}
.db-icon{font-size:18px;}
.db-msg{flex:1;color:var(--text);font-weight:500;}
.db-date{color:var(--green);font-size:11.5px;font-weight:600;}

/* PROGRESS BAR (for in-transit) */
.progress-strip{padding:10px 14px;border-bottom:1px solid #f5f5f5;}
.progress-labels{display:flex;justify-content:space-between;margin-bottom:6px;font-size:10px;color:var(--muted);}
.progress-track{height:4px;background:#e0e0e0;border-radius:2px;overflow:hidden;}
.progress-fill{height:100%;border-radius:2px;background:linear-gradient(90deg,var(--blue),#42a5f5);transition:.3s;}

/* ACTIONS ROW */
.order-actions{display:flex;gap:0;border-top:1px solid #f0f0f0;}
.oa-btn{
  flex:1;padding:11px;border:none;background:none;
  font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;
  transition:.15s;border-right:1px solid #f0f0f0;
  display:flex;align-items:center;justify-content:center;gap:6px;
}
.oa-btn:last-child{border-right:none;}
.oa-btn:hover{background:#f8fbff;}
.oa-btn.blue{color:var(--blue);}
.oa-btn.green{color:var(--green);}
.oa-btn.red{color:var(--red);}
.oa-btn.orange{color:var(--orange);}

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
<body data-fk-sync="auth,orders">

<header>
  <button class="h-back" onclick="goBackSmart('profile.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <span class="h-title">My Orders</span>
</header>

<!-- STATUS FILTER TABS -->
<div class="status-tabs">
  <button class="stab active" onclick="filterStatus('all',this)">All Orders</button>
  <button class="stab" onclick="filterStatus('delivered',this)">Delivered</button>
  <button class="stab" onclick="filterStatus('shipped',this)">Shipped</button>
  <button class="stab" onclick="filterStatus('processing',this)">Processing</button>
  <button class="stab" onclick="filterStatus('cancelled',this)">Cancelled</button>
</div>

<!-- SEARCH ORDERS -->
<div class="order-search">
  <input type="text" placeholder="Search by product name or order ID" oninput="searchOrders(this.value)">
</div>

<!-- EMPTY STATE -->
<div class="empty" id="emptyOrders">
  <div class="empty-icon"><svg viewBox="0 0 24 24" width="64" height="64" fill="#bdbdbd"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27z"/></svg></div>
  <h2>No orders yet!</h2>
  <p>You haven't placed any orders yet.<br>Start shopping to see your orders here.</p>
  <button class="empty-btn" onclick="window.location.href='index.php'">Start Shopping</button>
</div>

<!-- ORDERS LIST -->
<div class="orders-list" id="ordersList"></div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════
//  ORDER DATA  (from localStorage + demo)
// ══════════════════════════════════════
function getOrders() {
  const saved = (window.FK && FK.getOrders) ? FK.getOrders() : ((window.FK && FK.getJSON) ? FK.getJSON('fk_orders', []) : JSON.parse(localStorage.getItem('fk_orders') || '[]'));
  return Array.isArray(saved) ? saved.sort((a,b) => String(b.createdAt||'').localeCompare(String(a.createdAt||''))) : [];
}

function addDays(n) {
  const d=new Date(); d.setDate(d.getDate()+n);
  return d.toLocaleDateString('en-IN',{weekday:'short',day:'numeric',month:'short'});
}

// ══════════════════════════════════════
//  RENDER
// ══════════════════════════════════════
let currentFilter='all', currentSearch='';

function render() {
  let orders = getOrders();

  if (currentFilter !== 'all') orders = orders.filter(o=>o.status===currentFilter);
  if (currentSearch) {
    const q = currentSearch.toLowerCase();
    orders = orders.filter(o => o.product.name.toLowerCase().includes(q) || o.id.toLowerCase().includes(q));
  }

  const empty = document.getElementById('emptyOrders');
  const list  = document.getElementById('ordersList');

  if (!orders.length) {
    empty.style.display='flex'; list.innerHTML=''; return;
  }
  empty.style.display='none';

  const statusMeta = {
    delivered:  {label:'Delivered',   cls:'status-delivered', icon:'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'},
    shipped:    {label:'Shipped',      cls:'status-shipped',   icon:'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z"/></svg>'},
    processing: {label:'Processing',   cls:'status-processing',icon:'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27z"/></svg>'},
    cancelled:  {label:'Cancelled',    cls:'status-cancelled', icon:'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>'},
    returned:   {label:'Returned',     cls:'status-returned',  icon:'<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>'},
  };

  list.innerHTML = '';
  orders.forEach((order, i) => {
    const sm = statusMeta[order.status] || statusMeta.processing;
    const card = document.createElement('div');
    card.className = 'order-card';
    card.style.animationDelay = (i*0.06)+'s';

    const showProgress = order.status==='shipped' || order.status==='processing';
    const showCancel   = order.status==='processing';
    const showReturn   = order.status==='delivered';
    const showReorder  = order.status==='delivered' || order.status==='cancelled';
    const showTrack    = order.status==='shipped' || order.status==='processing';

    card.innerHTML = `
      <!-- ORDER HEADER -->
      <div class="order-header">
        <div class="oh-left">
          <div class="oh-oid">Order ID: ${esc(order.id)}</div>
          <div class="oh-date">Placed on ${esc(order.date)}</div>
        </div>
        <div class="oh-right">
          <div class="oh-total">₹${order.total.toLocaleString('en-IN')}</div>
          <div class="oh-items">${order.items} item${order.items>1?'s':''}</div>
        </div>
      </div>

      <!-- PRODUCT -->
      <div class="order-product" onclick="window.location.href='product.php?id=${encodeURIComponent((order.product&&order.product.id)||localStorage.getItem('pid')||'p1')}'">
        <div class="op-thumb">
          ${order.product.img
            ? `<img src="${order.product.img}" alt="${esc(order.product.name)}" onerror="this.parentElement.innerHTML='<svg viewBox=\"0 0 24 24\" width=\"40\" height=\"40\" fill=\"#e0e0e0\"><path d=\"M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z\"/></svg>'">`
            : '<svg viewBox="0 0 24 24" width="40" height="40" fill="#e0e0e0"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>'}
        </div>
        <div class="op-info">
          <div class="op-brand">${esc(order.product.brand||'')}</div>
          <div class="op-name">${esc(order.product.name)}</div>
          <div class="op-price">₹${order.product.price.toLocaleString('en-IN')}</div>
        </div>
        <div class="op-status-wrap">
          <span class="status-badge ${sm.cls}">${sm.icon} ${sm.label}</span>
        </div>
      </div>

      <!-- DELIVERY MESSAGE -->
      <div class="delivery-bar">
        <span class="db-icon">${sm.icon}</span>
        <span class="db-msg">${esc(order.deliveryMsg)}</span>
        <span class="db-date">${esc(order.deliveryDate)}</span>
      </div>

      <!-- PROGRESS BAR -->
      ${showProgress ? `
      <div class="progress-strip">
        <div class="progress-labels">
          <span>Ordered</span><span>Packed</span><span>Shipped</span><span>Delivered</span>
        </div>
        <div class="progress-track">
          <div class="progress-fill" style="width:${order.progress}%"></div>
        </div>
      </div>` : ''}

      <!-- ACTIONS -->
      <div class="order-actions">
        ${showTrack   ? `<button class="oa-btn blue" onclick="trackOrder('${(window.FK&&FK.escapeHTML)?FK.escapeHTML(order.id):order.id}')" ><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> Track</button>` : ''}
        ${showReorder ? `<button class="oa-btn green" onclick="reorder('${(window.FK&&FK.escapeHTML)?FK.escapeHTML(order.id):order.id}')" ><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg> Reorder</button>` : ''}
        ${showReturn  ? `<button class="oa-btn orange" onclick="returnOrder('${(window.FK&&FK.escapeHTML)?FK.escapeHTML(order.id):order.id}')">↩️ Return</button>` : ''}
        ${showCancel  ? `<button class="oa-btn red" onclick="cancelOrder('${(window.FK&&FK.escapeHTML)?FK.escapeHTML(order.id):order.id}')" ><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> Cancel</button>` : ''}
        <button class="oa-btn blue" onclick="downloadInvoice('${(window.FK&&FK.escapeHTML)?FK.escapeHTML(order.id):order.id}')"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> Invoice</button>
      </div>
    `;
    list.appendChild(card);
  });
}

// ══════════════════════════════════════
//  ACTIONS
// ══════════════════════════════════════
function filterStatus(status, el) {
  currentFilter = status;
  document.querySelectorAll('.stab').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  render();
document.addEventListener('fk:orders-sync', render);
}

function searchOrders(val) { currentSearch=val; render(); }

function trackOrder(id) { showToast('📍 Tracking order: ' + id.slice(-5)); }

function reorder(id) {
  const orders = (window.FK && FK.getOrders) ? FK.getOrders() : JSON.parse(localStorage.getItem('fk_orders')||'[]');
  const order  = orders.find(o=>o.id===id);
  if (!order) return;
  localStorage.setItem('pay_name',  order.product.name);
  localStorage.setItem('pay_brand', order.product.brand||'');
  localStorage.setItem('pay_price', order.product.price);
  localStorage.setItem('pay_mrp',   order.product.price);
  localStorage.setItem('pay_img',   order.product.img||'');
  showToast('Reordering... Redirecting to payment');
  setTimeout(()=>{ window.location.href='payment.php'; }, 900);
}

function returnOrder(id) { showToast('↩️ Return request submitted for Order #' + id.slice(-5)); }

function cancelOrder(id) {
  const orders = (window.FK && FK.getOrders) ? FK.getOrders() : JSON.parse(localStorage.getItem('fk_orders')||'[]');
  const order  = orders.find(o=>o.id===id);
  if (!order) return;
  order.status='cancelled';
  order.deliveryMsg='Cancelled by you';
  order.deliveryDate='Refund in 5-7 business days';
  order.progress=0;
  if (window.FK && FK.updateOrder) { FK.updateOrder(id, { status:'cancelled', deliveryMsg:'Cancelled by you', deliveryDate:'Refund in 5-7 business days', progress:0 }); } else { localStorage.setItem('fk_orders', JSON.stringify(orders)); }
  showToast('Order cancelled: #' + id.slice(-5) + ' cancelled');
  setTimeout(()=>render(), 600);
}

function downloadInvoice(id) { showToast('🧾 Invoice for Order #' + id.slice(-5) + ' downloaded!'); }

function showToast(msg) {
  const t=document.getElementById('toast');
  t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2500);
}

render();
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
