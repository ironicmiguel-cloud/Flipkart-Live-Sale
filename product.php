<?php
require_once __DIR__ . '/includes/bootstrap.php';

// PHP image scanner — file_exists() bypasses InfinityFree fake-200
$_pid     = preg_replace('/[^a-z0-9]/i', '', $_GET['id'] ?? 'p1');
$_pidNum  = ltrim(preg_replace('/^p/i', '', $_pid), '0') ?: '1';
$_imgDir  = __DIR__ . '/Images/TopPicksForYou/' . $_pidNum . '/';
$_exts    = ['jpg','jpeg','png','webp','avif'];
$_phpImgs = [];
if (is_dir($_imgDir)) {
    for ($_i = 1; $_i <= 20; $_i++) {
        $found = false;
        foreach ($_exts as $_ext) {
            if (file_exists($_imgDir . $_i . '.' . $_ext)) {
                $_phpImgs[] = 'Images/TopPicksForYou/' . $_pidNum . '/' . $_i . '.' . $_ext;
                $found = true; break;
            }
        }
        if (!$found && $_i > count($_phpImgs) + 2) break;
    }
}
$_phpImgsJson = json_encode($_phpImgs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Product – Flipkart</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent}
html,body{max-width:100%;overflow-x:hidden;}
body{font-family:'Noto Sans',sans-serif;background:#f1f3f6;padding-bottom:92px;color:#212121}

/* ── HEADER ── */
.header{background:#2874f0;height:56px;display:flex;align-items:center;gap:6px;padding:0 8px;position:sticky;top:0;z-index:200}

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

.header-search{flex:1;background:#fff;border-radius:3px;height:36px;display:flex;align-items:center;gap:8px;padding:0 10px;cursor:pointer}
.header-search span{font-size:12px;color:#b0b0b0}
.h-icon-btn{background:none;border:none;color:#fff;width:38px;height:38px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;flex-shrink:0}
.h-icon-btn:active{background:rgba(255,255,255,.15);border-radius:50%}
.cart-badge{position:absolute;top:2px;right:2px;background:#ff3f6c;color:#fff;font-size:8px;font-weight:700;min-width:15px;height:15px;border-radius:8px;display:none;align-items:center;justify-content:center;padding:0 3px;border:1.5px solid #2874f0}

/* ── IMAGE SECTION ── */
.img-section{background:#fff}
.img-topbar{display:flex;align-items:center;justify-content:space-between;padding:10px 14px 0}
.fk-assured{display:flex;align-items:center;gap:6px}
.fk-assured-text{font-size:11px;font-weight:700;color:#2874f0;display:flex;align-items:center;gap:3px}
.fk-assured-text em{color:#f0a500;font-style:normal}
.share-btn{background:none;border:none;cursor:pointer;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%}
.share-btn:active{background:#f5f5f5}

.main-img-wrap{position:relative;overflow:hidden;min-height:280px;background:#fff;touch-action:pan-y}
.img-slider{display:flex;transition:transform .28s cubic-bezier(.4,0,.2,1);will-change:transform}
.img-slide{flex:0 0 100%;display:flex;align-items:center;justify-content:center;padding:20px 16px 14px;min-height:280px;background:#fff}
.main-img{width:240px;height:240px;object-fit:contain;display:block;pointer-events:none}
/* ── REAL FLIPKART WISHLIST HEART ── */
.wish-btn{position:absolute;right:14px;top:14px;background:#fff;border:1px solid #e0e0e0;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.12);transition:border-color .2s}
.wish-btn svg .heart-border{fill:none;stroke:#878787;stroke-width:1.8;transition:all .25s}
.wish-btn svg .heart-fill{fill:transparent;transition:all .25s}
.wish-btn.active svg .heart-border{stroke:#ff3f6c}
.wish-btn.active svg .heart-fill{fill:#ff3f6c}
.wish-btn:active{background:#fff0f3}

.thumb-strip{display:flex;gap:7px;padding:0 14px 12px;overflow-x:auto}
.thumb-strip::-webkit-scrollbar{display:none}
.thumb{min-width:50px;width:50px;height:50px;border:1.5px solid #e0e0e0;border-radius:4px;object-fit:contain;background:#fafafa;cursor:pointer;padding:3px;transition:border-color .15s}
.thumb.active{border-color:#2874f0}

/* ── ASSURANCE BAR ── */
.assurance-bar{border-top:1px solid #f1f3f6;display:flex}
.a-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;padding:10px 4px 11px;font-size:10px;color:#555;text-align:center;line-height:1.3}
.a-div{width:1px;background:#e0e0e0;margin:10px 0}

/* ── PRODUCT INFO ── */
.product-info{background:#fff;margin-top:8px;padding:14px}
.pi-brand{font-size:13px;color:#878787;margin-bottom:3px}
.pi-name{font-size:15.5px;font-weight:500;color:#212121;line-height:1.45;margin-bottom:10px}
.rating-row{display:flex;align-items:center;gap:8px;margin-bottom:12px}
.r-pill{display:inline-flex;align-items:center;gap:3px;padding:3px 8px;border-radius:3px;font-size:12px;font-weight:700;color:#fff;background:#388e3c}
.r-pill.med{background:#ef9a1a}
.r-pill.low{background:#e53935}
.r-sep{color:#ccc;font-size:13px}
.r-count{font-size:13px;color:#878787}
.price-row{display:flex;align-items:baseline;flex-wrap:wrap;gap:8px;margin-bottom:3px}
.pi-price{font-size:27px;font-weight:700;color:#212121}
.pi-mrp{font-size:14px;color:#878787;text-decoration:line-through}
.pi-off{font-size:14px;font-weight:600;color:#388e3c}
.pi-stock{font-size:12px;font-weight:600;color:#d32f2f;margin:4px 0 8px;display:block}
.emi-line{font-size:13px;color:#212121;margin:6px 0 12px}
.emi-line b{color:#2874f0}
/* ── VARIANT SECTION ── */
.variant-section{margin:8px 0 10px}
.variant-label{font-size:13px;color:#212121;margin-bottom:6px}
.variant-label b{font-weight:600}
.variant-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px}
.color-chip{width:38px;height:38px;border-radius:50%;border:2px solid #e0e0e0;cursor:pointer;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center}
.color-chip.active{border:2px solid #2874f0;box-shadow:0 0 0 2px #2874f0}
.color-chip img{width:36px;height:36px;border-radius:50%;object-fit:cover}
.storage-chip{padding:6px 12px;border:1.5px solid #d0d0d0;border-radius:4px;font-size:12px;font-weight:600;color:#212121;background:#fff;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:1px;min-width:70px}
.storage-chip.active{border-color:#2874f0;color:#2874f0;background:#e8f0fe}
.storage-chip .s-off{font-size:11px;color:#388e3c;font-weight:700}
.storage-chip .s-mrp{font-size:10px;color:#878787;text-decoration:line-through}
.storage-chip .s-price{font-size:13px;font-weight:700;color:#212121}
.storage-chip.active .s-price{color:#2874f0}
.storage-chip.low-stock .s-left{font-size:10px;color:#ff6b35;margin-top:1px}
.pi-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:3px;margin-bottom:10px;letter-spacing:.3px}
.pi-badge.trending{background:#ff6161;color:#fff}
.pi-badge.bestseller{background:#2874f0;color:#fff}
.pi-badge.hotdeal{background:#ff6900;color:#fff}

/* ── OFFERS ── */
.offers-title{font-size:14px;font-weight:600;color:#212121;margin-bottom:8px}
.offer-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:9px}
.offer-ico{width:26px;height:26px;flex-shrink:0;display:flex;align-items:center;justify-content:center;margin-top:2px}
.offer-txt{font-size:12.5px;color:#212121;line-height:1.45}
.offer-txt b{font-weight:600}
.offer-txt a{color:#2874f0;font-weight:500;text-decoration:none}

/* ── DIVIDER ── */
.div8{height:8px;background:#f1f3f6}

/* ── DELIVERY ── */
.delivery{background:#fff;padding:14px}
.sec-title{font-size:14px;font-weight:600;color:#212121;margin-bottom:10px}
.pin-row{display:flex;align-items:center;gap:8px;border:1px solid #c9c9c9;border-radius:3px;padding:8px 12px;margin-bottom:12px}
.pin-row input{flex:1;border:none;outline:none;font-size:14px;color:#212121;font-family:inherit}
.pin-row button{background:none;border:none;color:#2874f0;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit}
.del-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:13px;color:#212121}
.del-icon{width:20px;height:20px;flex-shrink:0;margin-top:1px}
.dgreen{color:#388e3c;font-weight:500}

/* ── HIGHLIGHTS ── */
.highlights{background:#fff;padding:14px}
.hl-table{width:100%}
.hl-row{display:flex;gap:10px;padding:5px 0;border-bottom:1px solid #f5f5f5;font-size:13px}
.hl-row:last-child{border-bottom:none}
.hl-k{color:#878787;min-width:110px;flex-shrink:0}
.hl-v{color:#212121;line-height:1.4}

/* ── SELLER ── */
.seller{background:#fff;padding:14px}
.seller-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
.seller-name{font-size:14px;color:#2874f0;font-weight:600}
.seller-rp{background:#388e3c;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:3px}
.seller-sub{font-size:12px;color:#878787}

/* ── DESCRIPTION ── */
.desc-sec{background:#fff;padding:14px}
.desc-text{font-size:13px;color:#555;line-height:1.7;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.desc-text.open{display:block}
.desc-more{color:#2874f0;font-size:13px;font-weight:600;cursor:pointer;margin-top:6px;display:inline-block}

/* ── GALLERY ── */
.gallery-sec{background:#fff;padding:14px}
.gallery-grid{display:flex;flex-direction:row;gap:8px;margin-top:10px;overflow-x:auto;padding-bottom:4px}
.gallery-grid::-webkit-scrollbar{display:none}
.gallery-item{flex:0 0 72px;width:72px;height:72px;border:1.5px solid #e0e0e0;border-radius:6px;overflow:hidden;cursor:pointer;background:#fafafa;display:flex;align-items:center;justify-content:center}
.gallery-item img{width:100%;height:100%;object-fit:contain;padding:6px}
.gallery-item.active{border-color:#2874f0}
.lightbox{display:none;position:fixed;inset:0;background:#fff;z-index:9999;flex-direction:column;touch-action:none}
.lightbox.open{display:flex}
.lb-topbar{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #f0f0f0;flex-shrink:0}
.lb-counter-top{font-size:13px;color:#555;font-weight:500}
.lb-close{background:none;border:none;font-size:22px;color:#333;cursor:pointer;line-height:1;padding:4px 8px}
.lb-slider-wrap{flex:1;overflow:hidden;position:relative}
.lb-slider{display:flex;height:100%;transition:transform .28s cubic-bezier(.4,0,.2,1);will-change:transform}
.lb-slide{flex:0 0 100%;display:flex;align-items:center;justify-content:center;padding:16px;box-sizing:border-box}
.lb-img{max-width:100%;max-height:100%;object-fit:contain;display:block}

/* ── RATINGS ── */
.ratings-sec{background:#fff;padding:14px}
.rt-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.rt-viewall{font-size:13px;color:#2874f0;font-weight:600;cursor:pointer}
.rt-overview{display:flex;gap:14px;align-items:center;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f1f3f6}
.rt-big{text-align:center;min-width:76px}
.rt-num{font-size:44px;font-weight:700;color:#212121;line-height:1}
.rt-stars{font-size:16px;color:#388e3c;margin-top:3px}
.rt-total{font-size:11px;color:#878787;margin-top:2px}
.bars{flex:1}
.bar-row{display:flex;align-items:center;gap:6px;margin-bottom:5px}
.bar-lbl{font-size:11px;color:#555;min-width:20px;text-align:right}
.bar-track{flex:1;height:5px;background:#e0e0e0;border-radius:3px;overflow:hidden}
.bar-fill{height:100%;background:#388e3c;border-radius:3px}
.bar-fill.med{background:#ef9a1a}
.bar-fill.low{background:#e53935}
.bar-cnt{font-size:11px;color:#878787;min-width:28px}
.rv-card{padding:13px 0;border-top:1px solid #f1f3f6}
.rv-top{display:flex;align-items:center;gap:8px;margin-bottom:5px}
.rv-pill{display:inline-flex;align-items:center;gap:2px;background:#388e3c;color:#fff;font-size:11px;font-weight:700;padding:2px 7px;border-radius:3px}
.rv-pill.med{background:#ef9a1a}
.rv-pill.low{background:#e53935}
.rv-author{font-size:13px;font-weight:600;color:#212121}
.rv-cert{font-size:11px;color:#388e3c;margin-left:auto;display:flex;align-items:center;gap:3px}
.rv-title{font-size:13px;font-weight:600;color:#212121;margin-bottom:4px}
.rv-body{font-size:13px;color:#555;line-height:1.5}
.rv-date{font-size:11px;color:#878787;margin-top:5px}
.rv-helpful{display:flex;align-items:center;gap:10px;margin-top:10px}
.rv-helpful span{font-size:12px;color:#878787}
.rv-helpful button{background:#fff;border:1px solid #e0e0e0;border-radius:20px;padding:4px 14px;font-size:12px;cursor:pointer;color:#555;font-family:inherit;display:flex;align-items:center;gap:4px}
.rv-helpful button:active{background:#f5f5f5}

/* ── SECTIONS (Similar / Trending / etc.) ── */
.section{background:#fff;padding:14px}
.sec-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
.sec-title2{font-size:15px;font-weight:700;color:#212121}
.sec-sub{font-size:12px;color:#878787;margin-bottom:12px}
.sec-viewall{font-size:13px;color:#2874f0;font-weight:600;cursor:pointer;background:none;border:none;font-family:inherit;padding:2px 0}
.h-scroll{display:flex;gap:10px;overflow-x:auto;padding-bottom:2px}
.h-scroll::-webkit-scrollbar{display:none}

/* Product card in scroll */
.p-card{min-width:124px;width:124px;flex-shrink:0;cursor:pointer;position:relative}
.p-card-img{width:124px;height:124px;object-fit:contain;background:#f8f8f8;border-radius:6px;border:1px solid #efefef;padding:6px;display:block}
.p-card-badge{position:absolute;top:6px;left:6px;font-size:9px;font-weight:700;padding:2px 5px;border-radius:3px;color:#fff;z-index:1}
.p-card-badge.hot{background:#ff3f6c}
.p-card-badge.new{background:#2874f0}
.p-card-badge.deal{background:#388e3c}
.p-card-rrow{display:flex;align-items:center;gap:4px;margin-top:5px}
.p-card-rp{background:#388e3c;color:#fff;font-size:10px;font-weight:700;padding:1px 5px;border-radius:3px;display:flex;align-items:center;gap:2px}
.p-card-rc{font-size:10px;color:#878787}
.p-card-name{font-size:11.5px;color:#212121;margin-top:3px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.p-card-price{font-size:13px;font-weight:700;color:#212121;margin-top:3px}
.p-card-off{font-size:11px;color:#388e3c;font-weight:600}

/* Suggested for you — vertical list style */
.sfy-list{display:flex;flex-direction:column;gap:0}
.sfy-card{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f5f5f5;cursor:pointer}
.sfy-card:last-child{border-bottom:none}
.sfy-card:active{background:#f9f9f9}
.sfy-img{width:80px;height:80px;min-width:80px;object-fit:contain;background:#f8f8f8;border-radius:6px;border:1px solid #efefef;padding:4px;flex-shrink:0}
.sfy-info{flex:1;min-width:0}
.sfy-name{font-size:13px;color:#212121;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.sfy-rrow{display:flex;align-items:center;gap:5px;margin-top:3px}
.sfy-rp{background:#388e3c;color:#fff;font-size:10px;font-weight:700;padding:1px 5px;border-radius:3px}
.sfy-price{font-size:13.5px;font-weight:700;color:#212121;margin-top:3px}
.sfy-off{font-size:11px;color:#388e3c;font-weight:600;margin-left:3px}
.sfy-mrp{font-size:11px;color:#878787;text-decoration:line-through;margin-left:3px}
.sfy-arrow{color:#ccc;font-size:20px;flex-shrink:0;margin-left:4px}

/* Frequently Bought Together */
.fbt-wrap{display:flex;align-items:center;gap:0;overflow-x:auto;padding-bottom:10px}
.fbt-wrap::-webkit-scrollbar{display:none}
.fbt-item{display:flex;flex-direction:column;align-items:center;min-width:88px;cursor:pointer}
.fbt-item:active{opacity:.8}
.fbt-img{width:76px;height:76px;object-fit:contain;border:1px solid #e0e0e0;border-radius:6px;background:#fafafa;padding:4px}
.fbt-iname{font-size:10px;color:#555;text-align:center;margin-top:4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;max-width:80px}
.fbt-iprice{font-size:11px;font-weight:700;color:#212121;text-align:center;margin-top:2px}
.fbt-plus{font-size:18px;color:#878787;padding:0 4px;margin-top:-16px;flex-shrink:0}
.fbt-total-box{background:#fffbf0;border:1px solid #ffd740;border-radius:6px;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;margin:4px 0 10px}
.fbt-total-lbl{font-size:13px;color:#212121}
.fbt-save{font-size:12px;color:#388e3c;font-weight:600;margin-top:2px}
.fbt-total-price{font-size:15px;font-weight:700;color:#212121}
.fbt-btn{background:#ff9f00;color:#fff;border:none;border-radius:3px;padding:12px;font-size:13px;font-weight:700;width:100%;cursor:pointer;font-family:inherit}
.fbt-btn:active{background:#e68f00}

/* Deal card */
.deal-card{min-width:148px;width:148px;border-radius:6px;overflow:hidden;cursor:pointer;flex-shrink:0;background:#fff;border:1px solid #e8e8e8;position:relative}
.deal-card:active{box-shadow:0 2px 8px rgba(0,0,0,.1)}
.deal-img{width:148px;height:108px;object-fit:contain;padding:8px;background:#f8f9ff;display:block}
.deal-body{padding:7px 9px 10px}
.deal-name{font-size:11px;color:#212121;font-weight:500;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.deal-price{font-size:14px;font-weight:700;color:#212121;margin-top:3px}
.deal-off{display:inline-block;background:#388e3c;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:3px;margin-top:3px}
.deal-ribbon{position:absolute;top:8px;right:0;background:#fb641b;color:#fff;font-size:8px;font-weight:700;padding:3px 8px 3px 10px;clip-path:polygon(8px 0,100% 0,100% 100%,8px 100%,0 50%)}

/* Sponsored */
.spon-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.spon-card{border:1px solid #f0f0f0;border-radius:6px;overflow:hidden;background:#fafafa;cursor:pointer}
.spon-card:active{opacity:.85}
.spon-img-wrap{aspect-ratio:1;background:#fff;display:flex;align-items:center;justify-content:center;padding:8px}
.spon-img{width:100%;height:100%;object-fit:contain}
.spon-info{padding:6px 7px 8px}
.spon-brand{font-size:9.5px;color:#878787}
.spon-name{font-size:11px;color:#212121;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-top:1px}
.spon-price{font-size:13px;font-weight:700;color:#212121;margin-top:3px}
.spon-off{font-size:10px;color:#388e3c;font-weight:600}
.spon-ad{font-size:9px;color:#aaa;margin-top:2px}
.ad-tag{background:#e8f0fe;color:#2874f0;font-size:9px;font-weight:800;padding:2px 7px;border-radius:3px;border:1px solid #c5d8f8;letter-spacing:.3px}

/* ── BOTTOM BAR ── */
.bottom-bar{position:fixed;left:0;right:0;bottom:0;padding:10px 12px calc(10px + env(safe-area-inset-bottom));display:flex;align-items:stretch;gap:10px;background:#fff;box-shadow:0 -1px 4px rgba(0,0,0,.10);z-index:200}
.btn-cart,.btn-emi,.btn-buy{height:56px;border-radius:8px;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:opacity .12s ease,filter .12s ease;-webkit-tap-highlight-color:transparent;border:none;outline:none}
.btn-cart:active,.btn-emi:active,.btn-buy:active{opacity:.88}
/* ADD TO CART — white, gray outlined, half width */
.btn-cart{flex:1;background:#fff;color:#212121;border:1.5px solid #c2c2c2;flex-direction:row;gap:8px;font-size:15px;font-weight:700;letter-spacing:.01em;padding:0 10px}
.btn-cart svg{width:22px;height:22px;flex-shrink:0}
.btn-cart svg path{stroke:#212121}
.btn-cart svg circle{fill:#212121}
.btn-cart-label{font-size:15px;font-weight:700;color:#212121}
.btn-cart:active{background:#f5f5f5}
/* BUY WITH EMI — white middle button */
.btn-emi{flex:1;background:#fff;color:#212121;border:1.5px solid #c2c2c2;padding:0 8px;text-align:center;flex-direction:column;line-height:1.2}
.btn-emi span:first-child{font-size:14.5px;font-weight:700;color:#2b2b2b}
.btn-emi .emi-sub{font-size:11px;font-weight:500;color:#6b6b6b;margin-top:2px}
.btn-emi:active{background:#f7f7f7}
/* BUY NOW — Flipkart yellow, dark text */
.btn-buy{flex:1;background:#FFD814;color:#212121;border:none;padding:0 10px;text-align:center;flex-direction:column;line-height:1.2}
.btn-buy span:first-child{font-size:15px;font-weight:700;color:#212121}
.btn-buy .buy-sub{font-size:11.5px;font-weight:600;color:#212121;margin-top:2px;opacity:.85}
.btn-buy:active{background:#f0c800}
@media (max-width:380px){
  .bottom-bar{gap:8px;padding-left:8px;padding-right:8px}
  .btn-cart,.btn-emi,.btn-buy{height:52px;border-radius:8px}
  .btn-cart-label,.btn-emi span:first-child,.btn-buy span:first-child{font-size:13.5px}
  .btn-emi .emi-sub,.btn-buy .buy-sub{font-size:10.5px}
}

/* ── TOAST ── */
.toast{position:fixed;bottom:68px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:10px 20px;border-radius:24px;font-size:13px;z-index:9999;opacity:0;pointer-events:none;white-space:nowrap;transition:opacity .25s;max-width:90vw;text-align:center}
.toast.show{opacity:1}
.emi-modal{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10040;display:none;align-items:flex-end;justify-content:center;padding:0 0 env(safe-area-inset-bottom)}
.emi-modal.open{display:flex}
.emi-sheet{width:100%;max-width:520px;background:#fff;border-radius:18px 18px 0 0;box-shadow:0 -12px 32px rgba(0,0,0,.22);padding:16px 16px 18px;animation:emiUp .18s ease-out}
@keyframes emiUp{from{transform:translateY(18px);opacity:.7}to{transform:translateY(0);opacity:1}}
.emi-grab{width:44px;height:5px;border-radius:999px;background:#d7d7d7;margin:0 auto 14px}
.emi-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px}
.emi-head h3{font-size:18px;font-weight:700;color:#212121}
.emi-close{border:none;background:#f3f3f3;color:#333;width:32px;height:32px;border-radius:50%;font-size:18px;cursor:pointer}
.emi-subtxt{font-size:13px;color:#666;margin:-2px 0 12px}
.emi-plan-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:12px 0 16px}
.emi-plan{border:1.5px solid #e3e3e3;border-radius:12px;padding:12px 10px;background:#fff;text-align:left;cursor:pointer;transition:border-color .15s ease, box-shadow .15s ease, transform .15s ease}
.emi-plan.active{border-color:#2874f0;box-shadow:0 0 0 2px rgba(40,116,240,.10)}
.emi-plan .m{font-size:12px;color:#666;margin-bottom:6px}
.emi-plan .amt{font-size:17px;font-weight:700;color:#212121}
.emi-plan .meta{font-size:11px;color:#878787;margin-top:4px}
.emi-bank{font-size:12px;color:#4a4a4a;background:#f7f8fa;border-radius:10px;padding:10px 12px;margin-bottom:14px}
.emi-cta{display:flex;gap:10px}
.emi-cta button{flex:1;height:48px;border-radius:10px;border:none;font-family:inherit;font-weight:700;font-size:15px;cursor:pointer}
.emi-cancel{background:#f4f4f4;color:#212121}
.emi-continue{background:#ffd814;color:#212121}
.emi-note{font-size:11px;color:#878787;line-height:1.45;margin-top:10px}

/* ── Skeleton shimmer ── */
@keyframes shimmer {
    0%   { background-position: -400px 0; }
    100% { background-position: 400px 0; }
}
.skel {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 800px 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
    display: block;
}
#imgSkeleton {
    width: 100%; min-height: 280px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 10px; padding: 20px 16px;
    background: #fff;
}
#imgSkeleton .skel-img  { width: 200px; height: 200px; border-radius: 8px; }
#imgSkeleton .skel-line { height: 10px; width: 120px; border-radius: 4px; }
/* Product info skeleton */
.info-skel { padding: 14px; background: #fff; margin-bottom: 8px; }
.info-skel .skel-line { height: 12px; margin: 10px 0; border-radius: 4px; }
.info-skel .skel-line.wide { width: 85%; }
.info-skel .skel-line.mid  { width: 55%; }
.info-skel .skel-line.short{ width: 35%; }
/* Fade in images */
img.lazy-fade { opacity: 0; transition: opacity .35s ease; }
img.lazy-fade.loaded { opacity: 1; }

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
<body class="no-select" data-fk-sync="auth,cart,wishlist">

<!-- HEADER -->
<div class="header">
  <!-- BACK BUTTON -->
  <button class="back-btn" id="backBtn" onclick="goBack()" aria-label="Go back">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="white"/></svg>
  </button>
  <div class="header-search" onclick="window.location.href='search.php'" role="button">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
      <circle cx="11" cy="11" r="7" stroke="#b0b0b0" stroke-width="2"/>
      <path d="M20 20l-3.5-3.5" stroke="#b0b0b0" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <span>Search for products</span>
  </div>
  <a href="cart.php" id="hCartBtn" class="h-icon-btn" style="text-decoration:none" aria-label="Cart">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
      <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/>
      <line x1="3" y1="6" x2="21" y2="6" stroke="white" stroke-width="1.8"/>
      <path d="M16 10a4 4 0 01-8 0" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
    </svg>
    <span class="cart-badge" id="cartBadge"></span>
  </a>
  <button class="h-icon-btn" aria-label="More options">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
      <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
    </svg>
  </button>
</div>

<!-- IMAGE SECTION -->
<div class="img-section">
  <div class="img-topbar">
    <!-- Flipkart Assured — accurate badge -->
    <div class="fk-assured"><span class="shared-fk-badge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 5v6c0 5.25 3.5 10.15 8 11.35C16.5 21.15 20 16.25 20 11V5l-8-3z" fill="#2874f0"/><path d="M9.6 12.6l1.8 1.8 3.6-4.1" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Flipkart <em>Assured</em></span></div>
    <!-- Share — accurate icon -->
    <button class="share-btn" onclick="shareProduct()" aria-label="Share">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
      </svg>
    </button>
  </div>

  <div class="main-img-wrap" id="mainImgWrap">
    <!-- Skeleton shown while images load -->
    <div id="imgSkeleton">
      <span class="skel skel-img"></span>
      <span class="skel skel-line"></span>
    </div>
    <div class="img-slider" id="imgSlider">
      <!-- slides injected by JS -->
    </div>
    <!-- REAL FLIPKART WISHLIST HEART BUTTON -->
    <button class="wish-btn" id="wishBtn" onclick="toggleWish()" aria-label="Add to wishlist" style="position:absolute;right:14px;top:14px;z-index:2">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path class="heart-fill" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        <path class="heart-border" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
      </svg>
    </button>
  </div>

  <div class="thumb-strip" id="thumbStrip"></div>

  <!-- ASSURANCE BAR -->
  <div class="assurance-bar">
    <!-- 7 Days Replacement -->
    <div class="a-item">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z" fill="#2874f0"/>
      </svg>
      <span>7 Days<br>Replacement</span>
    </div>
    <div class="a-div"></div>
    <!-- Cash on Delivery -->
    <div class="a-item">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" fill="#2874f0"/>
        <circle cx="12" cy="15" r="2" fill="#2874f0"/>
      </svg>
      <span>Cash on<br>Delivery</span>
    </div>
    <div class="a-div"></div>
    <div class="a-item">
      <!-- Flipkart Assured shield -->
      <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="30" height="30" rx="6" fill="#2874f0"/>
  <path d="M15 5l9 4v6c0 5-4 9-9 10-5-1-9-5-9-10V9l9-4z" fill="white" opacity="0.2"/>
  <path d="M15 6l8 3.5v5.5c0 4.5-3.5 8-8 9-4.5-1-8-4.5-8-9V9.5L15 6z" fill="none" stroke="white" stroke-width="1.5"/>
  <text x="15" y="19.5" text-anchor="middle" font-family="Arial,sans-serif" font-weight="900" font-size="9" fill="white">FK</text>
</svg>
      <span>Flipkart<br>Assured</span>
    </div>
    <div class="a-div"></div>
    <div class="a-item">
      <!-- 1 Year Warranty -->
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M12 2L4 5v6c0 5.25 3.5 10.15 8 11.35C16.5 21.15 20 16.25 20 11V5l-8-3z" fill="#fff3e0" stroke="#f57c00" stroke-width="1.5"/>
  <path d="M9.5 12.5l2 2 4-4" stroke="#f57c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
      <span>1 Year<br>Warranty</span>
    </div>
  </div>
</div>

<!-- PRODUCT INFO -->
<div class="product-info">
  <div class="pi-brand" id="piBrand"></div>
  <div class="pi-name" id="piName"></div>
  <div class="rating-row">
    <div class="r-pill" id="rPill"></div>
    <span class="r-sep">|</span>
    <span class="r-count" id="rCount"></span>
  </div>
  <div id="ratingBarWrap" style="margin:-4px 0 8px;display:none">
    <svg width="100%" height="18" style="max-width:188px;display:block" viewBox="0 0 377 18" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M375 0.669922H341C340.735 0.669922 340.48 0.775279 340.293 0.962815C340.105 1.15035 340 1.40471 340 1.66992V16.3299C340 16.5951 340.105 16.8495 340.293 17.037C340.48 17.2246 340.735 17.3299 341 17.3299H375C375.265 17.3299 375.52 17.2246 375.707 17.037C375.895 16.8495 376 16.5951 376 16.3299V1.66992C376 1.40471 375.895 1.15035 375.707 0.962815C375.52 0.775279 375.265 0.669922 375 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M355.25 6.81002H353.93V7.81002H355.47V8.25002H353.38V5.00002H355.45V5.45002H353.93V6.34002H355.25V6.81002ZM357.71 7.49002H356.47L356.21 8.23002H355.64L356.85 5.00002H357.34L358.55 8.20002H358L357.71 7.49002ZM356.63 7.00002H357.56L357.09 5.67002L356.63 7.00002ZM360.63 7.37002C360.633 7.30709 360.62 7.2444 360.594 7.18706C360.568 7.12971 360.529 7.07933 360.48 7.04002C360.315 6.93811 360.137 6.86063 359.95 6.81002C359.731 6.75461 359.522 6.66698 359.33 6.55002C359.201 6.48147 359.093 6.37928 359.017 6.25429C358.941 6.1293 358.901 5.98615 358.9 5.84002C358.897 5.71641 358.924 5.59392 358.978 5.48264C359.032 5.37136 359.111 5.27449 359.21 5.20002C359.443 5.04698 359.722 4.97657 360 5.00002C360.203 4.99611 360.404 5.03709 360.59 5.12002C360.761 5.19814 360.906 5.32292 361.01 5.48002C361.11 5.62741 361.162 5.80193 361.16 5.98002H360.6C360.605 5.90705 360.594 5.83379 360.566 5.76608C360.538 5.69837 360.495 5.63808 360.44 5.59002C360.379 5.53624 360.307 5.49525 360.23 5.46949C360.153 5.44373 360.071 5.43371 359.99 5.44002C359.84 5.43119 359.692 5.47351 359.57 5.56002C359.521 5.59933 359.481 5.64971 359.455 5.70706C359.429 5.7644 359.417 5.82709 359.42 5.89002C359.423 5.9473 359.439 6.00315 359.467 6.05337C359.494 6.1036 359.533 6.1469 359.58 6.18002C359.748 6.28283 359.929 6.36033 360.12 6.41002C360.33 6.46645 360.532 6.55052 360.72 6.66002C360.837 6.75114 360.933 6.86729 361 7.00002C361.075 7.13071 361.113 7.27936 361.11 7.43002C361.113 7.55236 361.087 7.67373 361.033 7.78354C360.979 7.89335 360.899 7.98828 360.8 8.06002C360.559 8.23231 360.266 8.31699 359.97 8.30002C359.75 8.30326 359.531 8.25891 359.33 8.17002C359.15 8.093 358.994 7.96857 358.88 7.81002C358.771 7.6589 358.715 7.4762 358.72 7.29002H359.27C359.266 7.36911 359.28 7.44807 359.311 7.52085C359.342 7.59364 359.39 7.65834 359.45 7.71002C359.601 7.81816 359.784 7.87112 359.97 7.86002C360.126 7.87026 360.281 7.82802 360.41 7.74002C360.465 7.70398 360.51 7.65503 360.541 7.59748C360.572 7.53994 360.589 7.47557 360.59 7.41002L360.63 7.37002ZM362.72 6.51002L363.42 5.00002H364L362.93 7.00002V8.23002H362.37V7.06002L361.3 5.06002H361.91L362.72 6.51002ZM367.41 6.77002H366.09V7.77002H367.63V8.21002H365.5V5.00002H367.58V5.45002H366.05V6.34002H367.37L367.41 6.77002ZM368.76 5.00002L369.69 7.46002L370.61 5.00002H371.33V8.20002H370.77V7.18002L370.83 5.77002L369.88 8.23002H369.48L368.54 5.77002L368.6 7.18002V8.23002H368V5.00002H368.76ZM372.57 8.20002H372V5.00002H372.55L372.57 8.20002ZM355.93 11.69C355.938 11.9718 355.883 12.2518 355.77 12.51C355.673 12.737 355.509 12.9289 355.3 13.06C355.092 13.1857 354.853 13.2514 354.61 13.25C354.364 13.2502 354.122 13.1846 353.91 13.06C353.706 12.9276 353.543 12.7405 353.44 12.52C353.328 12.2648 353.274 11.9884 353.28 11.71V11.53C353.273 11.2451 353.328 10.9621 353.44 10.7C353.54 10.475 353.703 10.2839 353.91 10.15C354.116 10.0197 354.356 9.95356 354.6 9.96002C354.847 9.95396 355.09 10.02 355.3 10.15C355.505 10.2781 355.666 10.4668 355.76 10.69C355.877 10.9473 355.935 11.2274 355.93 11.51V11.69ZM355.38 11.52C355.399 11.2345 355.325 10.9504 355.17 10.71C355.108 10.6167 355.023 10.5409 354.923 10.4901C354.823 10.4393 354.712 10.4151 354.6 10.42C354.49 10.4178 354.381 10.4432 354.283 10.4938C354.185 10.5445 354.102 10.6188 354.04 10.71C353.89 10.9453 353.816 11.2213 353.83 11.5V11.69C353.812 11.9754 353.886 12.2591 354.04 12.5C354.106 12.589 354.192 12.6612 354.291 12.711C354.39 12.7608 354.499 12.7867 354.61 12.7867C354.721 12.7867 354.83 12.7608 354.929 12.711C355.028 12.6612 355.114 12.589 355.18 12.5C355.327 12.2528 355.397 11.9672 355.38 11.68V11.52ZM357.04 12.02V13.2H356.48V10H357.71C358.018 9.9862 358.32 10.0858 358.56 10.28C358.662 10.3744 358.743 10.4889 358.798 10.6163C358.853 10.7437 358.881 10.8812 358.88 11.02C358.887 11.1572 358.862 11.2941 358.809 11.4206C358.755 11.547 358.673 11.6596 358.57 11.75C358.322 11.9428 358.013 12.0388 357.7 12.02H357.04ZM357.04 11.57H357.71C357.872 11.5813 358.033 11.5313 358.16 11.43C358.215 11.3799 358.258 11.3179 358.286 11.2486C358.314 11.1792 358.325 11.1045 358.32 11.03C358.324 10.9527 358.312 10.8753 358.285 10.8029C358.257 10.7306 358.215 10.6648 358.16 10.61C358.037 10.5055 357.881 10.4487 357.72 10.45H357L357.04 11.57ZM361.67 10.45H360.67V13.2H360.12V10.48H359.12V10H361.65L361.67 10.45ZM362.67 13.2H362.12V10H362.67V13.2ZM365.91 11.69C365.918 11.9718 365.863 12.2518 365.75 12.51C365.65 12.735 365.486 12.9261 365.28 13.06C365.072 13.1857 364.833 13.2514 364.59 13.25C364.344 13.2502 364.102 13.1846 363.89 13.06C363.686 12.9276 363.523 12.7405 363.42 12.52C363.308 12.2648 363.254 11.9884 363.26 11.71V11.53C363.253 11.2451 363.308 10.9621 363.42 10.7C363.52 10.475 363.683 10.2839 363.89 10.15C364.096 10.0197 364.336 9.95356 364.58 9.96002C364.826 9.9551 365.069 10.021 365.28 10.15C365.485 10.2781 365.646 10.4668 365.74 10.69C365.857 10.9473 365.915 11.2274 365.91 11.51V11.69ZM365.35 11.52C365.372 11.2356 365.302 10.9516 365.15 10.71C365.088 10.6167 365.003 10.5409 364.903 10.4901C364.803 10.4393 364.692 10.4151 364.58 10.42C364.47 10.4178 364.361 10.4432 364.263 10.4938C364.165 10.5445 364.082 10.6188 364.02 10.71C363.87 10.9453 363.796 11.2213 363.81 11.5V11.69C363.792 11.9754 363.866 12.2591 364.02 12.5C364.082 12.5934 364.167 12.6692 364.267 12.72C364.367 12.7708 364.478 12.7949 364.59 12.79C364.699 12.7952 364.808 12.7722 364.906 12.7232C365.004 12.6741 365.088 12.6008 365.15 12.51C365.302 12.2648 365.372 11.9776 365.35 11.69V11.52ZM369 13.23H368.45L367 11V13.27H366.44V10H367L368.43 12.28V10H369V13.23ZM371.33 12.41C371.333 12.3471 371.32 12.2844 371.294 12.2271C371.268 12.1697 371.229 12.1193 371.18 12.08C371.015 11.9781 370.837 11.9006 370.65 11.85C370.431 11.7946 370.222 11.707 370.03 11.59C369.901 11.5215 369.793 11.4193 369.717 11.2943C369.641 11.1693 369.601 11.0262 369.6 10.88C369.597 10.7564 369.624 10.6339 369.678 10.5226C369.732 10.4114 369.811 10.3145 369.91 10.24C370.145 10.0626 370.436 9.97409 370.73 9.99002C370.933 9.98611 371.134 10.0271 371.32 10.11C371.49 10.1853 371.633 10.3109 371.73 10.47C371.834 10.6152 371.887 10.7913 371.88 10.97H371.33C371.335 10.897 371.324 10.8238 371.296 10.7561C371.268 10.6884 371.225 10.6281 371.17 10.58C371.109 10.5262 371.037 10.4853 370.96 10.4595C370.883 10.4337 370.801 10.4237 370.72 10.43C370.57 10.4212 370.422 10.4635 370.3 10.55C370.251 10.5893 370.211 10.6397 370.185 10.6971C370.159 10.7544 370.147 10.8171 370.15 10.88C370.15 10.9378 370.165 10.9945 370.193 11.0451C370.221 11.0958 370.261 11.1387 370.31 11.17C370.478 11.2728 370.659 11.3503 370.85 11.4C371.06 11.4564 371.262 11.5405 371.45 11.65C371.584 11.7325 371.697 11.8455 371.78 11.98C371.855 12.1107 371.893 12.2594 371.89 12.41C371.891 12.532 371.864 12.6525 371.81 12.7619C371.756 12.8713 371.677 12.9666 371.58 13.04C371.339 13.2123 371.046 13.297 370.75 13.28C370.53 13.2842 370.311 13.2398 370.11 13.15C369.928 13.0768 369.771 12.9516 369.66 12.79C369.551 12.6389 369.495 12.4562 369.5 12.27H370.05C370.046 12.3491 370.06 12.4281 370.091 12.5009C370.122 12.5736 370.17 12.6383 370.23 12.69C370.38 12.7997 370.564 12.8528 370.75 12.84C370.903 12.852 371.055 12.8095 371.18 12.72C371.23 12.6855 371.271 12.6391 371.299 12.5851C371.327 12.531 371.341 12.4709 371.34 12.41H371.33Z" fill="#4E4E4E"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M349.45 5.84V5H348.67V5.84H345.53V5H344.74V5.84H344.35C344.244 5.84387 344.139 5.86867 344.042 5.91299C343.945 5.95731 343.858 6.02028 343.786 6.0983C343.713 6.17632 343.657 6.26785 343.62 6.36766C343.583 6.46748 343.566 6.57361 343.57 6.68V12.16C343.565 12.3758 343.644 12.5851 343.792 12.7423C343.94 12.8996 344.144 12.9922 344.36 13H349.85C350.066 12.9922 350.27 12.8996 350.418 12.7423C350.565 12.5851 350.645 12.3758 350.64 12.16V6.68C350.645 6.46418 350.565 6.25494 350.418 6.09768C350.27 5.94042 350.066 5.84783 349.85 5.84H349.45ZM344.35 12.16V7.53H349.84V12.16H344.35Z" fill="#212121"/>
<path d="M346.22 9.8999C346.418 9.8999 346.611 9.84125 346.776 9.73137C346.94 9.62149 347.068 9.46531 347.144 9.28259C347.22 9.09986 347.24 8.89879 347.201 8.70481C347.162 8.51083 347.067 8.33265 346.927 8.1928C346.787 8.05294 346.609 7.9577 346.415 7.91912C346.221 7.88053 346.02 7.90034 345.838 7.97602C345.655 8.05171 345.499 8.17988 345.389 8.34433C345.279 8.50878 345.22 8.70212 345.22 8.8999C345.22 9.16512 345.326 9.41947 345.513 9.60701C345.701 9.79455 345.955 9.8999 346.22 9.8999Z" fill="#212121"/>
<path d="M347.79 11.79C347.988 11.79 348.181 11.7314 348.346 11.6215C348.51 11.5116 348.638 11.3554 348.714 11.1727C348.79 10.99 348.809 10.7889 348.771 10.5949C348.732 10.401 348.637 10.2228 348.497 10.0829C348.357 9.94308 348.179 9.84784 347.985 9.80925C347.791 9.77067 347.59 9.79047 347.407 9.86616C347.225 9.94185 347.068 10.07 346.959 10.2345C346.849 10.3989 346.79 10.5923 346.79 10.79C346.79 11.0553 346.895 11.3096 347.083 11.4971C347.27 11.6847 347.525 11.79 347.79 11.79Z" fill="#212121"/>
<path d="M347.76 8.58008L346.11 11.1401" stroke="#212121" stroke-width="0.63" stroke-linecap="square"/>
<path d="M333.67 0.669922H299.67C299.405 0.669922 299.15 0.775279 298.963 0.962815C298.775 1.15035 298.67 1.40471 298.67 1.66992V16.3299C298.67 16.5951 298.775 16.8495 298.963 17.037C299.15 17.2246 299.405 17.3299 299.67 17.3299H333.67C333.935 17.3299 334.189 17.2246 334.377 17.037C334.565 16.8495 334.67 16.5951 334.67 16.3299V1.66992C334.67 1.40471 334.565 1.15035 334.377 0.962815C334.189 0.775279 333.935 0.669922 333.67 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M312.47 7.18981C312.451 7.49543 312.315 7.78196 312.09 7.9898C311.842 8.19127 311.529 8.2945 311.21 8.2798C310.974 8.28478 310.742 8.22233 310.54 8.09981C310.345 7.97115 310.189 7.79122 310.09 7.57981C309.978 7.33507 309.92 7.06901 309.92 6.79981V6.50981C309.918 6.23498 309.972 5.96265 310.08 5.70981C310.184 5.49211 310.349 5.30923 310.555 5.18324C310.761 5.05725 310.999 4.99354 311.24 4.99981C311.55 4.98588 311.853 5.08946 312.09 5.28981C312.316 5.50072 312.452 5.79091 312.47 6.09981H311.91C311.906 5.9145 311.835 5.73697 311.71 5.59981C311.579 5.49093 311.41 5.43717 311.24 5.44981C311.131 5.44445 311.023 5.46635 310.926 5.51351C310.828 5.56068 310.743 5.6316 310.68 5.71981C310.537 5.95031 310.467 6.21881 310.48 6.4898V6.77981C310.467 7.05581 310.533 7.32976 310.67 7.56981C310.729 7.65779 310.81 7.72896 310.904 7.7763C310.999 7.82364 311.104 7.84552 311.21 7.83981C311.387 7.85562 311.562 7.80179 311.7 7.68981C311.83 7.55936 311.905 7.38399 311.91 7.19981L312.47 7.18981ZM314.76 7.4898H313.52L313.26 8.2298H312.68L313.89 4.99981H314.39L315.6 8.19981H315L314.76 7.4898ZM313.67 6.99981H314.6L314.14 5.66981L313.67 6.99981ZM317.67 7.36981C317.671 7.3072 317.657 7.24523 317.631 7.18823C317.606 7.13123 317.567 7.08057 317.52 7.03981C317.352 6.938 317.17 6.86056 316.98 6.80981C316.764 6.75665 316.558 6.66884 316.37 6.54981C316.24 6.48174 316.13 6.37993 316.053 6.25503C315.975 6.13013 315.933 5.98672 315.93 5.83981C315.927 5.71619 315.954 5.5937 316.008 5.48242C316.062 5.37115 316.141 5.27428 316.24 5.19981C316.466 5.05446 316.732 4.98444 317 4.99981C317.206 4.99605 317.411 5.03698 317.6 5.11981C317.766 5.19674 317.908 5.31781 318.01 5.46981C318.109 5.62134 318.161 5.79875 318.16 5.97981H317.61C317.613 5.90707 317.601 5.83448 317.573 5.76712C317.545 5.69976 317.503 5.63924 317.45 5.58981C317.325 5.48401 317.164 5.43032 317 5.43981C316.85 5.43097 316.702 5.47329 316.58 5.55981C316.53 5.59861 316.489 5.64864 316.461 5.70592C316.433 5.7632 316.419 5.82615 316.42 5.88981C316.423 5.94833 316.439 6.00533 316.469 6.05587C316.499 6.1064 316.54 6.14895 316.59 6.17981C316.755 6.28172 316.933 6.35919 317.12 6.40981C317.33 6.46623 317.532 6.5503 317.72 6.65981C317.861 6.73746 317.978 6.85141 318.06 6.9898C318.13 7.11836 318.165 7.26339 318.16 7.40981C318.164 7.53367 318.138 7.65675 318.084 7.7683C318.03 7.87985 317.95 7.97649 317.85 8.04981C317.609 8.22209 317.316 8.30677 317.02 8.28981C316.8 8.28824 316.583 8.24409 316.38 8.15981C316.202 8.07957 316.047 7.95575 315.93 7.79981C315.821 7.64868 315.765 7.46599 315.77 7.27981H316.33C316.326 7.3589 316.34 7.43785 316.371 7.51064C316.403 7.58342 316.45 7.64813 316.51 7.69981C316.657 7.80798 316.838 7.86107 317.02 7.84981C317.176 7.86165 317.331 7.81926 317.46 7.72981C317.515 7.69674 317.561 7.64983 317.592 7.59371C317.624 7.53758 317.64 7.4742 317.64 7.40981L317.67 7.36981ZM321.25 8.1898H320.7V6.80981H319.26V8.2298H318.71V4.99981H319.26V6.36981H320.7V4.99981H321.25V8.1898ZM325.58 6.67981C325.588 6.9616 325.533 7.24163 325.42 7.49981C325.323 7.72678 325.159 7.91869 324.95 8.04981C324.741 8.17394 324.503 8.23946 324.26 8.23946C324.017 8.23946 323.779 8.17394 323.57 8.04981C323.364 7.91993 323.2 7.7321 323.1 7.50981C322.98 7.25364 322.921 6.9727 322.93 6.68981V6.54981C322.923 6.2681 322.978 5.98827 323.09 5.72981C323.19 5.50478 323.353 5.31369 323.56 5.17981C323.714 5.0798 323.888 5.01416 324.07 4.98734C324.252 4.96052 324.437 4.97316 324.613 5.02439C324.79 5.07562 324.953 5.16425 325.092 5.28425C325.232 5.40424 325.343 5.55279 325.42 5.71981C325.528 5.97942 325.583 6.25844 325.58 6.53981V6.67981ZM325 6.54981C325.018 6.26122 324.944 5.97439 324.79 5.72981C324.727 5.64042 324.643 5.56805 324.545 5.51918C324.448 5.4703 324.339 5.44647 324.23 5.44981C324.119 5.44657 324.01 5.47038 323.91 5.51918C323.811 5.56797 323.725 5.64027 323.66 5.72981C323.512 5.96948 323.438 6.24814 323.45 6.52981V6.71981C323.432 7.00523 323.506 7.28886 323.66 7.52981C323.724 7.62118 323.809 7.69542 323.909 7.746C324.008 7.79657 324.118 7.82192 324.23 7.81981C324.341 7.82578 324.452 7.80318 324.551 7.75415C324.651 7.70513 324.737 7.63133 324.8 7.53981C324.947 7.29258 325.017 7.00698 325 6.71981V6.54981ZM328.64 8.2298H328.09L326.66 5.99981V8.2298H326.11V4.99981H326.66L328.09 7.27981V4.99981H328.64V8.2298ZM310.03 13.2298V9.99981H311C311.262 9.99872 311.52 10.0641 311.75 10.1898C311.968 10.3177 312.145 10.505 312.26 10.7298C312.383 10.9748 312.445 11.2458 312.44 11.5198V11.6798C312.447 11.9572 312.385 12.232 312.26 12.4798C312.144 12.7073 311.963 12.8953 311.74 13.0198C311.506 13.1425 311.244 13.2044 310.98 13.1998L310.03 13.2298ZM310.58 10.4798V12.7898H311C311.123 12.7981 311.247 12.7781 311.361 12.7313C311.476 12.6845 311.578 12.6122 311.66 12.5198C311.832 12.294 311.918 12.0134 311.9 11.7298V11.5498C311.922 11.2688 311.844 10.9891 311.68 10.7598C311.6 10.6644 311.498 10.5893 311.384 10.5406C311.269 10.492 311.144 10.4711 311.02 10.4798H310.58ZM314.84 11.7998H313.52V12.7998H315.06V13.2398H313V9.99981H315V10.4498H313.48V11.3398H314.8L314.84 11.7998ZM316.07 12.7998H317.52V13.2398H315.52V9.99981H316.08L316.07 12.7998ZM318.53 13.2398H318V9.99981H318.55L318.53 13.2398ZM320.34 12.5498L321.15 10.0398H321.77L320.61 13.2398H320.08L318.93 9.99981H319.54L320.34 12.5498ZM324 11.7998H322.68V12.7998H324.22V13.2398H322.13V9.99981H324.2V10.4498H322.68V11.3398H324V11.7998ZM325.85 11.9998H325.23V13.2298H324.67V9.99981H325.8C326.104 9.98272 326.404 10.071 326.65 10.2498C326.751 10.3392 326.831 10.4504 326.883 10.5752C326.935 10.6999 326.958 10.8349 326.95 10.9698C326.952 11.1604 326.9 11.3478 326.8 11.5098C326.69 11.6573 326.541 11.7715 326.37 11.8398L327.09 13.1698H326.49L325.85 11.9998ZM325.23 11.5498H325.8C325.959 11.5626 326.117 11.5123 326.24 11.4098C326.295 11.3617 326.338 11.3015 326.366 11.2337C326.394 11.166 326.406 11.0928 326.4 11.0198C326.405 10.9478 326.394 10.8757 326.368 10.8084C326.342 10.7411 326.302 10.6801 326.25 10.6298C326.192 10.5771 326.124 10.5366 326.05 10.5108C325.976 10.4851 325.898 10.4745 325.82 10.4798H325.23V11.5498ZM328.42 11.5498L329.16 10.0298H329.77L328.7 12.0298V13.1998H328.14V12.0598L327.07 10.0598H327.68L328.42 11.5498Z" fill="#4E4E4E"/>
<path d="M303.45 8.12986L303.27 8.62986H305.86C305.805 8.93062 305.637 9.19888 305.39 9.37986C304.943 9.66134 304.418 9.79433 303.89 9.75986H303.35V10.3299C303.594 10.3287 303.834 10.3982 304.04 10.5299C304.246 10.6829 304.425 10.8687 304.57 11.0799L306.36 13.4999H307.48L305.39 10.8099C305.242 10.589 305.064 10.3903 304.86 10.2199C305.346 10.1533 305.802 9.94459 306.17 9.61986C306.453 9.35861 306.631 9.00299 306.67 8.61986H307.82L307.98 8.11986H306.68C306.661 7.87406 306.59 7.63501 306.473 7.41804C306.356 7.20106 306.195 7.01094 306 6.85986H307.79L307.97 6.35986H303.41L303.25 6.85986H304.16C304.604 6.83145 305.043 6.9706 305.39 7.24986C305.638 7.47903 305.792 7.79304 305.82 8.12986H303.45Z" fill="#212121"/>
<path d="M292.33 0.669922H258.33C258.065 0.669922 257.811 0.775279 257.623 0.962815C257.435 1.15035 257.33 1.40471 257.33 1.66992V16.3299C257.33 16.5951 257.435 16.8495 257.623 17.037C257.811 17.2246 258.065 17.3299 258.33 17.3299H292.33C292.595 17.3299 292.85 17.2246 293.037 17.037C293.225 16.8495 293.33 16.5951 293.33 16.3299V1.66992C293.33 1.40471 293.225 1.15035 293.037 0.962815C292.85 0.775279 292.595 0.669922 292.33 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M272.86 7.9998H272.31L270.88 5.7298V7.9998H270.33V4.7998H270.88L272.31 7.0798V4.7998H272.86V7.9998ZM275.39 6.5698H274.08V7.5698H275.61V7.9998H273.52V4.7998H275.6V5.2498H274.08V6.1298H275.39V6.5698ZM278.39 5.2498H277.39V7.9998H276.84V5.2498H275.84V4.7998H278.38L278.39 5.2498ZM270.33 12.9998V9.7998H271.42C271.714 9.77859 272.006 9.85602 272.25 10.0198C272.346 10.0973 272.422 10.197 272.47 10.3102C272.519 10.4235 272.54 10.5469 272.53 10.6698C272.53 10.8089 272.488 10.9449 272.41 11.0598C272.328 11.1831 272.209 11.2775 272.07 11.3298C272.228 11.3709 272.366 11.4665 272.46 11.5998C272.563 11.7343 272.616 11.9005 272.61 12.0698C272.618 12.1993 272.595 12.3288 272.545 12.4484C272.495 12.568 272.418 12.6746 272.32 12.7598C272.08 12.9351 271.786 13.0201 271.49 12.9998H270.33ZM270.88 11.5598V12.5598H271.5C271.648 12.5643 271.792 12.5184 271.91 12.4298C271.959 12.384 271.996 12.3279 272.021 12.2656C272.045 12.2033 272.055 12.1364 272.05 12.0698C272.062 12.0011 272.057 11.9305 272.037 11.8639C272.016 11.7973 271.98 11.7365 271.931 11.6867C271.882 11.6369 271.822 11.5994 271.756 11.5774C271.69 11.5553 271.619 11.5493 271.55 11.5598H270.88ZM270.88 11.1498H271.43C271.573 11.1573 271.715 11.1149 271.83 11.0298C271.879 10.9905 271.919 10.9401 271.945 10.8828C271.971 10.8254 271.983 10.7627 271.98 10.6998C271.986 10.6346 271.976 10.569 271.952 10.5082C271.927 10.4474 271.889 10.3932 271.84 10.3498C271.714 10.2724 271.567 10.2374 271.42 10.2498H270.88V11.1498ZM275 12.2598H273.77L273.51 12.9998H272.93L274.14 9.7998H274.64L275.8 12.9998H275.22L275 12.2598ZM273.92 11.8098H274.85L274.38 10.4798L273.92 11.8098ZM278.7 12.9998H278.14L276.72 10.7298V12.9998H276.16V9.7998H276.72L278.15 12.0798V9.7998H278.7V12.9998ZM280.27 11.6198L279.91 11.9998V12.9998H279.36V9.7998H279.91V11.2998L280.22 10.9198L281.16 9.7998H281.83L280.64 11.2198L281.9 12.9998H281.24L280.27 11.6198ZM282.78 12.9998H282.23V9.7998H282.78V12.9998ZM286 12.9998H285.45L284 10.7298V12.9998H283.44V9.7998H284L285.43 12.0798V9.7998H286V12.9998ZM289.11 12.5798C288.989 12.7424 288.822 12.8645 288.63 12.9298C288.406 13.0098 288.168 13.0471 287.93 13.0398C287.682 13.0444 287.436 12.9822 287.22 12.8598C287.009 12.7412 286.841 12.5593 286.74 12.3398C286.624 12.0928 286.566 11.8226 286.57 11.5498V11.2998C286.541 10.8926 286.666 10.4894 286.92 10.1698C287.042 10.0261 287.196 9.91379 287.37 9.84242C287.544 9.77104 287.733 9.74271 287.92 9.7598C288.226 9.7394 288.528 9.83192 288.77 10.0198C288.99 10.2147 289.126 10.487 289.15 10.7798H288.61C288.604 10.697 288.581 10.6162 288.544 10.5422C288.506 10.4681 288.454 10.4022 288.391 10.3483C288.328 10.2944 288.254 10.2536 288.175 10.2281C288.096 10.2026 288.013 10.193 287.93 10.1998C287.822 10.1944 287.714 10.2163 287.616 10.2635C287.518 10.3107 287.433 10.3816 287.37 10.4698C287.224 10.7066 287.151 10.9815 287.16 11.2598V11.5098C287.146 11.7931 287.223 12.0734 287.38 12.3098C287.451 12.4031 287.544 12.478 287.65 12.5285C287.756 12.5789 287.873 12.6033 287.99 12.5998C288.099 12.6117 288.21 12.601 288.315 12.5683C288.42 12.5357 288.517 12.4817 288.6 12.4098V11.7798H287.94V11.3598H289.15L289.11 12.5798Z" fill="#4E4E4E"/>
<path d="M263.83 4C264.086 4.01284 264.328 4.12384 264.505 4.31C264.682 4.49616 264.78 4.74322 264.78 5V6C265.393 6.06008 265.962 6.34828 266.373 6.80754C266.784 7.2668 267.008 7.86366 267 8.48V11.08C267 11.3978 266.937 11.7125 266.816 12.0061C266.694 12.2997 266.516 12.5665 266.291 12.7912C266.067 13.0159 265.8 13.1942 265.506 13.3158C265.213 13.4374 264.898 13.5 264.58 13.5C264.262 13.5 263.948 13.4374 263.654 13.3158C263.36 13.1942 263.094 13.0159 262.869 12.7912C262.644 12.5665 262.466 12.2997 262.344 12.0061C262.223 11.7125 262.16 11.3978 262.16 11.08V8.48C262.152 7.86678 262.373 7.27253 262.78 6.81375C263.187 6.35498 263.75 6.06476 264.36 6V5C264.375 4.93418 264.375 4.86582 264.36 4.8C264.339 4.73162 264.301 4.66966 264.25 4.62C264.202 4.57089 264.144 4.53328 264.08 4.51C264.011 4.49626 263.939 4.49626 263.87 4.51C263.799 4.50995 263.728 4.52429 263.662 4.55217C263.597 4.58005 263.537 4.62088 263.488 4.67224C263.439 4.72359 263.4 4.7844 263.374 4.85103C263.349 4.91766 263.337 4.98873 263.34 5.06V5.28C263.34 5.53678 263.242 5.78384 263.065 5.97C262.888 6.15616 262.646 6.26716 262.39 6.28C262.135 6.26469 261.896 6.15261 261.721 5.96671C261.547 5.78081 261.45 5.53514 261.45 5.28C261.441 5.24387 261.441 5.20613 261.45 5.17C261.468 5.13619 261.496 5.10841 261.53 5.09C261.563 5.08107 261.597 5.08107 261.63 5.09C261.666 5.07972 261.704 5.07972 261.74 5.09C261.771 5.11115 261.796 5.13951 261.814 5.17262C261.831 5.20572 261.84 5.24259 261.84 5.28C261.83 5.35502 261.835 5.4314 261.857 5.50398C261.879 5.57656 261.916 5.64367 261.965 5.70077C262.015 5.75788 262.077 5.80366 262.145 5.83503C262.214 5.8664 262.289 5.88263 262.365 5.88263C262.441 5.88263 262.516 5.8664 262.585 5.83503C262.653 5.80366 262.715 5.75788 262.765 5.70077C262.814 5.64367 262.851 5.57656 262.873 5.50398C262.895 5.4314 262.9 5.35502 262.89 5.28V5C262.89 4.74486 262.987 4.49919 263.161 4.31329C263.336 4.12739 263.575 4.01531 263.83 4V4ZM264.57 7C264.531 6.98833 264.489 6.98833 264.45 7C264.41 7.01732 264.373 7.04095 264.34 7.07C264.313 7.10091 264.29 7.13441 264.27 7.17C264.265 7.2132 264.265 7.2568 264.27 7.3V8.48C264.269 8.52286 264.276 8.56555 264.291 8.60553C264.307 8.64552 264.33 8.68198 264.36 8.71277C264.39 8.74355 264.426 8.76802 264.465 8.78472C264.505 8.80142 264.547 8.81002 264.59 8.81H264.71L264.81 8.74C264.842 8.70987 264.866 8.67202 264.88 8.63C264.89 8.59059 264.89 8.54941 264.88 8.51V7.4C264.89 7.35719 264.89 7.31281 264.88 7.27C264.864 7.23222 264.84 7.19818 264.81 7.17L264.71 7.1C264.671 7.091 264.63 7.091 264.59 7.1L264.57 7Z" fill="black"/>
<path d="M251.67 0.669922H221.67C221.405 0.669922 221.15 0.775279 220.963 0.962815C220.775 1.15035 220.67 1.40471 220.67 1.66992V16.3299C220.67 16.5951 220.775 16.8495 220.963 17.037C221.15 17.2246 221.405 17.3299 221.67 17.3299H251.67C251.935 17.3299 252.189 17.2246 252.377 17.037C252.565 16.8495 252.67 16.5951 252.67 16.3299V1.66992C252.67 1.40471 252.565 1.15035 252.377 0.962815C252.189 0.775279 251.935 0.669922 251.67 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path opacity="0.94" fill-rule="evenodd" clip-rule="evenodd" d="M225.34 10.6401C225.34 10.6401 225.48 10.0201 225.86 8.64014C226.16 7.57014 226.45 6.57014 226.49 6.42014L226.58 6.14014H227.77C229.16 6.14014 229.37 6.14014 229.6 6.29014C229.99 6.49014 230.1 6.80014 229.97 7.29014C229.86 7.72984 229.584 8.10984 229.2 8.35014C229.12 8.41014 229.04 8.48014 229.04 8.50014C229.04 8.52014 229.04 8.60014 229.17 8.68014C229.242 8.7387 229.303 8.80985 229.35 8.89014C229.42 9.14205 229.42 9.40823 229.35 9.66014C229.301 9.87078 229.268 10.0847 229.25 10.3001V10.5701H228.67C228.467 10.5953 228.262 10.5953 228.06 10.5701C228 10.5101 228.01 10.3701 228.06 9.96014C228.11 9.55014 228.17 9.34014 228.06 9.23014C227.95 9.12014 227.84 9.05014 227.42 9.04014C227 9.03014 227.03 9.04014 226.98 9.04014C226.93 9.04014 226.83 9.29014 226.64 9.98014L226.48 10.5601H226C225.797 10.5716 225.593 10.5716 225.39 10.5601L225.34 10.6401ZM228.34 8.12014C228.62 8.04014 228.73 7.87014 228.73 7.53014C228.749 7.45811 228.749 7.38217 228.73 7.31014C228.65 7.22014 228.4 7.18014 227.93 7.21014H227.52L227.46 7.41014C227.385 7.62377 227.335 7.84518 227.31 8.07014C227.31 8.12014 227.31 8.14014 227.43 8.16014C227.764 8.1831 228.1 8.16293 228.43 8.10014L228.34 8.12014ZM230.27 10.7301C230.105 10.6746 229.968 10.556 229.89 10.4001C229.77 10.1401 229.89 9.55014 230.3 8.13014C230.4 7.79014 230.48 7.51014 230.48 7.50014C230.48 7.50014 230.61 7.50014 230.97 7.50014C231.136 7.48316 231.304 7.48316 231.47 7.50014H231.55L231.47 7.76014C231.29 8.40014 231.1 9.17014 231.07 9.42014C231.04 9.67014 231.07 9.70014 231.07 9.77014C231.07 9.84014 231.45 9.97014 231.7 9.84014C231.95 9.71014 232.04 9.57014 232.52 7.95014C232.546 7.79977 232.604 7.65668 232.69 7.53014L232.78 7.44014H233.25C233.61 7.44014 233.72 7.44014 233.73 7.44014C233.74 7.44014 233.15 9.66014 232.9 10.5101V10.6501H231.9C231.911 10.5986 231.928 10.5483 231.95 10.5001C232.05 10.2201 231.95 10.2001 231.55 10.5001C231.316 10.6946 231.024 10.8039 230.72 10.8101C230.575 10.8395 230.425 10.8395 230.28 10.8101L230.27 10.7301ZM233.62 10.6401C233.764 9.88484 233.958 9.13988 234.2 8.41014L234.83 6.20014H235.83C237.14 6.20014 237.41 6.20014 237.66 6.36014C237.834 6.42911 237.979 6.55633 238.07 6.72014C238.147 6.89322 238.187 7.08062 238.187 7.27014C238.187 7.45966 238.147 7.64706 238.07 7.82014C237.976 8.13574 237.805 8.42319 237.573 8.65695C237.341 8.89072 237.055 9.06356 236.74 9.16014C236.512 9.19796 236.281 9.22134 236.05 9.23014C235.12 9.29014 235.18 9.23014 235.13 9.40014C235.08 9.57014 235.01 9.77014 234.92 10.0801L234.76 10.6501H234.18C233.98 10.6614 233.78 10.6614 233.58 10.6501L233.62 10.6401ZM236.33 8.26014C236.445 8.25986 236.559 8.22923 236.659 8.17132C236.759 8.11341 236.842 8.03024 236.9 7.93014C236.952 7.84937 236.984 7.75764 236.995 7.6622C237.005 7.56676 236.993 7.4702 236.96 7.38014C236.87 7.24014 236.74 7.21014 236.25 7.21014C236.09 7.19625 235.93 7.19625 235.77 7.21014C235.667 7.51732 235.587 7.83146 235.53 8.15014C235.522 8.17959 235.522 8.21068 235.53 8.24014C235.789 8.27006 236.051 8.27006 236.31 8.24014L236.33 8.26014ZM238.04 10.7401C237.69 10.5901 237.6 10.3501 237.71 9.87014C237.88 9.19014 238.23 8.97014 239.45 8.77014C240.19 8.64014 240.37 8.55014 240.45 8.29014C240.53 8.03014 240.33 8.00014 240 8.00014C239.909 7.99255 239.818 8.00571 239.733 8.03863C239.648 8.07155 239.572 8.12336 239.51 8.19014L239.36 8.31014H239C238.55 8.31014 238.41 8.31014 238.41 8.24014C238.483 8.05537 238.6 7.89081 238.75 7.76014C238.943 7.59458 239.172 7.47795 239.42 7.42014C240.003 7.26719 240.616 7.26719 241.2 7.42014C241.364 7.49223 241.5 7.61487 241.59 7.77014C241.66 7.90014 241.64 8.03014 241.31 9.28014C241.16 9.90014 241.03 10.4501 241.03 10.5201V10.6301H240.66H240.15H240.01L239.95 10.4901C239.87 10.3201 239.87 10.3201 239.49 10.4901C239.217 10.633 238.917 10.7149 238.61 10.7301C238.444 10.7567 238.274 10.7359 238.12 10.6701L238.04 10.7401ZM239.66 10.0501C239.781 9.97261 239.883 9.86996 239.961 9.74917C240.039 9.62838 240.089 9.49228 240.11 9.35014V9.23014H239.92C239.631 9.27272 239.351 9.36046 239.09 9.49014C239.027 9.52931 238.973 9.58025 238.93 9.64014C238.895 9.70847 238.874 9.78342 238.87 9.86014C238.87 10.0001 238.87 10.0401 238.99 10.0801C239.21 10.1336 239.44 10.1336 239.66 10.0801V10.0501ZM241.32 12.0501C241.2 12.0501 241.2 11.9701 241.32 11.6501C241.44 11.3301 241.42 11.2201 241.7 11.1801C241.98 11.1401 242.13 11.0701 242.2 10.9401C242.294 10.1931 242.294 9.43718 242.2 8.69014C242.171 8.26729 242.171 7.84298 242.2 7.42014C242.2 7.42014 242.37 7.42014 242.77 7.42014H243.3V8.42014C243.279 8.75313 243.279 9.08714 243.3 9.42014C243.36 9.47014 243.56 9.14014 244.39 7.62014L244.5 7.40014H245C245.16 7.38773 245.32 7.38773 245.48 7.40014C245.48 7.40014 244.78 8.69014 244.06 9.90014C243.06 11.6301 242.85 11.9001 242.37 12.0001C242.039 12.0524 241.704 12.0691 241.37 12.0501H241.32Z" fill="#2A2C83"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M245.79 11.6698L247.19 6.58984L248.53 9.21984L245.79 11.6698Z" fill="#097A44"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M244.91 11.6899L246.32 6.60986L247.66 9.23986L244.91 11.6899Z" fill="#F46F20"/>
<path d="M214.43 1H185.57C185.242 1.00261 184.928 1.1341 184.696 1.36608C184.464 1.59806 184.333 1.91194 184.33 2.24V15.76C184.333 16.0881 184.464 16.4019 184.696 16.6339C184.928 16.8659 185.242 16.9974 185.57 17H214.43C214.758 16.9974 215.072 16.8659 215.304 16.6339C215.536 16.4019 215.667 16.0881 215.67 15.76V2.24C215.667 1.91194 215.536 1.59806 215.304 1.36608C215.072 1.1341 214.758 1.00261 214.43 1Z" fill="white"/>
<path d="M195.45 17H214.45C214.613 17.0013 214.774 16.9705 214.924 16.9093C215.074 16.848 215.211 16.7576 215.326 16.6433C215.442 16.5289 215.533 16.3929 215.596 16.243C215.658 16.0931 215.69 15.9324 215.69 15.77V10C215.69 10 208.55 14.84 195.47 17H195.45Z" fill="#F58220"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M189.06 8.43998C188.778 8.66612 188.42 8.77372 188.06 8.73998H187.86V6.30998H188.06C188.238 6.29165 188.418 6.30872 188.59 6.3602C188.761 6.41169 188.921 6.49657 189.06 6.60998C189.189 6.72336 189.293 6.86331 189.364 7.02034C189.435 7.17736 189.471 7.34777 189.47 7.51998C189.471 7.69368 189.435 7.86561 189.364 8.02427C189.294 8.18293 189.19 8.32466 189.06 8.43998V8.43998ZM188.2 5.68998H187.11V9.35998H188.19C188.68 9.38407 189.163 9.23149 189.55 8.92998C189.762 8.76084 189.932 8.54634 190.05 8.3023C190.168 8.05826 190.229 7.79092 190.23 7.51998C190.228 7.26557 190.174 7.01429 190.07 6.78184C189.967 6.54939 189.816 6.34077 189.629 6.16902C189.441 5.99727 189.22 5.86609 188.979 5.78366C188.739 5.70124 188.483 5.66935 188.23 5.68998H188.2ZM190.58 5.68998H191.31V9.35998H190.58V5.68998ZM193.12 7.08998C192.68 6.93998 192.55 6.82998 192.55 6.62998C192.55 6.42998 192.78 6.22998 193.1 6.22998C193.215 6.231 193.329 6.25754 193.432 6.30768C193.536 6.35782 193.627 6.43033 193.7 6.51998L194.08 6.02998C193.771 5.76603 193.376 5.62382 192.97 5.62998C192.681 5.6078 192.395 5.70036 192.174 5.88757C191.954 6.07478 191.815 6.34154 191.79 6.62998C191.79 7.12998 192.02 7.38998 192.72 7.62998C192.896 7.68541 193.066 7.75566 193.23 7.83998C193.295 7.8801 193.35 7.9349 193.39 7.99998C193.428 8.06712 193.449 8.14278 193.45 8.21998C193.449 8.29224 193.433 8.36349 193.403 8.42937C193.373 8.49524 193.331 8.55435 193.277 8.60305C193.224 8.65175 193.161 8.68902 193.093 8.71257C193.024 8.73612 192.952 8.74545 192.88 8.73998C192.711 8.74206 192.545 8.69704 192.4 8.60998C192.258 8.5231 192.144 8.39841 192.07 8.24998L191.59 8.69998C191.73 8.92184 191.925 9.10321 192.157 9.22603C192.388 9.34885 192.648 9.40883 192.91 9.39998C193.077 9.41384 193.245 9.39282 193.404 9.33825C193.562 9.28368 193.708 9.19675 193.831 9.08298C193.954 8.96921 194.052 8.83108 194.119 8.67735C194.186 8.52363 194.22 8.35766 194.22 8.18998C194.22 7.60998 193.96 7.33998 193.12 7.03998V7.08998ZM194.45 7.51998C194.451 7.77859 194.504 8.03433 194.606 8.272C194.708 8.50967 194.857 8.72444 195.043 8.90353C195.23 9.08263 195.45 9.22241 195.692 9.31456C195.934 9.40671 196.191 9.44937 196.45 9.43998C196.773 9.44288 197.092 9.36739 197.38 9.21998V8.37998C197.267 8.50653 197.128 8.60762 196.973 8.67657C196.818 8.74552 196.65 8.78077 196.48 8.77998C196.312 8.78696 196.145 8.75922 195.988 8.6985C195.831 8.63778 195.689 8.54539 195.57 8.42711C195.451 8.30883 195.357 8.16721 195.295 8.01112C195.233 7.85503 195.204 7.68783 195.21 7.51998C195.207 7.35547 195.237 7.19207 195.298 7.03931C195.36 6.88655 195.45 6.74749 195.566 6.63021C195.681 6.51294 195.819 6.41981 195.971 6.35625C196.122 6.29269 196.285 6.25996 196.45 6.25998C196.625 6.26061 196.798 6.29734 196.958 6.3679C197.118 6.43845 197.261 6.54129 197.38 6.66998V5.82998C197.099 5.68346 196.787 5.60793 196.47 5.60998C195.951 5.60427 195.451 5.80028 195.074 6.15664C194.697 6.51299 194.473 7.00183 194.45 7.51998V7.51998ZM203.37 8.14998L202.37 5.68998H201.56L203.17 9.44998H203.57L205.21 5.68998H204.41L203.41 8.14998H203.37ZM205.53 9.35998H207.63V8.73998H206.27V7.73998H207.58V7.11998H206.27V6.30998H207.63V5.68998H205.53V9.35998ZM209.07 7.35998H208.86V6.26998H209.09C209.55 6.26998 209.8 6.44998 209.8 6.80998C209.8 7.16998 209.55 7.37998 209.07 7.37998V7.35998ZM210.56 6.74998C210.56 6.05998 210.07 5.66998 209.22 5.66998H208.12V9.35998H208.86V7.87998H208.96L209.96 9.35998H210.87L209.7 7.80998C209.94 7.77602 210.16 7.65585 210.318 7.47187C210.476 7.28788 210.562 7.05264 210.56 6.80998V6.74998Z" fill="#1A1919"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M201.66 7.52979C201.66 8.06022 201.449 8.56893 201.074 8.944C200.699 9.31907 200.191 9.52978 199.66 9.52978C199.13 9.52978 198.621 9.31907 198.246 8.944C197.871 8.56893 197.66 8.06022 197.66 7.52979C197.66 6.99935 197.871 6.49064 198.246 6.11557C198.621 5.7405 199.13 5.52979 199.66 5.52979C200.191 5.52979 200.699 5.7405 201.074 6.11557C201.449 6.49064 201.66 6.99935 201.66 7.52979V7.52979Z" fill="#F58220"/>
<path d="M178.33 0.669922H148.33C148.065 0.669922 147.811 0.775279 147.623 0.962815C147.435 1.15035 147.33 1.40471 147.33 1.66992V16.3299C147.33 16.5951 147.435 16.8495 147.623 17.037C147.811 17.2246 148.065 17.3299 148.33 17.3299H178.33C178.595 17.3299 178.85 17.2246 179.037 17.037C179.225 16.8495 179.33 16.5951 179.33 16.3299V1.66992C179.33 1.40471 179.225 1.15035 179.037 0.962815C178.85 0.775279 178.595 0.669922 178.33 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M160.3 4H166.41C167.736 4 169.008 4.52678 169.945 5.46447C170.883 6.40215 171.41 7.67392 171.41 9C171.41 9.9894 171.116 10.9565 170.566 11.7789C170.016 12.6013 169.234 13.242 168.32 13.62C167.711 13.8704 167.058 13.9995 166.4 14H160.3V4Z" fill="#184977"/>
<path d="M160.3 13.5002C161.188 13.4903 162.053 13.2181 162.786 12.7176C163.52 12.2172 164.089 11.511 164.422 10.6879C164.755 9.86484 164.837 8.96165 164.657 8.09206C164.478 7.22246 164.046 6.42532 163.414 5.80098C162.783 5.17663 161.981 4.75299 161.11 4.58337C160.238 4.41375 159.336 4.50574 158.517 4.84775C157.697 5.18977 156.997 5.76652 156.505 6.50542C156.013 7.24432 155.75 8.11234 155.75 9.00021C155.753 9.59464 155.872 10.1827 156.103 10.7307C156.333 11.2788 156.669 11.776 157.092 12.194C157.514 12.612 158.015 12.9426 158.566 13.1667C159.116 13.3908 159.706 13.5042 160.3 13.5002V13.5002Z" fill="white" stroke="#184977"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M162.32 9C162.321 8.51604 162.179 8.04264 161.91 7.64C161.641 7.23953 161.258 6.92938 160.81 6.75V11.25C161.258 11.0706 161.641 10.7605 161.91 10.36C162.179 9.95736 162.321 9.48396 162.32 9V9ZM159.8 11.25V6.75C159.349 6.9286 158.962 7.23861 158.689 7.63982C158.417 8.04103 158.271 8.51492 158.271 9C158.271 9.48508 158.417 9.95897 158.689 10.3602C158.962 10.7614 159.349 11.0714 159.8 11.25V11.25Z" fill="#184977"/>
<path d="M141.67 0.669922H111.67C111.405 0.669922 111.15 0.775279 110.963 0.962815C110.775 1.15035 110.67 1.40471 110.67 1.66992V16.3299C110.67 16.5951 110.775 16.8495 110.963 17.037C111.15 17.2246 111.405 17.3299 111.67 17.3299H141.67C141.935 17.3299 142.189 17.2246 142.377 17.037C142.565 16.8495 142.67 16.5951 142.67 16.3299V1.66992C142.67 1.40471 142.565 1.15035 142.377 0.962815C142.189 0.775279 141.935 0.669922 141.67 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M128.79 11.69L128.51 11.35V11.69H127.59V11.1C127.472 11.1591 127.342 11.1899 127.21 11.19H126.88V11.69H125.5L125.25 11.36L125 11.69H122.71V9.51H125L125.25 9.83L125.5 9.51H133.9V2H120V7.87L120.52 6.64H121.42L121.72 7.32V6.64H122.84L123.02 7.14L123.19 6.64H128.19V6.89C128.393 6.7243 128.648 6.63574 128.91 6.64H130.51L130.81 7.32V6.64H131.76L132 7V6.64H133V8.82H132L131.75 8.43V8.82H130.37L130.23 8.49H129.86L129.72 8.82H128.87C128.619 8.83266 128.373 8.75056 128.18 8.59V8.82H126.77L126.49 8.48V8.82H121.25L121.12 8.49H120.74L120.6 8.82H120V15.91H133.91V11.53C133.756 11.641 133.57 11.6973 133.38 11.69H128.79Z" fill="#0079C1"/>
<path d="M133.92 9.78019L133.76 10.1502L132.98 9.99019C132.964 9.98359 132.947 9.98019 132.93 9.98019C132.913 9.98019 132.896 9.98359 132.88 9.99019C132.871 10.0195 132.871 10.0509 132.88 10.0802C132.88 10.1173 132.895 10.1529 132.921 10.1792C132.947 10.2054 132.983 10.2202 133.02 10.2202H133.4C133.73 10.2202 133.92 10.3902 133.92 10.6902C133.934 10.7595 133.934 10.8309 133.92 10.9002C133.892 10.9699 133.847 11.0317 133.79 11.0802C133.736 11.1298 133.673 11.1678 133.605 11.1919C133.536 11.216 133.463 11.2256 133.39 11.2202H132.54V10.8502H133.46C133.466 10.8341 133.466 10.8163 133.46 10.8002C133.465 10.7805 133.465 10.7599 133.46 10.7402H133.41H132.96C132.894 10.7545 132.826 10.7545 132.76 10.7402C132.696 10.7208 132.638 10.6865 132.59 10.6402C132.539 10.5942 132.501 10.5356 132.48 10.4702C132.466 10.4077 132.466 10.3427 132.48 10.2802C132.48 10.2072 132.497 10.1352 132.53 10.0702C132.558 10.0031 132.599 9.94198 132.65 9.89019C132.705 9.84042 132.769 9.80292 132.84 9.78019C132.909 9.76533 132.981 9.76533 133.05 9.78019H133.92Z" fill="#0079C1"/>
<path d="M105 0.669922H75C74.7348 0.669922 74.4804 0.775279 74.2929 0.962815C74.1054 1.15035 74 1.40471 74 1.66992V16.3299C74 16.5951 74.1054 16.8495 74.2929 17.037C74.4804 17.2246 74.7348 17.3299 75 17.3299H105C105.265 17.3299 105.52 17.2246 105.707 17.037C105.895 16.8495 106 16.5951 106 16.3299V1.66992C106 1.40471 105.895 1.15035 105.707 0.962815C105.52 0.775279 105.265 0.669922 105 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M86.46 14C87.4489 14 88.4156 13.7068 89.2378 13.1573C90.0601 12.6079 90.7009 11.827 91.0794 10.9134C91.4578 9.99979 91.5568 8.99446 91.3639 8.02455C91.171 7.05465 90.6948 6.16373 89.9955 5.46447C89.2962 4.76521 88.4053 4.289 87.4354 4.09608C86.4655 3.90315 85.4602 4.00217 84.5465 4.3806C83.6329 4.75904 82.852 5.39991 82.3026 6.22215C81.7532 7.0444 81.46 8.0111 81.46 9C81.46 10.3261 81.9867 11.5979 82.9244 12.5355C83.8621 13.4732 85.1339 14 86.46 14V14Z" fill="#007BDB"/>
<path d="M93.54 14.0001C94.5262 13.9883 95.487 13.685 96.3013 13.1285C97.1156 12.5719 97.7471 11.7869 98.1164 10.8723C98.4856 9.95773 98.5761 8.95434 98.3765 7.98842C98.1768 7.0225 97.696 6.13721 96.9944 5.44393C96.2928 4.75066 95.4018 4.28038 94.4336 4.09227C93.4653 3.90415 92.4631 4.00661 91.553 4.38675C90.6428 4.76689 89.8654 5.40772 89.3186 6.22862C88.7718 7.04951 88.4801 8.01379 88.48 9.00013C88.4799 9.66182 88.6112 10.3169 88.8662 10.9275C89.1213 11.5381 89.4949 12.092 89.9656 12.557C90.4363 13.0221 90.9946 13.3892 91.6081 13.6369C92.2217 13.8846 92.8783 14.0081 93.54 14.0001V14.0001Z" fill="#E42B00"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M90 12.5799C90.8967 11.5927 91.4325 10.3308 91.52 8.99992C91.4325 7.66909 90.8967 6.40716 90 5.41992C89.5084 5.87561 89.1187 6.4302 88.8568 7.04722C88.5948 7.66424 88.4664 8.32973 88.48 8.99992C88.4683 9.66985 88.5975 10.3348 88.8594 10.9515C89.1213 11.5683 89.5099 12.1231 90 12.5799V12.5799Z" fill="#1740CE"/>
<path d="M68.3301 0.669922H38.3301C38.0649 0.669922 37.8105 0.775279 37.623 0.962815C37.4354 1.15035 37.3301 1.40471 37.3301 1.66992V16.3299C37.3301 16.5951 37.4354 16.8495 37.623 17.037C37.8105 17.2246 38.0649 17.3299 38.3301 17.3299H68.3301C68.5953 17.3299 68.8496 17.2246 69.0372 17.037C69.2247 16.8495 69.3301 16.5951 69.3301 16.3299V1.66992C69.3301 1.40471 69.2247 1.15035 69.0372 0.962815C68.8496 0.775279 68.5953 0.669922 68.3301 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M49.8002 14.0001C50.7865 13.9883 51.7472 13.685 52.5615 13.1285C53.3759 12.5719 54.0074 11.7869 54.3766 10.8723C54.7459 9.95773 54.8364 8.95434 54.6367 7.98842C54.4371 7.0225 53.9562 6.13721 53.2546 5.44393C52.553 4.75066 51.6621 4.28038 50.6938 4.09227C49.7256 3.90415 48.7234 4.00661 47.8132 4.38675C46.9031 4.76689 46.1257 5.40772 45.5789 6.22862C45.0321 7.04951 44.7403 8.01379 44.7402 9.00013C44.7402 9.66182 44.8715 10.3169 45.1265 10.9275C45.3815 11.5381 45.7552 12.092 46.2258 12.557C46.6965 13.0221 47.2548 13.3892 47.8684 13.6369C48.482 13.8846 49.1386 14.0081 49.8002 14.0001V14.0001Z" fill="#CC0000"/>
<path d="M56.8701 14C57.859 14 58.8257 13.7068 59.648 13.1573C60.4702 12.6079 61.1111 11.827 61.4895 10.9134C61.868 9.99979 61.967 8.99446 61.774 8.02455C61.5811 7.05465 61.1049 6.16373 60.4057 5.46447C59.7064 4.76521 58.8155 4.289 57.8456 4.09608C56.8757 3.90315 55.8703 4.00217 54.9567 4.3806C54.0431 4.75904 53.2622 5.39991 52.7128 6.22215C52.1634 7.0444 51.8701 8.0111 51.8701 9C51.8701 10.3261 52.3969 11.5979 53.3346 12.5355C54.2723 13.4732 55.544 14 56.8701 14V14Z" fill="#FF9900"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M53.3299 12.5799C54.2267 11.5927 54.7625 10.3308 54.8499 8.99992C54.7625 7.66909 54.2267 6.40716 53.3299 5.41992C52.8406 5.8769 52.4532 6.43195 52.193 7.04884C51.9328 7.66572 51.8057 8.33056 51.8199 8.99992C51.8076 9.66902 51.9356 10.3333 52.1956 10.9499C52.4557 11.5665 52.8421 12.1218 53.3299 12.5799V12.5799Z" fill="#F16D27"/>
<path d="M31.6699 0.669922H1.66992C1.40471 0.669922 1.15035 0.775279 0.962815 0.962815C0.775279 1.15035 0.669922 1.40471 0.669922 1.66992V16.3299C0.669922 16.5951 0.775279 16.8495 0.962815 17.037C1.15035 17.2246 1.40471 17.3299 1.66992 17.3299H31.6699C31.9351 17.3299 32.1895 17.2246 32.377 17.037C32.5646 16.8495 32.6699 16.5951 32.6699 16.3299V1.66992C32.6699 1.40471 32.5646 1.15035 32.377 0.962815C32.1895 0.775279 31.9351 0.669922 31.6699 0.669922V0.669922Z" fill="white" stroke="#E0E0E0" stroke-width="0.67"/>
<path d="M14 5.79996L11.25 12.36H9.43L8.08 7.11996C8.06406 6.9972 8.01838 6.8802 7.94693 6.77912C7.87547 6.67804 7.78041 6.59594 7.67 6.53996C7.13718 6.29318 6.57643 6.11186 6 5.99996V5.79996H8.93C9.11859 5.80277 9.29977 5.87383 9.44 5.99996C9.58018 6.12604 9.67505 6.29469 9.71 6.47996L10.42 10.28L12.19 5.79996H14ZM21 10.22C21 8.47996 18.61 8.38996 18.62 7.60996C18.62 7.37996 18.85 7.12996 19.34 7.06996C19.9094 7.01357 20.4834 7.11385 21 7.35996L21.31 5.99996C20.8052 5.80868 20.2699 5.71042 19.73 5.70996C18.06 5.70996 16.87 6.59996 16.86 7.87996C16.86 8.81996 17.7 9.34996 18.35 9.65996C19 9.96996 19.23 10.19 19.23 10.48C19.23 10.92 18.7 11.11 18.23 11.12C17.6233 11.1336 17.0236 10.9888 16.49 10.7L16.18 12.14C16.7831 12.3719 17.4238 12.4906 18.07 12.49C19.85 12.49 21.02 11.61 21.02 10.25L21 10.22ZM25.43 12.35H27L25.63 5.79996H24.18C24.0266 5.79702 23.8761 5.84251 23.75 5.92996C23.6181 6.01225 23.5163 6.13504 23.46 6.27996L20.91 12.36H22.69L23.05 11.36H25.23L25.43 12.35ZM23.54 9.99996L24.43 7.53996L24.94 9.99996H23.54ZM16.4 5.79996L15 12.36H13.3L14.7 5.79996H16.4Z" fill="#1A1F71"/>
</svg>
  </div>
  <div id="piBadgeWrap"></div>
  <div class="price-row">
    <div class="pi-price" id="piPrice"></div>
    <div class="pi-mrp" id="piMrp"></div>
    <div class="pi-off" id="piOff"></div>
  </div>
  <span class="pi-stock" id="piStock"></span>

  <!-- COLOR VARIANTS -->
  <div class="variant-section" id="colorSection" style="display:none">
    <div class="variant-label">Selected Color: <b id="selectedColorName"></b></div>
    <div class="variant-row" id="colorRow"></div>
  </div>

  <!-- STORAGE/RAM VARIANTS -->
  <div class="variant-section" id="storageSection" style="display:none">
    <div class="variant-label">Variant: <b id="selectedVariantName"></b></div>
    <div class="variant-row" id="storageRow"></div>
  </div>

  <div class="emi-line" id="emiInfoLine" style="display:none;align-items:center;gap:6px">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="flex-shrink:0"><rect x="2" y="5" width="20" height="14" rx="3" stroke="#2874f0" stroke-width="2"/><path d="M2 10h20" stroke="#2874f0" stroke-width="2"/><path d="M6 15h4" stroke="#2874f0" stroke-width="2" stroke-linecap="round"/></svg>
    EMI from <b id="emiInfoAmt"></b>/month
  </div>

  <div class="offers-title">Available offers</div>
  <!-- Bank Offer -->
  <div class="offer-row">
    <span class="offer-ico">
      <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
        <rect width="26" height="26" rx="5" fill="#e8f0fe"/>
        <path d="M13 4L4 8.5h18L13 4z" fill="#2874f0"/>
        <rect x="6" y="9.5" width="2.5" height="7" rx="1" fill="#2874f0"/>
        <rect x="11.75" y="9.5" width="2.5" height="7" rx="1" fill="#2874f0"/>
        <rect x="17.5" y="9.5" width="2.5" height="7" rx="1" fill="#2874f0"/>
        <rect x="4" y="16.5" width="18" height="2" rx="1" fill="#2874f0"/>
      </svg>
    </span>
    <div class="offer-txt"><b>Bank Offer</b> 10% off on SBI Credit Card, up to ₹1,500. <a href="#">T&amp;C</a></div>
  </div>
  <!-- Special Price -->
  <div class="offer-row">
    <span class="offer-ico">
      <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
        <rect width="26" height="26" rx="5" fill="#fff3e0"/>
        <circle cx="13" cy="13" r="7.5" stroke="#f57c00" stroke-width="1.7" fill="none"/>
        <path d="M13 7.5v1.5M13 17v1.5M10.5 11c0-1.1.9-2 2.5-2s2.5.9 2.5 2c0 1.2-.9 1.8-2.5 2.5-1.6.7-2.5 1.4-2.5 2.5s.9 2 2.5 2 2.5-.9 2.5-2" stroke="#f57c00" stroke-width="1.6" stroke-linecap="round"/>
      </svg>
    </span>
    <div class="offer-txt"><b>Special Price</b> Extra 5% off (price inclusive of cashback/coupon). <a href="#">T&amp;C</a></div>
  </div>
  <!-- No Cost EMI -->
  <div class="offer-row">
    <span class="offer-ico"><svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="26" height="26" rx="5" fill="#e8f5e9"/><path d="M7 8h12v10H7z" stroke="#388e3c" stroke-width="1.7"/><path d="M7 11h12" stroke="#388e3c" stroke-width="1.7"/><path d="M10 16h6" stroke="#388e3c" stroke-width="1.7" stroke-linecap="round"/></svg></span>
    <div class="offer-txt"><b>No Cost EMI</b> on Bajaj Finserv above ₹2,999. <a href="#">T&amp;C</a></div>
  </div>
  <!-- Partner Offer -->
  <div class="offer-row">
    <span class="offer-ico">
      <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
        <rect width="26" height="26" rx="5" fill="#fce4ec"/>
        <path d="M13 5l2.5 5 5.5.8-4 3.9.95 5.5L13 17.75 8.05 20.2 9 14.7 5 10.8l5.5-.8L13 5z" fill="#e91e63" stroke="#e91e63" stroke-width=".5" stroke-linejoin="round"/>
      </svg>
    </span>
    <div class="offer-txt"><b>Partner Offer</b> Make a purchase and enjoy a surprise cashback/coupon. <a href="#">T&amp;C</a></div>
  </div>
</div>

<div class="div8"></div>

<!-- DELIVERY -->
<div class="delivery">
  <div class="sec-title">Delivery</div>
  <div class="pin-row">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="flex-shrink:0">
      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#e53935"/>
      <circle cx="12" cy="9" r="2.5" fill="white"/>
    </svg>
    <input id="pinInput" type="number" placeholder="Enter delivery pincode" maxlength="6">
    <button onclick="checkPin()">Check</button>
  </div>
  <!-- Free Delivery -->
  <div class="del-row">
    <svg class="del-icon" viewBox="0 0 24 24" fill="none">
      <rect x="1" y="6" width="14" height="11" rx="1.5" stroke="#2874f0" stroke-width="1.5" fill="#e3f2fd"/>
      <path d="M15 9h4l3 4v4h-7V9z" stroke="#2874f0" stroke-width="1.5" stroke-linejoin="round" fill="#e3f2fd"/>
      <circle cx="5.5" cy="18.5" r="1.5" fill="#2874f0"/>
      <circle cx="18.5" cy="18.5" r="1.5" fill="#2874f0"/>
    </svg>
    <span><b>Free delivery</b> by <span class="dgreen">Tomorrow, 10 AM</span></span>
  </div>
  <!-- 7 Days Replacement -->
  <div class="del-row">
    <svg class="del-icon" viewBox="0 0 24 24" fill="none">
      <path d="M2.5 11.5C3 7.5 6.5 4.5 11 4.5c2.8 0 5.3 1.3 6.9 3.3" stroke="#2874f0" stroke-width="1.5" stroke-linecap="round"/>
      <path d="M21.5 12.5C21 16.5 17.5 19.5 13 19.5c-2.8 0-5.3-1.3-6.9-3.3" stroke="#2874f0" stroke-width="1.5" stroke-linecap="round"/>
      <path d="M17.9 4.5v3.3h3.3" stroke="#2874f0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M6.1 19.5v-3.3H2.8" stroke="#2874f0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span>7 Days Replacement Policy</span>
  </div>
  <!-- Pay on Delivery -->
  <div class="del-row">
    <svg class="del-icon" viewBox="0 0 24 24" fill="none">
      <rect x="2" y="5" width="20" height="14" rx="2.5" stroke="#388e3c" stroke-width="1.5" fill="#e8f5e9"/>
      <rect x="2" y="9" width="20" height="3.5" fill="#388e3c"/>
      <rect x="4" y="14.5" width="4.5" height="1.5" rx=".75" fill="#388e3c" opacity=".6"/>
    </svg>
    <span>Pay on Delivery available</span>
  </div>
  <!-- Top Brand -->
  <div class="del-row">
    <svg class="del-icon" viewBox="0 0 24 24" fill="none">
      <path d="M12 2l3 6.5 7 1-5 4.9 1.18 7L12 18l-6.18 3.4L7 14.4 2 9.5l7-1L12 2z" fill="#ff9800" stroke="#e65100" stroke-width=".5"/>
    </svg>
    <span>Top Brand — <span class="dgreen">Genuine product assured</span></span>
  </div>
</div>

<div class="div8"></div>

<!-- HIGHLIGHTS -->
<div class="highlights">
  <div class="sec-title">Product Highlights</div>
  <div id="hlBox"></div>
</div>

<div class="div8"></div>

<!-- SELLER -->
<div class="seller">
  <div class="sec-title">Seller</div>
  <div class="seller-top">
    <div class="seller-name" id="sellerName" style="display:flex;align-items:center;gap:5px">
      <span id="sellerNameTxt">RetailNet</span><span class="shared-seller-assured" title="Flipkart Assured">FK</span>
    </div>
    <div class="seller-rp">4.5 ★</div>
  </div>
  <div class="seller-sub">7 days return &amp; exchange applicable</div>
</div>

<div class="div8"></div>

<!-- DESCRIPTION -->
<div class="desc-sec">
  <div class="sec-title">Product Description</div>
  <div class="desc-text" id="descBox"></div>
  <span class="desc-more" id="descMore" onclick="toggleDesc()">Read More ▼</span>
</div>

<div class="div8"></div>

<!-- GALLERY -->
<div class="gallery-sec" id="gallerySec" style="display:none">
  <div class="sec-title">Product Gallery</div>
  <div class="gallery-grid" id="galleryGrid"></div>
</div>
<div class="lightbox" id="lightbox">
  <div class="lb-topbar">
    <button class="lb-close" onclick="closeLB()"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
    <div class="lb-counter-top" id="lbCounter"></div>
    <div style="width:36px"></div>
  </div>
  <div class="lb-slider-wrap" id="lbSliderWrap">
    <div class="lb-slider" id="lbSlider"></div>
  </div>
</div>

<div class="div8"></div>

<!-- RATINGS & REVIEWS -->
<div class="ratings-sec">
  <div class="rt-head">
    <div class="sec-title" style="margin:0">Ratings &amp; Reviews</div>
    <span class="rt-viewall">View All →</span>
  </div>
  <div class="rt-overview">
    <div class="rt-big">
      <div class="rt-num" id="rtNum">4.2</div>
      <div class="rt-stars">★★★★☆</div>
      <div class="rt-total" id="rtTotal">2,456 Ratings</div>
    </div>
    <div class="bars">
      <div class="bar-row"><span class="bar-lbl">5★</span><div class="bar-track"><div class="bar-fill" style="width:58%"></div></div><span class="bar-cnt">1,424</span></div>
      <div class="bar-row"><span class="bar-lbl">4★</span><div class="bar-track"><div class="bar-fill" style="width:22%"></div></div><span class="bar-cnt">540</span></div>
      <div class="bar-row"><span class="bar-lbl">3★</span><div class="bar-track"><div class="bar-fill med" style="width:10%"></div></div><span class="bar-cnt">246</span></div>
      <div class="bar-row"><span class="bar-lbl">2★</span><div class="bar-track"><div class="bar-fill low" style="width:5%"></div></div><span class="bar-cnt">122</span></div>
      <div class="bar-row"><span class="bar-lbl">1★</span><div class="bar-track"><div class="bar-fill low" style="width:5%"></div></div><span class="bar-cnt">124</span></div>
    </div>
  </div>
  <div class="rv-card">
    <div class="rv-top">
      <div class="rv-pill">5 ★</div>
      <div class="rv-author">Rahul K.</div>
      <div class="rv-cert">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5.5" fill="#388e3c"/><path d="M3.5 6l1.8 1.8L8.5 4.5" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Certified Buyer
      </div>
    </div>
    <div class="rv-title">Best product in this range!</div>
    <div class="rv-body">Amazing quality! Highly recommended for the price. Packaging was great and delivery was super fast.</div>
    <div class="rv-date">15 Dec, 2025</div>
    <div class="rv-helpful"><span>Helpful?</span><button onclick="this.textContent='👍 1'">👍 0</button><button>👎</button></div>
  </div>
  <div class="rv-card">
    <div class="rv-top">
      <div class="rv-pill">4 ★</div>
      <div class="rv-author">Priya S.</div>
      <div class="rv-cert">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5.5" fill="#388e3c"/><path d="M3.5 6l1.8 1.8L8.5 4.5" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Certified Buyer
      </div>
    </div>
    <div class="rv-title">Good value for money</div>
    <div class="rv-body">Comfortable to use for long hours. Great audio quality for this price range.</div>
    <div class="rv-date">2 Jan, 2026</div>
    <div class="rv-helpful"><span>Helpful?</span><button onclick="this.textContent='👍 3'">👍 2</button><button>👎</button></div>
  </div>
  <div class="rv-card">
    <div class="rv-top">
      <div class="rv-pill med">3 ★</div>
      <div class="rv-author">Amit P.</div>
      <div class="rv-cert">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5.5" fill="#388e3c"/><path d="M3.5 6l1.8 1.8L8.5 4.5" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Certified Buyer
      </div>
    </div>
    <div class="rv-title">Average — expected better</div>
    <div class="rv-body">Build quality is decent but not premium. Expected better performance at this price.</div>
    <div class="rv-date">10 Jan, 2026</div>
    <div class="rv-helpful"><span>Helpful?</span><button><svg viewBox="0 0 24 24" width="14" height="14" fill="#666"><path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-1.91l-.01-.01L23 10z"/></svg> 0</button><button><svg viewBox="0 0 24 24" width="14" height="14" fill="#666"><path d="M15 3H6c-.83 0-1.54.5-1.84 1.22l-3.02 7.05c-.09.23-.14.47-.14.73v1.91l.01.01L1 14c0 1.1.9 2 2 2h6.31l-.95 4.57-.03.32c0 .41.17.79.44 1.06L9.83 23l6.59-6.59c.36-.36.58-.86.58-1.41V5c0-1.1-.9-2-2-2zm4 0v12h4V3h-4z"/></svg></button></div>
  </div>
</div>

<div class="div8"></div>

<!-- SIMILAR PRODUCTS -->
<div class="section" id="similarSec">
  <div class="sec-head">
    <div class="sec-title2">Similar Products</div>
    <button class="sec-viewall" onclick="goCategory()">View all →</button>
  </div>
  <div class="sec-sub"></div>
  <div class="h-scroll" id="simScroll"></div>
</div>

<div class="div8"></div>

<!-- TRENDING NOW -->
<div class="section" id="trendSec">
  <div class="sec-head">
    <div class="sec-title2"><svg width="14" height="14" viewBox="0 0 24 24" fill="#ff6b35" style="vertical-align:-2px;margin-right:4px"><path d="M13.5 0.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/></svg> Trending Now</div>
    <button class="sec-viewall" onclick="window.location.href='search.php'">View All</button>
  </div>
  <div class="sec-sub">Top picks loved by customers this week</div>
  <div class="h-scroll" id="trendScroll"></div>
</div>

<div class="div8"></div>

<!-- FREQUENTLY BOUGHT TOGETHER -->
<div class="section" id="fbtSec">
  <div class="sec-title" style="margin-bottom:12px">Frequently Bought Together</div>
  <div class="fbt-wrap" id="fbtWrap"></div>
  <div class="fbt-total-box">
    <div>
      <div class="fbt-total-lbl">Total Price for 3 items</div>
      <div class="fbt-save" id="fbtSave">You save ₹0</div>
    </div>
    <div class="fbt-total-price" id="fbtTotal">₹0</div>
  </div>
  <button class="fbt-btn" onclick="addFBT()">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:5px"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke="white" stroke-width="1.8" stroke-linejoin="round"/><line x1="3" y1="6" x2="21" y2="6" stroke="white" stroke-width="1.8"/><path d="M16 10a4 4 0 01-8 0" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
    Add All 3 to Cart
  </button>
</div>

<div class="div8"></div>

<!-- TOP DEALS OF THE DAY -->
<div class="section">
  <div class="sec-head">
    <div class="sec-title2"><svg viewBox="0 0 24 24" width="14" height="14" fill="#ff9800" style="vertical-align:-2px;margin-right:4px"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg> Top Deals of the Day</div>
    <button class="sec-viewall">See All</button>
  </div>
  <div class="sec-sub">Limited time offers — grab before they're gone!</div>
  <div class="h-scroll" id="dealsScroll"></div>
</div>

<div class="div8"></div>

<!-- SUGGESTED FOR YOU -->
<div class="section">
  <div class="sec-head">
    <div class="sec-title2"><svg viewBox="0 0 24 24" width="14" height="14" fill="#ffd600" style="vertical-align:-2px;margin-right:4px"><path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7z"/></svg> Suggested For You</div>
    <button class="sec-viewall" onclick="window.location.href='search.php'">View All</button>
  </div>
  <div class="sec-sub"></div>
  <div class="sfy-list" id="sfyList"></div>
</div>

<div class="div8"></div>

<!-- YOU MAY ALSO LIKE -->
<div class="section">
  <div class="sec-head">
    <div class="sec-title2"><svg viewBox="0 0 24 24" width="14" height="14" fill="#ff3f6c" style="vertical-align:-2px;margin-right:4px"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> You May Also Like</div>
    <button class="sec-viewall" onclick="window.location.href='search.php'">View All</button>
  </div>
  <div class="sec-sub"></div>
  <div class="h-scroll" id="ymlikeScroll"></div>
</div>

<div class="div8"></div>

<!-- SPONSORED PRODUCTS -->
<div class="section" id="sponsoredSec">
  <div class="sec-head">
    <div class="sec-title2">Sponsored</div>
    <span class="ad-tag">AD</span>
  </div>
  <div class="sec-sub" style="margin-bottom:10px">Products based on your browsing</div>
  <div class="spon-grid" id="sponsoredGrid"></div>
</div>

<div class="div8"></div>

<!-- BOTTOM BAR -->
<div class="bottom-bar">
  <button class="btn-cart" onclick="addCart()" aria-label="Add to cart">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M6.2 6h13.1l-1.36 5.86a2 2 0 0 1-1.95 1.54H9.92a2 2 0 0 1-1.95-1.58L6.1 4.8H4" stroke="#2874f0" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/>
      <circle cx="10.4" cy="18" r="1.45" fill="#2874f0"/>
      <circle cx="16.9" cy="18" r="1.45" fill="#2874f0"/>
    </svg>
    <span class="btn-cart-label">Add to Cart</span>
  </button>
  <button class="btn-emi" id="emiBtn" onclick="showEmiInfo()" style="display:none">
    <span>Buy with EMI</span>
    <span class="emi-sub" id="emiMonthly"></span>
  </button>
  <button class="btn-buy" onclick="buyNow()">
    <span id="buyNowLabel">Buy Now</span>
    <span class="buy-sub" id="buyNowPrice"></span>
  </button>
</div>

<div class="toast" id="toast"></div>

<div class="emi-modal" id="emiModal" aria-hidden="true">
  <div class="emi-sheet">
    <div class="emi-grab"></div>
    <div class="emi-head">
      <h3>EMI Options</h3>
      <button class="emi-close" type="button" onclick="closeEmiModal()">×</button>
    </div>
    <div class="emi-subtxt" id="emiSheetSub">Choose a plan for this product.</div>
    <div class="emi-plan-grid" id="emiPlanGrid"></div>
    <div class="emi-bank">Eligible bank offers and no-cost EMI availability can vary by card, bank and final order value.</div>
    <div class="emi-cta">
      <button class="emi-cancel" type="button" onclick="closeEmiModal()">Cancel</button>
      <button class="emi-continue" type="button" id="emiContinueBtn" onclick="continueEmiCheckout()">Continue with EMI</button>
    </div>
    <div class="emi-note">EMI is shown for convenience. Final EMI eligibility depends on payment method and gateway support.</div>
  </div>
</div>


<script>
// ── STATE ──────────────────────────────────────────────────────
const products = {};
const params = new URLSearchParams(location.search);
let currentId = params.get('id') || 'p1';
let descOpen = false, wished = false;

// Load wishlist state
try {
  const wl = JSON.parse(localStorage.getItem('fk_wishlist') || '[]');
  wished = wl.some(function(entry){ return (typeof entry === 'string' ? entry : entry && entry.id) === currentId; });
} catch(e){}

// ── BACK BUTTON — functional, works from any source ──────────
function goBack() { goBackSmart('index.php'); }
// Also handle Android hardware back gesture
window.addEventListener('popstate', function() {
  const nextId = (new URLSearchParams(location.search).get('id') || currentId || 'p1');
  if (nextId !== currentId) load(nextId);
});

// ── IMAGE HELPER ───────────────────────────────────────────────
const _EXTS = ['jpg','jpeg','png','webp','avif'];
function smartSrc(el, base, fallback, onFail) {
  if (!base) { if(fallback) el.src=fallback; else if(onFail) onFail(); return; }
  if (/\.\w{3,5}$/.test(base)) {
    el.onerror = function(){ el.onerror=null; if(fallback){el.src=fallback;}else if(onFail){onFail();} };
    el.src=base; return;
  }
  let i=0;
  function next() {
    if (i>=_EXTS.length) { el.onerror=null; if(fallback){el.src=fallback;}else if(onFail){onFail();} return; }
    el.onerror=next; el.src=base+'.'+_EXTS[i++];
  }
  next();
}
const FB_SVG = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Crect fill='%23f5f5f5' width='120' height='120'/%3E%3Ctext x='50%25' y='50%25' font-size='11' fill='%23bbb' text-anchor='middle' dominant-baseline='middle'%3ENo Image%3C/text%3E%3C/svg%3E";
// PHP-scanned images for the initial product — bypasses browser guessing
const FK_PHP_IMGS = <?= $_phpImgsJson ?>;
const currentPid  = '<?= htmlspecialchars($_pid, ENT_QUOTES) ?>';

function getProductImageList(id, p) {
  const pidNum = String(id || '').replace(/^p/i, '') || '1';
  let imgs = [];
  // 1. Use PHP server-scanned images for the current product (most reliable)
  if (id === currentPid && FK_PHP_IMGS.length) {
    return FK_PHP_IMGS;
  }
  // 2. Use images array from products.json
  if (p && Array.isArray(p.images)) {
    imgs = p.images.filter(Boolean).map(function(src){ return String(src).trim(); });
  }
  // 3. Fallback — guess avif paths
  if (!imgs.length) {
    imgs = Array.from({length: 8}, function(_, n){ return 'Images/TopPicksForYou/' + pidNum + '/' + (n + 1) + '.avif'; });
  }
  return imgs;
}
let selectedEmiMonths = 12;

// ── LOAD PRODUCT ───────────────────────────────────────────────
function load(id) {
  currentId = id;
  const p = products[id];
  if (!p) {
    // Show friendly error if product not found
    document.title = 'Product Not Found – Flipkart';
    const errDiv = document.getElementById('productErrorMsg');
    if (!errDiv) {
      const wrap = document.createElement('div');
      wrap.id = 'productErrorMsg';
      wrap.style.cssText = 'background:#fff;border-radius:8px;margin:24px 16px;padding:32px 20px;text-align:center;color:#666';
      wrap.innerHTML = '<svg width="64" height="64" viewBox="0 0 24 24" fill="#bdbdbd" style="margin-bottom:12px"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>'
        + '<div style="font-size:16px;font-weight:600;color:#212121;margin-bottom:6px">Product Not Found</div>'
        + '<div style="font-size:13px;margin-bottom:20px">The product you\'re looking for is unavailable or may have been removed.</div>'
        + '<a href="index.php" style="background:#2874f0;color:#fff;padding:10px 24px;border-radius:3px;font-size:14px;font-weight:600;text-decoration:none;display:inline-block">Go to Home</a>';
      // Insert after header
      const header = document.querySelector('.header');
      if (header && header.nextSibling) {
        header.parentNode.insertBefore(wrap, header.nextSibling);
      } else {
        document.body.appendChild(wrap);
      }
    }
    return;
  }

  // Bug 1 fix: remove "Product Not Found" error div if it was shown during early load
  const _existingErr = document.getElementById('productErrorMsg');
  if (_existingErr) _existingErr.remove();

  document.title = p.name.substring(0,50) + ' – Flipkart';
  document.getElementById('piBrand').textContent = p.brand || '';
  document.getElementById('piName').textContent = p.name;

  const r = parseFloat(p.rating);
  const pill = document.getElementById('rPill');
  pill.className = 'r-pill' + (r < 3 ? ' low' : r < 4 ? ' med' : '');
  pill.innerHTML = '<svg width="10" height="10" viewBox="0 0 12 12" fill="white" style="margin-bottom:1px"><path d="M6 1l1.5 3 3.3.5-2.4 2.3.57 3.3L6 8.75 3.03 10.1l.57-3.3L1.2 4.5 4.5 4 6 1z"/></svg> ' + p.rating;
  document.getElementById('rCount').textContent = p.rCount;
  document.getElementById('rtNum').textContent = p.rating;
  document.getElementById('rtTotal').textContent = (p.rCount||'').split('&')[0].trim();

  document.getElementById('piPrice').textContent = p.price;
  // Update bottom bar BUY NOW button — "Buy at ₹X" as main label
  const rawPrice = parseInt((p.price||'0').replace(/[₹,]/g,'')) || 0;
  const bnLabel = document.getElementById('buyNowLabel');
  const bnPrice = document.getElementById('buyNowPrice');
  if (rawPrice > 0) {
    if (bnLabel) bnLabel.textContent = 'Buy at \u20B9' + rawPrice.toLocaleString('en-IN');
    if (bnPrice) bnPrice.textContent = '';
  } else {
    if (bnLabel) bnLabel.textContent = 'Buy Now';
    if (bnPrice) bnPrice.textContent = '';
  }
  // Update EMI monthly — only show for products above ₹999
  const emiBtn = document.getElementById('emiBtn');
  const emiEl = document.getElementById('emiMonthly');
  const emiInfoLine = document.getElementById('emiInfoLine');
  const emiInfoAmt = document.getElementById('emiInfoAmt');
  if (rawPrice > 999) {
    const emiAmount = Math.round(rawPrice / 12);
    const emiStr = '\u20B9' + emiAmount.toLocaleString('en-IN');
    if (emiEl) emiEl.textContent = 'From ' + emiStr + '/m';
    if (emiInfoAmt) emiInfoAmt.textContent = emiStr;
    if (emiBtn) emiBtn.style.display = '';
    if (emiInfoLine) emiInfoLine.style.display = 'flex';
  } else {
    if (emiBtn) emiBtn.style.display = 'none';
    if (emiInfoLine) emiInfoLine.style.display = 'none';
  }
  document.getElementById('piMrp').textContent = p.mrp;
  document.getElementById('piOff').textContent = p.off;

  // ── VARIANT RENDERING ────────────────────────────────────────
  // COLOR variants
  const colorSec = document.getElementById('colorSection');
  const colorRow = document.getElementById('colorRow');
  colorRow.innerHTML = '';
  if (p.colors && p.colors.length > 1) {
    colorSec.style.display = '';
    document.getElementById('selectedColorName').textContent = p.colors[0].name || p.colors[0];
    p.colors.forEach(function(c, ci) {
      const chip = document.createElement('div');
      chip.className = 'color-chip' + (ci === 0 ? ' active' : '');
      chip.title = c.name || c;
      if (c.img) {
        const img = document.createElement('img');
        img.src = c.img; img.alt = c.name || c;
        chip.appendChild(img);
      } else {
        chip.style.background = c.hex || '#ccc';
      }
      chip.onclick = function() {
        colorRow.querySelectorAll('.color-chip').forEach(function(x){ x.classList.remove('active'); });
        chip.classList.add('active');
        document.getElementById('selectedColorName').textContent = c.name || c;
      };
      colorRow.appendChild(chip);
    });
  } else { colorSec.style.display = 'none'; }

  // STORAGE/RAM variants
  const storageSec = document.getElementById('storageSection');
  const storageRow = document.getElementById('storageRow');
  storageRow.innerHTML = '';
  if (p.variants && p.variants.length > 1) {
    storageSec.style.display = '';
    document.getElementById('selectedVariantName').textContent = p.variants[0].label || p.variants[0];
    p.variants.forEach(function(v, vi) {
      const chip = document.createElement('div');
      chip.className = 'storage-chip' + (vi === 0 ? ' active' : '');
      if (v.stock && v.stock < 5) chip.classList.add('low-stock');
      const lbl = v.label || v;
      const off = v.off || '';
      const mrp = v.mrp || '';
      const price = v.price || '';
      chip.innerHTML = (off ? '<span class="s-off">↓' + off + '</span>' : '') +
        (mrp ? '<span class="s-mrp">₹' + mrp + '</span>' : '') +
        '<span class="s-price">' + (price ? '₹' + price : lbl) + '</span>' +
        (price ? '<span>' + lbl + '</span>' : '') +
        (v.stock && v.stock < 5 ? '<span class="s-left">Only ' + v.stock + ' left</span>' : '');
      chip.onclick = function() {
        storageRow.querySelectorAll('.storage-chip').forEach(function(x){ x.classList.remove('active'); });
        chip.classList.add('active');
        document.getElementById('selectedVariantName').textContent = lbl;
        if (v.price) {
          document.getElementById('piPrice').textContent = '₹' + v.price;
          localStorage.setItem('pay_price', v.price);
        }
      };
      storageRow.appendChild(chip);
    });
  } else { storageSec.style.display = 'none'; }
  // ── END VARIANT RENDERING ─────────────────────────────────────

  // Badge
  const bw = document.getElementById('piBadgeWrap');
  if (p.badge) {
    const labels = {
      trending: '<svg width="12" height="12" viewBox="0 0 24 24" fill="#ff6b35" style="vertical-align:-1px"><path d="M13.5 0.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/></svg> Trending',
      bestseller: '<svg width="12" height="12" viewBox="0 0 24 24" fill="#f0a500" style="vertical-align:-1px"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg> Best Seller',
      hotdeal: '<svg width="12" height="12" viewBox="0 0 24 24" fill="#ff3f6c" style="vertical-align:-1px"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58s1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41s-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg> Hot Deal'
    };
    bw.innerHTML = `<span class="pi-badge ${p.badge}">${labels[p.badge]||p.badge}</span>`;
  } else { bw.innerHTML = ''; }

  // Stock
  const se = document.getElementById('piStock');
  if (p.stock > 0 && p.stock < 50) {
    se.textContent = p.stock < 10 ? `Hurry! Only ${p.stock} left` : `Only ${p.stock} left in stock`;
  } else { se.textContent = ''; }

  // ── SWIPEABLE IMAGE SLIDER ──────────────────────────────────
  const slider   = document.getElementById('imgSlider');
  const strip    = document.getElementById('thumbStrip');
  strip.innerHTML = '';
  slider.innerHTML = '';

  let slideIdx = 0;
  const _pidNum  = id.replace(/^p/i, '');
  const _baseDir = 'Images/TopPicksForYou/' + _pidNum + '/';
  const _runtimeImages = getProductImageList(id, p);

  function _buildSliderAndThumbs(imgBases) {
    var skelEl = document.getElementById('imgSkeleton');
    imgBases.forEach(function(base, i) {
      const slide = document.createElement('div');
      slide.className = 'img-slide';
      const img = document.createElement('img');
      img.className = 'main-img lazy-fade'; img.alt = 'Product image ' + (i+1);
      img.onload = function(){ img.classList.add('loaded'); if(i===0 && skelEl){ skelEl.style.display='none'; } };
      img.onerror = function(){ img.onerror=null; slide.remove(); syncSlider(); if(i===0 && skelEl){ skelEl.style.display='none'; } };
      img.src = base;
      slide.appendChild(img);
      slider.appendChild(slide);
    });

    imgBases.forEach(function(base, i) {
      const t = document.createElement('img');
      t.className = 'thumb' + (i===0?' active':'');
      t.alt = ''; t.loading = 'lazy';
      t.onerror = function(){ t.onerror=null; t.remove(); };
      t.src = base;
      t.onclick = function(){ goSlide(i); };
      strip.appendChild(t);
    });
  }

  // Build image slider from the currently selected product, not from the initial PHP-rendered product
  (function() {
    var imgBases = (_runtimeImages && _runtimeImages.length)
                 ? _runtimeImages
                 : Array.from({length:10}, function(_,n){ return _baseDir + (n + 1) + '.jpg'; });
    p._probedBases = imgBases.slice();
    _buildSliderAndThumbs(imgBases);
  })();

  function syncSlider() {
    const slides = slider.querySelectorAll('.img-slide');
    const count  = slides.length;
    if (!count) return;
    slideIdx = Math.max(0, Math.min(slideIdx, count - 1));
    slider.style.transform = 'translateX(-' + (slideIdx * 100) + '%)';
    // Sync thumb active state
    strip.querySelectorAll('.thumb').forEach(function(th, ti) { th.classList.toggle('active', ti === slideIdx); });
  }

  function goSlide(n) {
    const count = slider.querySelectorAll('.img-slide').length;
    slideIdx = ((n % count) + count) % count;
    syncSlider();
  }
  // Touch / swipe
  (function() {
    let startX = 0, startY = 0, dragging = false, dx = 0;
    const wrap = document.getElementById('mainImgWrap');

    wrap.ontouchstart = function(e) {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
      dragging = true; dx = 0;
      slider.style.transition = 'none';
    };

    wrap.ontouchmove = function(e) {
      if (!dragging) return;
      dx = e.touches[0].clientX - startX;
      const dy = e.touches[0].clientY - startY;
      if (Math.abs(dy) > Math.abs(dx) + 5) { dragging = false; slider.style.transition = ''; return; }
      slider.style.transform = 'translateX(calc(-' + (slideIdx * 100) + '% + ' + dx + 'px))';
    };

    wrap.ontouchend = function() {
      if (!dragging) return;
      dragging = false;
      const count = slider.querySelectorAll('.img-slide').length;
      slider.style.transition = 'transform .28s cubic-bezier(.4,0,.2,1)';
      if (dx < -40 && slideIdx < count - 1) slideIdx++;
      else if (dx > 40 && slideIdx > 0)  slideIdx--;
      syncSlider();
    };

    wrap.onclick = function(e) {
      if (e.target.closest('.wish-btn')) return;
      openLB(slideIdx);
    };
  })();

  // Make thumb clicks also update slider index correctly after failed images removed
  // (re-bind after a tick so removed slides are gone)
  setTimeout(function() {
    const thumbs = Array.from(strip.querySelectorAll('.thumb'));
    thumbs.forEach(function(th, ti) { th.onclick = function() { goSlide(ti); }; });
    syncSlider();
  }, 300);


  // Expose goSlide globally for gallery click
  window._goSlide = goSlide;

  // Gallery — uses probed image list, built after probe resolves
  const galSec = document.getElementById('gallerySec');
  const galGrid = document.getElementById('galleryGrid');
  galGrid.innerHTML = ''; galSec.style.display='none';

  function _buildGallery(allBases) {
    const galBases = allBases.slice(1);
    if (!galBases.length) return;
    galSec.style.display = '';
    galBases.forEach(function(base, idx) {
      const item = document.createElement('div');
      item.className = 'gallery-item';
      const gi = document.createElement('img');
      gi.loading='lazy'; gi.alt='';
      function rm(){item.remove();if(!galGrid.querySelector('.gallery-item'))galSec.style.display='none';}
      smartSrc(gi, base, null, rm);
      item.onclick=function(){
        if(typeof window._goSlide==='function') window._goSlide(idx+1);
        openLB(idx+1);
      };
      item.appendChild(gi); galGrid.appendChild(item);
    });
  }

  // If probe already finished (fast server), build now — else wait for it
  if (p._probedBases) {
    _buildGallery(p._probedBases);
  } else {
    // Poll until probe resolves (probe sets p._probedBases when done)
    (function waitForProbe() {
      if (p._probedBases) { _buildGallery(p._probedBases); }
      else { setTimeout(waitForProbe, 100); }
    })();
  }

  // Highlights
  const hlBox = document.getElementById('hlBox');
  const _hlIcons = {
    'USB': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:4px;opacity:.8"><path d="M12 3v10" stroke="#555" stroke-width="1.8" stroke-linecap="round"/><path d="M9 6l3-3 3 3" stroke="#555" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="17" r="2" stroke="#555" stroke-width="1.8"/><circle cx="15" cy="17" r="2" stroke="#555" stroke-width="1.8"/></svg>',
    'Cable': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:4px;opacity:.8"><path d="M8 7v4a4 4 0 1 0 8 0V7" stroke="#555" stroke-width="1.8" stroke-linecap="round"/><path d="M7 4h2v3H7zM15 4h2v3h-2z" fill="#555"/></svg>',
    'Charging': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:4px;opacity:.8"><rect x="4" y="7" width="14" height="10" rx="2" stroke="#555" stroke-width="1.8"/><path d="M18 10h2v4h-2" stroke="#555" stroke-width="1.8"/><path d="M10 9l-2 3h2l-1 3 4-5h-2l1-1z" fill="#555"/></svg>',
    'Battery': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:4px;opacity:.8"><rect x="3" y="7" width="16" height="10" rx="2" stroke="#555" stroke-width="1.8"/><path d="M19 10h2v4h-2" stroke="#555" stroke-width="1.8"/><rect x="5.5" y="9.5" width="8" height="5" rx="1" fill="#555"/></svg>',
    'Li-Ion': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:4px;opacity:.8"><path d="M12 3l6 3v5c0 4-2.5 7.4-6 8-3.5-.6-6-4-6-8V6l6-3z" stroke="#555" stroke-width="1.8"/><path d="M12 7l-2 4h2l-1 4 3-5h-2l1-3z" fill="#555"/></svg>',
    'In The Box': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;margin-right:4px;opacity:.8"><path d="M4 8l8-4 8 4-8 4-8-4z" stroke="#555" stroke-width="1.8" stroke-linejoin="round"/><path d="M4 8v8l8 4 8-4V8" stroke="#555" stroke-width="1.8" stroke-linejoin="round"/></svg>'
  };
  function _hlIcon(k) {
    for (const kw in _hlIcons) { if (k.includes(kw)) return _hlIcons[kw]; }
    return '';
  }
  // esc() defined in shared.js as FK.escapeHTML - fallback inline
  const _esc = (window.FK && FK.escapeHTML) ? FK.escapeHTML : function(s){ const d=document.createElement('div'); d.textContent=String(s==null?'':s); return d.innerHTML; };
  hlBox.innerHTML = (p.highlights||[]).map(function(h){
    const k=Array.isArray(h)?h[0]:h.key, v=Array.isArray(h)?h[1]:h.value;
    return `<div class="hl-row"><span class="hl-k">${_esc(k)}</span><span class="hl-v">${_hlIcon(k)}${_esc(v)}</span></div>`;
  }).join('');

  // Seller
  const sellers = ['RetailNet','TechZone','QuickShop','MegaDeal','FlashBuy'];
  document.getElementById('sellerName').textContent = sellers[parseInt(id.replace('p',''))%5];

  // Description
  descOpen = false;
  const db = document.getElementById('descBox');
  db.textContent = p.desc||'';
  db.className = 'desc-text';
  document.getElementById('descMore').textContent = 'Read More ▼';

  // Wishlist state for this product
  try {
    const wl = JSON.parse(localStorage.getItem('fk_wishlist')||'[]');
    wished = wl.some(function(entry){ return (typeof entry === 'string' ? entry : entry && entry.id) === id; });
  } catch(e){ wished=false; }
  const wb = document.getElementById('wishBtn');
  wb.className = 'wish-btn' + (wished?' active':'');

  // ── SIMILAR PRODUCTS ────────────────────────────────────────
  buildHScroll('simScroll', Object.keys(products).filter(k=>k!==id).sort(()=>(Math.sin(parseInt(id.replace('p',''))*0.7+Math.random()*0.01)-0.5)).slice(0,14), true);

  // ── TRENDING NOW ────────────────────────────────────────────
  const trendKeys = Object.keys(products).filter(k=>k!==id).sort((a,b)=>parseInt(products[b].off)-parseInt(products[a].off)).slice(0,12);
  buildHScroll('trendScroll', trendKeys, true, ['HOT','NEW','DEAL']);

  // ── YOU MAY ALSO LIKE ───────────────────────────────────────
  const ymKeys = Object.keys(products).filter(k=>k!==id).sort(()=>Math.random()-0.5).slice(0,10);
  buildHScroll('ymlikeScroll', ymKeys, false);

  // ── FREQUENTLY BOUGHT TOGETHER ──────────────────────────────
  const idN = parseInt(id.replace('p',''), 10) || 1;
  const candidateKeys = [id, `p${(idN%99)+1}`, `p${((idN+1)%99)+1}`, `p${((idN+2)%99)+1}`, `p${((idN+3)%99)+1}`];
  const fbtKeys = [];
  candidateKeys.forEach(function(k){ if (products[k] && !fbtKeys.includes(k) && fbtKeys.length < 3) fbtKeys.push(k); });
  if (fbtKeys.length < 3) {
    Object.keys(products).forEach(function(k){ if (k !== id && !fbtKeys.includes(k) && fbtKeys.length < 3) fbtKeys.push(k); });
  }
  window._currentFBTKeys = fbtKeys.slice();
  const fw = document.getElementById('fbtWrap');
  fw.innerHTML = '';
  let ftotal=0, fmrp=0;
  fbtKeys.forEach(function(k,i){
    const fp=products[k]; if(!fp) return;
    const pn=parseInt((fp.price||'0').replace(/[₹,]/g,''), 10) || 0;
    const mn=parseInt((fp.mrp||fp.price||'0').replace(/[₹,]/g,''), 10) || pn;
    ftotal+=pn; fmrp+=mn;
    if(i>0){const pl=document.createElement('div');pl.className='fbt-plus';pl.textContent='+';fw.appendChild(pl);}
    const fi=document.createElement('div'); fi.className='fbt-item';
    fi.onclick=function(){load(k);window.scrollTo(0,0);history.pushState(null,'',`?id=${k}`);};
    const img=document.createElement('img'); img.className='fbt-img'; img.alt=fp.name;
    smartSrc(img, (Array.isArray(fp.images) && fp.images[0]) ? fp.images[0] : ('Images/TopPicksForYou/' + String(k).replace(/^p/i,'') + '/1.avif'), FB_SVG);
    const nameEl = document.createElement('div'); nameEl.className='fbt-iname'; nameEl.textContent = fp.name;
    const priceEl = document.createElement('div'); priceEl.className='fbt-iprice'; priceEl.textContent = fp.price;
    fi.appendChild(img);
    fi.appendChild(nameEl);
    fi.appendChild(priceEl);
    fw.appendChild(fi);
  });
  document.getElementById('fbtTotal').textContent='₹'+ftotal.toLocaleString('en-IN');
  document.getElementById('fbtSave').textContent='You save ₹'+Math.max(0,(fmrp-ftotal)).toLocaleString('en-IN');

  // ── TOP DEALS ───────────────────────────────────────────────
  const ds=document.getElementById('dealsScroll'); ds.innerHTML='';
  ['p27','p43','p58','p15','p33'].filter(k=>products[k]).forEach(function(k){
    const dp=products[k];
    const dc=document.createElement('div'); dc.className='deal-card';
    dc.onclick=function(){load(k);window.scrollTo(0,0);history.pushState(null,'',`?id=${k}`);};
    const rib=document.createElement('span'); rib.className='deal-ribbon'; rib.textContent="TODAY'S DEAL";
    const di=document.createElement('img'); di.className='deal-img'; di.alt=dp.name;
    smartSrc(di,dp.images[0],FB_SVG);
    const db2=document.createElement('div'); db2.className='deal-body';
    db2.innerHTML=`<div class="deal-name">${dp.name}</div><div class="deal-price">${dp.price}</div><span class="deal-off">${dp.off}</span>`;
    dc.appendChild(rib); dc.appendChild(di); dc.appendChild(db2);
    ds.appendChild(dc);
  });

  // ── SUGGESTED FOR YOU ───────────────────────────────────────
  const sfyEl=document.getElementById('sfyList'); sfyEl.innerHTML='';
  ['p9','p14','p36','p43','p60','p58'].filter(k=>k!==id&&products[k]).forEach(function(k){
    const yp=products[k];
    const yc=document.createElement('div'); yc.className='sfy-card';
    yc.onclick=function(){load(k);window.scrollTo(0,0);history.pushState(null,'',`?id=${k}`);};
    const yi=document.createElement('img'); yi.className='sfy-img'; yi.alt=yp.name;
    smartSrc(yi,yp.images[0],FB_SVG);
    const ym=document.createElement('div'); ym.className='sfy-info';
    ym.innerHTML=`<div class="sfy-name">${yp.name}</div><div class="sfy-rrow"><div class="sfy-rp">${yp.rating}★</div></div><div class="sfy-price">${yp.price}<span class="sfy-off"> ${yp.off}</span><span class="sfy-mrp"> ${yp.mrp}</span></div>`;
    const ya=document.createElement('div'); ya.className='sfy-arrow'; ya.textContent='›';
    yc.appendChild(yi); yc.appendChild(ym); yc.appendChild(ya);
    sfyEl.appendChild(yc);
  });

  // ── SPONSORED ───────────────────────────────────────────────
  const sg=document.getElementById('sponsoredGrid'); sg.innerHTML='';
  ['p55','p62','p68'].filter(k=>k!==id&&products[k]).forEach(function(k){
    const sp=products[k];
    const sc=document.createElement('div'); sc.className='spon-card';
    sc.onclick=function(){load(k);window.scrollTo(0,0);history.pushState(null,'',`?id=${k}`);};
    sc.innerHTML=`<div class="spon-img-wrap"><img class="spon-img" alt="${sp.name}"></div><div class="spon-info"><div class="spon-brand">${sp.brand}</div><div class="spon-name">${sp.name}</div><div class="spon-price">${sp.price}</div><div class="spon-off">${sp.off}</div><div class="spon-ad">Sponsored</div></div>`;
    smartSrc(sc.querySelector('.spon-img'),sp.images[0],FB_SVG);
    sg.appendChild(sc);
  });

  window.scrollTo(0,0);
}

// ── HORIZONTAL SCROLL BUILDER ──────────────────────────────────
function buildHScroll(containerId, keys, showBadge, badges) {
  const c = document.getElementById(containerId);
  if (!c) return;
  c.innerHTML = '';
  const badgeList = badges || ['HOT','NEW','DEAL','HOT','NEW'];
  const badgeCls  = ['hot','new','deal','hot','new'];
  keys.forEach(function(k, i) {
    const sp = products[k]; if(!sp) return;
    const rc = Math.floor(500 + (parseInt(k.replace('p',''))*37)%4000);
    const card = document.createElement('div');
    card.className = 'p-card';
    card.onclick = function(){load(k);window.scrollTo(0,0);history.pushState(null,'',`?id=${k}`);};
    const img = document.createElement('img');
    img.className = 'p-card-img'; img.alt = sp.name;
    smartSrc(img, sp.images[0], FB_SVG);
    card.appendChild(img);
    if (showBadge && badges) {
      const b=document.createElement('span');
      b.className='p-card-badge '+badgeCls[i%badgeCls.length];
      b.textContent=badgeList[i%badgeList.length];
      card.appendChild(b);
    }
    const meta=document.createElement('div');
    meta.innerHTML=`<div class="p-card-rrow"><div class="p-card-rp"><svg width="9" height="9" viewBox="0 0 12 12" fill="white"><path d="M6 1l1.5 3 3.3.5-2.4 2.3.57 3.3L6 8.75 3.03 10.1l.57-3.3L1.2 4.5 4.5 4 6 1z"/></svg>${sp.rating}</div><div class="p-card-rc">(${rc.toLocaleString('en-IN')})</div></div><div class="p-card-name">${sp.name}</div><div class="p-card-price">${sp.price}</div><div class="p-card-off">${sp.off}</div>`;
    card.appendChild(meta);
    c.appendChild(card);
  });
}

// ── WISHLIST — real Flipkart behaviour ─────────────────────────
function toggleWish() {
  const p = products[currentId]; if (!p) return;
  const wb = document.getElementById('wishBtn');
  const item = { id: currentId, name: p.name, brand: p.brand || '', price: parseInt((p.price || '0').replace(/[₹,]/g,''), 10) || 0, mrp: parseInt((p.mrp || p.price || '0').replace(/[₹,]/g,''), 10) || 0, img: (p.images && p.images[0]) || '', added: Date.now() };
  const applyState = function(active){
    wished = !!active;
    wb.className = 'wish-btn' + (wished ? ' active' : '');
    wb.style.transform = 'scale(1.3)';
    setTimeout(function(){ wb.style.transform='scale(1)'; }, 200);
    showToast(wished ? '♥ Saved to Wishlist' : 'Removed from Wishlist');
  };
  if (window.FK && FK.toggleWishlist) {
    FK.toggleWishlist(item).then(applyState);
    return;
  }
  wished = !wished;
  try {
    let wl = JSON.parse(localStorage.getItem('fk_wishlist')||'[]');
    if (wished) {
      if(!wl.some(function(entry){ return (typeof entry === 'string' ? entry : entry && entry.id) === currentId; })) wl.push(item);
    } else {
      wl = wl.filter(function(entry){ return (typeof entry === 'string' ? entry : entry && entry.id) !== currentId; });
    }
    localStorage.setItem('fk_wishlist', JSON.stringify(wl));
  } catch(e){}
  applyState(wished);
}

// ── GALLERY / LIGHTBOX ─────────────────────────────────────────
let _lbImgs=[], _lbIdx=0;

function openLB(idx) {
  const slides = document.querySelectorAll('#imgSlider .img-slide img');
  _lbImgs = Array.from(slides).map(i => i.src).filter(Boolean);
  if (!_lbImgs.length) return;
  _lbIdx = Math.max(0, Math.min(idx, _lbImgs.length - 1));
  const lbSlider = document.getElementById('lbSlider');
  lbSlider.innerHTML = '';
  _lbImgs.forEach(function(src) {
    const slide = document.createElement('div');
    slide.className = 'lb-slide';
    const img = document.createElement('img');
    img.className = 'lb-img'; img.src = src; img.alt = '';
    slide.appendChild(img);
    lbSlider.appendChild(slide);
  });
  document.getElementById('lightbox').classList.add('open');
  document.body.style.overflow = 'hidden';
  _lbRender(false);
  _lbTouch(document.getElementById('lbSliderWrap'));
}

function closeLB() {
  document.getElementById('lightbox').classList.remove('open');
  document.body.style.overflow = '';
}

function lbNav(d) {
  _lbIdx = ((_lbIdx + d) + _lbImgs.length) % _lbImgs.length;
  _lbRender();
}

function _lbRender(animate) {
  const lbSlider = document.getElementById('lbSlider');
  lbSlider.style.transition = animate === false ? 'none' : 'transform .28s cubic-bezier(.4,0,.2,1)';
  lbSlider.style.transform = 'translateX(-' + (_lbIdx * 100) + '%)';
  document.getElementById('lbCounter').textContent = (_lbIdx + 1) + ' / ' + _lbImgs.length;
}

function _lbTouch(wrap) {
  let sx = 0, dragging = false, dx = 0;
  wrap.ontouchstart = function(e) {
    sx = e.touches[0].clientX; dragging = true; dx = 0;
    document.getElementById('lbSlider').style.transition = 'none';
  };
  wrap.ontouchmove = function(e) {
    if (!dragging) return;
    dx = e.touches[0].clientX - sx;
    document.getElementById('lbSlider').style.transform =
      'translateX(calc(-' + (_lbIdx * 100) + '% + ' + dx + 'px))';
  };
  wrap.ontouchend = function() {
    if (!dragging) return; dragging = false;
    if (dx < -40 && _lbIdx < _lbImgs.length - 1) _lbIdx++;
    else if (dx > 40 && _lbIdx > 0) _lbIdx--;
    _lbRender();
  };
}

// ── DESCRIPTION ────────────────────────────────────────────────
function toggleDesc() {
  descOpen = !descOpen;
  document.getElementById('descBox').className = 'desc-text'+(descOpen?' open':'');
  document.getElementById('descMore').textContent = descOpen ? 'Read Less ▲' : 'Read More ▼';
}

// ── PINCODE ────────────────────────────────────────────────────
async function checkPin() {
  const v = document.getElementById('pinInput').value.trim();
  if (v.length!==6 || !/^\d{6}$/.test(v)) { showToast('Please enter a valid 6-digit pincode'); return; }
  showToast('Checking...');
  try {
    const r = await fetch('https://api.postalpincode.in/pincode/'+v, {signal:AbortSignal.timeout(4000)});
    const d = await r.json();
    if (d&&d[0]&&d[0].Status==='Success'&&d[0].PostOffice&&d[0].PostOffice.length) {
      const po=d[0].PostOffice[0];
      showToast('✓ Free delivery to '+(po.District||po.Name)+', '+(po.State||'')+' by Tomorrow!');
      return;
    }
  } catch(e){}
  showToast('✓ Delivery available for this pincode');
}

// ── SHARE ──────────────────────────────────────────────────────
function shareProduct() {
  const p = products[currentId];
  if (navigator.share && p) {
    navigator.share({title:p.name, text:p.name+' — '+p.price, url:location.href}).catch(()=>{});
  } else {
    navigator.clipboard && navigator.clipboard.writeText(location.href).then(()=>showToast('Link copied!'));
  }
}

// ── CART / BUY ─────────────────────────────────────────────────
function addCart() {
  const p=products[currentId]; if(!p) return;
  const currentImages = getProductImageList(currentId, p);
  const item = {id:currentId,name:p.name,brand:p.brand,price:parseInt((p.price||'0').replace(/[₹,]/g,''),10)||0,mrp:parseInt((p.mrp||p.price||'0').replace(/[₹,]/g,''),10)||0,off:p.off,img:(currentImages[0]||''),qty:1};
  if (window.FK && FK.addToCart) {
    FK.addToCart(item).then(function(){ showToast('✓ Added to Cart!'); updateCartBadge(); });
    return;
  }
  const cart=JSON.parse(localStorage.getItem('flipkart_cart')||'[]');
  const ex=cart.find(c=>c.id===currentId);
  if(ex){ ex.qty=Math.min(10,ex.qty+1); showToast('🛒 Quantity updated!'); }
  else {
    cart.push(item);
    showToast('✓ Added to Cart!');
  }
  localStorage.setItem('flipkart_cart',JSON.stringify(cart));
  updateCartBadge();
}
function buyNow(isEmi, emiMonths) {
  isEmi = !!isEmi;
  emiMonths = parseInt(emiMonths || 0, 10) || 0;
  const p = products[currentId]; if (!p) return;
  const price = parseInt((p.price || '0').toString().replace(/[₹,]/g,''), 10) || 0;
  const mrp   = parseInt((p.mrp   || p.price || '0').toString().replace(/[₹,]/g,''), 10) || 0;
  const currentImages = getProductImageList(currentId, p);
  const img   = currentImages[0] || ('Images/TopPicksForYou/' + currentId.replace('p','') + '/1.avif');
  const item  = { id: currentId, name: p.name, brand: p.brand||'', price: price, mrp: mrp, off: p.off||'', img: img, images: currentImages.slice(), qty: 1 };

  // Set localStorage directly — no FK dependency
  const token = 'CHK' + Date.now().toString(36).toUpperCase();
  localStorage.setItem('fk_checkout_token', token);
  localStorage.setItem('pid',        currentId);
  localStorage.setItem('pay_cart',   JSON.stringify([item]));
  localStorage.setItem('pay_name',   p.name   || 'Product');
  localStorage.setItem('pay_brand',  p.brand  || '');
  localStorage.setItem('pay_price',  String(price));
  localStorage.setItem('pay_mrp',    String(mrp));
  localStorage.setItem('pay_off',    p.off    || '');
  localStorage.setItem('pay_img',    img);
  localStorage.setItem('pay_qty',    '1');
  localStorage.removeItem('pay_total');   // fresh checkout
  localStorage.removeItem('pay_delivery');
  if (isEmi && emiMonths > 0) {
    localStorage.setItem('pay_emi_enabled', '1');
    localStorage.setItem('pay_emi_plan', String(emiMonths));
    localStorage.setItem('pay_emi_monthly', String(Math.round(price / emiMonths)));
  } else {
    localStorage.removeItem('pay_emi_enabled');
    localStorage.removeItem('pay_emi_plan');
    localStorage.removeItem('pay_emi_monthly');
  }

  // Also call FK if available for compatibility
  if (window.FK && FK.prepareCheckout) FK.prepareCheckout([item], { pid: currentId, name: p.name, brand: p.brand||'', off: p.off||'', img: img });

  showToast(isEmi && emiMonths ? ('Redirecting to ' + emiMonths + '-month EMI checkout...') : 'Redirecting...');
  setTimeout(function(){ window.location.href = 'address.php'; }, 500);
}
function addFBT() {
  const keys = Array.isArray(window._currentFBTKeys) ? window._currentFBTKeys.slice(0,3) : [];
  const items = keys.map(function(k){
    const fp = products[k];
    if (!fp) return null;
    const imgs = getProductImageList(k, fp);
    return {
      id:k,
      name:fp.name,
      brand:fp.brand||'',
      price:parseInt((fp.price||'0').replace(/[₹,]/g,''),10)||0,
      mrp:parseInt((fp.mrp||fp.price||'0').replace(/[₹,]/g,''),10)||0,
      off:fp.off||'',
      img:(imgs[0]||''),
      qty:1
    };
  }).filter(Boolean);
  if (!items.length) { showToast('No bundle items found'); return; }
  if (window.FK && FK.addToCart) {
    Promise.all(items.map(function(item){ return FK.addToCart(item); })).then(function(){
      updateCartBadge();
      showToast('✓ Added bundle to Cart');
    });
    return;
  }
  var cart = normalizeCartForBadge(JSON.parse(localStorage.getItem('flipkart_cart')||'[]'));
  items.forEach(function(item){
    var ex = cart.find(function(entry){ return entry.id === item.id; });
    if (ex) ex.qty = Math.max(1, Math.min(10, (parseInt(ex.qty,10)||1) + 1));
    else cart.push(item);
  });
  localStorage.setItem('flipkart_cart', JSON.stringify(cart));
  updateCartBadge();
  showToast('✓ Added bundle to Cart');
}

// ── CATEGORY ───────────────────────────────────────────────────
function goCategory() {
  const catMap = {
    earbuds:[1,2,6,9,10,25,27,28,32,36,45,59,66,72,80,82,85,89,90,97,98,99],
    smartphones:[5,23,48,63,76,79,87],tablets:[17,20,24,44,47,53,67],
    speakers:[8,22,30,65],smartwatches:[12,15,26,34],trimmers:[55,56],
    tvs:[16,18,42],chairs:[13,69,81],health:[4,11,35,49,84,88,92,93,95],
  };
  const num=parseInt((currentId||'p1').replace('p',''));
  let cat='all';
  for(const[k,ids] of Object.entries(catMap)){if(ids.includes(num)){cat=k;break;}}
  window.location.href='search.php?q='+cat;
}

// ── CART BADGE ─────────────────────────────────────────────────
function repairCartPrices() {
  // Bug 2 fix: items added before FK_PRODUCTS loaded end up with price=0
  // and are then filtered out by normalizeCartForBadge / FK.normalizeCartItems.
  // Once products are available, patch any zero-price entries with real prices.
  try {
    var rawCart = JSON.parse(localStorage.getItem('flipkart_cart') || '[]');
    if (!Array.isArray(rawCart) || !rawCart.length) return;
    var changed = false;
    rawCart = rawCart.map(function(item) {
      if (!item || !item.id) return item;
      if ((parseFloat(item.price || 0) || 0) > 0) return item; // already has price
      var p = products[item.id];
      if (!p) return item;
      var fixedPrice = parseInt((p.price || '0').replace(/[₹,]/g, ''), 10) || 0;
      if (fixedPrice <= 0) return item;
      changed = true;
      return Object.assign({}, item, {
        price: fixedPrice,
        mrp: parseInt((p.mrp || p.price || '0').replace(/[₹,]/g, ''), 10) || fixedPrice,
        off: item.off || p.off || '',
        name: item.name || p.name || '',
        brand: item.brand || p.brand || ''
      });
    });
    if (changed) localStorage.setItem('flipkart_cart', JSON.stringify(rawCart));
  } catch(e) {}
}
function updateCartBadge() {
  try {
    const cart = (window.FK && FK.getCart) ? FK.getCart() : normalizeCartForBadge(JSON.parse(localStorage.getItem('flipkart_cart')||'[]'));
    const total = (Array.isArray(cart) ? cart : []).reduce(function(sum, item){
      return sum + Math.max(1, Math.min(10, parseInt(item && item.qty, 10) || 1));
    }, 0);
    const b=document.getElementById('cartBadge');
    if(b){b.textContent=total>99?'99+':String(total||'');b.style.display=total>0?'flex':'none';}
  } catch(e){}
}
function normalizeCartForBadge(items){
  return (Array.isArray(items)?items:[]).map(function(item){
    if(!item || !item.id) return null;
    var price = parseFloat(item.price||0)||0;
    return Object.assign({}, item, { qty: Math.max(1, Math.min(10, parseInt(item.qty||1,10)||1)) });
  }).filter(Boolean);
}

// ── TOAST ──────────────────────────────────────────────────────
function showToast(msg) {
  const t=document.getElementById('toast');
  t.textContent=msg; t.classList.add('show');
  clearTimeout(t._t); t._t=setTimeout(()=>t.classList.remove('show'),2500);
}

// ── KEYBOARD ──────────────────────────────────────────────────
document.addEventListener('keydown',e=>{
  const lb=document.getElementById('lightbox');
  if(!lb.classList.contains('open')) return;
  if(e.key==='ArrowLeft') lbNav(-1);
  if(e.key==='ArrowRight') lbNav(1);
  if(e.key==='Escape') lb.classList.remove('open');
});

// ── INIT ──────────────────────────────────────────────────────
document.addEventListener('fk:ready', function() {
  Object.assign(products, window.FK_PRODUCTS||{});
  repairCartPrices(); // Bug 2 fix: patch zero-price cart items now that products are loaded
  load(currentId); updateCartBadge();
});
if (Object.keys(window.FK_PRODUCTS||{}).length) {
  Object.assign(products, window.FK_PRODUCTS);
  repairCartPrices(); // Bug 2 fix: same for synchronous early-load path
  load(currentId); updateCartBadge();
}
window.addEventListener('storage', updateCartBadge);
document.addEventListener('fk:cart-sync', updateCartBadge);
document.addEventListener('fk:wishlist-sync', function(){ load(currentId); });

const _emiModalEl = document.getElementById('emiModal');
if (_emiModalEl) {
  _emiModalEl.addEventListener('click', function(e){
    if (e.target === _emiModalEl) closeEmiModal();
  });
}


function showEmiInfo() {
  const price = parseFloat((document.getElementById('piPrice').textContent||'0').replace(/[₹,]/g,'')) || 0;
  if (price <= 999) { showToast('EMI not available for this product'); return; }
  const modal = document.getElementById('emiModal');
  const grid = document.getElementById('emiPlanGrid');
  const sub = document.getElementById('emiSheetSub');
  if (!modal || !grid || !sub) { showToast('EMI options unavailable'); return; }
  selectedEmiMonths = 12;
  sub.textContent = 'Select a plan for ₹' + price.toLocaleString('en-IN') + '.';
  grid.innerHTML = '';
  [3,6,12].forEach(function(months){
    const amt = Math.round(price / months);
    const card = document.createElement('button');
    card.type = 'button';
    card.className = 'emi-plan' + (months === selectedEmiMonths ? ' active' : '');
    card.innerHTML = '<div class="m">' + months + ' months</div>' +
      '<div class="amt">₹' + amt.toLocaleString('en-IN') + '/m</div>' +
      '<div class="meta">Approx total ₹' + (amt * months).toLocaleString('en-IN') + '</div>';
    card.onclick = function(){
      selectedEmiMonths = months;
      grid.querySelectorAll('.emi-plan').forEach(function(el){ el.classList.remove('active'); });
      card.classList.add('active');
    };
    grid.appendChild(card);
  });
  modal.classList.add('open');
  modal.setAttribute('aria-hidden', 'false');
}
function closeEmiModal() {
  const modal = document.getElementById('emiModal');
  if (!modal) return;
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden', 'true');
}
function continueEmiCheckout() {
  closeEmiModal();
  buyNow(true, selectedEmiMonths || 12);
}

</script>
<script>
</script>
<script>
(function(){
    if (!('IntersectionObserver' in window)) return;
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if(e.isIntersecting){
                var img = e.target;
                if(img.dataset.src){ img.src = img.dataset.src; }
                img.classList.add('lazy-fade');
                img.onload = function(){ img.classList.add('loaded'); };
                io.unobserve(img);
            }
        });
    }, { rootMargin: '250px' });
    window.addEventListener('load', function(){
        document.querySelectorAll('img[loading="lazy"]').forEach(function(img){ io.observe(img); });
    });
})();
</script>

<?php $pv = @filemtime(__DIR__.'/assets/products.json') ?: '1'; ?>
<script src="assets/products-data.js?v=<?= $pv ?>" defer></script>
<script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
