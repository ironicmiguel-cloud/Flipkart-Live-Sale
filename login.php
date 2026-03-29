<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Login – Flipkart</title>
    <meta name="description" content="Flipkart – login page">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --blue:#2874f0; --orange:#fb641b; --green:#388e3c;
  --red:#d32f2f; --text:#212121; --muted:#878787;
  --border:#e0e0e0; --bg:#f1f3f6; --card:#fff;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Noto Sans',sans-serif;background:var(--bg);min-height:100vh;display:flex;flex-direction:column;}

/* SPLIT LAYOUT */
.page{display:flex;min-height:100vh;}
.left-panel{
  background:linear-gradient(160deg,#1a56db 0%,#2874f0 50%,#1565c0 100%);
  width:38%;flex-shrink:0;
  display:flex;flex-direction:column;justify-content:center;
  padding:40px 36px;position:relative;overflow:hidden;
}
.left-panel::before{
  content:'';position:absolute;top:-80px;right:-80px;
  width:300px;height:300px;
  background:rgba(255,255,255,.07);border-radius:50%;
}
.left-panel::after{
  content:'';position:absolute;bottom:-60px;left:-60px;
  width:200px;height:200px;
  background:rgba(255,255,255,.05);border-radius:50%;
}
.lp-logo{color:#fff;font-size:26px;font-weight:700;font-style:italic;margin-bottom:6px;}
.lp-plus{font-size:12px;font-weight:600;color:rgba(255,255,255,.75);letter-spacing:.08em;margin-bottom:28px;}
.lp-title{color:#fff;font-size:22px;font-weight:700;line-height:1.4;margin-bottom:10px;}
.lp-sub{color:rgba(255,255,255,.75);font-size:13px;line-height:1.7;}
.lp-img{margin-top:32px;font-size:80px;text-align:center;animation:float 3s ease-in-out infinite;}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}

/* RIGHT PANEL */
.right-panel{
  flex:1;display:flex;align-items:center;justify-content:center;
  padding:24px 16px;
}
.form-box{width:100%;max-width:380px;}

/* TABS */
.tabs{display:flex;background:#f5f5f5;border-radius:10px;padding:4px;margin-bottom:24px;}
.tab{
  flex:1;padding:10px;text-align:center;
  font-size:14px;font-weight:600;color:var(--muted);
  cursor:pointer;border-radius:8px;transition:.2s;border:none;background:none;
  font-family:inherit;
}
.tab.active{background:#fff;color:var(--blue);box-shadow:0 2px 8px rgba(0,0,0,.1);}

/* FORM */
.form-title{font-size:20px;font-weight:700;color:var(--text);margin-bottom:4px;}
.form-sub{font-size:13px;color:var(--muted);margin-bottom:22px;}

.field{margin-bottom:16px;position:relative;}
.field label{display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.03em;text-transform:uppercase;}
.field input{
  width:100%;border:1.5px solid var(--border);
  border-radius:8px;padding:12px 14px;
  font-size:14px;font-family:inherit;color:var(--text);
  outline:none;transition:.2s;background:#fff;
}
.field input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(40,116,240,.1);}
.field input.error{border-color:var(--red);}
.field .eye-btn{
  position:absolute;right:12px;bottom:12px;
  background:none;border:none;cursor:pointer;font-size:18px;color:var(--muted);
}
.err-msg{font-size:11.5px;color:var(--red);margin-top:4px;display:none;}
.err-msg.show{display:block;}

/* PASSWORD STRENGTH */
.strength-wrap{margin-top:6px;display:none;}
.strength-wrap.show{display:block;}
.strength-bars{display:flex;gap:4px;margin-bottom:4px;}
.s-bar{flex:1;height:4px;border-radius:2px;background:#e0e0e0;transition:.3s;}
.s-label{font-size:11px;color:var(--muted);}

.forgot{text-align:right;margin-top:-8px;margin-bottom:16px;}
.forgot a{font-size:12.5px;color:var(--blue);font-weight:600;text-decoration:none;}
.forgot a:hover{text-decoration:underline;}

/* SUBMIT */
.submit-btn{
  width:100%;padding:14px;
  background:linear-gradient(135deg,#ff9800,#fb641b);
  color:#fff;border:none;border-radius:8px;
  font-size:15px;font-weight:700;cursor:pointer;
  font-family:inherit;letter-spacing:.03em;
  box-shadow:0 3px 10px rgba(251,100,27,.35);
  transition:.2s;position:relative;overflow:hidden;
}
.submit-btn:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(251,100,27,.4);}
.submit-btn:active{transform:translateY(0);}
.submit-btn.loading{pointer-events:none;opacity:.8;}

/* DIVIDER */
.or-divider{display:flex;align-items:center;gap:10px;margin:18px 0;color:var(--muted);font-size:12px;}
.or-divider::before,.or-divider::after{content:'';flex:1;height:1px;background:var(--border);}

/* SOCIAL */
.social-btns{display:flex;gap:10px;}
.social-btn{
  flex:1;padding:11px;border:1.5px solid var(--border);
  border-radius:8px;background:#fff;cursor:pointer;
  font-size:13px;font-weight:600;color:var(--text);
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:.15s;font-family:inherit;
}
.social-btn:hover{border-color:var(--blue);background:#f5f8ff;}

/* TERMS */
.terms{font-size:11.5px;color:var(--muted);text-align:center;margin-top:16px;line-height:1.6;}
.terms a{color:var(--blue);text-decoration:none;font-weight:500;}

/* MOBILE */
@media(max-width:600px){
  .left-panel{display:none;}
  .right-panel{padding:20px 16px;align-items:flex-start;padding-top:40px;}
  .form-box{max-width:100%;}
}

/* TOAST */
.toast{
  position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
  background:#323232;color:#fff;padding:10px 20px;
  border-radius:24px;font-size:13px;z-index:9999;
  opacity:0;transition:.3s;pointer-events:none;white-space:nowrap;
}
.toast.show{opacity:1;}

@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.form-box{animation:fadeUp .4s ease both;}

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
<div class="page">

  <!-- LEFT PANEL -->
  <div class="left-panel">
    <div class="lp-logo">Flipkart</div>
    <div class="lp-plus">✦ PLUS MEMBER</div>
    <div class="lp-title">Login for the<br>Best Experience</div>
    <div class="lp-sub">Get access to your Orders,<br>Wishlist and Recommendations</div>
    <div class="lp-img"><svg viewBox="0 0 24 24" width="48" height="48" fill="#2874f0"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg></div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <div class="form-box">

      <!-- TABS -->
      <div class="tabs">
        <button class="tab active" id="tabLogin" onclick="switchTab('login')">Login</button>
        <button class="tab" id="tabSignup" onclick="switchTab('signup')">Sign Up</button>
      </div>

      <!-- LOGIN FORM -->
      <div id="loginForm">
        <div class="form-title">Welcome Back!</div>
        <div class="form-sub">Login to your Flipkart account</div>

        <div class="field">
          <label>Mobile / Email</label>
          <input type="text" id="loginId" placeholder="Enter mobile or email" oninput="clearErr('loginIdErr')">
          <div class="err-msg" id="loginIdErr">Please enter a valid mobile or email</div>
        </div>

        <div class="field">
          <label>Password</label>
          <input type="password" id="loginPass" placeholder="Enter your password" oninput="clearErr('loginPassErr')">
          <button class="eye-btn" onclick="toggleEye('loginPass',this)" type="button"><svg viewBox="0 0 24 24" width="18" height="18" fill="#888"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
          <div class="err-msg" id="loginPassErr">Password must be at least 6 characters</div>
        </div>

        <div class="forgot"><a href="#" onclick="forgotPass()">Forgot Password?</a></div>

        <button class="submit-btn" onclick="doLogin()" id="loginBtn">LOGIN</button>

        <div class="or-divider">or continue with</div>
        <div class="social-btns">
          <button class="social-btn" onclick="socialLogin('Google')">
            <span style="font-size:16px;">G</span> Google
          </button>
          <button class="social-btn" onclick="socialLogin('Facebook')">
            <span style="font-size:16px;">f</span> Facebook
          </button>
        </div>

        <div class="terms">By continuing, you agree to Flipkart's <a href="#">Terms</a> and <a href="#">Privacy Policy</a></div>
      </div>

      <!-- SIGNUP FORM -->
      <div id="signupForm" style="display:none;">
        <div class="form-title">Create Account</div>
        <div class="form-sub">Join millions of happy shoppers</div>

        <div class="field">
          <label>Full Name</label>
          <input type="text" id="signName" placeholder="Enter your full name" oninput="clearErr('signNameErr')">
          <div class="err-msg" id="signNameErr">Please enter your name</div>
        </div>

        <div class="field">
          <label>Mobile Number</label>
          <input type="tel" id="signMobile" placeholder="10-digit mobile number" maxlength="10" oninput="clearErr('signMobileErr')">
          <div class="err-msg" id="signMobileErr">Enter a valid 10-digit mobile number</div>
        </div>

        <div class="field">
          <label>Email Address</label>
          <input type="email" id="signEmail" placeholder="yourname@email.com" oninput="clearErr('signEmailErr')">
          <div class="err-msg" id="signEmailErr">Enter a valid email address</div>
        </div>

        <div class="field">
          <label>Password</label>
          <input type="password" id="signPass" placeholder="Create a strong password" oninput="checkStrength();clearErr('signPassErr')">
          <button class="eye-btn" onclick="toggleEye('signPass',this)" type="button"><svg viewBox="0 0 24 24" width="18" height="18" fill="#888"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
          <div class="err-msg" id="signPassErr">Password must be at least 6 characters</div>
          <div class="strength-wrap" id="strengthWrap">
            <div class="strength-bars">
              <div class="s-bar" id="sb1"></div>
              <div class="s-bar" id="sb2"></div>
              <div class="s-bar" id="sb3"></div>
              <div class="s-bar" id="sb4"></div>
            </div>
            <div class="s-label" id="sLabel">Weak</div>
          </div>
        </div>

        <button class="submit-btn" onclick="doSignup()" id="signupBtn">CREATE ACCOUNT</button>

        <div class="terms">By signing up, you agree to Flipkart's <a href="#">Terms</a> and <a href="#">Privacy Policy</a></div>
      </div>

    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
function fkLocalList(key){ try { return JSON.parse(localStorage.getItem(key) || '[]') || []; } catch(e){ return []; } }
function fkLocalSet(key, value){ try { localStorage.setItem(key, JSON.stringify(value)); } catch(e) {} }
function authService(){
  if (window.FK && typeof window.FK.login === 'function' && typeof window.FK.signup === 'function') return window.FK;
  if (window.__fkAuthFallback) return window.__fkAuthFallback;
  async function api(body){
    const opts = { credentials:'same-origin', cache:'no-store' };
    if (body) {
      opts.method = 'POST';
      opts.headers = { 'Content-Type':'application/json' };
      opts.body = JSON.stringify(body);
    }
    const res = await fetch('assets/auth_api.php', opts);
    const data = await res.json().catch(function(){ return { ok:false, error:'Invalid server response' }; });
    if (!res.ok || !data || data.ok === false) throw new Error((data && data.error) || ('Request failed: ' + res.status));
    return data;
  }
  window.__fkAuthFallback = {
    login: async function(identifier, password){
      const data = await api({ action:'login', identifier:identifier, password:password, guest_cart: fkLocalList('flipkart_cart'), guest_wishlist: fkLocalList('fk_wishlist') });
      if (data.user) {
        try { localStorage.setItem('fk_user', JSON.stringify(data.user)); localStorage.setItem('isLoggedIn', 'true'); } catch(e) {}
      }
      return data.user || null;
    },
    signup: async function(payload){
      payload = payload || {};
      const data = await api({ action:'signup', name:payload.name || '', mobile:payload.mobile || '', email:payload.email || '', password:payload.password || '', guest_cart: fkLocalList('flipkart_cart'), guest_wishlist: fkLocalList('fk_wishlist') });
      if (data.user) {
        try { localStorage.setItem('fk_user', JSON.stringify(data.user)); localStorage.setItem('isLoggedIn', 'true'); } catch(e) {}
      }
      return data.user || null;
    },
    logout: async function(){ await api({ action:'logout' }); try { localStorage.removeItem('fk_user'); localStorage.removeItem('isLoggedIn'); } catch(e) {} return true; },
    syncCurrentUser: async function(){ const data = await api(null); if (data && data.user) { try { localStorage.setItem('fk_user', JSON.stringify(data.user)); localStorage.setItem('isLoggedIn', 'true'); } catch(e) {} return data.user; } try { localStorage.removeItem('fk_user'); localStorage.removeItem('isLoggedIn'); } catch(e) {} return null; }
  };
  return window.__fkAuthFallback;
}

// ══════════════════════════════════
//  TAB SWITCH
// ══════════════════════════════════
function switchTab(tab) {
  document.getElementById('tabLogin').classList.toggle('active', tab==='login');
  document.getElementById('tabSignup').classList.toggle('active', tab==='signup');
  document.getElementById('loginForm').style.display  = tab==='login'  ? 'block' : 'none';
  document.getElementById('signupForm').style.display = tab==='signup' ? 'block' : 'none';
}

// ══════════════════════════════════
//  LOGIN
// ══════════════════════════════════
async function doLogin() {
  const id   = document.getElementById('loginId').value.trim();
  const pass = document.getElementById('loginPass').value;
  let valid  = true;

  if (!id) { showErr('loginIdErr'); valid=false; }
  if (pass.length < 6) { showErr('loginPassErr'); valid=false; }
  if (!valid) return;

  setLoading('loginBtn', true);
  try {
    const auth = authService();
    const user = await auth.login(id, pass);
    showToast('✅ Welcome back, ' + ((user && user.name) || 'User') + '!');
    const ret = new URLSearchParams(location.search).get('return');
    setTimeout(() => { window.location.href = ret || 'index.php'; }, 600);
  } catch (err) {
    setLoading('loginBtn', false);
    const msg = (err && err.message) || 'Login failed';
    if (/Account not found/i.test(msg)) {
      showToast('❌ Account not found. Please sign up!');
      switchTab('signup');
    } else if (/Incorrect password/i.test(msg)) {
      showErr('loginPassErr', 'Incorrect password');
    } else {
      showToast('⚠️ ' + msg);
    }
    return;
  }
  setLoading('loginBtn', false);
}

// ══════════════════════════════════
//  SIGNUP
// ══════════════════════════════════
async function doSignup() {
  const name   = document.getElementById('signName').value.trim();
  const mobile = document.getElementById('signMobile').value.trim();
  const email  = document.getElementById('signEmail').value.trim();
  const pass   = document.getElementById('signPass').value;
  let valid = true;

  if (!name)                { showErr('signNameErr');   valid=false; }
  if (!/^\d{10}$/.test(mobile)) { showErr('signMobileErr'); valid=false; }
  if (!/\S+@\S+\.\S+/.test(email)) { showErr('signEmailErr'); valid=false; }
  if (pass.length < 6)      { showErr('signPassErr');   valid=false; }
  if (!valid) return;

  setLoading('signupBtn', true);
  try {
    const auth = authService();
    await auth.signup({ name, mobile, email, password: pass });
    showToast('🎉 Account created! Welcome, ' + name + '!');
    const ret = new URLSearchParams(location.search).get('return');
    setTimeout(() => { window.location.href = ret || 'index.php'; }, 600);
  } catch (err) {
    setLoading('signupBtn', false);
    const msg = (err && err.message) || 'Signup failed';
    if (/already exists/i.test(msg)) {
      showToast('⚠️ Account already exists! Please login.');
      switchTab('login');
    } else {
      showToast('⚠️ ' + msg);
    }
    return;
  }
  setLoading('signupBtn', false);
}

// ══════════════════════════════════
//  HELPERS
// ══════════════════════════════════
function toggleEye(id, btn) {
  const inp = document.getElementById(id);
  if (inp.type === 'password') { inp.type='text'; btn.innerHTML='<svg viewBox="0 0 24 24" width="18" height="18" fill="#888"><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>'; }
  else { inp.type='password'; btn.innerHTML='<svg viewBox="0 0 24 24" width="18" height="18" fill="#888"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>'; }
}

function checkStrength() {
  const pass = document.getElementById('signPass').value;
  const wrap = document.getElementById('strengthWrap');
  if (!pass) { wrap.classList.remove('show'); return; }
  wrap.classList.add('show');
  let score = 0;
  if (pass.length >= 6)  score++;
  if (pass.length >= 10) score++;
  if (/[A-Z]/.test(pass) && /[0-9]/.test(pass)) score++;
  if (/[^A-Za-z0-9]/.test(pass)) score++;
  const colors = ['#d32f2f','#ff9800','#2874f0','#388e3c'];
  const labels = ['Weak','Fair','Good','Strong'];
  [1,2,3,4].forEach(i => {
    const b = document.getElementById('sb'+i);
    b.style.background = i <= score ? colors[score-1] : '#e0e0e0';
  });
  document.getElementById('sLabel').textContent = labels[score-1] || 'Weak';
  document.getElementById('sLabel').style.color = colors[score-1] || '#d32f2f';
}

function showErr(id, msg) {
  const el = document.getElementById(id);
  if (msg) el.textContent = msg;
  el.classList.add('show');
  document.getElementById(id.replace('Err','')).classList.add('error');
}
function clearErr(id) {
  document.getElementById(id).classList.remove('show');
  document.getElementById(id.replace('Err','')).classList.remove('error');
}
function setLoading(btnId, on) {
  const btn = document.getElementById(btnId);
  btn.classList.toggle('loading', on);
  btn.textContent = on ? 'Please wait...' : (btnId==='loginBtn' ? 'LOGIN' : 'CREATE ACCOUNT');
}
function forgotPass() { showToast('📧 Reset link sent to your email!'); }
function socialLogin(p) { showToast('Coming soon: ' + p + ' Login'); }
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}

document.addEventListener('fk:auth-sync', function(e){
  const user = e.detail && e.detail.user;
  if (user && !new URLSearchParams(location.search).get('return')) {
    // Already authenticated visitors do not need the auth page.
  }
});
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
