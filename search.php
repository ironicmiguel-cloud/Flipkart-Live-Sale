<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Search – Flipkart</title>
    <meta name="description" content="Flipkart – search page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --blue:#2874f0;--orange:#fb641b;--green:#388e3c;
  --bg:#f1f3f6;--card:#fff;--border:#e0e0e0;
  --text:#212121;--muted:#878787;--radius:8px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Noto Sans',sans-serif;background:var(--bg);color:var(--text);font-size:14px;padding-bottom:30px;}

/* HEADER */
.search-header{
  background:var(--blue);padding:10px 14px;
  display:flex;align-items:center;gap:10px;
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

.search-input-wrap{
  flex:1;background:#fff;border-radius:6px;
  display:flex;align-items:center;gap:8px;
  padding:0 12px;height:40px;
}
.search-input-wrap svg{flex-shrink:0;opacity:.5;}
#searchInput{
  flex:1;border:none;outline:none;
  font-size:15px;font-family:inherit;color:var(--text);
  background:transparent;
}
.clear-btn{background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px;display:none;padding:2px;}

/* FILTER ROW */
.filter-row{
  background:var(--card);padding:10px 12px;
  display:flex;gap:8px;overflow-x:auto;
  border-bottom:1px solid var(--border);
}
.filter-row::-webkit-scrollbar{display:none;}
.filter-chip{
  white-space:nowrap;padding:6px 14px;
  border-radius:20px;border:1.5px solid var(--border);
  font-size:12.5px;font-weight:500;color:var(--muted);
  cursor:pointer;background:#fff;transition:.15s;font-family:inherit;
  display:flex;align-items:center;gap:5px;
}
.filter-chip:hover{border-color:var(--blue);color:var(--blue);}
.filter-chip.active{background:var(--blue);border-color:var(--blue);color:#fff;}

/* SORT BAR */
.result-bar{
  background:var(--card);padding:10px 14px;
  display:flex;align-items:center;justify-content:space-between;
  border-bottom:1px solid var(--border);font-size:13px;
}
.result-bar .rc{color:var(--muted);}
.sort-sel{border:none;outline:none;font-size:12.5px;color:var(--blue);font-weight:600;font-family:inherit;background:transparent;cursor:pointer;}

/* RECENT SEARCHES */
.recent-section{background:var(--card);margin:10px 12px;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;}
.rs-head{padding:12px 14px 8px;font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid var(--border);background:#fafafa;display:flex;justify-content:space-between;align-items:center;}
.rs-head button{background:none;border:none;font-size:12px;color:var(--blue);font-weight:600;cursor:pointer;font-family:inherit;}
.recent-item{
  display:flex;align-items:center;gap:10px;
  padding:11px 14px;border-bottom:1px solid #f5f5f5;cursor:pointer;transition:.15s;
}
.recent-item:hover{background:#f8fbff;}
.recent-item:last-child{border-bottom:none;}
.ri-icon{color:var(--muted);font-size:16px;}
.ri-text{flex:1;font-size:13.5px;}
.ri-arrow{color:var(--muted);font-size:14px;}

/* POPULAR TAGS */
.popular-section{background:var(--card);margin:10px 12px;border-radius:var(--radius);border:1px solid var(--border);padding:14px;}
.pop-head{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;}
.tags{display:flex;flex-wrap:wrap;gap:8px;}
.tag{
  padding:6px 14px;border-radius:20px;
  background:#f0f4ff;border:1px solid #c5d5ff;
  font-size:13px;color:var(--blue);font-weight:500;cursor:pointer;transition:.15s;
}
.tag:hover{background:var(--blue);color:#fff;}

/* RESULTS GRID */
.results-grid{
  display:grid;grid-template-columns:repeat(2,1fr);
  gap:10px;padding:10px;
}
.product-card{
  background:var(--card);border-radius:var(--radius);
  border:1px solid var(--border);overflow:hidden;cursor:pointer;
  transition:.2s;animation:fadeUp .25s ease both;
}
.product-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.12);transform:translateY(-2px);}
.pc-img{
  aspect-ratio:1;background:#f8f8f8;
  display:flex;align-items:center;justify-content:center;
  font-size:40px;overflow:hidden;position:relative;
}
.pc-img img{width:100%;height:100%;object-fit:contain;padding:8px;}
.pc-disc{position:absolute;top:6px;left:6px;background:var(--orange);color:#fff;font-size:9.5px;font-weight:700;padding:2px 6px;border-radius:3px;}
.pc-wish{
  position:absolute;top:6px;right:6px;
  background:#fff;border:none;border-radius:50%;
  width:30px;height:30px;display:flex;align-items:center;justify-content:center;
  font-size:14px;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.1);transition:.15s;
}
.pc-wish:hover{transform:scale(1.15);}
.pc-info{padding:10px 10px 12px;}
.pc-brand{font-size:10.5px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;}
.pc-name{font-size:12.5px;font-weight:600;margin:3px 0 6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4;}
.pc-price-row{display:flex;align-items:center;gap:5px;margin-bottom:4px;flex-wrap:wrap;}
.pc-price{font-size:14px;font-weight:700;}
.pc-mrp{font-size:11px;color:var(--muted);text-decoration:line-through;}
.pc-off{font-size:11px;color:var(--green);font-weight:600;}
.pc-rating{display:flex;align-items:center;gap:4px;}
.pc-rp{background:var(--green);color:#fff;font-size:10px;font-weight:700;padding:1px 5px;border-radius:3px;}
.pc-rc{font-size:10px;color:var(--muted);}

/* NO RESULTS */
.no-results{
  display:none;flex-direction:column;align-items:center;
  padding:60px 20px;text-align:center;
}
.no-results .nr-icon{font-size:70px;margin-bottom:16px;opacity:.6;}
.no-results h3{font-size:18px;font-weight:700;margin-bottom:8px;}
.no-results p{color:var(--muted);font-size:13px;line-height:1.6;}

/* SKELETON */
.skeleton{background:#f0f0f0;border-radius:4px;animation:shimmer 1.2s infinite linear;}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}

/* TOAST */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:10px 20px;border-radius:24px;font-size:13px;z-index:9999;opacity:0;transition:.3s;pointer-events:none;white-space:nowrap;}
.toast.show{opacity:1;}

@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

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

<!-- HEADER -->
<div class="search-header">
  <button class="h-back" onclick="goBackSmart('index.php')">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <div class="search-input-wrap">
    <svg width="17" height="17" viewBox="0 0 24 24" fill="#878787"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
    <input id="searchInput" type="text" placeholder="Search for products, brands..." autofocus oninput="onSearch(this.value)">
    <button class="clear-btn" id="clearBtn" onclick="clearSearch()"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
  </div>
</div>

<!-- FILTERS (shown when searching) -->
<div class="filter-row" id="filterRow" style="display:none;">
  <button class="filter-chip active" data-filter="all" onclick="toggleFilter(this,'all')">All</button>
  <button class="filter-chip" data-filter="electronics" onclick="toggleFilter(this,'electronics')">Electronics</button>
  <button class="filter-chip" data-filter="mobiles" onclick="toggleFilter(this,'mobiles')">Mobiles</button>
  <button class="filter-chip" data-filter="fashion" onclick="toggleFilter(this,'fashion')">Fashion</button>
  <button class="filter-chip" data-filter="home" onclick="toggleFilter(this,'home')">Home</button>
  <button class="filter-chip" data-filter="sports" onclick="toggleFilter(this,'sports')">Sports</button>
  <button class="filter-chip" data-filter="beauty" onclick="toggleFilter(this,'beauty')">Beauty</button>
  <button class="filter-chip" onclick="priceFilter()"><svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" style="vertical-align:-2px;margin-right:3px"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg> Price</button>
</div>

<!-- SORT + RESULT COUNT -->
<div class="result-bar" id="resultBar" style="display:none;">
  <span class="rc" id="resultCount">0 results</span>
  <select class="sort-sel" onchange="sortResults(this.value)">
    <option value="rel">Relevance</option>
    <option value="price_low">Price ↑</option>
    <option value="price_high">Price ↓</option>
    <option value="discount">Discount</option>
    <option value="rating">Rating</option>
  </select>
</div>

<!-- DEFAULT STATE (no search) -->
<div id="defaultState">
  <!-- RECENT SEARCHES -->
  <div class="recent-section" id="recentSection">
    <div class="rs-head">
      Recent Searches
      <button onclick="clearRecent()">Clear All</button>
    </div>
    <div id="recentList"></div>
  </div>

  <!-- POPULAR -->
  <div class="popular-section">
    <div class="pop-head"><svg viewBox="0 0 24 24" width="13" height="13" fill="#ff5722" style="vertical-align:-2px;margin-right:3px"><path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67z"/></svg> Trending Searches</div>
    <div class="tags">
      <div class="tag" onclick="doSearch('Headphones')">Headphones</div>
      <div class="tag" onclick="doSearch('Smart Watch')">Smart Watch</div>
      <div class="tag" onclick="doSearch('Earbuds')">Earbuds</div>
      <div class="tag" onclick="doSearch('Speaker')">Speaker</div>
      <div class="tag" onclick="doSearch('Gaming Mouse')">Gaming Mouse</div>
      <div class="tag" onclick="doSearch('Power Bank')">Power Bank</div>
      <div class="tag" onclick="doSearch('Laptop')">Laptop</div>
      <div class="tag" onclick="doSearch('Keyboard')">Keyboard</div>
      <div class="tag" onclick="doSearch('Phone')">Phone</div>
      <div class="tag" onclick="doSearch('Camera')">Camera</div>
      <div class="tag" onclick="doSearch('Shoes')">Shoes</div>
      <div class="tag" onclick="doSearch('T-Shirt')">T-Shirt</div>
    </div>
  </div>
</div>

<!-- SEARCH RESULTS -->
<div id="searchState" style="display:none;">
  <div class="no-results" id="noResults">
    <div class="nr-icon"><svg viewBox="0 0 24 24" width="64" height="64" fill="#bdbdbd"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></div>
    <h3>No results found</h3>
    <p>We couldn't find anything for "<span id="noResultQuery"></span>"<br>Try different keywords or check spelling.</p>
  </div>
  <div class="results-grid" id="resultsGrid"></div>
</div>

<div class="toast" id="toast"></div>


<script>
// ══════════════════════════════════════
//  PRODUCT DATA  (real catalog – synced from index.php)
// ══════════════════════════════════════
let allProducts = [];


// ══════════════════════════════════════
//  RECENT SEARCHES
// ══════════════════════════════════════
function getRecent() { try{return JSON.parse(localStorage.getItem('fk_recent')||'[]');}catch(e){return[];} }
function saveRecent(q) {
  let r = getRecent().filter(x=>x!==q);
  r.unshift(q); r=r.slice(0,8);
  localStorage.setItem('fk_recent',JSON.stringify(r));
}
function clearRecent() { localStorage.removeItem('fk_recent'); renderRecent(); }
function renderRecent() {
  const r = getRecent();
  const sec = document.getElementById('recentSection');
  const list = document.getElementById('recentList');
  if (!r.length) { sec.style.display='none'; list.innerHTML=''; return; }
  sec.style.display='block';
  list.innerHTML = '';
  r.forEach(function(q){
    const item = document.createElement('div');
    item.className = 'recent-item';
    item.onclick = function(){ doSearch(q); };
    item.innerHTML = `
      <span class="ri-icon"><svg viewBox="0 0 24 24" width="14" height="14" fill="#888"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg></span>
      <span class="ri-text"></span>
      <span class="ri-arrow">↗</span>`;
    item.querySelector('.ri-text').textContent = q;
    list.appendChild(item);
  });
}
renderRecent();

// ══════════════════════════════════════
//  SEARCH LOGIC
// ══════════════════════════════════════
let searchTimer=null;
let currentFilter='all';
let currentSort='rel';

function getParam(name){ return new URLSearchParams(location.search).get(name) || ''; }
function setActiveFilterChip(filter){
  document.querySelectorAll('.filter-chip[data-filter]').forEach(function(chip){
    chip.classList.toggle('active', chip.getAttribute('data-filter') === filter);
  });
}
function applyInitialState(){
  const categoryParam = (getParam('category') || '').toLowerCase();
  const categoryMap = { mobile:'mobiles', mobiles:'mobiles', phone:'mobiles', phones:'mobiles', electronics:'electronics', fashion:'fashion', home:'home', sports:'sports', beauty:'beauty' };
  const mappedFilter = categoryMap[categoryParam] || 'all';
  currentFilter = mappedFilter;
  setActiveFilterChip(mappedFilter);
  const urlQ = getParam('q');
  if (urlQ) {
    doSearch(urlQ);
  } else {
    showDefault();
  }
}
function initSearchCatalog(){
  allProducts = Array.isArray(window.FK_SEARCH_PRODUCTS) ? window.FK_SEARCH_PRODUCTS.slice() : [];
  applyInitialState();
}

function onSearch(val) {
  document.getElementById('clearBtn').style.display = val ? 'block' : 'none';
  clearTimeout(searchTimer);
  if (!val.trim()) { showDefault(); return; }
  searchTimer = setTimeout(() => doSearch(val), 300);
}

// XSS-safe HTML escaping helper
function esc(s){ const d=document.createElement('div'); d.textContent=String(s==null?'':s); return d.innerHTML; }

function doSearch(query) {
  document.getElementById('searchInput').value = query;
  document.getElementById('clearBtn').style.display = 'block';
  saveRecent(query);

  document.getElementById('defaultState').style.display = 'none';
  document.getElementById('searchState').style.display = 'block';
  document.getElementById('filterRow').style.display = 'flex';
  document.getElementById('resultBar').style.display = 'flex';

  let results = allProducts.filter(p =>
    p.name.toLowerCase().includes(query.toLowerCase()) ||
    p.brand.toLowerCase().includes(query.toLowerCase())
  );

  // Filter by tag
  if (currentFilter !== 'all') {
    results = results.filter(p => {
      if (p.tags && p.tags.includes(currentFilter)) return true;
      // fallback keyword match
      const n = (p.name + ' ' + p.brand).toLowerCase();
      const catMap = {
        electronics:['earbuds','speaker','headphone','mouse','keyboard','tv','tablet','cable','charger','watch','laptop','monitor','usb','bluetooth'],
        mobiles:['phone','smartphone','realme','oppo','samsung galaxy','redmi','motorola','nokia'],
        fashion:['shoes','backpack','bag','belt','wallet','sunglasses','perfume','shirt','chair'],
        sports:['protein','creatine','fitness','slimming','yoga','band'],
        beauty:['face wash','hair','cream','cetaphil','foxtale','trimmer','toothpaste','perfume'],
        home:['refrigerator','fridge','wardrobe','furniture','chair','sofa']
      };
      const kws = catMap[currentFilter] || [];
      return kws.some(k => n.includes(k));
    });
  }

  // Sort
  if (currentSort==='price_low')  results.sort((a,b)=>a.price-b.price);
  if (currentSort==='price_high') results.sort((a,b)=>b.price-a.price);
  if (currentSort==='discount')   results.sort((a,b)=>b.off-a.off);
  if (currentSort==='rating')     results.sort((a,b)=>b.rating-a.rating);

  renderResults(results, query);
}

function renderResults(results, query) {
  const grid    = document.getElementById('resultsGrid');
  const noRes   = document.getElementById('noResults');
  const resCount= document.getElementById('resultCount');

  resCount.textContent = results.length + ' result' + (results.length!==1?'s':'') + ' for "' + query + '"';

  if (!results.length) {
    noRes.style.display='flex'; grid.style.display='none';
    document.getElementById('noResultQuery').textContent = query;
    return;
  }
  noRes.style.display='none'; grid.style.display='grid';

  const wishlist = JSON.parse(localStorage.getItem('fk_wishlist')||'[]');

  grid.innerHTML='';
  results.forEach((p,i) => {
    const wished = wishlist.some(w=>w.id===p.id);
    const card = document.createElement('div');
    card.className='product-card';
    card.style.animationDelay=(i*0.04)+'s';
    card.onclick=()=>{ window.location.href=`product.php?id=${p.id}`; };
    card.innerHTML=`
      <div class="pc-img">
        ${(p.images&&p.images[0])?`<img src="${p.images[0]}" alt="${esc(p.name)}" onerror="this.parentElement.innerHTML='<svg viewBox=\"0 0 24 24\" width=\"40\" height=\"40\" fill=\"#e0e0e0\"><path d=\"M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z\"/></svg>'">` : '<svg viewBox="0 0 24 24" width="40" height="40" fill="#e0e0e0"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>'}
        ${p.off>0?`<div class="pc-disc">${p.off}% off</div>`:''}
        <button class="pc-wish" onclick="wishToggle(event,'${p.id}',this)">${wished?'<svg viewBox="0 0 24 24" width="18" height="18" fill="#ff3f6c"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>':'<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#aaa" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>'}</button>
      </div>
      <div class="pc-info">
        <div class="pc-brand">${esc(p.brand)}</div>
        <div class="pc-name">${esc(p.name)}</div>
        <div class="pc-price-row">
          <span class="pc-price">₹${p.price.toLocaleString('en-IN')}</span>
          ${p.mrp>p.price?`<span class="pc-mrp">₹${p.mrp.toLocaleString('en-IN')}</span>`:''}
          ${p.off>0?`<span class="pc-off">${p.off}% off</span>`:''}
        </div>
        <div class="pc-rating">
          <span class="pc-rp">${p.rating}★</span>
          <span class="pc-rc">(${p.reviews.toLocaleString()})</span>
        </div>
      </div>`;
    grid.appendChild(card);
  });
}

function wishToggle(e, id, btn) {
  e.stopPropagation();
  const p = allProducts.find(x=>x.id===id);
  if (!p) return;
  const item = {id:p.id,name:p.name,brand:p.brand,price:p.price,mrp:p.mrp,img:(p.images&&p.images[0])||'',added:Date.now()};
  if (window.FK && FK.toggleWishlist) {
    FK.toggleWishlist(item).then(function(active){
      btn.innerHTML = active ? '<svg viewBox="0 0 24 24" width="18" height="18" fill="#ff3f6c"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' : '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#aaa" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
      showToast(active ? '❤️ Added to wishlist!' : '💔 Removed from wishlist');
    });
    return;
  }
  const list = JSON.parse(localStorage.getItem('fk_wishlist')||'[]');
  const idx  = list.findIndex(i=>i.id===id);
  if (idx>=0) { list.splice(idx,1); btn.innerHTML='<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#aaa" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>'; showToast('💔 Removed from wishlist'); }
  else { list.push(item); btn.innerHTML='<svg viewBox="0 0 24 24" width="18" height="18" fill="#ff3f6c"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'; showToast('❤️ Added to wishlist!'); }
  localStorage.setItem('fk_wishlist',JSON.stringify(list));
}

function showDefault() {
  document.getElementById('defaultState').style.display='block';
  document.getElementById('searchState').style.display='none';
  document.getElementById('filterRow').style.display='none';
  document.getElementById('resultBar').style.display='none';
  document.getElementById('clearBtn').style.display='none';
  renderRecent();
}

function clearSearch() {
  document.getElementById('searchInput').value='';
  showDefault();
}

function toggleFilter(el, val) {
  document.querySelectorAll('.filter-chip').forEach(c=>c.classList.remove('active'));
  el.classList.add('active');
  currentFilter=val;
  const q=document.getElementById('searchInput').value.trim();
  if(q) doSearch(q);
}

function sortResults(val) {
  currentSort=val;
  const q=document.getElementById('searchInput').value.trim();
  if(q) doSearch(q);
}

function priceFilter() { showToast('Price filter coming soon!'); }

function showToast(msg) {
  const t=document.getElementById('toast');
  t.textContent=msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2500);
}

// Initialize once product catalog is ready
document.addEventListener('fk:wishlist-sync', function(){ const q=document.getElementById('searchInput').value.trim(); if(q){ doSearch(q); } });
if (Array.isArray(window.FK_SEARCH_PRODUCTS) && window.FK_SEARCH_PRODUCTS.length) {
  initSearchCatalog();
} else {
  document.addEventListener('fk:ready', initSearchCatalog, { once: true });
  setTimeout(function(){ if (!allProducts.length) initSearchCatalog(); }, 1200);
}
</script>
<script>
// Smart image extension resolver
(function() {
  const EXTS = ['jpg','jpeg','png','webp','avif'];
  document.addEventListener('DOMContentLoaded', function() {
    // MutationObserver to catch dynamically added images
    const obs = new MutationObserver(function(muts) {
      muts.forEach(function(m) {
        m.addedNodes.forEach(function(n) {
          if (n.nodeType === 1) {
            n.querySelectorAll && n.querySelectorAll('img').forEach(applyFallback);
            if (n.tagName === 'IMG') applyFallback(n);
          }
        });
      });
    });
    obs.observe(document.body, {childList:true, subtree:true});
  });
  function applyFallback(img) {
    if (img.dataset.smartDone) return;
    img.dataset.smartDone = '1';
    const orig = img.src;
    const base = orig.replace(/\.(avif|webp|png|jpe?g)$/i,'');
    if (base === orig) return; // no extension to strip
    let ei = 0;
    img.onerror = function tryExt() {
      if (ei >= EXTS.length) { img.onerror=null; return; }
      img.onerror = tryExt;
      img.src = base + '.' + EXTS[ei++];
    };
  }
  window.applyImgFallback = applyFallback;
})();
</script>
<?php $pv = @filemtime(__DIR__.'/assets/products.json') ?: '1'; ?>
<script src="assets/products-data.js?v=<?= $pv ?>" defer fetchpriority="high"></script>
<script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
