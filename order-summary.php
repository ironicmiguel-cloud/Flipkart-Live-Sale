<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>Order Summary</title>
<link rel="icon" type="image/png" href="assets/icons/app-icon.png">
<style>
*{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent}
body{font-family:-apple-system,'Segoe UI',sans-serif;background:#f1f3f6;color:#212121;min-height:100vh;padding-bottom:80px}

/* ── TOPBAR ── */
.topbar{background:#2874f0;padding:0 14px;height:52px;display:flex;align-items:center;gap:14px;position:sticky;top:0;z-index:100;box-shadow:0 2px 8px rgba(40,116,240,.3)}
.topbar-back{color:#fff;font-size:20px;cursor:pointer;padding:4px;background:none;border:none;line-height:1}
.topbar-title{color:#fff;font-size:16px;font-weight:600;letter-spacing:.2px}

/* ── STEPPER ── */
.stepper{background:#fff;padding:14px 16px;display:flex;align-items:center;justify-content:center;gap:0;border-bottom:1px solid #e0e0e0}
.step{display:flex;flex-direction:column;align-items:center;gap:5px;flex:1;position:relative}
.step-circle{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;z-index:1}
.step-circle.done{background:#2874f0;color:#fff}
.step-circle.active{background:#2874f0;color:#fff;box-shadow:0 0 0 3px rgba(40,116,240,.2)}
.step-circle.todo{background:#fff;color:#9e9e9e;border:2px solid #d0d0d0}
.step-label{font-size:11px;font-weight:600;color:#9e9e9e;white-space:nowrap}
.step-label.active{color:#2874f0;font-weight:700}
.step-label.done-lbl{color:#212121}
.step-line{flex:1;height:2px;background:#d0d0d0;margin-top:-22px;z-index:0}
.step-line.done-line{background:#2874f0}

/* ── SECTIONS ── */
.section{background:#fff;margin-bottom:8px}
.section-pad{padding:14px 16px}
.divider{height:1px;background:#f0f0f0;margin:0 16px}

/* ── ADDRESS ── */
.addr-row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.addr-name{font-size:14px;font-weight:700;color:#212121;display:flex;align-items:center;gap:8px}
.addr-tag{font-size:10px;font-weight:700;color:#717171;background:#f5f5f5;border:1px solid #e0e0e0;border-radius:3px;padding:2px 6px;letter-spacing:.4px}
.addr-text{font-size:13px;color:#212121;line-height:1.55;margin-top:5px}
.addr-phone{font-size:13px;color:#212121;margin-top:4px;font-weight:500}
.btn-change{padding:6px 14px;border:1.5px solid #2874f0;border-radius:4px;background:#fff;color:#2874f0;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0;font-family:inherit}
.btn-change:active{background:#e8f0fe}

/* ── PRODUCT CARD ── */
.prod-row{display:flex;gap:12px;align-items:flex-start}
.prod-img{width:88px;height:88px;border-radius:4px;object-fit:contain;flex-shrink:0;background:#f8f8f8;border:1px solid #f0f0f0}
.prod-img-ph{width:88px;height:88px;border-radius:4px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:32px;flex-shrink:0;border:1px solid #f0f0f0}
.prod-info{flex:1;min-width:0}
.prod-name{font-size:14px;font-weight:400;color:#212121;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.prod-variant{font-size:12px;color:#878787;margin-top:2px}
.rating-row{display:flex;align-items:center;gap:6px;margin-top:5px}
.stars{display:flex;gap:1px}
.star{font-size:11px}
.star.filled{color:#388e3c}
.star.half{color:#388e3c}
.star.empty{color:#d0d0d0}
.rating-num{font-size:12px;font-weight:700;color:#388e3c}
.review-cnt{font-size:11px;color:#878787}
.assured-badge{display:flex;align-items:center;gap:3px;background:#fff;border:1px solid #e0e0e0;border-radius:3px;padding:1px 5px}
.assured-badge span{font-size:10px;font-weight:700;color:#2874f0}
.qty-row{display:flex;align-items:center;gap:10px;margin-top:8px}
.qty-sel{border:1.5px solid #d0d0d0;border-radius:4px;padding:4px 8px;font-size:13px;font-weight:600;color:#212121;background:#fff;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:4px}
.price-row2{display:flex;align-items:center;gap:8px;margin-top:0}
.price-off{font-size:13px;font-weight:700;color:#388e3c;display:flex;align-items:center;gap:2px}
.price-mrp{font-size:13px;color:#878787;text-decoration:line-through}
.price-final{font-size:16px;font-weight:700;color:#212121}
.protect-fee{font-size:12px;color:#212121;margin-top:4px}
.pay-alt{font-size:12px;color:#212121;margin-top:3px;display:flex;align-items:center;gap:4px}
.pay-alt .fk-coin{background:#f5a623;color:#fff;font-size:10px;font-weight:800;width:16px;height:16px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center}
.offers-link{font-size:12px;font-weight:700;color:#2874f0;margin-top:4px;display:block}

/* ── DELIVERY OPTIONS ── */
.del-opt{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;cursor:pointer}
.del-opt:active{background:#f5f9ff}
.radio-outer{width:18px;height:18px;border-radius:50%;border:2px solid #d0d0d0;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;transition:border-color .15s}
.radio-outer.checked{border-color:#2874f0}
.radio-inner{width:9px;height:9px;border-radius:50%;background:#2874f0;display:none}
.radio-outer.checked .radio-inner{display:block}
.del-info{flex:1}
.del-title{font-size:13px;font-weight:600;color:#212121;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.del-fee{font-size:13px;color:#212121;font-weight:600}
.del-note{font-size:12px;color:#878787;margin-top:3px;display:flex;align-items:center;gap:4px}
.minutes-logo{height:16px;vertical-align:middle}
.del-separator{font-size:13px;color:#878787;font-weight:600;padding:0 4px}

/* ── DOORSTEP / GST ── */
.info-row{display:flex;align-items:center;gap:10px;padding:13px 16px}
.info-icon{font-size:22px;flex-shrink:0}
.info-text{font-size:13px;color:#212121}
.info-link{color:#2874f0;font-weight:600;text-decoration:none}
.gst-row{display:flex;align-items:center;gap:10px;padding:13px 16px;cursor:pointer}
.checkbox{width:18px;height:18px;border:2px solid #9e9e9e;border-radius:2px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s}
.checkbox.checked{background:#2874f0;border-color:#2874f0}
.checkbox svg{display:none}
.checkbox.checked svg{display:block}
.gst-label{font-size:13px;color:#212121}

/* ── BOTTOM BAR ── */
.bottom-bar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e0e0e0;padding:10px 16px;display:flex;align-items:center;justify-content:space-between;z-index:100;box-shadow:0 -2px 8px rgba(0,0,0,.07)}
.bottom-left{}
.bottom-mrp{font-size:12px;color:#878787;text-decoration:line-through}
.bottom-price{font-size:20px;font-weight:700;color:#212121;line-height:1.1}
.view-price{font-size:12px;color:#2874f0;font-weight:600;cursor:pointer;display:block;margin-top:2px}
.btn-continue{background:#fb641b;color:#fff;border:none;border-radius:2px;padding:13px 28px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;letter-spacing:.3px;white-space:nowrap}
.btn-continue:active{background:#e55a17}
/* ── PRICE DETAILS DRAWER ── */
.drawer-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;display:none;opacity:0;transition:opacity .25s}
.drawer-overlay.open{display:block;opacity:1}
.price-drawer{position:fixed;bottom:0;left:0;right:0;background:#fff;border-radius:12px 12px 0 0;z-index:201;padding:0 0 24px;transform:translateY(100%);transition:transform .3s cubic-bezier(.4,0,.2,1)}
.price-drawer.open{transform:translateY(0)}
.drawer-handle{width:36px;height:4px;background:#e0e0e0;border-radius:2px;margin:12px auto 0}
.drawer-title{font-size:15px;font-weight:700;padding:14px 16px 10px;border-bottom:1px solid #f0f0f0}
.drawer-row{display:flex;justify-content:space-between;padding:10px 16px;font-size:13px}
.drawer-row.saving{color:#388e3c}
.drawer-row.total{font-weight:700;font-size:14px;border-top:1px solid #e0e0e0;padding-top:12px;margin-top:2px}


/* ── DONATION ── */
.don-section{background:#fff;margin-bottom:8px;padding:14px 16px;animation:fadeUp .3s .22s ease both}
.don-btn{padding:8px 20px;border:1.5px solid #d0d0d0;border-radius:20px;background:#fff;font-size:13px;font-weight:600;color:#212121;cursor:pointer;font-family:inherit;transition:all .15s}
.don-btn:hover{border-color:#2874f0;color:#2874f0}
.don-btn.selected{background:#2874f0;border-color:#2874f0;color:#fff}
/* ── OPEN BOX DELIVERY ── */
.openbox-section{background:#fff;margin-bottom:8px;padding:14px 16px;border-left:4px solid #f5a623}
.openbox-title{font-size:14px;font-weight:700;color:#212121;display:flex;align-items:center;gap:8px;margin-bottom:6px}
.openbox-desc{font-size:12px;color:#555;line-height:1.55}
.openbox-desc a{color:#2874f0;font-weight:600;text-decoration:none}

/* ── PRICE BREAKDOWN CARD ── */
.price-card-wrap{background:#f1f3f6;padding:12px 10px;margin-bottom:8px}
.price-card{background:#fff;border-radius:8px;padding:0 0 4px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.pc-row{display:flex;justify-content:space-between;align-items:center;padding:11px 16px;font-size:13px;color:#212121}
.pc-row.sep{border-top:1px dashed #e8e8e8}
.pc-row.bold{font-weight:700;font-size:14px;border-top:1px solid #e0e0e0;padding-top:13px;margin-top:2px}
.pc-section-hdr{display:flex;justify-content:space-between;align-items:center;padding:10px 16px 4px;font-size:13px;font-weight:700;color:#212121;cursor:pointer;user-select:none}
.pc-section-hdr .chevron{font-size:11px;transition:transform .2s}
.pc-section-hdr.collapsed .chevron{transform:rotate(180deg)}
.pc-sub{padding:0}
.pc-sub-row{display:flex;justify-content:space-between;padding:6px 16px;font-size:12px;color:#878787}
.pc-sub-row .val{color:#212121}
.pc-discount-val{color:#388e3c;font-weight:600}
.savings-pill{margin:10px 14px 14px;background:#e8f5e9;border-radius:6px;padding:10px 14px;text-align:center;font-size:13px;font-weight:600;color:#2e7d32}

/* ── TERMS ── */
.terms-section{padding:12px 16px 10px;font-size:11px;color:#878787;line-height:1.6;background:#f1f3f6}
.terms-section a{color:#2874f0;text-decoration:none}

/* ── ANIMATIONS ── */
@keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.section{animation:fadeUp .3s ease both}
.openbox-section{animation:fadeUp .3s .05s ease both}
.price-card-wrap{animation:fadeUp .3s .25s ease both}
</style>
    <link rel="stylesheet" href="assets/shared.css?v=20260320">
</head>
<body data-fk-sync="auth,cart">

<!-- TOPBAR -->
<div class="topbar">
  <button class="topbar-back" onclick="history.back()">&#8592;</button>
  <div class="topbar-title">Order Summary</div>
</div>

<!-- STEPPER -->
<div class="stepper">
  <div class="step">
    <div class="step-circle done">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
    </div>
    <div class="step-label done-lbl">Address</div>
  </div>
  <div class="step-line done-line"></div>
  <div class="step">
    <div class="step-circle active">2</div>
    <div class="step-label active">Order Summary</div>
  </div>
  <div class="step-line"></div>
  <div class="step">
    <div class="step-circle todo">3</div>
    <div class="step-label">Payment</div>
  </div>
</div>

<!-- DELIVER TO -->
<div class="section" style="margin-top:8px">
  <div class="section-pad">
    <div class="addr-row">
      <div style="flex:1">
        <div class="addr-name">
          <span id="addrName">Loading…</span>
          <span class="addr-tag">HOME</span>
        </div>
        <div class="addr-text" id="addrText">—</div>
        <div class="addr-phone" id="addrPhone">—</div>
      </div>
      <button class="btn-change" onclick="window.location.href='address.php'">Change</button>
    </div>
  </div>
</div>

<!-- PRODUCT -->
<div class="section">
  <div class="section-pad">
    <div class="prod-row">
      <div id="prodImgWrap">
        <div class="prod-img-ph" id="prodPh">🛍️</div>
      </div>
      <div class="prod-info">
        <div class="prod-name" id="prodName">Product</div>
        <div class="prod-variant" id="prodVariant"></div>
        <div class="rating-row">
          <div class="stars" id="starsEl"></div>
          <span class="rating-num" id="ratingNum"></span>
          <span class="review-cnt" id="reviewCnt"></span>
          <div class="assured-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="#2874f0"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
            <span>Assured</span>
          </div>
        </div>
        <div class="qty-row">
          <div class="qty-sel">Qty: <span id="qtyVal">1</span> &#9660;</div>
          <div class="price-row2">
            <span class="price-off" id="priceOff"></span>
            <span class="price-mrp" id="priceMrp"></span>
            <span class="price-final" id="priceFinal">₹0</span>
          </div>
        </div>
        <div class="protect-fee">+ ₹9 Protect Promise Fee <span style="color:#9e9e9e;font-size:12px">ⓘ</span></div>
        <div class="pay-alt">Or Pay <span id="payAltAmt" style="font-weight:700">₹0</span> + <span class="fk-coin">F</span><span style="font-weight:700">50</span></div>
        <a class="offers-link">4 offers available</a>
      </div>
    </div>
  </div>

  <div class="divider"></div>

  <!-- DELIVERY OPTIONS -->
  <div class="del-opt" id="opt1" onclick="selectDelivery(1)">
    <div class="radio-outer" id="radio1">
      <div class="radio-inner"></div>
    </div>
    <div class="del-info">
      <div class="del-title">Delivery by 11 PM, Today</div>
    </div>
  </div>

  <div class="del-opt" id="opt2" onclick="selectDelivery(2)">
    <div class="radio-outer checked" id="radio2">
      <div class="radio-inner"></div>
    </div>
    <div class="del-info">
      <div class="del-title">
        Delivery in&nbsp;
        <span style="display:inline-flex;align-items:center;gap:3px">
          <span style="background:#c62828;color:#fff;font-size:11px;font-weight:900;padding:1px 5px;border-radius:2px;letter-spacing:.5px">13</span>
          <img src="data:image/png;base64,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"
               alt="MINUTES" class="minutes-logo">
        </span>
        <span class="del-separator">•</span>
        <span class="del-fee" style="color:#fb641b">₹9</span>
      </div>
      <div class="del-note">Changed to a new seller for a faster delivery ⓘ</div>
    </div>
  </div>

  <div class="divider"></div>

  <!-- DOORSTEP CANCELLATION -->
  <div class="info-row">
    <span class="info-icon">🏠</span>
    <div class="info-text">
      Doorstep Cancellation Allowed &nbsp;<a class="info-link" href="#">Know more</a>
    </div>
  </div>
</div>

<!-- USE GST INVOICE -->
<div class="section">
  <div class="gst-row" id="gstRow" onclick="toggleGst()">
    <div class="checkbox" id="gstCheck">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="white"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
    </div>
    <span class="gst-label">Use GST Invoice</span>
  </div>
</div>


<!-- DONATION SECTION -->
<div class="don-section">
  <div style="display:flex;align-items:center;gap:14px;">
    <div style="flex:1">
      <div style="font-size:14px;font-weight:700;color:#212121;margin-bottom:3px;">Donate to Flipkart Foundation</div>
      <div style="font-size:12px;color:#878787;line-height:1.4;">Support transformative social work in India</div>
    </div>
    <div class="shared-donation-art" aria-label="Flipkart Foundation"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s-6.5-4.35-8.5-8.34C1.56 8.8 3.2 5 6.95 5c2.05 0 3.29 1.1 4.05 2.18C11.76 6.1 13 5 15.05 5 18.8 5 20.44 8.8 20.5 12.66 18.5 16.65 12 21 12 21z" fill="#fb641b"/><path d="M12 8v7M8.5 11.5H15.5" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg></div>
  </div>
  <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap;">
    <button class="don-btn" onclick="selectDonation(this,10)">₹10</button>
    <button class="don-btn" onclick="selectDonation(this,20)">₹20</button>
    <button class="don-btn" onclick="selectDonation(this,50)">₹50</button>
    <button class="don-btn" onclick="selectDonation(this,100)">₹100</button>
  </div>
  <div id="donNote" style="display:none;margin-top:10px;font-size:11px;color:#878787;background:#f9f9f9;border:1px solid #e8e8e8;border-radius:6px;padding:8px 10px;">
    Note: GST and No cost EMI will not be applicable on donation amount.
  </div>
</div>

<!-- OPEN BOX DELIVERY -->
<div class="openbox-section">
  <div class="openbox-title">
    <span style="font-size:22px">📦</span>
    Rest assured with Open Box Delivery
  </div>
  <div class="openbox-desc">
    Delivery agent will open the package so you can check for correct product, damage or missing items. Share OTP to accept the delivery. <a href="#">Why?</a>
  </div>
</div>

<!-- INLINE PRICE BREAKDOWN CARD -->
<div class="price-card-wrap">
  <div class="price-card">

    <!-- MRP -->
    <div class="pc-row">
      <span>MRP</span>
      <span id="pcMrp">₹0</span>
    </div>

    <!-- FEES SECTION -->
    <div class="pc-section-hdr" id="feesHdr" onclick="toggleSection('fees')">
      <span>Fees</span>
      <span class="chevron">&#8743;</span>
    </div>
    <div class="pc-sub" id="feesBody">
      <div class="pc-sub-row pc-sep">
        <span>Protect Promise Fee</span>
        <span class="val" id="pcProtect">+₹9</span>
      </div>
      <div class="pc-sub-row">
        <span>Convenience Fee</span>
        <span class="val" id="pcConvenience">+₹9</span>
      </div>
      <div class="pc-sub-row" id="pcDeliveryRow">
        <span>Delivery Charges</span>
        <span class="val" id="pcDelivery">+₹9</span>
      </div>
    </div>

    <!-- DISCOUNTS SECTION -->
    <div class="pc-section-hdr sep" id="discHdr" onclick="toggleSection('disc')" style="border-top:1px dashed #e8e8e8">
      <span>Discounts</span>
      <span class="chevron">&#8743;</span>
    </div>
    <div class="pc-sub" id="discBody">
      <div class="pc-sub-row">
        <span>Discount on MRP</span>
        <span class="pc-discount-val" id="pcDiscount">-₹0</span>
      </div>
      <div class="pc-sub-row" id="pcDonationRow" style="display:none">
        <span>Donation</span>
        <span class="val" id="pcDonation">+₹0</span>
      </div>
    </div>

    <!-- TOTAL -->
    <div class="pc-row bold" style="border-top:1px solid #e0e0e0">
      <span>Total Amount</span>
      <span id="pcTotal">₹0</span>
    </div>

    <!-- SAVINGS PILL -->
    <div class="savings-pill" id="savingsPill" style="display:none">
      You will save <span id="savingsAmt">₹0</span> on this order
    </div>

  </div>
</div>

<!-- TERMS -->
<div class="terms-section">
  By continuing with the order, you confirm that you are above 18 years of age, and you agree to Flipkart's <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a>
</div>

<!-- BOTTOM BAR -->
<div class="bottom-bar">
  <div class="bottom-left">
    <div class="bottom-mrp" id="barMrp"></div>
    <div class="bottom-price" id="barPrice">₹0</div>
    <span class="view-price" onclick="openDrawer()">View price details</span>
  </div>
  <button class="btn-continue" onclick="goPayment()">Continue</button>
</div>

<!-- PRICE DRAWER -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
<div class="price-drawer" id="priceDrawer">
  <div class="drawer-handle"></div>
  <div class="drawer-title">Price Details</div>
  <div class="drawer-row">
    <span id="drItemLbl">Price (1 item)</span>
    <span id="drMrp">₹0</span>
  </div>
  <div class="drawer-row saving">
    <span>Discount</span>
    <span id="drDiscount">-₹0</span>
  </div>
  <div class="drawer-row" id="drDeliveryRow">
    <span>Delivery Charges</span>
    <span id="drDelivery" style="color:#388e3c;font-weight:600">FREE</span>
  </div>
  <div class="drawer-row">
    <span>Protect Promise Fee</span>
    <span>+₹9</span>
  </div>
  <div class="drawer-row saving" id="drDonationRow" style="display:none">
    <span>Donation</span>
    <span id="drDonation">+₹0</span>
  </div>
  <div class="drawer-row total">
    <span>Total Amount</span>
    <span id="drTotal">₹0</span>
  </div>
</div>

<script>
// ── READ DATA ──────────────────────────────────────────────────
const payPrice   = parseFloat(localStorage.getItem('pay_price'))  || 0;
const payMrp     = parseFloat(localStorage.getItem('pay_mrp'))    || payPrice;
const payName    = localStorage.getItem('pay_name')   || '';
const payBrand   = localStorage.getItem('pay_brand')  || '';
const payOff     = localStorage.getItem('pay_off')    || '';
const payImg     = localStorage.getItem('pay_img')    || '';
const payQty     = parseInt(localStorage.getItem('pay_qty') || '1');
const payRating  = parseFloat(localStorage.getItem('pay_rating') || '0');
const payReviews = localStorage.getItem('pay_reviews') || '0';
const payVariant = localStorage.getItem('pay_variant') || '';
const isEmiCheckout = localStorage.getItem('pay_emi_enabled') === '1';
const emiMonths  = parseInt(localStorage.getItem('pay_emi_plan') || '0', 10) || 0;
const emiMonthly = parseFloat(localStorage.getItem('pay_emi_monthly') || '0') || 0;
const checkoutBasePrice = (isEmiCheckout && emiMonthly > 0) ? emiMonthly : payPrice;
const addrName   = localStorage.getItem('pay_address_name')  || '';
const addrFull   = localStorage.getItem('pay_address')       || '';
const addrPhone  = localStorage.getItem('pay_address_phone') || '';

let selectedDelivery = 2;
let donationAmt = 0;
let gstChecked = false;
const DELIVERY_FEE   = 9;
const PROTECT_FEE    = 9;
const CONVENIENCE_FEE = 9;

// ── POPULATE ──────────────────────────────────────────────────
if (!payName || !checkoutBasePrice) {
  window.location.href = 'cart.php';
}

document.getElementById('addrName').textContent  = addrName || 'Add delivery details';
document.getElementById('addrText').textContent  = addrFull || 'Choose an address before continuing';
document.getElementById('addrPhone').textContent = addrPhone || '';
document.getElementById('prodName').textContent  = payName;
document.getElementById('qtyVal').textContent    = payQty;
if (payVariant) document.getElementById('prodVariant').textContent = payVariant;
if (isEmiCheckout && emiMonths > 0) {
  const pv = document.getElementById('prodVariant');
  if (pv) pv.textContent = (pv.textContent ? pv.textContent + ' • ' : '') + 'EMI ' + emiMonths + ' months';
}

// Product image
if (payImg) {
  const img = document.createElement('img');
  img.src       = payImg;
  img.className = 'prod-img';
  img.alt       = payName;
  img.onerror   = function() {
    const ph = document.createElement('div');
    ph.className = 'prod-img-ph';
    ph.textContent = '🛍️';
    img.replaceWith(ph);
  };
  document.getElementById('prodPh').replaceWith(img);
}

// Price
const offPct = payMrp > payPrice ? Math.round((1 - payPrice/payMrp)*100) : (parseInt(payOff)||0);
if (offPct > 0) {
  document.getElementById('priceOff').innerHTML = '&#8595;' + offPct + '%';
  document.getElementById('priceMrp').textContent = '₹' + payMrp.toLocaleString('en-IN');
}
document.getElementById('priceFinal').textContent  = '₹' + checkoutBasePrice.toLocaleString('en-IN');
document.getElementById('payAltAmt').textContent   = isEmiCheckout && emiMonths > 0 ? ('EMI ₹' + emiMonthly.toLocaleString('en-IN') + ' x ' + emiMonths) : ('₹' + Math.max(0, payPrice - 50).toLocaleString('en-IN'));

// Stars
const starsEl = document.getElementById('starsEl');
for (let i=1; i<=5; i++) {
  const s = document.createElement('span');
  s.className = 'star ' + (i <= Math.floor(payRating) ? 'filled' : (i - payRating < 1 && payRating % 1 >= 0.5 ? 'half' : 'empty'));
  s.textContent = '★';
  starsEl.appendChild(s);
}
document.getElementById('ratingNum').textContent  = payRating.toFixed(1);
document.getElementById('reviewCnt').textContent  = '(' + payReviews + ')';

// ── TOTALS ────────────────────────────────────────────────────
function calcTotal() {
  const delFee = selectedDelivery === 2 ? DELIVERY_FEE : 0;
  return checkoutBasePrice + PROTECT_FEE + CONVENIENCE_FEE + delFee + donationAmt;
}

function updateUI() {
  const total    = calcTotal();
  const delFee   = selectedDelivery === 2 ? DELIVERY_FEE : 0;
  const totalMrp = payMrp * payQty;
  const discount = totalMrp > payPrice ? totalMrp - payPrice : 0;
  const savings  = totalMrp - total;

  // ── Bottom bar ──
  if (payMrp > payPrice) {
    document.getElementById('barMrp').textContent = '₹' + totalMrp.toLocaleString('en-IN');
  }
  document.getElementById('barPrice').textContent = '₹' + total.toLocaleString('en-IN');

  // ── Drawer ──
  document.getElementById('drItemLbl').textContent  = 'Price (' + payQty + (payQty===1?' item)'  :' items)');
  document.getElementById('drMrp').textContent      = '₹' + totalMrp.toLocaleString('en-IN');
  document.getElementById('drDiscount').textContent = discount > 0 ? '-₹' + discount.toLocaleString('en-IN') : '₹0';
  document.getElementById('drTotal').textContent    = '₹' + total.toLocaleString('en-IN');
  const _ddr = document.getElementById('drDonationRow');
  if (_ddr) {
    _ddr.style.display = donationAmt > 0 ? '' : 'none';
    document.getElementById('drDonation').textContent = '+₹' + donationAmt;
  }
  if (delFee > 0) {
    document.getElementById('drDelivery').textContent  = '+₹' + delFee;
    document.getElementById('drDelivery').style.color  = '#212121';
    document.getElementById('drDelivery').style.fontWeight = '400';
  } else {
    document.getElementById('drDelivery').textContent  = 'FREE';
    document.getElementById('drDelivery').style.color  = '#388e3c';
    document.getElementById('drDelivery').style.fontWeight = '600';
  }

  // ── Inline price card ──
  document.getElementById('pcMrp').textContent       = '₹' + totalMrp.toLocaleString('en-IN');
  document.getElementById('pcProtect').textContent   = '+₹' + PROTECT_FEE;
  document.getElementById('pcConvenience').textContent = '+₹' + CONVENIENCE_FEE;
  if (delFee > 0) {
    document.getElementById('pcDelivery').textContent = '+₹' + delFee;
    document.getElementById('pcDeliveryRow').style.display = '';
  } else {
    document.getElementById('pcDelivery').textContent = 'FREE';
    document.getElementById('pcDeliveryRow').style.display = '';
  }
  document.getElementById('pcDiscount').textContent  = discount > 0 ? '-₹' + discount.toLocaleString('en-IN') : '₹0';
  document.getElementById('pcTotal').textContent     = '₹' + total.toLocaleString('en-IN');

  // Donation row in price card
  const pdRow = document.getElementById('pcDonationRow');
  if (pdRow) {
    pdRow.style.display = donationAmt > 0 ? '' : 'none';
    document.getElementById('pcDonation').textContent = '+₹' + donationAmt;
  }

  // Savings pill
  const pill = document.getElementById('savingsPill');
  if (savings > 0) {
    pill.style.display = '';
    document.getElementById('savingsAmt').textContent = '₹' + savings.toLocaleString('en-IN');
  } else {
    pill.style.display = 'none';
  }
}
updateUI();

// ── DELIVERY SELECTION ────────────────────────────────────────
function selectDelivery(n) {
  selectedDelivery = n;
  document.getElementById('radio1').classList.toggle('checked', n===1);
  document.getElementById('radio2').classList.toggle('checked', n===2);
  updateUI();
}

// ── GST TOGGLE ────────────────────────────────────────────────
function toggleGst() {
  gstChecked = !gstChecked;
  document.getElementById('gstCheck').classList.toggle('checked', gstChecked);
}

// ── COLLAPSIBLE SECTIONS ──────────────────────────────────────
function toggleSection(id) {
  const body = document.getElementById(id + 'Body');
  const hdr  = document.getElementById(id + 'Hdr');
  const collapsed = body.style.display === 'none';
  body.style.display = collapsed ? '' : 'none';
  hdr.classList.toggle('collapsed', !collapsed);
}

// ── PRICE DRAWER ─────────────────────────────────────────────
function openDrawer() {
  document.getElementById('drawerOverlay').classList.add('open');
  document.getElementById('priceDrawer').classList.add('open');
}
function closeDrawer() {
  document.getElementById('drawerOverlay').classList.remove('open');
  document.getElementById('priceDrawer').classList.remove('open');
}

// ── CONTINUE ─────────────────────────────────────────────────
function goPayment() {
  // ── Address check ──────────────────────────────────────────
  // 1. Saved addresses exist karte hain?
  let savedList = [];
  try { savedList = JSON.parse(localStorage.getItem('fk_addresses') || '[]'); } catch(e){}
  if (!savedList.length) {
    showToast('⚠️ Pehle address add karo!');
    setTimeout(() => { window.location.href = 'address.php'; }, 1000);
    return;
  }
  // 2. Koi address select hua?
  const addr = localStorage.getItem('pay_address') || '';
  if (!addr.trim()) {
    showToast('⚠️ Address select karo — "Deliver Here" tap karo!');
    setTimeout(() => { window.location.href = 'address.php'; }, 1200);
    return;
  }
  const total  = calcTotal();
  const delFee = selectedDelivery === 2 ? DELIVERY_FEE : 0;
  localStorage.setItem('pay_total',    total);
  localStorage.setItem('pay_delivery', delFee);
  localStorage.setItem('pay_donation', donationAmt);
  if (isEmiCheckout && emiMonths > 0) {
    localStorage.setItem('pay_emi_enabled', '1');
    localStorage.setItem('pay_emi_plan', String(emiMonths));
    localStorage.setItem('pay_emi_monthly', String(emiMonthly));
  }
  window.location.href = 'payment.php';
}

// ── DONATION ─────────────────────────────────────────────────
function selectDonation(btn, amt) {
  const btns = document.querySelectorAll('.don-btn');
  if (btn.classList.contains('selected')) {
    btn.classList.remove('selected');
    donationAmt = 0;
    document.getElementById('donNote').style.display = 'none';
  } else {
    btns.forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    donationAmt = amt;
    document.getElementById('donNote').style.display = 'block';
  }
  updateUI();
}

// ── STAGGERED SECTION ANIMATIONS ─────────────────────────────
document.querySelectorAll('.section').forEach(function(el, i) {
  el.style.animationDelay = (0.05 + i * 0.05) + 's';
});
</script>
    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
