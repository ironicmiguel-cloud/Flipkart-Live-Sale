(function(){
  const FK = window.FK = window.FK || {};
  const CART_KEY = 'flipkart_cart';
  const WISH_KEY = 'fk_wishlist';
  const USER_KEY = 'fk_user';
  const PROFILE_EXTRA_KEY = 'fk_profile_extra';

  FK.getJSON = function(key, fallback){
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch(e) { return fallback; }
  };

  FK.setJSON = function(key, value){
    FK._setLocalJSON(key, value);
    return value;
  };

  FK.escapeHTML = function(value){
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  };
  FK.escapeAttr = FK.escapeHTML;

  FK.toNumber = function(v){
    if (typeof v === 'number') return isFinite(v) ? v : 0;
    const n = parseFloat(String(v == null ? '' : v).replace(/[^0-9.\-]/g,''));
    return isFinite(n) ? n : 0;
  };

  FK.normalizeCartItems = function(items){
    return (Array.isArray(items) ? items : []).map(function(item){
      if (!item || !item.id) return null;
      var price = FK.toNumber(item.price);
      var mrp = FK.toNumber(item.mrp || item.price);
      var qty = Math.max(1, Math.min(10, parseInt(item.qty, 10) || 1));
      if (price <= 0) return null;
      return Object.assign({}, item, {
        id: String(item.id),
        name: String(item.name || 'Product').trim(),
        brand: String(item.brand || '').trim(),
        price: price,
        mrp: mrp > 0 ? mrp : price,
        off: String(item.off || '').trim(),
        img: String(item.img || '').trim(),
        qty: qty,
      });
    }).filter(Boolean);
  };

  FK._sameState = function(a, b){
    try { return JSON.stringify(a || []) === JSON.stringify(b || []); }
    catch(e) { return false; }
  };

  FK.totalQty = function(items){
    return FK.normalizeCartItems(items).reduce(function(s, item){ return s + (parseInt(item && item.qty, 10) || 1); }, 0);
  };

  FK._stateCache = {
    authLoaded: false,
    user: null,
    cart: null,
    wishlist: null
  };
  FK._suppressLocalSync = false;
  FK._pendingSync = {};

  FK._setLocalJSON = function(key, value){
    FK._suppressLocalSync = true;
    try { localStorage.setItem(key, JSON.stringify(value)); }
    finally { FK._suppressLocalSync = false; }
  };

  FK._removeLocal = function(key){
    FK._suppressLocalSync = true;
    try { localStorage.removeItem(key); }
    finally { FK._suppressLocalSync = false; }
  };

  FK._dispatch = function(name, detail){
    try { document.dispatchEvent(new CustomEvent(name, { detail: detail || {} })); } catch(e) {}
  };

  FK._cloneArray = function(items){ return Array.isArray(items) ? items.map(function(item){ return Object.assign({}, item); }) : []; };

  FK._mirrorUser = function(user){
    FK._stateCache.user = user || null;
    if (user) {
      FK._setLocalJSON(USER_KEY, user);
      FK._setLocalJSON(PROFILE_EXTRA_KEY, { gender: user.gender || '', dob: user.dob || '' });
      FK._suppressLocalSync = true;
      try { localStorage.setItem('isLoggedIn', 'true'); } finally { FK._suppressLocalSync = false; }
    } else {
      FK._removeLocal(USER_KEY);
      FK._removeLocal(PROFILE_EXTRA_KEY);
      FK._suppressLocalSync = true;
      try { localStorage.removeItem('isLoggedIn'); } finally { FK._suppressLocalSync = false; }
    }
    FK._dispatch('fk:auth-sync', { user: user || null });
    FK.updateGlobalBadges();
  };

  FK._mirrorState = function(type, items){
    const key = type === 'cart' ? CART_KEY : WISH_KEY;
    const clean = type === 'cart' ? FK.normalizeCartItems(items || []) : FK._cloneArray(items || []);
    FK._stateCache[type] = clean;
    FK._setLocalJSON(key, clean);
    FK._dispatch('fk:' + type + '-sync', { items: clean, totalQty: type === 'cart' ? FK.totalQty(clean) : clean.length });
    FK.updateGlobalBadges();
    return clean;
  };

  FK.getCurrentUser = function(){
    if (FK._stateCache.user) return FK._stateCache.user;
    return FK.getJSON(USER_KEY, null);
  };

  FK.getCart = function(){
    var raw = Array.isArray(FK._stateCache.cart) ? FK._cloneArray(FK._stateCache.cart) : FK.getJSON(CART_KEY, []);
    var clean = FK.normalizeCartItems(raw);
    if (!FK._sameState(raw, clean)) {
      FK._stateCache.cart = clean;
      FK._setLocalJSON(CART_KEY, clean);
      FK.scheduleStateSync('cart');
    }
    return FK._cloneArray(clean);
  };

  FK.getWishlist = function(){
    if (Array.isArray(FK._stateCache.wishlist)) return FK._cloneArray(FK._stateCache.wishlist);
    return FK.getJSON(WISH_KEY, []);
  };

  FK.isLoggedIn = function(){ return !!FK.getCurrentUser(); };
  FK.requireLogin = function(returnUrl){
    if (FK.isLoggedIn()) return true;
    window.location.href = 'login.php?return=' + encodeURIComponent(returnUrl || window.location.href);
    return false;
  };

  FK.prepareCheckout = function(items, opts){
    opts = opts || {};
    const list = (Array.isArray(items) ? items : []).filter(Boolean);
    if (!list.length) return null;

    const first = list[0] || {};
    const totalQty = FK.totalQty(list);
    const totalPrice = list.reduce((s, item) => s + (FK.toNumber(item.price) * (parseInt(item.qty,10)||1)), 0);
    const totalMrp = list.reduce((s, item) => s + (FK.toNumber(item.mrp || item.price) * (parseInt(item.qty,10)||1)), 0);
    const summaryName = list.length === 1
      ? (opts.name || first.name || 'Product')
      : ((first.name || 'Product').slice(0, 28) + ((first.name || '').length > 28 ? '…' : '') + ' & ' + (list.length - 1) + ' more');
    const offPct = totalMrp > 0 ? Math.round((1 - totalPrice / totalMrp) * 100) : 0;
    const token = 'CHK' + Date.now().toString(36).toUpperCase() + Math.random().toString(36).slice(2,6).toUpperCase();

    localStorage.setItem('fk_checkout_token', token);
    localStorage.setItem('pid', opts.pid || first.id || 'p1');
    localStorage.setItem('pay_cart', JSON.stringify(list));
    localStorage.setItem('pay_name', summaryName);
    localStorage.setItem('pay_brand', opts.brand || (list.length === 1 ? (first.brand || '') : totalQty + ' items'));
    localStorage.setItem('pay_price', String(totalPrice));
    localStorage.setItem('pay_mrp', String(totalMrp));
    localStorage.setItem('pay_off', opts.off || (offPct > 0 ? offPct + '% off' : ''));
    localStorage.setItem('pay_img', opts.img || first.img || (Array.isArray(first.images) ? first.images[0] : '') || '');
    localStorage.setItem('pay_qty', String(totalQty));
    if (opts.variant) localStorage.setItem('pay_variant', opts.variant); else localStorage.removeItem('pay_variant');
    if (opts.rating != null) localStorage.setItem('pay_rating', String(opts.rating));
    if (opts.reviews != null) localStorage.setItem('pay_reviews', String(opts.reviews));

    return { token, items:list, totalPrice, totalMrp, totalQty, first };
  };

  FK.goBackSmart = function(fallback){
    fallback = fallback || 'index.php';
    if (document.referrer && document.referrer !== window.location.href) {
      history.back();
    } else {
      window.location.href = fallback;
    }
  };
  window.goBackSmart = FK.goBackSmart;

  FK._csrfToken = null;

  FK._api = async function(url, options){
    const res = await fetch(url, Object.assign({ credentials: 'same-origin', cache: 'no-store' }, options || {}));
    const data = await res.json().catch(function(){ return { ok:false, error:'Invalid server response' }; });
    if (data && data.csrf) FK._csrfToken = data.csrf;
    if (!res.ok || data.ok === false) {
      throw new Error((data && data.error) || ('Request failed: ' + res.status));
    }
    return data;
  };

  FK._statePost = async function(payload){
    if (!FK._csrfToken) {
      try { await FK._api('assets/state_api.php?type=' + encodeURIComponent(payload.type || 'cart')); } catch(e) {}
    }
    return FK._api('assets/state_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.assign({}, payload, { csrf: FK._csrfToken || '' }))
    });
  };

  FK.syncAuthFromServer = async function(){
    try {
      const data = await FK._api('assets/auth_api.php');
      FK._stateCache.authLoaded = true;
      FK._mirrorUser(data.user || null);
      return data.user || null;
    } catch(e) {
      FK._stateCache.authLoaded = true;
      FK._mirrorUser(FK.getJSON(USER_KEY, null));
      return FK.getCurrentUser();
    }
  };

  FK.syncStateFromServer = async function(type){
    if (type !== 'cart' && type !== 'wishlist') return [];
    const localItems = type === 'cart' ? FK.getJSON(CART_KEY, []) : FK.getJSON(WISH_KEY, []);
    try {
      const data = await FK._api('assets/state_api.php?type=' + encodeURIComponent(type));
      let items = Array.isArray(data.items) ? data.items : [];
      if (!items.length && Array.isArray(localItems) && localItems.length) {
        items = await FK.replaceState(type, localItems, { silent: true });
      } else {
        items = FK._mirrorState(type, items);
      }
      return items;
    } catch(e) {
      return FK._mirrorState(type, localItems || []);
    }
  };

  FK.replaceState = async function(type, items, opts){
    opts = opts || {};
    const data = await FK._statePost({ action: 'replace', type: type, items: items || [] });
    return opts.silent ? FK._mirrorState(type, data.items || []) : FK._mirrorState(type, data.items || []);
  };

  FK.scheduleStateSync = function(type){
    if (type !== 'cart' && type !== 'wishlist') return;
    clearTimeout(FK._pendingSync[type]);
    FK._pendingSync[type] = setTimeout(function(){
      const items = type === 'cart' ? FK.getJSON(CART_KEY, []) : FK.getJSON(WISH_KEY, []);
      FK.replaceState(type, items, { silent: true }).catch(function(){});
    }, 160);
  };

  FK.addToCart = async function(item, qty){
    const incoming = Object.assign({}, item || {});
    incoming.qty = Math.max(1, Math.min(10, parseInt(qty != null ? qty : incoming.qty || 1, 10) || 1));
    try {
      const data = await FK._statePost({ action:'add', type:'cart', item: incoming });
      return FK._mirrorState('cart', data.items || []);
    } catch(e) {
      const cart = FK.getCart();
      const existing = cart.find(function(c){ return c.id === incoming.id; });
      if (existing) existing.qty = Math.max(1, Math.min(10, (existing.qty || 1) + incoming.qty));
      else cart.push(incoming);
      FK._mirrorState('cart', cart);
      FK.scheduleStateSync('cart');
      return cart;
    }
  };

  FK.setCartQty = async function(id, qty){
    qty = Math.max(1, Math.min(10, parseInt(qty, 10) || 1));
    try {
      const data = await FK._statePost({ action:'set_qty', type:'cart', id:id, qty:qty });
      return FK._mirrorState('cart', data.items || []);
    } catch(e) {
      const cart = FK.getCart().map(function(item){
        if (item.id === id) item.qty = qty;
        return item;
      });
      FK._mirrorState('cart', cart);
      FK.scheduleStateSync('cart');
      return cart;
    }
  };

  FK.removeFromCart = async function(id){
    try {
      const data = await FK._statePost({ action:'remove', type:'cart', id:id });
      return FK._mirrorState('cart', data.items || []);
    } catch(e) {
      const cart = FK.getCart().filter(function(item){ return item.id !== id; });
      FK._mirrorState('cart', cart);
      FK.scheduleStateSync('cart');
      return cart;
    }
  };

  FK.clearCart = async function(){
    try {
      const data = await FK._statePost({ action:'clear', type:'cart' });
      return FK._mirrorState('cart', data.items || []);
    } catch(e) {
      return FK._mirrorState('cart', []);
    }
  };

  FK.toggleWishlist = async function(item){
    const incoming = Object.assign({}, item || {});
    try {
      const data = await FK._statePost({ action:'toggle', type:'wishlist', item: incoming });
      FK._mirrorState('wishlist', data.items || []);
      return !!data.active;
    } catch(e) {
      const list = FK.getWishlist();
      const idx = list.findIndex(function(entry){ return entry.id === incoming.id; });
      const active = idx < 0;
      if (idx >= 0) list.splice(idx, 1); else list.push(incoming);
      FK._mirrorState('wishlist', list);
      FK.scheduleStateSync('wishlist');
      return active;
    }
  };

  FK.replaceWishlist = function(items){ return FK.replaceState('wishlist', items || []); };
  FK.isInWishlist = function(id){ return FK.getWishlist().some(function(item){ return item && item.id === id; }); };

  FK.login = async function(identifier, password){
    const guestCart = FK.getJSON(CART_KEY, []);
    const guestWishlist = FK.getJSON(WISH_KEY, []);
    const data = await FK._api('assets/auth_api.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ action:'login', identifier:identifier, password:password, guest_cart:guestCart, guest_wishlist:guestWishlist })
    });
    FK._mirrorUser(data.user || null);
    await FK.syncStateFromServer('cart');
    await FK.syncStateFromServer('wishlist');
    return data.user || null;
  };

  FK.signup = async function(payload){
    payload = payload || {};
    const data = await FK._api('assets/auth_api.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({
        action:'signup',
        name: payload.name || '',
        mobile: payload.mobile || '',
        email: payload.email || '',
        password: payload.password || '',
        guest_cart: FK.getJSON(CART_KEY, []),
        guest_wishlist: FK.getJSON(WISH_KEY, [])
      })
    });
    FK._mirrorUser(data.user || null);
    await FK.syncStateFromServer('cart');
    await FK.syncStateFromServer('wishlist');
    return data.user || null;
  };

  FK.logout = async function(){
    try { await FK._api('assets/auth_api.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ action:'logout' })
    }); } catch(e) {}
    FK._mirrorUser(null);
    await FK.syncStateFromServer('cart');
    await FK.syncStateFromServer('wishlist');
    return true;
  };

  FK.updateProfile = async function(payload){
    const data = await FK._api('assets/auth_api.php', {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify(Object.assign({ action:'update_profile' }, payload || {}))
    });
    FK._mirrorUser(data.user || null);
    return data.user || null;
  };

  FK.updateGlobalBadges = function(){
    const cart = FK.getCart();
    const total = FK.totalQty(cart);
    const ids = ['cartBadge', 'cartNavBadge'];
    ids.forEach(function(id){
      const badge = document.getElementById(id);
      if (!badge) return;
      badge.textContent = total > 99 ? '99+' : String(total || '');
      badge.style.display = total > 0 ? 'flex' : 'none';
    });
    const user = FK.getCurrentUser();
    document.documentElement.dataset.fkLoggedIn = user ? '1' : '0';
  };

  FK._handleLocalMutation = function(key){
    if (FK._suppressLocalSync) return;
    if (key === CART_KEY) {
      FK._stateCache.cart = FK.normalizeCartItems(FK.getJSON(CART_KEY, []));
      if (!FK._sameState(FK.getJSON(CART_KEY, []), FK._stateCache.cart)) FK._setLocalJSON(CART_KEY, FK._stateCache.cart);
      FK._dispatch('fk:cart-sync', { items: FK._cloneArray(FK._stateCache.cart), totalQty: FK.totalQty(FK._stateCache.cart) });
      FK.updateGlobalBadges();
      FK.scheduleStateSync('cart');
    }
    if (key === WISH_KEY) {
      FK._stateCache.wishlist = FK.getJSON(WISH_KEY, []);
      FK._dispatch('fk:wishlist-sync', { items: FK._cloneArray(FK._stateCache.wishlist), totalQty: FK._stateCache.wishlist.length });
      FK.updateGlobalBadges();
      FK.scheduleStateSync('wishlist');
    }
  };

  FK._patchStorage = function(){
    if (Storage.prototype._fkPatched) return;
    const originalSet = Storage.prototype.setItem;
    const originalRemove = Storage.prototype.removeItem;
    Storage.prototype.setItem = function(key, value){
      originalSet.apply(this, arguments);
      if (this === window.localStorage) FK._handleLocalMutation(String(key || ''));
    };
    Storage.prototype.removeItem = function(key){
      originalRemove.apply(this, arguments);
      if (this === window.localStorage) FK._handleLocalMutation(String(key || ''));
    };
    Storage.prototype._fkPatched = true;
  };
  FK._patchStorage();

  FK._fmtDate = function(d){
    return d.toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' });
  };
  FK._fmtDelivery = function(days){
    const d = new Date(); d.setDate(d.getDate() + days);
    return d.toLocaleDateString('en-IN', { weekday:'short', day:'numeric', month:'short' });
  };

  FK.getOrders = function(){
    const orders = FK.getJSON('fk_orders', []);
    return Array.isArray(orders) ? orders.sort((a,b)=> String((b && b.createdAt) || '').localeCompare(String((a && a.createdAt) || ''))) : [];
  };

  FK._dispatchOrdersSync = function(){
    try { document.dispatchEvent(new CustomEvent('fk:orders-sync')); } catch(e) {}
  };

  FK.setOrders = function(orders){
    const normalized = Array.isArray(orders) ? orders : [];
    localStorage.setItem('fk_orders', JSON.stringify(normalized));
    FK._dispatchOrdersSync();
    return normalized;
  };

  FK.syncOrdersFromServer = async function(){
    try {
      const res = await fetch('assets/orders_api.php', { credentials:'same-origin', cache:'no-store' });
      if (!res.ok) throw new Error('Orders sync failed');
      const data = await res.json();
      if (data && Array.isArray(data.orders)) {
        FK.setOrders(data.orders);
        return data.orders;
      }
    } catch(e) {}
    return FK.getOrders();
  };

  FK.updateOrder = function(id, patch){
    const orders = FK.getOrders();
    const idx = orders.findIndex(o => o && o.id === id);
    if (idx < 0) return null;
    orders[idx] = Object.assign({}, orders[idx], patch || {});
    FK.setOrders(orders);
    fetch('assets/orders_api.php', {
      method:'POST',
      credentials:'same-origin',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ action:'update', id:id, patch:patch || {} })
    }).catch(function(){});
    if (localStorage.getItem('last_order_id') === id) {
      localStorage.setItem('last_order_snapshot', JSON.stringify(orders[idx]));
    }
    return orders[idx];
  };

  FK.persistLatestOrder = function(meta){
    meta = meta || {};
    const cart = FK.getJSON('pay_cart', []);
    const token = localStorage.getItem('fk_checkout_token') || ('CHK' + Date.now().toString(36).toUpperCase());
    const orders = FK.getOrders();
    const existing = orders.find(o => o && o.checkoutToken === token);
    if (existing) {
      localStorage.setItem('last_order_id', existing.id);
      localStorage.setItem('last_order_snapshot', JSON.stringify(existing));
      return existing;
    }

    const qtyTotal = cart.length ? FK.totalQty(cart) : (parseInt(localStorage.getItem('pay_qty') || '1', 10) || 1);
    const total = FK.toNumber(localStorage.getItem('pay_total')) || FK.toNumber(localStorage.getItem('pay_price'));
    const mrp = FK.toNumber(localStorage.getItem('pay_mrp')) || total;
    const first = cart[0] || {};
    const orderId = 'FK' + Date.now().toString().slice(-10);
    const deliveryDays = meta.paymentMode === 'cod' ? 4 : 3;
    const user = FK.getCurrentUser() || {};
    const order = {
      id: orderId,
      checkoutToken: token,
      createdAt: new Date().toISOString(),
      date: FK._fmtDate(new Date()),
      total: total,
      items: qtyTotal,
      status: meta.status || 'processing',
      deliveryMsg: meta.deliveryMsg || 'Order confirmed & being packed',
      deliveryDate: meta.deliveryDate || ('Expected by ' + FK._fmtDelivery(deliveryDays)),
      progress: meta.progress || 25,
      paymentMode: meta.paymentMode || 'upi',
      user: {
        email: user.email || '',
        mobile: user.mobile || '',
        name: user.name || ''
      },
      address: {
        name: localStorage.getItem('pay_address_name') || user.name || '',
        phone: localStorage.getItem('pay_address_phone') || user.mobile || '',
        line: localStorage.getItem('pay_address') || ''
      },
      pricing: {
        subtotal: FK.toNumber(localStorage.getItem('pay_price')),
        mrp: mrp,
        delivery: FK.toNumber(localStorage.getItem('pay_delivery')),
        donation: FK.toNumber(localStorage.getItem('pay_donation')),
        total: total,
        emi: meta.emi || null
      },
      product: {
        id: first.id || localStorage.getItem('pid') || '',
        brand: first.brand || localStorage.getItem('pay_brand') || '',
        name: localStorage.getItem('pay_name') || first.name || 'Product',
        price: total,
        img: first.img || localStorage.getItem('pay_img') || ''
      },
      cart: cart
    };

    orders.unshift(order);
    FK.setOrders(orders);
    localStorage.setItem('last_order_id', orderId);
    localStorage.setItem('last_order_snapshot', JSON.stringify(order));

    fetch('assets/orders_api.php', {
      method:'POST',
      credentials:'same-origin',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ action:'create', order:order })
    }).catch(function(){});

    return order;
  };

  FK.getLatestOrder = function(){
    const snap = localStorage.getItem('last_order_snapshot');
    if (snap) {
      try { return JSON.parse(snap); } catch(e) {}
    }
    const id = localStorage.getItem('last_order_id');
    if (!id) return null;
    const orders = FK.getOrders();
    return orders.find(o => o && o.id === id) || null;
  };

  FK.attachImageFallbacks = function(scope){
    scope = scope || document;
    try {
      scope.querySelectorAll('img').forEach(function(img){
        if (img.dataset.fkImgFallbackBound) return;
        img.dataset.fkImgFallbackBound = '1';
        img.addEventListener('error', function(){
          if (img.dataset.fkImgFallbackApplied) return;
          img.dataset.fkImgFallbackApplied = '1';
          img.src = 'assets/placeholder.png';
        }, { once: true });
      });
    } catch(e) {}
  };

  if (typeof document !== 'undefined') {
    function _fkBoot() {
      FK.attachImageFallbacks(document);

      // ── Selective sync — only fetch what this page needs ──
      // Pages declare needs via <body data-fk-sync="auth,cart,wishlist,orders">
      // Fallback: if no attribute, sync auth+cart+wishlist (safe default, skip orders)
      var bodyEl   = document.body || {};
      var syncAttr = (bodyEl.getAttribute && bodyEl.getAttribute('data-fk-sync')) || 'auth,cart,wishlist';
      var needs    = syncAttr.split(',').map(function(s){ return s.trim(); });

      var syncs = [];
      if (needs.indexOf('auth')     >= 0) syncs.push(FK.syncAuthFromServer());
      if (needs.indexOf('cart')     >= 0) syncs.push(FK.syncStateFromServer('cart'));
      if (needs.indexOf('wishlist') >= 0) syncs.push(FK.syncStateFromServer('wishlist'));
      if (needs.indexOf('orders')   >= 0) syncs.push(FK.syncOrdersFromServer());

      Promise.allSettled(syncs).then(function(){ FK.updateGlobalBadges(); });

      // ── Narrow MutationObserver — only watch for added IMG nodes ──
      if (window.MutationObserver) {
        try {
          var obs = new MutationObserver(function(muts){
            for (var i = 0; i < muts.length; i++) {
              var nodes = muts[i].addedNodes;
              for (var j = 0; j < nodes.length; j++) {
                var node = nodes[j];
                if (!node || node.nodeType !== 1) continue;
                if (node.tagName === 'IMG') {
                  FK.attachImageFallbacks(node.parentNode || document);
                } else if (node.querySelector) {
                  // Only attach if the subtree actually contains images
                  if (node.querySelector('img')) FK.attachImageFallbacks(node);
                }
              }
            }
          });
          // Watch only the main content area, not the whole document
          var watchTarget = document.getElementById('main') ||
                            document.getElementById('content') ||
                            document.body;
          obs.observe(watchTarget, { childList: true, subtree: true });
        } catch(e) {}
      }
    }
    // Safe DOMContentLoaded — works whether deferred or inline
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', _fkBoot);
    } else {
      _fkBoot();
    }
  }
})();
