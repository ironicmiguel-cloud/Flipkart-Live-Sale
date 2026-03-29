<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Wishlist – Flipkart</title>
    <meta name="description" content="Flipkart – wishlist page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --blue:#2874f0;--orange:#fb641b;--green:#388e3c;
  --red:#e91e63;--bg:#f1f3f6;--card:#fff;
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
.h-count{background:#fff;color:var(--blue);font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;}

/* EMPTY */
.empty{
  display:none;flex-direction:column;align-items:center;
  justify-content:center;padding:70px 20px;text-align:center;
}
.empty-heart{font-size:80px;animation:heartbeat 1.5s ease infinite;margin-bottom:20px;}
@keyframes heartbeat{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}
.empty h2{font-size:20px;font-weight:700;margin-bottom:8px;}
.empty p{color:var(--muted);font-size:14px;margin-bottom:24px;line-height:1.6;}
.empty-btn{background:var(--blue);color:#fff;border:none;border-radius:8px;padding:13px 32px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;}

/* SORT BAR */
.sort-bar{
  background:var(--card);padding:10px 14px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  font-size:13px;
}
.sort-bar span{color:var(--muted);}
.sort-bar select{border:none;outline:none;font-size:13px;color:var(--blue);font-weight:600;font-family:inherit;background:transparent;cursor:pointer;}

/* GRID */
.wishlist-grid{
  display:grid;grid-template-columns:repeat(2,1fr);
  gap:10px;padding:10px 10px;
}

/* ITEM CARD */
.wish-card{
  background:var(--card);border-radius:var(--radius);
  border:1px solid var(--border);overflow:hidden;
  position:relative;cursor:pointer;
  transition:box-shadow .2s,transform .2s;
  animation:fadeUp .3s ease both;
}
.wish-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.12);transform:translateY(-2px);}

.wish-img-wrap{
  background:#f8f8f8;
  aspect-ratio:1;display:flex;align-items:center;justify-content:center;
  font-size:42px;overflow:hidden;position:relative;
}
.wish-img-wrap img{width:100%;height:100%;object-fit:contain;padding:8px;}

/* HEART BTN */
.heart-btn{
  position:absolute;top:8px;right:8px;
  background:#fff;border:1px solid #eee;
  border-radius:50%;width:34px;height:34px;
  display:flex;align-items:center;justify-content:center;
  font-size:17px;cursor:pointer;
  box-shadow:0 2px 6px rgba(0,0,0,.1);
  transition:.2s;border:none;
  z-index:10;
}
.heart-btn:hover{transform:scale(1.15);}
.heart-btn.active{animation:pop .3s ease;}
@keyframes pop{0%{transform:scale(1)}50%{transform:scale(1.35)}100%{transform:scale(1)}}

/* DISCOUNT BADGE */
.disc-badge{
  position:absolute;top:8px;left:8px;
  background:var(--orange);color:#fff;
  font-size:10px;font-weight:700;
  padding:2px 6px;border-radius:3px;
}

.wish-info{padding:10px 10px 12px;}
.wi-brand{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;}
.wi-name{
  font-size:12.5px;font-weight:600;color:var(--text);
  margin:3px 0 6px;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  line-height:1.4;
}
.wi-price-row{display:flex;align-items:center;gap:6px;margin-bottom:10px;flex-wrap:wrap;}
.wi-price{font-size:15px;font-weight:700;}
.wi-mrp{font-size:11px;color:var(--muted);text-decoration:line-through;}
.wi-off{font-size:11px;color:var(--green);font-weight:600;}

.wi-btns{display:flex;gap:6px;}
.wi-cart{
  flex:1;padding:8px;border:1.5px solid var(--blue);
  background:#fff;color:var(--blue);
  border-radius:6px;font-size:12px;font-weight:700;
  cursor:pointer;font-family:inherit;transition:.15s;
}
.wi-cart:hover{background:var(--blue);color:#fff;}
.wi-buy{
  flex:1;padding:8px;border:none;
  background:linear-gradient(135deg,#ff9800,#fb641b);
  color:#fff;border-radius:6px;
  font-size:12px;font-weight:700;cursor:pointer;
  font-family:inherit;transition:.15s;
}
.wi-buy:hover{opacity:.9;}

/* MOVE ALL TO CART */
.bulk-bar{
  background:var(--card);margin:10px 10px 0;
  border-radius:var(--radius);border:1px solid var(--border);
  padding:12px 14px;display:flex;align-items:center;justify-content:space-between;
  animation:fadeUp .3s .1s ease both;
}
.bulk-bar span{font-size:13px;color:var(--muted);}
.bulk-btn{
  background:var(--blue);color:#fff;border:none;
  border-radius:6px;padding:9px 16px;
  font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;
}

/* TOAST */
.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
  background:#323232;color:#fff;padding:10px 20px;border-radius:24px;
  font-size:13px;z-index:9999;opacity:0;transition:.3s;
  pointer-events:none;white-space:nowrap;
}
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
<body data-fk-sync="auth,wishlist">

<header>
  <button class="h-back" onclick="goBackSmart('index.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <span class="h-title">My Wishlist</span>
  <span class="h-count" id="wishCount">0 items</span>
</header>

<!-- EMPTY STATE -->
<div class="empty" id="emptyWish">
  <div class="empty-heart"><svg viewBox="0 0 24 24" width="64" height="64" fill="#f48fb1"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></div>
  <h2>Your wishlist is empty!</h2>
  <p>Save items you love by tapping the heart<br>on any product page.</p>
  <button class="empty-btn" onclick="window.location.href='index.php'">Explore Products</button>
</div>

<!-- SORT + BULK BAR -->
<div id="wishContent" style="display:none;">
  <div class="sort-bar">
    <span id="sortLabel">0 items saved</span>
    <select onchange="sortWishlist(this.value)">
      <option value="recent">Recently Added</option>
      <option value="price_low">Price: Low to High</option>
      <option value="price_high">Price: High to Low</option>
      <option value="discount">Highest Discount</option>
    </select>
  </div>

  <div class="bulk-bar">
    <span>Move all items to cart?</span>
    <button class="bulk-btn" onclick="moveAllToCart()"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="vertical-align:-2px;margin-right:4px"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 16.1 6.9 18 9 18h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 23.4 5H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg> Move All to Cart</button>
  </div>

  <div class="wishlist-grid" id="wishGrid"></div>
</div>

<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════
//  WISHLIST STORAGE
// ══════════════════════════════════════
function getWishlist() {
  try { return (window.FK && FK.getWishlist) ? FK.getWishlist() : JSON.parse(localStorage.getItem('fk_wishlist') || '[]'); }
  catch(e) { return []; }
}
function saveWishlist(list) {
  if (window.FK && FK._mirrorState && FK.replaceWishlist) { FK._mirrorState('wishlist', list); FK.replaceWishlist(list).catch(function(){}); }
  else { localStorage.setItem('fk_wishlist', JSON.stringify(list)); }
}

// Toggle from product pages
window.toggleWishItem = function(id, name, brand, price, mrp, img) {
  const list = getWishlist();
  const idx  = list.findIndex(i => i.id === id);
  if (idx >= 0) { list.splice(idx,1); saveWishlist(list); return false; }
  else { list.push({id,name,brand,price:parseInt(price),mrp:parseInt(mrp),img,added:Date.now()}); saveWishlist(list); return true; }
};

// ══════════════════════════════════════
//  RENDER
// ══════════════════════════════════════
let currentSort = 'recent';

// XSS-safe HTML escaping helper
function esc(s){ const d=document.createElement('div'); d.textContent=String(s==null?'':s); return d.innerHTML; }

function render() {
  let list = getWishlist();
  const empty   = document.getElementById('emptyWish');
  const content = document.getElementById('wishContent');

  if (list.length === 0) {
    empty.style.display = 'flex';
    content.style.display = 'none';
    document.getElementById('wishCount').textContent = '0 items';
    return;
  }

  empty.style.display = 'none';
  content.style.display = 'block';
  document.getElementById('wishCount').textContent = list.length + ' item' + (list.length>1?'s':'');
  document.getElementById('sortLabel').textContent = list.length + ' item' + (list.length>1?'s':'') + ' saved';

  // Sort
  if (currentSort==='price_low')  list.sort((a,b)=>a.price-b.price);
  if (currentSort==='price_high') list.sort((a,b)=>b.price-a.price);
  if (currentSort==='discount')   list.sort((a,b)=>(b.mrp-b.price)-(a.mrp-a.price));
  if (currentSort==='recent')     list.sort((a,b)=>b.added-a.added);

  const grid = document.getElementById('wishGrid');
  grid.innerHTML = '';

  list.forEach((item, i) => {
    const offPct = item.mrp > item.price ? Math.round((1-item.price/item.mrp)*100) : 0;
    const card = document.createElement('div');
    card.className = 'wish-card';
    card.style.animationDelay = (i*0.05)+'s';
    card.innerHTML = `
      <div class="wish-img-wrap">
        ${item.img
          ? `<img src="${item.img}" alt="${esc(item.name)}" onerror="this.parentElement.innerHTML='<svg viewBox=\"0 0 24 24\" width=\"40\" height=\"40\" fill=\"#e0e0e0\"><path d=\"M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z\"/></svg>'">`
          : '<svg viewBox="0 0 24 24" width="60" height="60" fill="#e0e0e0"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>'}
        ${offPct>0 ? `<div class="disc-badge">${offPct}% off</div>` : ''}
        <button class="heart-btn active" onclick="removeItem('${item.id}',event)"><svg viewBox="0 0 24 24" width="20" height="20" fill="#ff3f6c"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg></button>
      </div>
      <div class="wish-info">
        <div class="wi-brand">${esc(item.brand||'')}</div>
        <div class="wi-name">${esc(item.name)}</div>
        <div class="wi-price-row">
          <span class="wi-price">₹${item.price.toLocaleString('en-IN')}</span>
          ${item.mrp>item.price ? `<span class="wi-mrp">₹${item.mrp.toLocaleString('en-IN')}</span>` : ''}
          ${offPct>0 ? `<span class="wi-off">${offPct}% off</span>` : ''}
        </div>
        <div class="wi-btns">
          <button class="wi-cart" onclick="addToCart('${item.id}',event)"><svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C5 16.1 6.9 18 9 18h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 23.4 5H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg> Cart</button>
          <button class="wi-buy"  onclick="buyNow('${item.id}',event)">Buy Now</button>
        </div>
      </div>
    `;
    grid.appendChild(card);
  });
}

function removeItem(id, e) {
  e.stopPropagation();
  const list = getWishlist().filter(i => i.id !== id);
  saveWishlist(list);
  render();
  showToast('💔 Removed from wishlist');
}

function addToCart(id, e) {
  e.stopPropagation();
  const item = getWishlist().find(i => i.id === id);
  if (!item) return;
  if (window.FK && FK.addToCart) {
    FK.addToCart(Object.assign({}, item, { qty: 1 })).then(function(){ showToast('✅ Added to cart!'); });
    return;
  }
  const cart = JSON.parse(localStorage.getItem('flipkart_cart') || '[]');
  const ex = cart.find(c => c.id === id);
  if (ex) ex.qty = Math.min(10, ex.qty+1);
  else cart.push({...item, qty:1});
  localStorage.setItem('flipkart_cart', JSON.stringify(cart));
  showToast('✅ Added to cart!');
}

function buyNow(id, e) {
  e.stopPropagation();
  const item = getWishlist().find(i => i.id === id);
  if (!item) return;
  if (window.FK && FK.prepareCheckout) {
    FK.prepareCheckout([{...item, qty:1}], { pid:item.id, name:item.name, brand:item.brand||'', img:item.img||'' });
  }
  window.location.href = 'address.php';
}

function moveAllToCart() {
  const list = getWishlist();
  if (!list.length) return;
  if (window.FK && FK.getCart && FK.replaceState) {
    const cart = FK.getCart();
    list.forEach(item => {
      const ex = cart.find(c => c.id === item.id);
      if (ex) ex.qty = Math.min(10, (ex.qty || 1) + 1);
      else cart.push({...item, qty:1});
    });
    FK.replaceState('cart', cart).then(function(){
      showToast('🛒 All items moved to cart!');
      setTimeout(() => { window.location.href = 'cart.php'; }, 700);
    });
    return;
  }
  const cart = JSON.parse(localStorage.getItem('flipkart_cart') || '[]');
  list.forEach(item => {
    const ex = cart.find(c => c.id === item.id);
    if (ex) ex.qty = Math.min(10, ex.qty+1);
    else cart.push({...item, qty:1});
  });
  localStorage.setItem('flipkart_cart', JSON.stringify(cart));
  showToast('🛒 All items moved to cart!');
  setTimeout(() => { window.location.href = 'cart.php'; }, 900);
}

function sortWishlist(val) { currentSort=val; render(); }

function showToast(msg) {
  const t=document.getElementById('toast');
  t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2500);
}

document.addEventListener('fk:wishlist-sync', render);
document.addEventListener('fk:cart-sync', render);
render();
</script>
<script>
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
