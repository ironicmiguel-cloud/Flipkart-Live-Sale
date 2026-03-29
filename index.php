<?php require_once __DIR__ . '/includes/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="assets/icons/app-icon.png">
    <link rel="apple-touch-icon" href="assets/icons/app-icon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Flipkart – Online Shopping for Mobiles, Electronics & More</title>
    <meta name="description" content="Shop the best deals on mobiles, electronics, fashion, home appliances and more at Flipkart. Fast delivery, secure payments, easy returns.">
    <meta property="og:title" content="Flipkart – Shop Smart, Save More">
    <meta property="og:description" content="Best deals on mobiles, electronics, fashion and more.">
    <meta name="theme-color" content="#2874f0">
    <!-- Performance: preconnect to font origin -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Use a consistent font across pages -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans', sans-serif;
            background-color: #f5f5f5;
            padding-top: 0;
            overflow-x: hidden;
        }

        /* ── App-style top tabs (Flipkart | Travel) ── */
        .top-tabs-wrap {
            background: #e8f0fe;
            padding: 10px 10px 10px 10px;
            display: flex;
            gap: 8px;
            /* NOT fixed — scrolls away with page */
        }

        .top-tab {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 14px;
            padding: 14px 10px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .top-tab.fk-tab {
            background: #ffe500;
        }

        .top-tab.travel-tab {
            background: #ffffff;
        }

        .top-tab .tab-label {
            font-size: 16px;
            font-weight: 900;
            font-style: italic;
            color: #2874f0;
            letter-spacing: -0.3px;
        }

        .top-tab.travel-tab .tab-label {
            font-style: normal;
            color: #212121;
            font-size: 15px;
        }

        /* ── Sticky search + category wrapper ── */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #2874f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        /* Ensure body never clips sticky */
        body {
            overflow-x: hidden;
        }

        /* Search bar wrapper — white card below blue header */
        .search-bar-wrapper {
            background: #fff;
            padding: 10px 12px 8px;
        }

        .search-bar {
            background: #fff;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 30px;
            border: 1.5px solid #2874f0;
            cursor: pointer;
            box-shadow: none;
            margin: 0;
        }

        .search-bar svg {
            flex-shrink: 0;
            fill: #2874f0;
        }

        .search-bar input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 14px;
            color: #878787;
            background: white;
            padding: 2px 0;
            cursor: pointer;
            pointer-events: none;
        }

        /* category tab styles removed - replaced by category-icons-section */

        .banner {
            margin: 15px;
            border-radius: 8px;
            overflow: hidden;
        }

        .banner img {
            width: 100%;
            display: block;
        }

        .section-title {
            padding: 20px 15px 10px;
            font-size: 18px;
            font-weight: 600;
            color: #212121;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1px;
            background: #f8f8f8;
            margin-bottom: 8px;
        }

        /* ─── Product Card – Flipkart style ─────────────────── */
        /* ── Flipkart-style lightweight product card ── */
        .product-card {
            background: #fff;
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
            transition: box-shadow 0.15s;
        }
        .product-card:active { opacity: .88; }
        .product-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.12); }

        /* Image area */
        .product-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
            background: #fff;
            padding: 8px;
            display: block;
        }

        /* Info area */
        .product-info {
            padding: 6px 8px 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        /* Brand bold, name muted below */
        .product-title-line {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .product-brand {
            font-weight: 700;
            color: #212121;
            font-size: 12.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-name {
            color: #878787;
            font-size: 11.5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Price row */
        .product-price {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-top: 4px;
            flex-wrap: wrap;
        }
        .price-current {
            font-size: 14px;
            font-weight: 700;
            color: #212121;
        }
        .price-original {
            font-size: 11px;
            color: #878787;
            text-decoration: line-through;
            font-weight: 400;
        }
        .price-discount {
            font-size: 11px;
            color: #388e3c;
            font-weight: 600;
        }


        /* ── Image Wrap + Rating Badge ─────────────────────── */
        .product-image-wrap {
            position: relative;
            display: block;
            width: 100%;
            background: #fff;
        }
        .product-image-wrap .product-image {
            display: block;
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
            padding: 8px;
            background: #fff;
        }
        .rating-badge {
            position: absolute;
            bottom: 6px;
            left: 6px;
            background: #388e3c;
            border-radius: 3px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 2px;
            z-index: 10;
            pointer-events: none;
        }
        .rating-badge .r-star {
            color: #fff;
            font-size: 11px;
            line-height: 1;
        }
        .rating-badge .r-count {
            display: none;
        }

        .product-tag {
            display: inline-block;
            background: #ff5722;
            color: white;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            margin-top: 8px;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #878787;
            font-size: 11px;
            text-decoration: none;
        }

        .nav-item.active {
            color: #2874f0;
        }

        .nav-icon {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .offer-cards {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
        }

        .offer-card {
            min-width: 140px;
            max-width: 140px;
            width: 140px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
            scroll-snap-align: start;
            border: 1px solid #f0f0f0;
        }

        .offer-card img {
            width: 100%;
            /* Real Flipkart offer card image: square 1:1 */
            aspect-ratio: 1 / 1;
            height: auto;
            object-fit: contain;
            margin-bottom: 6px;
        }

        .offer-text {
            font-size: 13px;
            font-weight: 700;
            color: #212121;
            margin-bottom: 3px;
        }

        .offer-subtitle {
            font-size: 11px;
            color: #878787;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.3;
            max-height: 2.6em;
        }

        /* Sponsored Section */
        .sponsored-section {
            margin: 12px 10px;
            background: white;
            padding-bottom: 15px;
            border-radius: 12px;
            overflow: visible;
            box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        }

        .sponsored-header {
            padding: 15px 15px 10px;
            font-size: 18px;
            font-weight: 700;
            color: #212121;
        }

        .sponsored-banner {
            display: block;
            width: calc(100% - 24px);
            margin: 0 12px 15px 12px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.10);
            aspect-ratio: 820 / 360;
            background: #f8f8f8;
            box-sizing: border-box;
        }
        .sponsored-section {
            overflow: hidden !important;
        }

        .sponsored-banner img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center;
        }

        .sponsored-products {
            display: flex;
            gap: 12px;
            padding: 0 15px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
        }

        .sponsored-products::-webkit-scrollbar {
            display: none;
        }

        .sponsored-card {
            min-width: 140px;
            background: #f8f8f8;
            border-radius: 8px;
            overflow: hidden;
            scroll-snap-align: start;
            text-align: center;
        }

        .sponsored-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: white;
        }

        .sponsored-card-info {
            padding: 10px;
        }

        .sponsored-price {
            font-size: 14px;
            font-weight: 600;
            color: #212121;
            margin-bottom: 4px;
        }

        .sponsored-subtitle {
            font-size: 12px;
            color: #878787;
        }

        /* Suggested For You Section */
        .suggested-section {
            margin: 15px 0;
            background: white;
            padding-bottom: 15px;
        }

        .suggested-header {
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .suggested-title {
            font-size: 18px;
            font-weight: 700;
            color: #212121;
        }

        .view-all-btn {
            background: #2874f0;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .suggested-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 0 15px;
        }

        .suggested-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
        }

        .suggested-card img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            background: #f8f8f8;
        }

        .suggested-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .suggested-badge svg {
            width: 12px;
            height: 12px;
            fill: #388e3c;
        }

        .suggested-card-info {
            padding: 8px;
        }

        .suggested-card-name {
            font-size: 11px;
            color: #212121;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .suggested-card-price {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
        }

        .suggested-price-current {
            font-size: 14px;
            font-weight: 600;
            color: #212121;
        }

        .suggested-price-original {
            font-size: 10px;
            color: #878787;
            text-decoration: line-through;
        }

        .suggested-offer {
            font-size: 10px;
            color: #388e3c;
            font-weight: 600;
        }

        .hot-deal-badge {
            background: #ff9800;
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 3px;
            font-weight: 600;
            display: inline-block;
        }

        /* Bestseller, Trending, AD Tags */
        .tag-bestseller {
            position: absolute;
            top: 8px;
            left: 0;
            background: #2874f0;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 0 3px 3px 0;
            z-index: 2;
        }

        .tag-trending {
            position: absolute;
            top: 8px;
            left: 0;
            background: #ff6161;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 0 3px 3px 0;
            z-index: 2;
        }


        .tag-hotdeal {
            position: absolute;
            top: 8px;
            left: 0;
            background: #ff6900;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 0 3px 3px 0;
            z-index: 2;
        }
        .tag-ad {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0,0,0,0.55);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            z-index: 2;
        }

        .suggested-card {
            position: relative;
        }

        /* Mid Carousel Banner */
        .mid-carousel-container {
            margin: 10px 0;
            position: relative;
            overflow: hidden;
            background: #f8f8f8;
            border-radius: 0;
            box-shadow: none;
            width: 100%;
            box-sizing: border-box;
            /* Real Flipkart mid-banner: 2:1 ratio, edge-to-edge */
            aspect-ratio: 2 / 1;
        }

        .mid-carousel-slider {
            display: flex;
            transition: transform 0.4s ease;
            height: 100%;
            width: 100%;
            will-change: transform;
        }

        .mid-carousel-slide {
            min-width: 100%;
            width: 100%;
            flex-shrink: 0;
            height: 100%;
            overflow: hidden;
        }

        .mid-carousel-slide img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center;
            border-radius: 0;
        }

        .mid-carousel-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 14px;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .mid-carousel-arrow.left { left: 8px; }
        .mid-carousel-arrow.right { right: 8px; }

        .mid-carousel-dots {
            display: flex;
            justify-content: center;
            gap: 5px;
            padding: 6px 0;
        }

        .mid-carousel-dots .mdot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ccc;
            cursor: pointer;
            transition: all 0.3s;
        }

        .mid-carousel-dots .mdot.active {
            background: #2874f0;
            width: 16px;
            border-radius: 3px;
        }

        /* You May Also Like Section */
        .recommendation-section {
            margin: 15px 0;
            background: white;
            padding: 15px 0;
        }

        .recommendation-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px 15px;
        }

        .recommendation-title {
            font-size: 16px;
            font-weight: 700;
            color: #212121;
        }

        .see-all-badge {
            background: #e0e0e0;
            color: #212121;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
        }

        .recommendation-scroll {
            display: flex;
            gap: 12px;
            padding: 0 15px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
        }

        .recommendation-scroll::-webkit-scrollbar {
            display: none;
        }

        .recommendation-card {
            min-width: 140px;
            background: #f8f8f8;
            border-radius: 8px;
            overflow: hidden;
            scroll-snap-align: start;
        }

        .recommendation-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: white;
        }

        .recommendation-info {
            padding: 10px;
        }

        .recommendation-rating {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: #388e3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .recommendation-name {
            font-size: 12px;
            color: #212121;
            margin-bottom: 6px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 500;
        }

        .recommendation-discount {
            font-size: 11px;
            color: #388e3c;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .recommendation-price-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .recommendation-old-price {
            font-size: 11px;
            color: #878787;
            text-decoration: line-through;
        }

        .recommendation-current-price {
            font-size: 14px;
            font-weight: 700;
            color: #212121;
        }

        .recommendation-bank-offer {
            font-size: 10px;
            color: #2874f0;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Upgrade to Premium Section */
        .premium-section {
            margin: 15px 0;
            background: white;
            padding: 15px 0;
        }

        .premium-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 15px 15px;
        }

        .premium-title {
            font-size: 16px;
            font-weight: 700;
            color: #212121;
        }

        .premium-arrow {
            background: #212121;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
        }


        /* Recently Viewed – hide until populated */
        .recently-viewed-section { display: none; }

        /* Recently Viewed Section */
        .recently-viewed-section {
            margin: 15px 0;
            background: white;
            padding: 15px 0;
        }

        

        /* Star icon for ratings */
        .star-icon {
            width: 10px;
            height: 10px;
            fill: white;
        }

        /* AD Banner Section */
        /* Mid-page Ad Banner */
        .mid-ad-banner {
            position: relative;
            margin: 10px 10px;
            background: #fff;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.10);
        }
        .mid-ad-banner img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }
        .mid-ad-banner .ad-tag {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            z-index: 2;
        }

        .ad-banner-section {
            margin: 12px 10px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.10);
            width: calc(100% - 20px);
            box-sizing: border-box;
        }

        .ad-badge {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(0,0,0,0.55);
            color: white;
            padding: 3px 9px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 2;
        }

        .ad-banner-image {
            position: relative;
            width: 100%;
            /* Matched to actual image: 1080x566 ≈ 1.91:1 */
            aspect-ratio: 1080 / 566;
            overflow: hidden;
            background: #f0f0f0;
        }

        .ad-banner-image img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center;
        }

        .ad-banner-footer {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #e0e0e0;
        }

        .ad-banner-text {
            flex: 1;
            font-size: 14px;
            font-weight: 600;
            color: #212121;
        }

        .ad-banner-arrow {
            color: #2874f0;
            font-size: 24px;
            font-weight: bold;
        }

        /* Top Stories Section */
        .top-stories-section {
            background: #f8f8f8;
            padding: 20px 15px;
            margin-top: 20px;
        }

        .top-stories-header {
            font-size: 14px;
            font-weight: 600;
            color: #212121;
            margin-bottom: 12px;
        }

        .brand-links {
            font-size: 11px;
            color: #878787;
            line-height: 1.8;
        }

        .brand-links a {
            color: #878787;
            text-decoration: none;
        }

        /* Footer Section */
        .footer {
            background: #172337;
            color: #878787;
            padding: 30px 15px;
            font-size: 11px;
        }

        .footer-section {
            margin-bottom: 25px;
        }

        .footer-title {
            color: white;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-links a {
            color: #878787;
            text-decoration: none;
        }

        .footer-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .footer-divider {
            border-top: 1px solid #3e4152;
            margin: 25px 0;
        }

        .footer-company-info {
            margin-bottom: 15px;
        }

        .footer-company-info p {
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-icons a {
            color: #878787;
            font-size: 20px;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            align-items: center;
        }

        .payment-icon {
            width: 52px;
            height: 52px;
            object-fit: contain;
            background: white;
            border-radius: 10px;
            padding: 4px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #3e4152;
            font-size: 10px;
        }

        .footer-bottom-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .footer-bottom-links a {
            color: #878787;
            text-decoration: none;
        }

        /* Location Bar — white bg, real Flipkart style */
        .location-bar {
            background: #fff;
            padding: 7px 14px 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .location-pin {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .location-text {
            flex: 1;
            font-size: 12px;
            color: #212121;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 400;
        }

        .location-city {
            font-weight: 700;
            color: #212121;
            font-size: 12px;
        }

        .location-link {
            color: #2874f0;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .location-link::after {
            content: ' ›';
            font-size: 14px;
        }


        /* ── Pincode Modal ─────────────────────────────── */
        .pin-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.55); z-index: 9999;
            align-items: flex-end; justify-content: center;
        }
        .pin-overlay.open { display: flex; }
        .pin-sheet {
            background: #fff; width: 100%; max-width: 480px;
            border-radius: 18px 18px 0 0; padding: 24px 20px 36px;
            animation: slideUp .25s ease;
        }
        @keyframes slideUp { from{transform:translateY(100%)} to{transform:translateY(0)} }
        .pin-sheet h3 { font-size: 16px; font-weight: 700; color: #212121; margin-bottom: 4px; }
        .pin-sheet p  { font-size: 12px; color: #777; margin-bottom: 18px; }
        .pin-input-row {
            display: flex; gap: 10px; margin-bottom: 14px;
        }
        .pin-input-row input {
            flex: 1; padding: 13px 14px; border: 1.5px solid #e0e0e0;
            border-radius: 8px; font-size: 15px; font-weight: 600;
            outline: none; letter-spacing: 2px;
            transition: border-color .2s;
        }
        .pin-input-row input:focus { border-color: #2874f0; }
        .pin-input-row button {
            padding: 13px 20px; background: #2874f0; color: #fff;
            border: none; border-radius: 8px; font-size: 14px;
            font-weight: 700; cursor: pointer; white-space: nowrap;
        }
        .pin-input-row button:disabled { background: #90b4f5; cursor: not-allowed; }
        .pin-result {
            min-height: 44px; padding: 10px 14px;
            border-radius: 8px; font-size: 13px; font-weight: 600;
            display: none; line-height: 1.7; white-space: pre-line;
        }
        .pin-result.ok      { background: #e8f5e9; color: #2e7d32; display: block; }
        .pin-result.err     { background: #fdecea; color: #c62828; display: block; }
        .pin-result.loading { background: #e3f2fd; color: #1565c0; display: block; }
        .pin-close {
            position: absolute; top: 16px; right: 18px;
            background: none; border: none; font-size: 22px;
            cursor: pointer; color: #666; line-height: 1;
        }
        .pin-quick { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .pin-quick button {
            padding: 6px 12px; background: #f0f4ff; color: #2874f0;
            border: 1.5px solid #c5d8f8; border-radius: 20px;
            font-size: 12px; font-weight: 600; cursor: pointer;
        }
        .pin-saved-info {
            font-size: 11px; color: #888; margin-top: 12px; text-align: center;
        }

        /* Category Icons Row - Flipkart style below search bar */
        .category-icons-section {
            background: white;
            padding: 8px 0 0 0;
            border-bottom: none;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin-bottom: 0;
        }
        .category-icons-section::-webkit-scrollbar { display: none; }

        .category-icons-grid {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: 0;
            padding: 0 6px;
            width: max-content;
        }

        .category-icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #212121;
            padding: 6px 12px 8px;
            min-width: 64px;
            position: relative;
        }

        .category-icon-item.active-cat .category-icon-name {
            color: #2874f0;
            font-weight: 700;
        }
        .category-icon-item.active-cat::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0; right: 0;
            height: 2px;
            background: #2874f0;
        }

        /* Category SVG icons — no circle bg, clean on white */
        .cat-svg-icon { width: 34px; height: 34px; }
        .category-icon-wrapper .cat-svg-icon { width: 100%; height: 100%; }
        .category-icon-wrapper {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            overflow: hidden;
        }
        /* Active item gets light highlight */
        .category-icon-item.active-cat .category-icon-wrapper {
            background: #e8f0fe;
        }

        .category-icon-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .category-icon-name {
            font-size: 11px;
            text-align: center;
            font-weight: 400;
            color: #212121;
            white-space: nowrap;
            letter-spacing: 0;
            line-height: 1.3;
        }

        /* Deals of the Day Section */
        .deals-header {
            background: white;
            padding: 14px 14px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f3f6;
            margin-top: 8px;
        }
        .deals-title {
            font-size: 18px;
            font-weight: 700;
            color: #212121;
        }
        .deals-view-all {
            font-size: 13px;
            color: #2874f0;
            font-weight: 600;
            text-decoration: none;
        }
        .sale-live-btn {
            background: linear-gradient(135deg, #e53935 0%, #b71c1c 100%);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            cursor: default;
            animation: pulse-red 1.5s infinite;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            letter-spacing: .3px;
        }
        @keyframes pulse-red {
            0%   { box-shadow: 0 0 0 0 rgba(229,57,53,0.6); }
            70%  { box-shadow: 0 0 0 6px rgba(229,57,53,0); }
            100% { box-shadow: 0 0 0 0 rgba(229,57,53,0); }
        }
        .deals-products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: #e8e8e8;
            margin-bottom: 8px;
        }
        .deals-product-card {
            background: #fff;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            display: block;
            text-decoration: none;
        }
        .deals-product-card:active { opacity: .85; }
        .deals-product-card img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: contain;
            padding: 8px;
            background: #fff;
        }
        .deals-product-card .deal-discount {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.68));
            color: white;
            text-align: center;
            padding: 20px 4px 6px;
            line-height: 1.5;
        }
        .deal-pct { font-size: 13px; font-weight: 900; color: #ff6161; display: block; }
        .deal-price { font-size: 11.5px; font-weight: 700; color: #fff; display: block; }
        /* ========================================= */
/* SLIDING BANNER CAROUSEL */
/* ========================================= */

.banner-container {
    position: relative;
    margin: 8px 10px 4px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.10);
    width: calc(100% - 20px);
    max-width: calc(100% - 20px);
    box-sizing: border-box;
    /* Real Flipkart banner ratio ~820:360 */
    aspect-ratio: 820 / 360;
    background: #f0f4ff;
}

.banner-slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
    height: 100%;
    width: 100%;
    /* Critical fix: prevent slider from expanding beyond container */
    will-change: transform;
}

.banner-slide {
    min-width: 100%;
    width: 100%;
    position: relative;
    height: 100%;
    overflow: hidden;
    flex-shrink: 0;
}

.banner-slide img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    object-position: center;
}

/* Navigation Dots */
.banner-dots {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 5px;
    z-index: 10;
}

.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255,255,255,0.55);
    cursor: pointer;
    transition: all 0.3s;
    flex-shrink: 0;
}

.dot.active {
    background: white;
    width: 20px;
    border-radius: 3px;
}

/* Navigation Arrows */
.banner-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.85);
    color: #333;
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    box-shadow: 0 1px 4px rgba(0,0,0,.18);
    transition: background 0.2s;
}

.banner-arrow:hover { background: #fff; }
.banner-arrow.left  { left: 8px; }
.banner-arrow.right { right: 8px; }

@media (max-width: 768px) {
    .banner-arrow { display: none; }
}
        /* ── Stock Urgency Text ── */
        .stock-tag {
            font-size: 11px;
            font-weight: 600;
            color: #d32f2f;
            margin-top: 2px;
            display: block;
        }
        .stock-tag.yellow {
            color: #f5a623;
        }

        /* ── Product Card AD Tag ── */
        .product-ad-tag {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #e0e0e0;
            color: #757575;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            z-index: 10;
            pointer-events: none;
            letter-spacing: 0.3px;
        }
    
        /* ── Stock Urgency Text ── */
        .stock-tag {
            font-size: 11px;
            font-weight: 600;
            color: #d32f2f;
            margin-top: 2px;
            display: block;
        }
        .stock-tag.yellow {
            color: #f5a623;
        }

        
        /* ── Product Card AD Tag ── */
        .product-ad-tag {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #e0e0e0;
            color: #757575;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            z-index: 10;
            pointer-events: none;
            letter-spacing: 0.3px;
        }

        
        /* ── Hide scrollbars on category & carousels ── */
        .categories::-webkit-scrollbar { display: none; }
        .categories { scrollbar-width: none; }
        .offer-cards::-webkit-scrollbar { display: none; }
        .offer-cards { scrollbar-width: none; }

        /* ── Fix product card border-radius (less rounded) ── */
        /* product-card border-radius handled in card CSS */

        /* ── Show discount % in green ── */
        .price-discount {
            display: inline;
            font-size: 11px;
            font-weight: 600;
            color: #388e3c;
        }

        /* ── Fix recommendation & sponsored image fit ── */
        .recommendation-card img { object-fit: contain; background: #fff; }
        .sponsored-card img { object-fit: contain; background: #fff; }
        .suggested-card img { object-fit: contain; background: #f8f8f8; }

        /* ── Wishlist heart on product cards ── */
        .wishlist-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(255,255,255,0.95);
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.18);
            z-index: 11;
            font-size: 14px;
            color: #878787;
            transition: color 0.15s, transform 0.15s;
        }
        .wishlist-btn:hover { color: #ff3f6c; transform: scale(1.12); }
        .wishlist-btn.active { color: #ff3f6c; }

        /* ── Location bar default city ── */
        /* location-city style now in .location-bar block above */

        /* ── Section title polish ── */
        .section-title {
            padding: 16px 15px 8px;
            font-size: 16px;
            font-weight: 700;
            color: #212121;
            border-top: 6px solid #f1f3f6;
        }

        .stock-tag.red {
            color: #d32f2f;
        }

        /* ── Lazy‑load card fade‑in (disabled)
           The original Flipkart UI faded product cards into view using
           IntersectionObserver. However, on some browsers (especially
           when running locally via the file:// protocol) the observer
           never fires, leaving all cards invisible after the first
           handful. To ensure every product appears by default the
           animation is effectively disabled: lazy cards are always
           visible. Should you wish to re‑enable the fade effect in the
           future you can restore the opacity and transform rules below. */
        .lazy-card {
            opacity: 1 !important;
            transform: none !important;
            transition: opacity 0.3s ease, transform 0.3s ease;
            content-visibility: visible;
            contain-intrinsic-size: auto;
        }
        .lazy-card.visible {
            /* Left empty on purpose – class retained for compatibility */
        }

        /* ── view-all-btn text ── */
        .view-all-btn { font-size: 13px; padding: 6px 14px; }

    
/* HOME DEAL SECTIONS */
.deal-showcase{
    background: white;
    padding: 12px 12px 6px;
    margin-top: 8px;
}
.deal-bucket{
    border-radius: 16px;
    padding: 14px 14px 14px;
    margin-bottom: 12px;
    overflow: hidden;
    box-shadow: none;
}
.deal-bucket--red{
    background: linear-gradient(180deg, #d91f34 0%, #ff4a57 100%);
}
.deal-bucket--lime{
    background: linear-gradient(180deg, #b5dc14 0%, #e1f4a6 100%);
}
.deal-bucket__head{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}
.deal-bucket__title{
    font-size: 18px;
    font-weight: 800;
    color: #fff;
    line-height: 1.15;
}
.deal-bucket--lime .deal-bucket__title{
    color: #111;
}
.deal-bucket__tag{
    flex-shrink: 0;
    min-width: 74px;
    height: 74px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 10px;
    line-height: 1.05;
    font-weight: 900;
    padding: 10px;
    transform: rotate(-10deg);
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}
.deal-bucket--red .deal-bucket__tag{
    background: #ffe566;
    color: #0f4fa8;
}
.deal-bucket--lime .deal-bucket__tag{
    background: #fff6b7;
    color: #5a7b00;
}
.deal-bucket__grid{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.deal-promo-card{
    text-decoration: none;
    display: block;
}
.deal-promo-card__media{
    background: rgba(255,255,255,0.96);
    border-radius: 20px;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    min-height: 0;
    display:flex;
    align-items:center;
    justify-content:center;
    padding: 10px;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.24);
}
.deal-promo-card__media img{
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    display: block;
    background: transparent;
    mix-blend-mode: normal;
}
.deal-promo-card__offer{
    margin-top: 8px;
    background: rgba(208, 0, 24, 0.92);
    color: #fff;
    border-radius: 999px;
    text-align: center;
    font-size: 12px;
    font-weight: 800;
    padding: 8px 6px;
}
.deal-bucket--lime .deal-promo-card__offer{
    background: rgba(187, 219, 0, 0.92);
    color: #111;
}
.deal-promo-card__label{
    text-align: center;
    font-size: 11px;
    color: #fff;
    margin-top: 7px;
    line-height: 1.25;
    min-height: 28px;
}
.deal-bucket--lime .deal-promo-card__label{
    color: #111;
}
.deal-bucket__grid > .deal-promo-card,
.deal-bucket__grid > .tt-card-wrap{
    align-self: stretch;
}
.deal-promo-card{
    height: 100%;
}

/* ── Tick-Tock Timer Row ── */
.tt-timer-row{
    display:flex;align-items:center;gap:6px;margin-top:5px;
}
.tt-timer-label{
    font-size:11px;font-weight:600;color:#3a5900;
}
.tt-timer-display{
    display:inline-flex;align-items:center;gap:2px;
    background:#fff;border-radius:6px;padding:3px 8px;
    box-shadow:0 2px 8px rgba(0,0,0,0.10);
}
.tt-digit{
    font-size:14px;font-weight:900;color:#0f4fa8;
    min-width:20px;text-align:center;font-variant-numeric:tabular-nums;
}
.tt-colon{
    font-size:14px;font-weight:900;color:#0f4fa8;
    animation:ttBlink 1s step-end infinite;
}
@keyframes ttBlink{0%,100%{opacity:1}50%{opacity:0.3}}

/* ── Locked card overlay ── */
.tt-card-wrap{
    position:relative;
}
.tt-card-wrap.tt-locked .deal-promo-card__media::after{
    content:'';
    position:absolute;inset:0;
    background:rgba(0,0,0,0.52);
    border-radius:20px;
    backdrop-filter:blur(3px);
    -webkit-backdrop-filter:blur(3px);
}
.tt-card-wrap.tt-locked .deal-promo-card__media{
    position:relative;
}
.tt-lock-badge{
    display:none;
    position:absolute;top:50%;left:50%;
    transform:translate(-50%,-50%);
    background:rgba(255,255,255,0.95);
    border-radius:12px;
    padding:8px 10px;
    text-align:center;
    z-index:2;
    pointer-events:none;
    min-width:80px;
    box-shadow:0 4px 14px rgba(0,0,0,0.18);
}
.tt-card-wrap.tt-locked .tt-lock-badge{
    display:block;
}
.tt-lock-badge .tt-lock-icon{
    font-size:20px;display:block;margin-bottom:3px;
}
.tt-lock-badge .tt-unlock-in{
    font-size:9px;font-weight:700;color:#555;line-height:1.2;
}
.tt-lock-badge .tt-unlock-time{
    font-size:11px;font-weight:900;color:#0f4fa8;
    font-variant-numeric:tabular-nums;
}
.tt-card-wrap.tt-locked .deal-promo-card__offer{
    opacity:0.4;pointer-events:none;
}
.tt-card-wrap.tt-locked .deal-promo-card__label{
    opacity:0.5;
}
.tt-card-wrap.tt-locked a{
    pointer-events:none;
}
.tt-card-wrap.tt-active{
    animation:ttPulse .6s ease;
}
@keyframes ttPulse{
    0%{transform:scale(1)}
    40%{transform:scale(1.04)}
    100%{transform:scale(1)}
}
.people-viewed{
    padding: 8px 12px 14px;
}
.people-viewed__head{
    background: linear-gradient(135deg, #db2235 0%, #ff4d5c 100%);
    color: #fff;
    font-size: 17px;
    font-weight: 800;
    border-radius: 16px 16px 0 0;
    padding: 14px 16px 22px;
}
.people-viewed__body{
    background: #fff;
    border-radius: 0 0 16px 16px;
    padding: 0 12px 14px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    margin-top: -10px;
}
.people-viewed__grid{
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px 12px;
}
.deal-product-card{
    text-decoration: none;
    color: inherit;
    display: block;
    padding-top: 14px;
}
.deal-product-card__media{
    position: relative;
    background: #f6f6f6;
    border-radius: 18px;
    overflow: hidden;
    aspect-ratio: 1 / 1;
}
.deal-product-card__media img{
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    background: #f6f6f6;
}
.deal-product-card__rating{
    position: absolute;
    left: 8px;
    bottom: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(255,255,255,0.92);
    border-radius: 999px;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 700;
    color: #333;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.deal-product-card__rating b{
    color: #0a9b50;
}
.deal-product-card__brand{
    margin-top: 10px;
    font-size: 12px;
    font-weight: 800;
    color: #222;
}
.deal-product-card__name{
    font-size: 12px;
    color: #666;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.deal-product-card__price{
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    flex-wrap: wrap;
}
.deal-product-card__off{
    background: #6a19ff;
    color: #fff;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 800;
    padding: 5px 7px;
}
.deal-product-card__mrp{
    color: #8c8c8c;
    text-decoration: line-through;
    font-size: 12px;
}
.deal-product-card__sp{
    color: #222;
    font-size: 14px;
    font-weight: 800;
}
@media (max-width: 380px){
    .deal-bucket__grid{
        gap: 8px;
    }
    .people-viewed__grid{
        gap: 12px 10px;
    }
}


html, body{
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
}
img{
    max-width: 100%;
}
body > *{
    max-width: 100%;
}
.deal-showcase,
.people-viewed,
.category-icons-section,
.top-picks-section,
.deals-header,
.deals-products-grid,
.sponsored-section,
.offer-strip,
.trending-section{
    width: 100%;
    max-width: 100%;
}
.deal-bucket__grid,
.people-viewed__grid{
    width: 100%;
}
.deal-bucket__grid > *,
.people-viewed__grid > *{
    min-width: 0;
}
.deal-promo-card__media,
.deal-product-card__media{
    width: 100%;
}
@media (max-width: 480px){
    .top-tabs-wrap{
        padding: 8px 8px 8px 8px;
        gap: 6px;
    }
    .top-tab{
        padding: 12px 8px;
        border-radius: 12px;
    }
    .top-tab .tab-label{
        font-size: 14px;
    }
    .location-bar{
        padding: 4px 10px 2px;
        gap: 5px;
    }
    .location-link{
        font-size: 11px;
    }
    .search-bar{
        margin: 6px 10px;
        padding: 7px 10px;
        gap: 8px;
        border-radius: 4px;
    }
    .search-bar input{
        min-width: 0;
        font-size: 13px;
    }
    .categories{
        padding: 8px 12px;
        gap: 16px;
    }
    .deal-showcase{
    background: white;
        padding: 12px 12px 4px;
    }
    .deal-bucket{
        border-radius: 20px;
        padding: 16px 12px 14px;
        margin-bottom: 16px;
    }
    .deal-bucket__head{
        gap: 8px;
        margin-bottom: 12px;
    }
    .deal-bucket__title{
        font-size: 16px;
    }
    .deal-bucket__tag{
        min-width: 60px;
        width: 60px;
        height: 60px;
        font-size: 9px;
        padding: 8px;
    }
    .deal-bucket__grid{
        gap: 10px;
    }
    .deal-promo-card__media{
        border-radius: 16px;
        aspect-ratio: 1 / 1;
        padding: 8px;
    }
    .deal-promo-card__offer{
        margin-top: 6px;
        font-size: 11px;
        padding: 7px 4px;
    }
    .deal-promo-card__label{
        font-size: 10px;
        min-height: 24px;
        margin-top: 6px;
    }
    .people-viewed{
        padding: 8px 12px 16px;
    }
    .people-viewed__head{
        font-size: 16px;
        padding: 16px 14px 24px;
        border-radius: 20px 20px 0 0;
    }
    .people-viewed__body{
        padding: 0 10px 12px;
        border-radius: 0 0 20px 20px;
    }
    .people-viewed__grid{
        gap: 12px 10px;
    }
    .deal-product-card{
        padding-top: 12px;
    }
    .deal-product-card__media{
        border-radius: 14px;
    }
    .deal-product-card__rating{
        left: 6px;
        bottom: 6px;
        padding: 4px 7px;
        font-size: 11px;
    }
    .deal-product-card__brand{
        margin-top: 8px;
        font-size: 11px;
    }
    .deal-product-card__name{
        font-size: 11px;
    }
    .deal-product-card__price{
        gap: 5px;
        margin-top: 6px;
    }
    .deal-product-card__off,
    .deal-product-card__mrp,
    .deal-product-card__sp{
        font-size: 11px;
    }
}


/* ── Skeleton shimmer loading ── */
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
.skel-img  { width: 100%; aspect-ratio: 1; border-radius: 6px; }
.skel-line { height: 12px; margin: 8px 0; border-radius: 4px; }
.skel-line.short { width: 60%; }
.skel-card {
    min-width: 140px;
    background: #fff;
    border-radius: 8px;
    padding: 10px;
    flex-shrink: 0;
}
.skel-banner { width: 100%; aspect-ratio: 820/360; border-radius: 10px; }
/* Fade-in when real image loads */
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

        /* 2026-03 homepage cleanup: realer Flipkart-ish DOTD + remove dead banner gaps */
        .deals-section {
            margin: 12px 10px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.10);
        }
        .deals-section .deals-header {
            margin-top: 0;
            padding: 14px 14px 12px;
            background: #fff;
            border-bottom: 1px solid #f1f3f6;
        }
        .deals-title-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .sale-live-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff3f3;
            color: #d32f2f;
            border: 1px solid #ffd9d9;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            text-transform: uppercase;
        }
        .sale-live-fire { font-size: 12px; line-height: 1; }
        .deals-section .deals-view-all {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 8px;
            background: #2874f0;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }
        .deals-section .deals-products-grid {
            margin-bottom: 0;
            gap: 1px;
            background: #ececec;
        }
        .deals-section .deals-product-card {
            padding: 10px 8px 12px;
            min-width: 0;
        }
        .deals-section .deals-product-media {
            width: 100%;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            overflow: hidden;
        }
        .deals-section .deals-product-card img {
            width: 100%;
            height: 100%;
            padding: 0;
            background: #fff;
            object-fit: contain;
            object-position: center;
            display: block;
        }
        .deals-section .deal-name {
            margin-top: 8px;
            font-size: 12px;
            line-height: 1.3;
            color: #212121;
            min-height: 31px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .deals-section .deal-discount {
            position: static;
            left: auto;
            right: auto;
            bottom: auto;
            background: none;
            padding: 0;
            margin-top: 6px;
            text-align: left;
            line-height: 1.25;
        }
        .deals-section .deal-pct {
            color: #388e3c;
            font-size: 12px;
            font-weight: 700;
        }
        .deals-section .deal-price {
            color: #212121;
            font-size: 15px;
            font-weight: 700;
            margin-top: 2px;
        }
        .product-grid .product-card:last-child:nth-child(odd) {
            grid-column: 1 / -1;
            width: calc(50% - 0.5px);
            justify-self: start;
        }
        @media (max-width: 380px) {
            .deals-section .deals-header {
                padding: 12px 12px 10px;
            }
            .deals-section .deals-title {
                font-size: 17px;
            }
            .deals-section .sale-live-chip {
                font-size: 10px;
                padding: 4px 7px;
            }
            .deals-section .deal-name {
                font-size: 11px;
                min-height: 29px;
            }
            .deals-section .deal-price {
                font-size: 14px;
            }
        }

</style>
<script>
// Smart image extension resolver — tries jpg→jpeg→png→webp→avif
(function() {
  const EXTS = ['jpg','jpeg','png','webp','avif'];
  function smartImg(el) {
    const base = el.dataset.base || el.src.replace(/\.(avif|webp|png|jpe?g)$/i,'');
    let ei = 0;
    el.dataset.base = base;
    function next() {
      if (ei >= EXTS.length) {
        el.onerror = null;
        // When no supported image format exists, display a placeholder image
        el.src = 'assets/placeholder.png';
        return;
      }
      el.onerror = next;
      el.src = base + '.' + EXTS[ei++];
    }
    next();
  }
  window.smartImg = smartImg;

  // Auto-apply to all product images on DOMContentLoaded
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('img[src*="TopPicksForYou"], img[src*="DealsOfTheDay"], img[src*="SuggestedForYou"], img[src*="YouMayAlsoLike"], img[src*="UpgradeToPremium"], img[src*="Sponsored"], img[src*="BannerSlider"], img[src*="CategoryIcons"], img[src*="MidCarousel"]').forEach(function(img) {
      const base = img.src.replace(/\.(avif|webp|png|jpe?g)$/i,'');
      img.dataset.base = base;
      const EXTS2 = ['jpg','jpeg','png','webp','avif'];
      let ei2 = 0;
      // Already tried the original extension (avif) implicitly — start from jpg
      img.onerror = function tryExt() {
        if (ei2 >= EXTS2.length) {
          img.onerror = null;
          // Fallback to a placeholder image when no variant is available
          img.src = 'assets/placeholder.png';
          return;
        }
        img.onerror = tryExt;
        img.src = base + '.' + EXTS2[ei2++];
      };
    });
  });
})();
</script>
    <link rel="stylesheet" href="assets/shared.css?v=20260320">
</head>
<body data-fk-sync="auth,cart,wishlist">
    <!-- ── App top tabs: Flipkart | Travel ── -->
    <div class="top-tabs-wrap">

        <!-- Flipkart tab (active) -->
        <div class="top-tab fk-tab">
            <img src="assets/icon.php?name=fk-tab-f" alt="f" style="height:28px;width:auto;">
            <img src="assets/icon.php?name=fk-tab-word" alt="Flipkart" style="height:20px;width:auto;">
        </div>

        <!-- Travel tab -->
        <a href="info.php?page=travel" class="top-tab travel-tab">
            <!-- Airplane icon (orange, pointing up-right) -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z" fill="#f77c17"/>
            </svg>
            <span class="tab-label">Travel</span>
        </a>

    </div>

    <!-- ── Sticky: Location + Search + Category tabs ── -->
    <div class="sticky-header">

    <!-- Location Bar (inside sticky) -->
    <div class="location-bar" onclick="openPinModal()" style="cursor:pointer">
        <div class="location-pin">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="#212121">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
        </div>
        <div class="location-text">
            <span id="loc-label">Location not set</span>
        </div>
        <a href="#" class="location-link" onclick="openPinModal();return false;">Select delivery location</a>
    </div>

    <!-- Search Bar section -->
    <div class="search-bar-wrapper">
    <div class="search-bar" onclick="window.location.href='search.php'" style="cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="#878787">
            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </svg>
        <input type="text" placeholder="Search for Products" readonly onclick="window.location.href='search.php'" style="cursor:pointer;">
    </div>
    </div><!-- end search-bar-wrapper -->

        <!-- ========================================= -->
    <!-- CATEGORY ICONS SECTION -->
    <!-- ========================================= -->
    <div class="category-icons-section">
        <div class="category-icons-grid">
            <!-- For You -->
            <a href="#" class="category-icon-item active-cat">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M16 4l2.5 5.5 6 .9-4.35 4.2 1.02 5.93L16 17.75l-5.17 2.78 1.02-5.93L7.5 10.4l6-.9L16 4z" fill="#FFE51F" stroke="#333333" stroke-width="1.3" stroke-linejoin="round"/>
<circle cx="16" cy="24" r="2.5" fill="#FFE51F" stroke="#333333" stroke-width="1.3"/>
                    </svg>
                </div>
                <div class="category-icon-name">For You</div>
            </a>
            <!-- Fashion → Flipkart -->
            <a href="search.php?q=Fashion&category=fashion" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M8.58301 24.6445H23.3717V25.7525C23.3717 27.4093 22.0285 28.7525 20.3717 28.7525H11.583C9.92615 28.7525 8.58301 27.4093 8.58301 25.7525V24.6445Z" fill="#FFE51F"/>
<path d="M16.0003 10.6766C13.1536 10.6766 12.1563 8.21071 11.9404 6.48294C11.8966 6.13193 11.5352 5.88942 11.2056 6.01794C10.418 6.3251 9.33827 6.73537 8.60601 6.97946C7.6201 7.3081 6.82589 8.75958 6.55203 9.44424L4.79622 14.7117C4.62878 15.214 4.88191 15.7597 5.37351 15.9564L8.60601 17.2494V26.7517C8.60601 27.8562 9.50144 28.7517 10.606 28.7517H21.3947C22.4992 28.7517 23.3947 27.8562 23.3947 26.7517V17.2494L26.6645 15.9414C27.1406 15.751 27.3961 15.232 27.2499 14.7405C26.631 12.6601 25.6079 9.47765 25.0379 8.62264C24.3806 7.63673 23.6685 7.11639 23.3947 6.97946L20.7839 6.00041C20.457 5.87783 20.1047 6.11968 20.0623 6.4662C19.8508 8.19473 18.8563 10.6766 16.0003 10.6766Z" stroke="#333333" stroke-width="1.4"/>
<path d="M8.99414 24.6445H22.9612" stroke="#333333" stroke-width="1.4" stroke-linecap="round"/>
<path d="M23.3941 17.661V13.9639M8.60547 17.661V13.9639" stroke="#333333" stroke-width="1.4" stroke-linecap="round"/>
</svg>
                </div>
                <div class="category-icon-name">Fashion</div>
            </a>
            <!-- Mobiles → Flipkart -->
            <a href="search.php?q=Phone&category=mobiles" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#mob1)">
<path d="M9.7998 24.9199V27.1199C9.7998 28.5899 10.9898 29.7799 12.4598 29.7799H19.7598C21.2298 29.7799 22.4198 28.5899 22.4198 27.1199V25.0799" fill="#FFE51F"/>
<path d="M9.7998 24.9199V27.1199C9.7998 28.5899 10.9898 29.7799 12.4598 29.7799H19.7598C21.2298 29.7799 22.4198 28.5899 22.4198 27.1199V25.0799" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M12.4198 6.7998H19.7998C21.2498 6.7998 22.4198 7.9698 22.4198 9.4198V27.1298C22.4198 28.5998 21.2298 29.7898 19.7598 29.7898H12.4598C10.9898 29.7898 9.7998 28.5998 9.7998 27.1298V9.4198C9.7998 7.9698 10.9698 6.7998 12.4198 6.7998Z" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M14.8994 9.24023H16.8994" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M14.1699 27.4102H18.1699" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
</g>
<defs><clipPath id="mob1"><rect width="14.22" height="24.59" fill="white" transform="translate(9 6)"/></clipPath></defs>
</svg>
                </div>
                <div class="category-icon-name">Mobiles</div>
            </a>
            <!-- Beauty → Flipkart -->
            <a href="search.php?q=Beauty&category=beauty" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.2291 14.4229H19.5191C20.3191 14.4229 20.9691 15.0729 20.9691 15.8729V26.9529C20.9691 28.2529 19.9091 29.3129 18.6091 29.3129H13.1491C11.8491 29.3129 10.7891 28.2529 10.7891 26.9529V15.8729C10.7891 15.0729 11.4391 14.4229 12.2391 14.4229H12.2291Z" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M18.1886 14.4427V9.24269C18.1886 9.06269 18.1086 8.88269 17.9586 8.77269L14.5386 6.03269C14.1386 5.71269 13.5586 6.00269 13.5586 6.50269V14.4427H18.1886Z" fill="#FFE51F" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M11.3691 17.4727L20.8691 17.6027" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
</svg>
                </div>
                <div class="category-icon-name">Beauty</div>
            </a>
            <!-- Electronics → YOUR SITE -->
            <a href="search.php?q=electronics" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M4.99121 23.2591V10.0236C4.99121 9.03574 5.78867 8.23828 6.77657 8.23828H25.3086C26.2965 8.23828 27.094 9.03574 27.094 10.0236V23.2591" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M2.26483 24.3418H29.7475V26.508C29.7475 28.0315 28.5096 29.2694 26.9861 29.2694H5.01428C3.49078 29.2694 2.25293 28.0315 2.25293 26.508V24.3418H2.26483Z" fill="#FFE51F" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M13.751 26.9131H18.3453" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
</svg>
                </div>
                <div class="category-icon-name">Electronics</div>
            </a>
            <!-- Home & Kitchen → Flipkart -->
            <a href="search.php?q=Home&category=home" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M28.1679 23.9892C28.0729 24.8557 27.2301 25.5205 26.2211 25.5205H5.13934C4.09475 25.5205 3.22821 24.8083 3.1926 23.918L2.21923 18.2439C2.17175 17.8641 2.51599 17.5317 2.95519 17.5317H5.28179C5.60229 17.5317 5.88718 17.7098 5.98214 17.9709L7.08609 21.4133C7.19292 21.7338 7.52529 21.9475 7.90514 21.9475H23.9302C24.3219 21.9475 24.6661 21.7219 24.7611 21.3896L25.7345 17.9828C25.8175 17.6979 26.1143 17.4961 26.4585 17.4961H29.0344C29.3291 17.4961 29.5804 17.6451 29.7028 17.8545C29.7727 17.9741 29.8006 18.1134 29.7704 18.2558L28.1679 23.9892Z" fill="#FFE51F" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M8.03613 21.7937L9.22317 12.1193C9.22317 10.505 10.5289 9.19922 12.1433 9.19922H19.7047C21.3191 9.19922 22.6248 10.505 22.6248 12.1193L23.8119 21.7937" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
</svg>
                </div>
                <div class="category-icon-name">Home</div>
            </a>
            <!-- Appliances → Flipkart -->
            <a href="search.php?q=Appliances&category=electronics" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="4" y="4" width="24" height="24" rx="3" stroke="#333333" stroke-width="1.4"/>
<circle cx="16" cy="18" r="6" stroke="#333333" stroke-width="1.4"/>
<circle cx="16" cy="18" r="3" fill="#FFE51F" stroke="#333333" stroke-width="1.2"/>
<circle cx="8" cy="9" r="1.5" fill="#FFE51F" stroke="#333333" stroke-width="1.1"/>
<line x1="12" y1="9" x2="24" y2="9" stroke="#333333" stroke-width="1.4" stroke-linecap="round"/>
</svg>
                </div>
                <div class="category-icon-name">Appliances</div>
            </a>
            <!-- Travel → Flipkart -->
            <a href="search.php?q=Travel" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="5" y="10" width="22" height="17" rx="3" stroke="#333333" stroke-width="1.4"/>
<path d="M11 10V8a2 2 0 012-2h6a2 2 0 012 2v2" stroke="#333333" stroke-width="1.4" stroke-linecap="round"/>
<path d="M5 17h22" stroke="#333333" stroke-width="1.4"/>
<rect x="14" y="13.5" width="4" height="7" rx="1" fill="#FFE51F" stroke="#333333" stroke-width="1.2"/>
<line x1="9" y1="27" x2="9" y2="29.5" stroke="#333333" stroke-width="1.4" stroke-linecap="round"/>
<line x1="23" y1="27" x2="23" y2="29.5" stroke="#333333" stroke-width="1.4" stroke-linecap="round"/>
</svg>
                </div>
                <div class="category-icon-name">Travel</div>
            </a>
            <!-- Food & Health → Flipkart -->
            <a href="search.php?q=Grocery" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#food1)">
<path d="M20.1605 9.0098V6.8698C20.1605 6.2798 17.8805 5.7998 15.0705 5.7998C12.2605 5.7998 9.98047 6.2798 9.98047 6.8698V9.0098C9.98047 9.5998 12.2605 10.0798 15.0705 10.0798C17.8805 10.0798 20.1605 9.5998 20.1605 9.0098Z" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M18.9098 11.9404H11.0998C8.72498 11.9404 6.7998 13.8656 6.7998 16.2404V25.0204C6.7998 27.3953 8.72498 29.3204 11.0998 29.3204H18.9098C21.2846 29.3204 23.2098 27.3953 23.2098 25.0204V16.2404C23.2098 13.8656 21.2846 11.9404 18.9098 11.9404Z" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
<path d="M22.6597 24.2096H6.96973V17.3096H22.6597" fill="#FFE51F"/>
<path d="M22.6597 24.2096H6.96973V17.3096H22.6597" stroke="#333333" stroke-width="1.4" stroke-miterlimit="10" stroke-linecap="round"/>
</g>
<defs><clipPath id="food1"><rect width="18.01" height="25.12" fill="white" transform="translate(6 5)"/></clipPath></defs>
</svg>
                </div>
                <div class="category-icon-name">Grocery</div>
            </a>
            <!-- Furniture → Flipkart -->
            <a href="search.php?q=Furniture&category=home" class="category-icon-item">
                <div class="category-icon-wrapper">
                    <svg class="cat-svg-icon" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M27.6826 23.6752H3V26.0571H27.6826V23.6752Z" fill="#FCE531"/>
<path d="M27.7912 10.3187H3.519C3.23236 10.3187 3 10.5527 3 10.8413V25.2545C3 25.5431 3.23236 25.7771 3.519 25.7771H27.7912C28.0779 25.7771 28.3102 25.5431 28.3102 25.2545V10.8413C28.3102 10.5527 28.0779 10.3187 27.7912 10.3187Z" stroke="#333333" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M23.6321 29.6176L17.7421 28.0256C17.2351 27.8919 16.873 27.4179 16.873 26.8832V26.4457H13.747V26.8832C13.747 27.4179 13.3849 27.8797 12.878 28.0256L7 29.6176" stroke="#333333" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                </div>
                <div class="category-icon-name">Furniture</div>
            </a>
        </div>
    </div>
    </div><!-- end .sticky-header -->

   <!-- ========================================= -->
<!-- SLIDING BANNER CAROUSEL -->
<!-- ========================================= -->
<div class="banner-container">
    <div class="banner-slider" id="bannerSlider">
        <!-- Slides injected by JS via assets/banners.php -->
    </div>
    <button class="banner-arrow left" onclick="moveSlide(-1)"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/></svg></button>
    <button class="banner-arrow right" onclick="moveSlide(1)"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></button>
    <div class="banner-dots" id="bannerDots"></div>
</div>


    <div class="deal-showcase">
        <section class="deal-bucket deal-bucket--red">
            <div class="deal-bucket__head">
                <div class="deal-bucket__title">Top trendy deals</div>
                <div class="deal-bucket__tag">BIG<br>SAVING<br>DAYS</div>
            </div>
            <div class="deal-bucket__grid" id="topTrendyDealsGrid"></div>
        </section>

        <section class="deal-bucket deal-bucket--lime" id="tickTockSection">
            <div class="deal-bucket__head">
                <div>
                    <div class="deal-bucket__title">New deals every hour</div>
                    <div class="tt-timer-row">
                        <span class="tt-timer-label">Next deal in:</span>
                        <span class="tt-timer-display" id="ttTimerDisplay">
                            <span class="tt-digit" id="ttMM">00</span>
                            <span class="tt-colon">:</span>
                            <span class="tt-digit" id="ttSS">00</span>
                        </span>
                    </div>
                </div>
                <div class="deal-bucket__tag">TICK<br>TOCK<br>DEALS</div>
            </div>
            <div class="deal-bucket__grid" id="hourlyDealsGrid"></div>
        </section>
    </div>

    <section class="people-viewed">
        <div class="people-viewed__head">People also viewed</div>
        <div class="people-viewed__body">
            <div class="people-viewed__grid" id="peopleViewedGrid"></div>
        </div>
    </section>
    <!-- ========================================= -->
    <!-- DEALS OF THE DAY SECTION -->
    <!-- ========================================= -->
    <section class="deals-section">
        <div class="deals-header">
            <div class="deals-title-wrap">
                <div class="deals-title">Deals of the Day</div>
                <div class="sale-live-chip"><span class="sale-live-fire">🔥</span><span id="sale-timer">59:00</span> Sale Live</div>
            </div>
            <a href="search.php?q=deals" class="deals-view-all">View All</a>
        </div>

        <div class="deals-products-grid" id="dealsOfDayGrid">
            <!-- Injected by JS -->
        </div>
    </section>

    <!-- ========================================= -->
    <!-- SPONSORED SECTION -->
    <!-- ========================================= -->
    <!-- Sponsored Banner — OUTSIDE container, full section width -->
    <div id="sponsoredBannerWrap" style="margin:12px 10px;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.12);aspect-ratio:820/360;background:#f8f8f8;">
        <img id="sponsoredBannerImg" src="Images/Sponsored/banner.jpg" alt="Flipkart Sale" loading="lazy" style="width:100%;height:100%;display:block;object-fit:cover;" onerror="window.smartImg&&smartImg(this)">
    </div>

    <div class="sponsored-section">
        <div class="sponsored-header" style="display:flex;align-items:center;justify-content:space-between;padding:12px 15px 8px;">
            <span style="font-size:16px;font-weight:700;color:#212121;">Sponsored</span>
            <img src="Images/Brands/boat.jpg" alt="boAt" loading="lazy" style="height:28px;width:auto;object-fit:contain;border-radius:4px;" onerror="this.onerror=null;this.src='assets/placeholder.png'">
        </div>
        
        <!-- Sponsored Product Cards — generated by JS from products.json -->
        <div class="sponsored-products" id="sponsoredProducts"></div>
    </div>

    <!-- ========================================= -->
    <!-- SUGGESTED FOR YOU SECTION -->
    <!-- ========================================= -->
    <div class="suggested-section">
        <div class="suggested-header">
            <div class="suggested-title">Suggested For You</div>
            <button class="view-all-btn" onclick="window.location.href='search.php?q=all'">View All ›</button>
        </div>
        
        <div class="suggested-grid" id="suggestedGrid"></div>
    </div>

    <!-- Offer Cards — generated by JS -->
    <div style="background:#fff;padding:10px 12px 0;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:16px;font-weight:700;color:#212121;">Top Picks For You</div>
        <a href="search.php?q=all" style="font-size:13px;color:#2874f0;font-weight:600;text-decoration:none;">View All ›</a>
    </div>
    <div class="offer-cards" id="offerCards"></div>

    <!-- ========================================= -->
    <!-- MID-PAGE AD BANNER — Nirvana by boAt -->
    <!-- ========================================= -->
    <div class="mid-ad-banner" onclick="window.location.href='search.php?q=boat%20earbuds&category=electronics'" style="cursor:pointer;">
        <span class="ad-tag">AD</span>
        <img id="midAdBannerImg" src="banners/nirvana_banner.webp" alt="Nirvana by boAt — India's #1 Audio Brand" loading="lazy" onerror="window.smartImg&&smartImg(this)">
    </div>

    <!-- ========================================= -->
    <!-- YOU MAY ALSO LIKE SECTION -->
    <!-- ========================================= -->
    <div class="recommendation-section">
        <div class="recommendation-header">
            <div class="recommendation-title">You may also like</div>
            <a href="#" class="see-all-badge">All</a>
        </div>
        <div class="recommendation-scroll" id="youMayLikeScroll"></div>
    </div>

    <!-- ========================================= -->
    <!-- UPGRADE TO PREMIUM SECTION -->
    <!-- ========================================= -->
    <div class="premium-section">
        <div class="premium-header">
            <div class="premium-title">Upgrade to Premium!</div>
            <button class="premium-arrow">→</button>
        </div>
        
        <div class="recommendation-scroll" id="premiumScroll"></div>
    </div>

    <!-- ========================================= -->
    <!-- RECENTLY VIEWED SECTION -->
    <!-- ========================================= -->
    <div class="recently-viewed-section">
        <div class="recommendation-header">
            <div class="recommendation-title">Recently Viewed</div>
            <button class="premium-arrow">→</button>
        </div>
        
        <div class="recommendation-scroll" id="rv-scroll">
            <!-- Populated by JS from localStorage -->
        </div>
    </div>

    <!-- Product Section -->
    <div style="background:#fff;padding:10px 12px 6px;display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
        <div style="font-size:16px;font-weight:700;color:#212121;">Top Picks For You</div>
        <a href="search.php" style="font-size:13px;color:#2874f0;font-weight:600;text-decoration:none;">View All ›</a>
    </div>

    <!-- Product Grid — generated from products.json -->
    <div class="product-grid" id="productGrid">
        <!-- Cards injected by renderProductGrid() after products.json loads -->
    </div>

    <!-- ========================================= -->
    <!-- SUPERCOIN BANNER -->
    <!-- ========================================= -->
    <div id="supercoinBannerWrap" style="margin:10px 0;cursor:pointer;display:none;" onclick="window.location.href='#'">
        <img id="supercoinBannerImg" src="" alt="Extra 5% Off with SuperCoins" loading="lazy" style="width:100%;display:block;aspect-ratio:832/193;object-fit:cover;">
    </div>

    <!-- ========================================= -->
    <!-- AD BANNER SECTION -->
    <!-- ========================================= -->
    <div class="ad-banner-section">
        <div class="ad-banner-image">
            <div class="ad-badge">AD</div>
            <img id="adBannerImg" src="banners/ads/banner.jpg" alt="Special Offer" loading="lazy" onerror="window.smartImg&&smartImg(this)">
        </div>
        <div class="ad-banner-footer">
            <div class="ad-banner-text">Nirvana Series by boAt — From ₹1,599 | 10% off with SBI Card</div>
            <div class="ad-banner-arrow">›</div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- TOP STORIES / BRAND DIRECTORY SECTION -->
    <!-- ========================================= -->
    <div class="top-stories-section">
        <div class="top-stories-header">Top Stories : Brand Directory</div>
        <div class="brand-links">
            NULL AUDIO & VIDEO | AIRTEL HD | APPLE EARFODS | APPLE TV | BLUETOOTH HEADSETS | LOGITECH SPEAKERS | SPEAKER | MP3 PLAYER ONLINE | SONY SPEAKER PRICE LIST | CREATIVE 5.1 SPEAKERS | LEAF SPEAKERS | FRONTECH SPEAKERS | SONY SPEAKERS 5.1 | SONY HOME THEATRE 5.1 | HEADPHONE SPLITTER | JBL BLUETOOTH HEADPHONES | MOBILE HEADPHONES | SKULL CANDY EARPHONES | HEADSET WITH MIC | PORTABLE SPEAKERS | INTEX BLUETOOTH SPEAKERS | PHILIPS WIRELESS SPEAKERS | TABLET PHONE PRICE | LAP DESK | SONY MDR XB450 | GAS DETECTORS
        </div>
    </div>

    <!-- ========================================= -->
    <!-- FOOTER SECTION -->
    <!-- ========================================= -->
    <div class="footer">
        <!-- Main Footer Columns -->
        <div class="footer-columns">
            <!-- About Column -->
            <div class="footer-section">
                <div class="footer-title">ABOUT</div>
                <div class="footer-links">
                    <a href="info.php?page=contact">Contact Us</a>
                    <a href="info.php?page=about">About Us</a>
                    <a href="info.php?page=careers">Careers</a>
                    <a href="info.php?page=stories">Stories</a>
                    <a href="info.php?page=press">Press</a>
                    <a href="info.php?page=corporate">Corporate Info</a>
                </div>
            </div>

            <!-- Group Companies Column -->
            <div class="footer-section">
                <div class="footer-title">GROUP COMPANIES</div>
                <div class="footer-links">
                    <a href="https://www.myntra.com/" target="_blank" rel="noopener">Myntra</a>
                    <a href="https://www.cleartrip.com/" target="_blank" rel="noopener">Cleartrip</a>
                    <a href="https://www.shopsy.in/" target="_blank" rel="noopener">Shopsy</a>
                </div>
            </div>

            <!-- Help Column -->
            <div class="footer-section">
                <div class="footer-title">HELP</div>
                <div class="footer-links">
                    <a href="info.php?page=payments">Payments</a>
                    <a href="info.php?page=shipping">Shipping</a>
                    <a href="info.php?page=returns">Cancellation & Returns</a>
                    <a href="info.php?page=faq">FAQ</a>
                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        <!-- Consumer Policy -->
        <div class="footer-section">
            <div class="footer-title">CONSUMER POLICY</div>
            <div class="footer-links">
                <a href="info.php?page=returns">Cancellation & Returns</a>
                <a href="info.php?page=terms">Terms Of Use</a>
                <a href="info.php?page=security">Security</a>
                <a href="info.php?page=privacy">Privacy</a>
                <a href="info.php?page=sitemap">Sitemap</a>
                <a href="info.php?page=grievance">Grievance Redressal</a>
                <a href="info.php?page=epr">EPR Compliance</a>
            </div>
        </div>

        <div class="footer-divider"></div>

        <!-- Company Information -->
        <div class="footer-section">
            <div class="footer-title">Mail Us:</div>
            <div class="footer-company-info">
                <p>Flipkart Internet Private Limited,</p>
                <p>Buildings Alyssa, Begonia &</p>
                <p>Clove Embassy Tech Village,</p>
                <p>Outer Ring Road, Devarabeesanahalli Village,</p>
                <p>Bengaluru, 560103,</p>
                <p>Karnataka, India</p>
            </div>
        </div>

        <div class="footer-divider"></div>

        <!-- Registered Office -->
        <div class="footer-section">
            <div class="footer-title">Registered Office Address:</div>
            <div class="footer-company-info">
                <p>Flipkart Internet Private Limited,</p>
                <p>Buildings Alyssa, Begonia &</p>
                <p>Clove Embassy Tech Village,</p>
                <p>Outer Ring Road, Devarabeesanahalli Village,</p>
                <p>Bengaluru, 560103,</p>
                <p>Karnataka, India</p>
                <p>CIN : U51109KA2012PTC066107</p>
                <p>Telephone: <a href="tel:044-45614700" style="color: #2874f0;">044-45614700</a> / <a href="tel:044-67415800" style="color: #2874f0;">044-67415800</a></p>
            </div>
        </div>

        <div class="footer-divider"></div>

        <!-- Social Media -->
        <div class="footer-section">
            <div class="footer-title">Social:</div>
            <div class="social-icons">
                <a href="https://www.facebook.com/flipkart" target="_blank" rel="noopener" title="Flipkart on Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="https://twitter.com/flipkart" target="_blank" rel="noopener" title="Flipkart on X (Twitter)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                </a>
                <a href="https://www.youtube.com/flipkart" target="_blank" rel="noopener" title="Flipkart on YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/flipkart" target="_blank" rel="noopener" title="Flipkart on Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="footer-divider"></div>

        <!-- Payment Methods -->
        <div class="footer-section">
            <div class="payment-methods">
                <!-- PhonePe -->
                <img class="payment-icon" src="assets/icon.php?name=phonepe" alt="PhonePe">
                <!-- GPay -->
                <img class="payment-icon" src="assets/icon.php?name=gpay" alt="Google Pay">
                <!-- Paytm -->
                <img class="payment-icon" src="assets/icon.php?name=paytm" alt="Paytm">
                <!-- UPI -->
                <img class="payment-icon" src="assets/icon.php?name=upi" alt="UPI">
                <!-- QR Scan -->
                <svg class="payment-icon" viewBox="0 0 52 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="52" height="52" rx="10" fill="white"/>
                    <rect x="10" y="10" width="12" height="12" rx="2" fill="none" stroke="#333" stroke-width="1.8"/>
                    <rect x="13" y="13" width="6" height="6" rx="1" fill="#333"/>
                    <rect x="30" y="10" width="12" height="12" rx="2" fill="none" stroke="#333" stroke-width="1.8"/>
                    <rect x="33" y="13" width="6" height="6" rx="1" fill="#333"/>
                    <rect x="10" y="30" width="12" height="12" rx="2" fill="none" stroke="#333" stroke-width="1.8"/>
                    <rect x="13" y="33" width="6" height="6" rx="1" fill="#333"/>
                    <path d="M30 30h4v4h-4zM38 30h4v4h-4zM34 34h4v4h-4zM30 38h4v4h-4zM38 38h4v4h-4z" fill="#333"/>
                </svg>
                <!-- Flipkart Assured -->
                <img class="payment-icon" src="assets/icon.php?name=fk-assured" alt="Flipkart Assured">
            </div>
        </div>

        <!-- Bottom Links -->
        <div class="footer-bottom">
            <div class="footer-bottom-links">
                <a href="info.php?page=seller"> Become a Seller</a>
                <a href="info.php?page=advertise"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" style="vertical-align:-1px;margin-right:3px"><path d="M18 11v2h4v-2h-4zm-2 6.61c.96.71 2.21 1.65 3.2 2.39.4-.53.8-1.07 1.2-1.6-.99-.74-2.24-1.68-3.2-2.4-.4.54-.8 1.08-1.2 1.61zM20.4 5.6c-.4-.53-.8-1.07-1.2-1.6-.99.74-2.24 1.68-3.2 2.4.4.53.8 1.07 1.2 1.6.96-.72 2.21-1.65 3.2-2.4zM4 9c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2h1v4h2v-4h1l5 3V6L8 9H4zm11.5 3c0-1.33-.58-2.53-1.5-3.35v6.69c.92-.81 1.5-2.01 1.5-3.34z"/></svg> Advertise</a>
                <a href="info.php?page=giftcards"><svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" style="vertical-align:-1px;margin-right:3px"><path d="M20 6h-2.18c.07-.28.18-.51.18-.8C18 3.88 16.12 2 13.8 2c-1.13 0-2.08.49-2.77 1.26L10 4.54l-1.03-1.28C8.28 2.49 7.33 2 6.2 2 3.88 2 2 3.88 2 6.2c0 .3.11.52.18.8H0v14h20v-14zm-9.5 11H4V8h6.5v9zM6.2 4c1 0 1.9.68 2.2 1.69L9 7H6.2C5.11 7 4.2 6.09 4.2 5s.91-1 2-1zm7.6 0c1.1 0 2 .9 2 2S14.9 7 13.8 7H11l.6-1.31C11.9 4.68 12.8 4 13.8 4zM20 18h-6.5V8H20v10z"/></svg> Gift Cards</a>
                <a href="info.php?page=help"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" style="vertical-align:-1px;margin-right:3px"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg> Help Center</a>
            </div>
            <p style="margin-top: 15px;">© 2007-2026 Flipkart.com</p>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <div class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
            </div>
            <div>Home</div>
        </a>
        <a href="search.php" class="nav-item">
            <div class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </div>
            <div>Search</div>
        </a>
        <a href="wishlist.php" class="nav-item">
            <div class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </div>
            <div>Wishlist</div>
        </a>
        <a href="profile.php" class="nav-item">
            <div class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </div>
            <div>Account</div>
        </a>

    </div>

    <!-- ========================================= -->
<!-- JAVASCRIPT FOR SLIDING BANNER -->
<!-- ========================================= -->
<?php $pv = @filemtime(__DIR__.'/assets/products.json') ?: '1'; ?>
<script src="assets/products-data.js?v=<?= $pv ?>" defer fetchpriority="high"></script>
<script>
// ============================================================
//  🛍️ RENDER PRODUCT GRID — builds all cards from products.json
//  Runs once after fk:ready. No more hardcoded HTML needed.
// ============================================================
function renderProductGrid() {
    var products = window.FK_SEARCH_PRODUCTS || [];
    var fkP      = window.FK_PRODUCTS        || {};
    var grid     = document.getElementById('productGrid');
    if (!grid || !products.length) return;

    var BADGES = {
        bestseller: '<div class="tag-bestseller">⭐ Best Seller</div>',
        trending:   '<div class="tag-trending"><svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor" style="vertical-align:-1px;margin-right:2px"><path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67z"/></svg> Trending</div>',
        hotdeal:    '<div class="tag-hotdeal"><svg viewBox="0 0 24 24" width="10" height="10" fill="currentColor" style="vertical-align:-1px;margin-right:2px"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/></svg> Hot Deal</div>'
    };

    var html = '';
    products.forEach(function(p) {
        var num      = parseInt(p.id.replace('p',''), 10);
        var fp       = fkP[p.id] || {};
        var price    = p.price;
        var mrp      = p.mrp;
        var off      = p.off;
        var name     = p.name  || fp.name  || '';
        var brand    = fp.brand || p.brand || '';
        var badge    = (p.badge || '').toLowerCase();
        var stock    = p.stock || 0;
        var rating   = fp.rating || p.rating || '4.0';
        var rcount   = p.rCount ? p.rCount.toLocaleString('en-IN') : '0';
        var isLazy   = num > 20;

        var stockTag = stock > 0 && stock < 50
            ? '<span class="stock-tag' + (stock < 20 ? ' low' : '') + '">' + stock + ' left</span>'
            : '';
        var adTag    = (num % 7 === 0) ? '<div class="product-ad-tag">AD</div>' : '';

        html += '<div class="product-card' + (isLazy ? ' lazy-card' : '') + '">' +
            '<div class="product-image-wrap">' +
                (BADGES[badge] || '') +
                '<button class="wishlist-btn" onclick="event.stopPropagation();toggleWish(this,getCardId(this))">♡</button>' +
                '<img ' + (isLazy ? 'loading="lazy" ' : '') +
                    'src="Images/TopPicksForYou/' + num + '/1.avif" ' +
                    'alt="' + name.replace(/"/g,'') + '" ' +
                    'class="product-image" ' +
                    'onerror="window.smartImg&&smartImg(this)">' +
                '<div class="rating-badge"><span class="r-star">★</span>' + rating +
                    ' <span class="r-count">(' + Number(rcount).toLocaleString('en-IN') + ')</span></div>' +
                adTag +
            '</div>' +
            '<div class="product-info">' +
                '<div class="product-title-line">' +
                    '<span class="product-brand">' + brand + '</span>' +
                    '<span class="product-name">' + name + '</span>' +
                '</div>' +
                '<div class="product-price">' +
                    (mrp > price ? '<span class="price-original">₹' + mrp.toLocaleString('en-IN') + '</span>' : '') +
                    '<span class="price-current">₹' + price.toLocaleString('en-IN') + '</span>' +
                    (off > 0 ? '<span class="price-discount">' + off + '% off</span>' : '') +
                '</div>' +
                stockTag +
            '</div>' +
        '</div>';
    });

    grid.innerHTML = html;

    // Re-attach lazy observer and link clicks after render
    if (typeof initLazyCards === 'function') initLazyCards();
    if (typeof linkProducts  === 'function') linkProducts();
}

// ============================================================
//  Section configs — change pid/img here to update any section
// ============================================================
var SECTION_CONFIG = {
    // Sponsored — boAt + electronics products (all valid p1-p68)
    sponsored: [
        {pid:'p6',  img:'Images/TopPicksForYou/6/1.avif'},   // boAt Airdopes 141
        {pid:'p21', img:'Images/TopPicksForYou/21/1.avif'},  // boAt Airdopes Alpha
        {pid:'p24', img:'Images/TopPicksForYou/24/1.avif'},  // boAt Airdopes Loop OWS
        {pid:'p30', img:'Images/TopPicksForYou/30/1.avif'}   // boAt Smart Ring
    ],
    // Suggested For You — mixed popular categories
    suggested: [
        {pid:'p27', img:'Images/TopPicksForYou/27/1.avif'},  // NoiseFit Halo Smartwatch
        {pid:'p26', img:'Images/TopPicksForYou/26/1.avif'},  // Amazon Echo Pop
        {pid:'p46', img:'Images/TopPicksForYou/46/1.avif'},  // JBL Flip 6
        {pid:'p50', img:'Images/TopPicksForYou/50/1.avif'},  // WiFi Security Camera
        {pid:'p61', img:'Images/TopPicksForYou/61/1.avif'},  // Smart Glasses
        {pid:'p38', img:'Images/TopPicksForYou/38/1.avif'}   // Bluetooth Tracker
    ],
    // Offer Cards — Top Picks (horizontal scroll)
    offerCards: [
        {pid:'p32', img:'Images/TopPicksForYou/32/1.avif'},  // Gaming Mouse
        {pid:'p45', img:'Images/TopPicksForYou/45/1.avif'},  // Tripod
        {pid:'p39', img:'Images/TopPicksForYou/39/1.avif'},  // Power Bank
        {pid:'p47', img:'Images/TopPicksForYou/47/1.avif'},  // Smart LED Strip
        {pid:'p44', img:'Images/TopPicksForYou/44/1.avif'},  // USB-C Hub
        {pid:'p37', img:'Images/TopPicksForYou/37/1.avif'}   // Mobile Cooling Pad
    ],
    // You May Also Like — varied discovery
    youMayLike: [
        {pid:'p52', img:'Images/TopPicksForYou/52/1.avif'},  // Projector
        {pid:'p20', img:'Images/TopPicksForYou/20/1.avif'},  // Tablet
        {pid:'p34', img:'Images/TopPicksForYou/34/1.avif'},  // Elgato Stream Deck
        {pid:'p60', img:'Images/TopPicksForYou/60/1.avif'},  // Fitness Band
        {pid:'p63', img:'Images/TopPicksForYou/63/1.avif'},  // Crypto Wallet
        {pid:'p31', img:'Images/TopPicksForYou/31/1.avif'}   // Logitech Gaming Mouse
    ],
    // Upgrade to Premium — higher-end products
    premium: [
        {pid:'p7',  img:'Images/TopPicksForYou/7/1.avif'},   // boAt PartyPal Speaker
        {pid:'p15', img:'Images/TopPicksForYou/15/1.avif'},  // Rode NT1 Microphone
        {pid:'p34', img:'Images/TopPicksForYou/34/1.avif'},  // Elgato Stream Deck XL
        {pid:'p8',  img:'Images/TopPicksForYou/8/1.avif'},   // Dell Inspiron Laptop
        {pid:'p54', img:'Images/TopPicksForYou/54/1.avif'}   // 4K Mini Projector
    ]
};

// ── Sponsored ────────────────────────────────────────────────
function renderSponsored() {
    var el = document.getElementById('sponsoredProducts');
    if (!el) return;
    var fkP = window.FK_PRODUCTS || {};
    var html = '';
    SECTION_CONFIG.sponsored.forEach(function(item) {
        var p = fkP[item.pid] || {};
        var rawPrice = p.price || '';
        var cleaned  = String(rawPrice).replace(/[^0-9.]/g, '');
        var priceNum = 0;
        if (cleaned) { var parsed = Number(cleaned); priceNum = isNaN(parsed) ? 0 : parsed; }
        var pidNum = parseInt(item.pid.replace('p','')) || 1;
        var fallbackImg = 'Images/TopPicksForYou/' + pidNum + '/1.avif';
        html += '<div class="sponsored-card" onclick="window.location.href=\'product.php?id=' + item.pid + '\'" style="cursor:pointer;">' +
            '<img loading="lazy" class="lazy-fade" onload="this.classList.add(\'loaded\')" src="' + item.img + '" alt="' + (p.name||'') + '" onerror="this.classList.add(\'loaded\');this.onerror=null;this.src=\'' + fallbackImg + '\'">' +
            '<div class="sponsored-card-info">' +
                '<div class="sponsored-price">' + (priceNum ? '₹' + priceNum.toLocaleString('en-IN') : '') + '</div>' +
                '<div class="sponsored-subtitle">' + (p.name||'') + '</div>' +
            '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

// ── Suggested For You ────────────────────────────────────────
function renderSuggested() {
    var el = document.getElementById('suggestedGrid');
    if (!el) return;
    var fkP  = window.FK_PRODUCTS || {};
    var fkSP = {};
    (window.FK_SEARCH_PRODUCTS||[]).forEach(function(p){ fkSP[p.id]=p; });
    var BADGES = {bestseller:'<div class="tag-bestseller">Bestseller</div>', trending:'<div class="tag-trending">Trending</div>', hotdeal:'<div class="tag-hotdeal">Hot Deal</div>'};
    var html = '';
    SECTION_CONFIG.suggested.forEach(function(item) {
        var p  = fkP[item.pid]  || {};
        var sp = fkSP[item.pid] || {};
        // Parse numeric values from the product search object.  Strings may
        // contain commas, currency symbols or percentage signs; strip all
        // non-digit/non-decimal characters before parsing.  Default to zero on
        // failure to avoid NaN in the UI.
        var priceRaw = sp.price || 0;
        var mrpRaw   = sp.mrp   || 0;
        var offRaw   = sp.off   || 0;
        var price = 0;
        var mrp   = 0;
        var off   = 0;
        if (priceRaw !== null && priceRaw !== undefined) {
            var cp = String(priceRaw).replace(/[^0-9.]/g,'');
            var pn = Number(cp);
            price = isNaN(pn) ? 0 : pn;
        }
        if (mrpRaw !== null && mrpRaw !== undefined) {
            var cm = String(mrpRaw).replace(/[^0-9.]/g,'');
            var mn = Number(cm);
            mrp = isNaN(mn) ? 0 : mn;
        }
        // offRaw may include '%' or be a string; use parseInt
        off = parseInt(offRaw, 10);
        if (isNaN(off)) off = 0;
        var badge = (sp.badge||'').toLowerCase();
        var pidNum = parseInt(item.pid.replace('p','')) || 1;
        var fallbackImg = 'Images/TopPicksForYou/' + pidNum + '/1.avif';
        html += '<div class="suggested-card" onclick="window.location.href=\'product.php?id=' + item.pid + '\'" style="cursor:pointer;">' +
            (BADGES[badge]||'') +
            '<img loading="lazy" class="lazy-fade" onload="this.classList.add(\'loaded\')" src="' + item.img + '" alt="' + (p.name||'') + '" onerror="this.classList.add(\'loaded\');this.onerror=null;this.src=\'' + fallbackImg + '\'">' +
            '<div class="suggested-card-info">' +
                '<div class="suggested-card-name">' + (p.name||'') + '</div>' +
                '<div class="suggested-card-price">' +
                    '<span class="suggested-price-current">' + (price > 0 ? '₹' + price.toLocaleString('en-IN') : '') + '</span>' +
                    (mrp > price ? '<span class="suggested-price-original">₹' + mrp.toLocaleString('en-IN') + '</span>' : '') +
                '</div>' +
                (off > 0 ? '<div class="suggested-offer">' + off + '% off</div>' : '') +
            '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

// ── Offer Cards ───────────────────────────────────────────────
function renderOfferCards() {
    var el = document.getElementById('offerCards');
    if (!el) return;
    var fkSP = {};
    (window.FK_SEARCH_PRODUCTS||[]).forEach(function(p){ fkSP[p.id]=p; });
    var html = '';
    SECTION_CONFIG.offerCards.forEach(function(item) {
        var sp  = fkSP[item.pid] || {};
        // parse integer discount; if not numeric, default to 0
        var off = 0;
        if (sp.off !== undefined && sp.off !== null) {
            var o = parseInt(sp.off, 10);
            off = isNaN(o) ? 0 : o;
        }
        var pidNum = parseInt(item.pid.replace('p','')) || 1;
        var fallbackImg = 'Images/TopPicksForYou/' + pidNum + '/1.avif';
        html += '<div class="offer-card" onclick="window.location.href=\'product.php?id=' + item.pid + '\'" style="cursor:pointer;">' +
            '<img loading="lazy" src="' + item.img + '" alt="' + (sp.name||'') + '" onerror="this.onerror=null;this.src=\'' + fallbackImg + '\'">' +
            (off > 0 ? '<div class="offer-text">' + off + '% OFF</div>' : '') +
            '<div class="offer-subtitle">' + (sp.name||'') + '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

// ── You May Also Like ─────────────────────────────────────────
function renderYouMayLike() {
    var el = document.getElementById('youMayLikeScroll');
    if (!el) return;
    var fkP  = window.FK_PRODUCTS || {};
    var fkSP = {};
    (window.FK_SEARCH_PRODUCTS||[]).forEach(function(p){ fkSP[p.id]=p; });
    var html = '';
    SECTION_CONFIG.youMayLike.forEach(function(item) {
        var p  = fkP[item.pid]  || {};
        var sp = fkSP[item.pid] || {};
        var pidNum = parseInt(item.pid.replace('p','')) || 1;
        var fallbackImg = 'Images/TopPicksForYou/' + pidNum + '/1.avif';
        html += '<div class="recommendation-card" onclick="window.location.href=\'product.php?id=' + item.pid + '\'" style="cursor:pointer;">' +
            '<img loading="lazy" src="' + item.img + '" alt="' + (p.name||'') + '" onerror="this.onerror=null;this.src=\'' + fallbackImg + '\'">' +
            '<div class="recommendation-info">' +
                '<div class="recommendation-rating">' + (sp.rating||'4.0') + ' ★</div>' +
                '<div class="recommendation-name">' + (p.name||'') + '</div>' +
                (sp.off > 0 ? '<div class="recommendation-discount">' + sp.off + '% OFF</div>' : '') +
                '<div class="recommendation-price-group">' +
                    (sp.mrp > sp.price ? '<span class="recommendation-old-price">₹' + Number(sp.mrp).toLocaleString('en-IN') + '</span>' : '') +
                    '<span class="recommendation-current-price">₹' + Number(sp.price||0).toLocaleString('en-IN') + '</span>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

// ── Upgrade to Premium ────────────────────────────────────────
function renderPremium() {
    var el = document.getElementById('premiumScroll');
    if (!el) return;
    var fkP  = window.FK_PRODUCTS || {};
    var fkSP = {};
    (window.FK_SEARCH_PRODUCTS||[]).forEach(function(p){ fkSP[p.id]=p; });
    var html = '';
    SECTION_CONFIG.premium.forEach(function(item) {
        var p  = fkP[item.pid]  || {};
        var sp = fkSP[item.pid] || {};
        var pidNum = parseInt(item.pid.replace('p','')) || 1;
        var fallbackImg = 'Images/TopPicksForYou/' + pidNum + '/1.avif';
        var bankOffer = sp.price ? '₹' + Math.round(sp.price * 0.95).toLocaleString('en-IN') + ' with Bank offer' : '';
        html += '<div class="recommendation-card" onclick="window.location.href=\'product.php?id=' + item.pid + '\'" style="cursor:pointer;">' +
            '<img loading="lazy" src="' + item.img + '" alt="' + (p.name||'') + '" onerror="this.onerror=null;this.src=\'' + fallbackImg + '\'">' +
            '<div class="recommendation-info">' +
                '<div class="recommendation-rating">' + (sp.rating||'4.0') + ' ★</div>' +
                '<div class="recommendation-name">' + (p.name||'') + '</div>' +
                (sp.off > 0 ? '<div class="recommendation-discount">' + sp.off + '% OFF</div>' : '') +
                '<div class="recommendation-price-group">' +
                    (sp.mrp > sp.price ? '<span class="recommendation-old-price">₹' + Number(sp.mrp).toLocaleString('en-IN') + '</span>' : '') +
                    '<span class="recommendation-current-price">₹' + Number(sp.price||0).toLocaleString('en-IN') + '</span>' +
                '</div>' +
                (bankOffer ? '<div class="recommendation-bank-offer">' + bankOffer + '</div>' : '') +
            '</div>' +
        '</div>';
    });
    el.innerHTML = html;
}

// ============================================================
//  TICK-TOCK HOURLY DEALS SYSTEM
// ============================================================

// ── Shared helpers ─────────────────────────────────────────
function ttGetActiveSlot(deals)  { 
    var len = (deals && deals.length) ? deals.length : 3;
    // Cycle based on minutes — every 20 min slot changes, repeats infinitely
    return Math.floor(new Date().getMinutes() / (60/len)) % len; 
}
function ttSecsLeftInSlot(deals) { 
    var len = (deals && deals.length) ? deals.length : 3;
    var slotMins = Math.floor(60/len);
    var n = new Date(); 
    var minsInSlot = n.getMinutes() % slotMins;
    return (slotMins - 1 - minsInSlot)*60 + (59 - n.getSeconds()) + 1; 
}
function ttSecsLeftInHour() { const n=new Date(); return (59-n.getMinutes())*60+(59-n.getSeconds())+1; }
function ttFmt(secs)        { const m=Math.floor(secs/60),s=secs%60; return [m,s].map(v=>String(v).padStart(2,'0')); }

// ── Timer: runs immediately on script parse, no product data needed ──
(function() {
    const mmEl = document.getElementById('ttMM');
    const ssEl = document.getElementById('ttSS');
    if (!mmEl || !ssEl) return;
    let lastSlot = ttGetActiveSlot(window._ttDealsCache);
    function tick() {
        const deals = window._ttDealsCache || [];
        const [mm,ss] = ttFmt(ttSecsLeftInSlot(deals));
        mmEl.textContent = mm; ssEl.textContent = ss;
        const cur = ttGetActiveSlot(deals);
        if (cur !== lastSlot) {
            lastSlot = cur;
            // Re-render tick-tock cards on slot change using cached deals
            var hourEl = document.getElementById('hourlyDealsGrid');
            if (hourEl && window._ttDealsCache && window._ttDealsCache.length) {
                // Re-fetch to pick up any admin changes
                fetch('assets/data.php?f=ticktock_deals')
                    .then(function(r){ return r.ok ? r.json() : window._ttDealsCache; })
                    .then(function(deals){
                        window._ttDealsCache = deals;
                        if (typeof buildTickTockHTML === 'function') buildTickTockHTML(deals, ttGetActiveSlot(deals));
                    })
                    .catch(function(){
                        if (typeof buildTickTockHTML === 'function') buildTickTockHTML(window._ttDealsCache, ttGetActiveSlot());
                    });
            }
        }
    }
    tick();
    var _dealTimer = setInterval(tick, 1000);
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) { clearInterval(_dealTimer); }
        else { _dealTimer = setInterval(tick, 1000); }
    });
})();

function renderCuratedSections() {
    const searchProducts = Array.isArray(window.FK_SEARCH_PRODUCTS) ? window.FK_SEARCH_PRODUCTS : [];
    const detailProducts = window.FK_PRODUCTS || {};
    if (!searchProducts.length) return;

    const searchMap = new Map(searchProducts.map(p => [p.id, p]));

    const curatedSections = {
        topTrendyDealsGrid: [
            { id: 'p27', offer: 'From ₹1,299', label: 'NoiseFit Smartwatches' },
            { id: 'p6',  offer: 'Up to 80% off', label: 'boAt Earbuds' },
            { id: 'p46', offer: 'Under ₹9,999', label: 'JBL Speakers' },
            { id: 'p52', offer: 'From ₹7,999', label: 'Smart Projectors' },
            { id: 'p31', offer: 'From ₹2,495', label: 'Gaming Peripherals' },
            { id: 'p26', offer: 'Under ₹4,999', label: 'Smart Speakers' }
        ],
        peopleViewedGrid: ['p49', 'p12', 'p25', 'p51', 'p16', 'p36']
    };

    // ── Top Trendy Deals ───────────────────────────────────────
    function makePromoCard(item) {
        const product = searchMap.get(item.id);
        if (!product) return '';
        const n = parseInt(product.id.replace('p','')) || 1;
        return `<a class="deal-promo-card" href="product.php?id=${product.id}">
            <div class="deal-promo-card__media">
                <img src="Images/TopPicksForYou/${n}/1.avif" alt="${product.name}" loading="lazy" onerror="this.onerror=null;this.src='assets/placeholder.png'">
            </div>
            <div class="deal-promo-card__offer">${item.offer}</div>
            <div class="deal-promo-card__label">${item.label}</div>
        </a>`;
    }

    // ── People Also Viewed ─────────────────────────────────────
    function makeProductCard(productId) {
        const product = searchMap.get(productId);
        if (!product) return '';
        const detail = detailProducts[productId] || {};
        const price = '₹' + Number(product.price||0).toLocaleString('en-IN');
        const mrp = Number(product.mrp||0) > Number(product.price||0) ? '₹'+Number(product.mrp).toLocaleString('en-IN') : '';
        const off = Number(product.off||0) > 0 ? '↓'+product.off+'%' : '';
        const fullName = detail.name || product.name || '';
        const shortName = fullName.length > 42 ? fullName.slice(0,39)+'...' : fullName;
        const n = parseInt(product.id.replace('p','')) || 1;
        return `<a class="deal-product-card" href="product.php?id=${product.id}">
            <div class="deal-product-card__media">
                <img src="Images/TopPicksForYou/${n}/1.avif" alt="${fullName}" loading="lazy" onerror="this.onerror=null;this.src='assets/placeholder.png'">
                <div class="deal-product-card__rating"><b>${Number(product.rating||0).toFixed(1)} ★</b> (${Number(product.rCount||0).toLocaleString('en-IN')})</div>
            </div>
            <div class="deal-product-card__brand">${product.brand||(detail.brand||'').trim()||'Flipkart Pick'}</div>
            <div class="deal-product-card__name">${shortName}</div>
            <div class="deal-product-card__price">
                ${off?`<span class="deal-product-card__off">${off}</span>`:''}
                ${mrp?`<span class="deal-product-card__mrp">${mrp}</span>`:''}
                <span class="deal-product-card__sp">${price}</span>
            </div>
        </a>`;
    }

    // ── Render top trendy + people viewed ─────────────────────
    const topEl = document.getElementById('topTrendyDealsGrid');
    if (topEl) topEl.innerHTML = curatedSections.topTrendyDealsGrid.map(makePromoCard).join('');

    const viewedEl = document.getElementById('peopleViewedGrid');
    if (viewedEl) viewedEl.innerHTML = curatedSections.peopleViewedGrid.map(makeProductCard).join('');

    // ── Tick-Tock Cards — loaded from assets/ticktock_deals.json ──
    const hourEl = document.getElementById('hourlyDealsGrid');
    if (!hourEl) return;

    function buildTickTockHTML(deals, activeSlot) {
        if (!deals || !deals.length) {
            hourEl.innerHTML = '<div style="padding:20px;color:#888;font-size:13px">No hourly deals set yet — add them in Admin → Home Sections</div>';
            return;
        }
        var slotMins = Math.floor(60 / deals.length);
        hourEl.innerHTML = deals.map(function(item, idx) {
            const product = searchMap.get(item.id);
            if (!product) return '';
            const n = parseInt(product.id.replace('p','')) || 1;
            const isActive   = idx === activeSlot;
            const slotsUntil = (idx - activeSlot + deals.length) % deals.length;
            var unlockMin = (new Date().getMinutes() + slotsUntil * slotMins) % 60;
            const unlockStr  = new Date().getHours().toString().padStart(2,'0') + ':' + unlockMin.toString().padStart(2,'0');
            return `<div class="tt-card-wrap ${isActive ? 'tt-active' : 'tt-locked'}" id="tt-slot-${idx}">
                <a class="deal-promo-card" href="${isActive ? 'product.php?id='+product.id : '#'}" ${isActive?'':'onclick="return false"'}>
                    <div class="deal-promo-card__media" style="position:relative">
                        <img src="Images/TopPicksForYou/${n}/1.avif" alt="${product.name}" loading="lazy" onerror="this.onerror=null;this.src='assets/placeholder.png'">
                        ${!isActive ? `<div class="tt-lock-badge">
                            <span class="tt-lock-icon"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/></svg></span>
                            <div class="tt-unlock-in">Unlocks at</div>
                            <div class="tt-unlock-time">${unlockStr}</div>
                        </div>` : ''}
                    </div>
                    <div class="deal-promo-card__offer">${item.offer}</div>
                    <div class="deal-promo-card__label">${item.label}</div>
                </a>
            </div>`;
        }).join('');
    }

    // Fetch from ticktock_deals.json (set via admin), render and cache in one request
    window._ttDealsCache = null;
    fetch('assets/data.php?f=ticktock_deals')
        .then(function(r){ return r.ok ? r.json() : []; })
        .then(function(deals){
            window._ttDealsCache = deals;
            buildTickTockHTML(deals, ttGetActiveSlot(deals));
        })
        .catch(function(){
            window._ttDealsCache = [];
            buildTickTockHTML([], ttGetActiveSlot());
        });
}

// ── Deals of the Day ─────────────────────────────────────────
function renderDealsOfDay() {
    var el = document.getElementById('dealsOfDayGrid');
    if (!el) return;

    var fkSP = {};
    (window.FK_SEARCH_PRODUCTS || []).forEach(function(p){ fkSP[p.id] = p; });

    function esc(s) {
        return String(s || '').replace(/[&<>"]/g, function(ch) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[ch] || ch;
        });
    }

    function buildDotdHtml(deals) {
        if (!deals.length) { el.innerHTML = ''; return; }
        var html = deals.map(function(d, idx) {
            var pid = d.id || d.pid || '';
            var customDiscount = parseInt(d.discount, 10) || 0;
            var num = parseInt(String(pid).replace('p',''), 10) || (idx + 1);
            var p = fkSP[pid] || {};
            var off = customDiscount > 0 ? customDiscount : (parseInt(p.off, 10) || 0);
            var priceNum = Number(p.price || 0);
            var price = priceNum ? '₹' + priceNum.toLocaleString('en-IN') : '';
            var title = (p.name || p.title || 'Top Deal').trim();
            var shortTitle = title.length > 34 ? title.slice(0, 31) + '…' : title;
            var dealText = off > 0 ? off + '% off' : 'Top Deal';

            return '<a href="product.php?id=' + pid + '" class="deals-product-card" style="text-decoration:none;display:block;cursor:pointer;">' +
                '<div class="deals-product-media">' +
                    '<img loading="lazy" src="Images/TopPicksForYou/' + num + '/1.avif" alt="' + esc(title) + '" onerror="window.smartImg&&smartImg(this)">' +
                '</div>' +
                '<div class="deal-name">' + esc(shortTitle) + '</div>' +
                '<div class="deal-discount">' +
                    '<span class="deal-pct">' + esc(dealText) + '</span>' +
                    '<span class="deal-price">' + esc(price) + '</span>' +
                '</div>' +
            '</a>';
        }).join('');
        el.innerHTML = html;
        if (typeof linkProducts === 'function') linkProducts();
    }

    fetch('assets/data.php?f=dotd_deals')
        .then(function(r){ return r.ok ? r.json() : null; })
        .then(function(data) {
            if (data && Array.isArray(data) && data.length) {
                buildDotdHtml(data);
            } else {
                var fallback = ['p5','p19','p6','p7','p22','p47'];
                var deals = fallback.map(function(pid){ return {id:pid,discount:0}; })
                    .filter(function(d){ return !!fkSP[d.id]; });
                buildDotdHtml(deals);
            }
        })
        .catch(function() {
            var fallback = ['p5','p19','p6','p7','p22','p47'];
            var deals = fallback.map(function(pid){ return {id:pid,discount:0}; })
                .filter(function(d){ return !!fkSP[d.id]; });
            buildDotdHtml(deals);
        });
}

// ── Master render — called once on fk:ready ───────────────────
function renderAllSections() {
    renderProductGrid();
    renderSponsored();
    renderSuggested();
    renderOfferCards();
    renderYouMayLike();
    renderPremium();
    renderDealsOfDay();
    if (typeof renderCuratedSections === 'function') renderCuratedSections();
}

// Run after products.json fetch completes
document.addEventListener('fk:ready', renderAllSections);
// Fallback if already ready
if (Object.keys(window.FK_PRODUCTS || {}).length) renderAllSections();

    let currentSlide = 0;
    let totalSlides = 0;
    const slider = document.getElementById('bannerSlider');
    const dotsContainer = document.getElementById('bannerDots');
    const autoSlideInterval = 3000;
    let autoSlideTimer;

    function initBannerSlider() {
        totalSlides = slider.querySelectorAll('.banner-slide').length;
        if (!totalSlides) return;
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.className = 'dot';
            dot.onclick = () => goToSlide(i);
            dotsContainer.appendChild(dot);
        }
        currentSlide = 0;
        updateDots();
        clearInterval(autoSlideTimer);
        startAutoSlide();
        // Touch swipe
        let tx = 0;
        slider.addEventListener('touchstart', e => { tx = e.touches[0].clientX; }, {passive:true});
        slider.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - tx;
            if (Math.abs(dx) > 40) moveSlide(dx < 0 ? 1 : -1);
        }, {passive:true});
    }

    function updateDots() {
        dotsContainer.querySelectorAll('.dot').forEach((d,i) => d.classList.toggle('active', i === currentSlide));
    }

    function goToSlide(index) {
        currentSlide = index;
        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
        updateDots();
        resetAutoSlide();
    }

    function moveSlide(direction) {
        currentSlide += direction;
        if (currentSlide >= totalSlides) currentSlide = 0;
        else if (currentSlide < 0) currentSlide = totalSlides - 1;
        goToSlide(currentSlide);
    }

    function startAutoSlide() {
        clearInterval(autoSlideTimer);
        if (!document.hidden) autoSlideTimer = setInterval(() => moveSlide(1), autoSlideInterval);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideTimer);
        startAutoSlide();
    }

    // Pause/resume banner on tab visibility
    document.addEventListener('visibilitychange', function() {
        document.hidden ? clearInterval(autoSlideTimer) : startAutoSlide();
    });

    // Load banners dynamically from PHP — picks up admin uploads automatically
    (function loadBanners() {
        fetch('assets/banners.php?t=' + Date.now(), { cache: 'no-store' })
            .then(function(r){ return r.ok ? r.json() : null; })
            .then(function(banners) {
                if (!banners || !banners.length) throw new Error('no banners');
                slider.innerHTML = banners.map(function(b, idx) {
                    return '<div class="banner-slide"><img src="' + b.src + '" alt="Sale Banner ' + b.slot + '" loading="' + (idx === 0 ? 'eager' : 'lazy') + '" onerror="window.smartImg&&smartImg(this)"></div>';
                }).join('');
                initBannerSlider();
            })
            .catch(function() {
                // Hard fallback — build slides for slots 1-5 using smartImg
                var html = '';
                for (var i = 1; i <= 5; i++) {
                    html += '<div class="banner-slide"><img src="Images/BannerSlider/' + i + '.jpg" alt="Banner ' + i + '" loading="' + (i === 1 ? 'eager' : 'lazy') + '" onerror="window.smartImg&&smartImg(this)"></div>';
                }
                slider.innerHTML = html;
                initBannerSlider();
            });
    })();

    const bannerContainer = document.querySelector('.banner-container');
    if (bannerContainer) {
        bannerContainer.addEventListener('mouseenter', () => clearInterval(autoSlideTimer));
        bannerContainer.addEventListener('mouseleave', () => startAutoSlide());
    }

    // ============================================
    // MID CAROUSEL JAVASCRIPT (3 Carousels)
    // ============================================
    const midCarouselState = {};

    function moveMidCarousel(sliderId, dotsId, direction) {
        const slider = document.getElementById(sliderId);
        if (!slider) return;
        const dots = document.getElementById(dotsId).querySelectorAll('.mdot');
        const total = slider.children.length;
        if (!midCarouselState[sliderId]) midCarouselState[sliderId] = 0;
        midCarouselState[sliderId] += direction;
        if (midCarouselState[sliderId] >= total) midCarouselState[sliderId] = 0;
        if (midCarouselState[sliderId] < 0) midCarouselState[sliderId] = total - 1;
        slider.style.transform = `translateX(-${midCarouselState[sliderId] * 100}%)`;
        dots.forEach((d,i) => d.classList.toggle('active', i === midCarouselState[sliderId]));
    }

    function goMidSlide(sliderId, dotsId, index) {
        const slider = document.getElementById(sliderId);
        if (!slider) return;
        const dots = document.getElementById(dotsId).querySelectorAll('.mdot');
        midCarouselState[sliderId] = index;
        slider.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((d,i) => d.classList.toggle('active', i === index));
    }

    // Auto-slide all 3 mid carousels — paused when tab is hidden
    var midTimers = [
        setInterval(() => moveMidCarousel('midSlider1','midDots1',1), 3500),
        setInterval(() => moveMidCarousel('midSlider2','midDots2',1), 4000),
        setInterval(() => moveMidCarousel('midSlider3','midDots3',1), 4500)
    ];
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            midTimers.forEach(clearInterval);
        } else {
            midTimers = [
                setInterval(() => moveMidCarousel('midSlider1','midDots1',1), 3500),
                setInterval(() => moveMidCarousel('midSlider2','midDots2',1), 4000),
                setInterval(() => moveMidCarousel('midSlider3','midDots3',1), 4500)
            ];
        }
    });

    // ============================================
    // 🛍️ PRODUCT CLICK - OPENS PRODUCT PAGE
    // ============================================






    // ============================================================
    //  ★ RECENTLY VIEWED  – uses localStorage key: fk_rv
    //  ★ Saves product data when user clicks a product card
    //  ★ Renders saved items in the Recently Viewed section
    // ============================================================

    const RV_KEY = 'fk_rv';
    const RV_MAX = 10;

    // Build a product data map — reads from FK_PRODUCTS (name/brand) +
    // FK_SEARCH_PRODUCTS (mrp/off) so recently-viewed always has live data.
    function buildProductMap() {
        const map  = {};
        const fkP  = window.FK_PRODUCTS        || {};
        const fkSP = window.FK_SEARCH_PRODUCTS || [];

        // Build allProducts lookup for mrp/off
        const apById = {};
        fkSP.forEach(p => { apById[p.id] = p; });

        // Union of all known pids
        const pids = new Set([...Object.keys(fkP), ...fkSP.map(p => p.id)]);

        pids.forEach(pid => {
            const num  = parseInt(pid.replace('p',''), 10);
            const fp   = fkP[pid]   || {};
            const ap   = apById[pid]|| {};

            const rawPrice = ap.price || (parseInt((fp.price||'').replace(/[₹,]/g,''),10)||0);
            const rawMrp   = ap.mrp   || (parseInt((fp.mrp  ||'').replace(/[₹,]/g,''),10)||0);
            const rawOff   = ap.off   || (rawMrp > rawPrice && rawPrice > 0
                               ? Math.round((1 - rawPrice/rawMrp)*100) : 0);

            const card   = document.querySelector(`img[src*="TopPicksForYou/${num}/"]`)?.closest('.product-card');
            const rBadge = card?.querySelector('.rating-badge');
            const rating = rBadge ? rBadge.innerText.replace(/\s+/g,' ').trim() : '';

            map[pid] = {
                pid,
                brand: fp.brand || '',
                name:  fp.name  || ap.name || '',
                price: rawPrice ? '₹' + rawPrice.toLocaleString('en-IN') : '',
                orig:  rawMrp > rawPrice ? '₹' + rawMrp.toLocaleString('en-IN') : '',
                disc:  rawOff > 0 ? rawOff + '% off' : '',
                rating,
                img: 'Images/TopPicksForYou/' + num + '/1.avif'
            };
        });
        return map;
    }

    // Save a viewed product to localStorage
    function saveRecentlyViewed(pid) {
        const map  = buildProductMap();
        const item = map[pid];
        if (!item) return;
        item.ts = Date.now();
        let list = [];
        try { list = JSON.parse(localStorage.getItem(RV_KEY)) || []; } catch(e){}
        list = list.filter(x => x.pid !== pid);
        list.unshift(item);
        if (list.length > RV_MAX) list = list.slice(0, RV_MAX);
        localStorage.setItem(RV_KEY, JSON.stringify(list));
    }

    // Render Recently Viewed section from localStorage
    function renderRecentlyViewed() {
        const section = document.querySelector('.recently-viewed-section');
        const wrap    = document.getElementById('rv-scroll');
        if (!section || !wrap) return;

        let list = [];
        try { list = JSON.parse(localStorage.getItem(RV_KEY)) || []; } catch(e){}

        if (!list.length) {
            section.style.display = 'none';
            return;
        }

        section.style.display = '';   // show section
        wrap.innerHTML = list.map(item => `
            <div class="recommendation-card" style="cursor:pointer;"
                 onclick="window.location.href='product.php?id=${item.pid}'">
                <img loading="lazy" src="${item.img}" alt="${item.brand}"
                     onerror="this.style.opacity='0.3'">
                <div class="recommendation-info">
                    <div class="recommendation-rating">${item.rating}</div>
                    <div class="recommendation-name">${item.brand} ${item.name}</div>
                    <div class="recommendation-discount">${item.disc}</div>
                    <div class="recommendation-price-group">
                        <span class="recommendation-old-price">${item.orig}</span>
                        <span class="recommendation-current-price">${item.price}</span>
                    </div>
                </div>
            </div>`).join('');
    }

    // ── Patch linkProducts to SAVE on every product click ──
    function linkProducts() {
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.cursor = 'pointer';
            const fresh = card.cloneNode(true);
            card.parentNode.replaceChild(fresh, card);

            fresh.addEventListener('click', function(e) {
                if (e.target.tagName === 'BUTTON') return;
                const img   = fresh.querySelector('img[src*="Images/TopPicksForYou/"]');
                if (!img) return;
                const match = img.src.match(/TopPicksForYou\/(\d+)\//); if(match) match[1]='p'+match[1];
                if (!match) return;
                const pid = match[1];
                saveRecentlyViewed(pid);
                window.location.href = 'product.php?id=' + pid;
            });

            fresh.addEventListener('mouseenter', () => {
                fresh.style.boxShadow = '0 4px 16px rgba(0,0,0,0.15)';
                fresh.style.transform = 'translateY(-2px)';
                fresh.style.transition = 'all 0.2s';
            });
            fresh.addEventListener('mouseleave', () => {
                fresh.style.boxShadow = '';
                fresh.style.transform = '';
            });
        });
    }

    // ── Run ──
    renderRecentlyViewed();

    // ============================================================
    //  ❤️  WISHLIST – save/load from localStorage on index page
    // ============================================================
    function getCardId(btn) {
        const wrap = btn.closest('.product-card') || btn.closest('.product-image-wrap');
        if (!wrap) return null;
        const img = wrap.querySelector('img[src*="Images/TopPicksForYou/"]') || wrap.closest('.product-card')?.querySelector('img[src*="Images/TopPicksForYou/"]');
        if (!img) return null;
        const m = img.src.match(/TopPicksForYou\/(\d+)\//); return m ? 'p'+m[1] : null;
        }

    function getCardData(pid) {
        const card = document.querySelector(`img[src*="Images/TopPicksForYou/${pid.replace('p','')}/"]`)?.closest('.product-card');
        if (!card) return null;
        const name  = card.querySelector('.product-name')?.innerText?.trim() || '';
        const brand = card.querySelector('.product-brand')?.innerText?.trim() || '';
        const price = card.querySelector('.price-current')?.innerText?.replace('₹','').replace(/,/g,'').trim() || '0';
        const mrp   = card.querySelector('.price-original')?.innerText?.replace('₹','').replace(/,/g,'').trim() || price;
        return { id: pid, name, brand, price: parseInt(price)||0, mrp: parseInt(mrp)||0,
                 img: `Images/TopPicksForYou/${pid.replace('p','')}/1.avif`, added: Date.now() };
    }

    function toggleWish(btn, pid) {
        if (!pid) return;
        let list = [];
        try { list = JSON.parse(localStorage.getItem('fk_wishlist') || '[]'); } catch(e){}
        const idx = list.findIndex(i => i.id === pid);
        if (idx >= 0) {
            list.splice(idx, 1);
            btn.classList.remove('active');
            btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
            showWishToast('💔 Removed from wishlist');
        } else {
            const data = getCardData(pid);
            if (data) list.push(data);
            btn.classList.add('active');
            btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="#ff3f6c"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
            showWishToast('❤️ Added to wishlist!');
        }
        localStorage.setItem('fk_wishlist', JSON.stringify(list));
        updateCartBadge();
    document.addEventListener('fk:cart-sync', updateCartBadge);
    document.addEventListener('fk:wishlist-sync', function(){ restoreWishHearts(); });
    }

    function showWishToast(msg) {
        let t = document.getElementById('wishToast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'wishToast';
            t.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#323232;color:#fff;padding:10px 20px;border-radius:24px;font-size:13px;z-index:9999;opacity:0;transition:.3s;pointer-events:none;white-space:nowrap;font-family:sans-serif;';
            document.body.appendChild(t);
        }
        t.textContent = msg; t.style.opacity = '1';
        setTimeout(() => { t.style.opacity = '0'; }, 2200);
    }

    // Restore heart state from localStorage on page load
    (function restoreWishHearts() {
        let list = [];
        try { list = JSON.parse(localStorage.getItem('fk_wishlist') || '[]'); } catch(e){}
        list.forEach(item => {
            const img = document.querySelector(`img[src*="Images/TopPicksForYou/${item.id.replace('p','')}/"]`);
            if (!img) return;
            const btn = img.closest('.product-image-wrap')?.querySelector('.wishlist-btn');
            if (btn) { btn.classList.add('active'); btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="#ff3f6c"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>'; }
        });
    })();

    // ============================================================
    //  🛒  CART BADGE – shows item count on cart nav icon
    // ============================================================
    function updateCartBadge() {
        let cart = [];
        try { cart = JSON.parse(localStorage.getItem('flipkart_cart') || '[]'); } catch(e){}
        const total = cart.reduce((s, i) => s + (i.qty || 1), 0);
        // Cart badge lives only on header cart icon now (bottom nav cart removed)
        const headerCart = document.querySelector('a[href="cart.php"].header-icon');
        if (!headerCart) return;
        let badge = document.getElementById('cartNavBadge');
        if (!badge) {
            badge = document.createElement('span');
            badge.id = 'cartNavBadge';
            badge.style.cssText = 'position:absolute;top:-2px;right:-2px;background:#ff3f6c;color:#fff;font-size:9px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;border:2px solid #2874f0;';
            headerCart.style.position = 'relative';
            headerCart.appendChild(badge);
        }
        badge.textContent = total > 0 ? (total > 99 ? '99+' : total) : '';
        badge.style.display = total > 0 ? 'flex' : 'none';
    }
    updateCartBadge();
    document.addEventListener('fk:cart-sync', updateCartBadge);
    document.addEventListener('fk:wishlist-sync', function(){ restoreWishHearts(); });

    // linkProducts() and initLazyCards() are called by renderProductGrid()
    // after products.json loads and cards are injected into the DOM.

    // ============================================================
    //  📦  LAZY LOADING – IntersectionObserver for product cards
    // ============================================================
    function initLazyCards() {
        // Native lazy loading is already set on <img> tags.
        // IntersectionObserver handles the card fade-in reveal.
        if (!('IntersectionObserver' in window)) {
            // Fallback: just show everything immediately
            document.querySelectorAll('.lazy-card').forEach(el => el.classList.add('visible'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Stop watching once visible
                }
            });
        }, {
            // Pre-load cards 400px before they scroll into view — eliminates visible pop-in
            rootMargin: '0px 0px 400px 0px',
            threshold: 0
        });

        document.querySelectorAll('.lazy-card').forEach(card => observer.observe(card));
    }

    // Also make deal/recommendation/sponsored cards clickable
    ['deals-product-card','suggested-card','recommendation-card','sponsored-card','offer-card'].forEach(cls => {
        document.querySelectorAll('.' + cls).forEach(card => {
            const img = card.querySelector('img');
            if (!img) return;
            const m = img.src.match(/Products\/(p\d+)\//);
            if (m) {
                card.style.cursor = 'pointer';
                card.onclick = () => { window.location.href = 'product.php?id=' + m[1]; };
            }
        });
    });


    // ============================================================
    //  SALE COUNTDOWN TIMER — moved to own script tag below
    // ============================================================

</script>

<script>
// ⏱ SALE COUNTDOWN TIMER – 59 min, persists in localStorage
(function() {
    function startTimer() {
        var TIMER_KEY = 'fk_sale_end';
        var endTime = parseInt(localStorage.getItem(TIMER_KEY) || '0');
        var now = Date.now();
        if (!endTime || endTime <= now) {
            endTime = now + 59 * 60 * 1000;
            localStorage.setItem(TIMER_KEY, endTime);
        }
        var el = document.getElementById('sale-timer');
        if (!el) return;
        function tick() {
            var left = Math.max(0, endTime - Date.now());
            var m = Math.floor(left / 60000);
            var s = Math.floor((left % 60000) / 1000);
            el.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            if (left <= 0) { clearInterval(tid); el.textContent = '00:00'; }
        }
        tick();
        var tid = setInterval(tick, 1000);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startTimer);
    } else {
        startTimer();
    }
})();

</script>

<script>
// ── Sponsored Banner Dynamic Loader ──────────────────────────
(function() {
    function loadSponsoredBanner() {
        var img = document.getElementById('sponsoredBannerImg');
        if (!img) return;
        fetch('assets/banners.php?type=sponsored&t=' + Date.now(), { cache: 'no-store' })
            .then(function(r){ return r.ok ? r.json() : null; })
            .then(function(data) { if (data && data.src) img.src = data.src; })
            .catch(function(){});
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadSponsoredBanner);
    } else { loadSponsoredBanner(); }
})();

// ── Mid-page Ad Banner (above You May Like) ───────────────────
(function() {
    function loadMidBanner() {
        var img = document.getElementById('midAdBannerImg');
        if (!img) return;
        fetch('assets/banners.php?type=midbanner&t=' + Date.now(), { cache: 'no-store' })
            .then(function(r){ return r.ok ? r.json() : null; })
            .then(function(data) { if (data && data.src) img.src = data.src; })
            .catch(function(){});
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadMidBanner);
    } else { loadMidBanner(); }
})();


// ── SuperCoin Banner Dynamic Loader ─────────────────────────
(function() {
    function loadSupercoinBanner() {
        var img = document.getElementById('supercoinBannerImg');
        var wrap = document.getElementById('supercoinBannerWrap');
        if (!img || !wrap) return;
        wrap.style.display = 'none';
        img.onload = function() { wrap.style.display = 'block'; };
        img.onerror = function() { wrap.style.display = 'none'; };
        fetch('assets/banners.php?type=supercoin&t=' + Date.now(), { cache: 'no-store' })
            .then(function(r){ return r.ok ? r.json() : null; })
            .then(function(data) {
                if (data && data.src) {
                    img.src = data.src;
                } else {
                    wrap.style.display = 'none';
                }
            })
            .catch(function(){ wrap.style.display = 'none'; });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadSupercoinBanner);
    } else { loadSupercoinBanner(); }
})();

// ── End-of-page Ad Banner ─────────────────────────────────────
(function() {
    function loadAdBanner() {
        var img = document.getElementById('adBannerImg');
        if (!img) return;
        fetch('assets/banners.php?type=adbanner&t=' + Date.now(), { cache: 'no-store' })
            .then(function(r){ return r.ok ? r.json() : null; })
            .then(function(data) { if (data && data.src) img.src = data.src; })
            .catch(function(){});
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAdBanner);
    } else { loadAdBanner(); }
})();

/*
  The sponsored section auto-scroll was removed.
  This keeps the homepage from jumping downward after products load
  or when users come back from a product page.
*/
</script>

<!-- ── Pincode / Delivery Location Modal ───────────────── -->
<div class="pin-overlay" id="pinOverlay" onclick="if(event.target===this)closePinModal()">
  <div class="pin-sheet" style="position:relative">
    <button class="pin-close" onclick="closePinModal()"><svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
    <h3><svg viewBox="0 0 24 24" width="15" height="15" fill="#2874f0" style="vertical-align:-2px;margin-right:5px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> Select Delivery Location</h3>
    <p>Enter your pincode to see delivery options &amp; estimated dates</p>

    <div class="pin-quick" id="pinQuick"></div>

    <div class="pin-input-row">
      <input type="tel" id="pinModalInput" placeholder="Enter 6-digit pincode"
             maxlength="6" inputmode="numeric" pattern="[0-9]*"
             oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6); pinInputChange()">
      <button id="pinCheckBtn" onclick="checkPinModal()" disabled>Check</button>
    </div>
    <div class="pin-result" id="pinResult"></div>
    <div class="pin-saved-info" id="pinSavedInfo"></div>
  </div>
</div>


<script>
// ── Pincode & Delivery Location System ────────────────────────
(function () {

  // Restore saved pincode on page load
  var saved     = localStorage.getItem('fk_pincode');
  var savedCity = localStorage.getItem('fk_city');
  if (saved && savedCity) updateLocationBar(saved, savedCity);

  /* ── Open / Close ── */
  window.openPinModal = function () {
    var overlay = document.getElementById('pinOverlay');
    overlay.classList.add('open');
    renderPinQuick();
    var cur = localStorage.getItem('fk_pincode');
    var inp = document.getElementById('pinModalInput');
    inp.value = cur || '';
    document.getElementById('pinCheckBtn').disabled = !cur || cur.length !== 6;
    clearResult();
    var info = document.getElementById('pinSavedInfo');
    info.textContent = cur
      ? 'Delivering to: ' + (localStorage.getItem('fk_city') || cur)
      : '';
    setTimeout(function () { inp.focus(); inp.select(); }, 120);
  };

  window.closePinModal = function () {
    document.getElementById('pinOverlay').classList.remove('open');
  };

  /* ── Input handler: enable button + auto-fire at 6 digits ── */
  window.pinInputChange = function () {
    var val = document.getElementById('pinModalInput').value;
    document.getElementById('pinCheckBtn').disabled = val.length !== 6;
    clearResult();
    if (val.length === 6) checkPinModal();   // ← auto-lookup!
  };

  /* ── Main lookup ── */
  window.checkPinModal = async function () {
    var pin = document.getElementById('pinModalInput').value.trim();
    if (!/^\d{6}$/.test(pin)) {
      showPinResult('err', '❌ Please enter a valid 6-digit pincode.');
      return;
    }

    var btn = document.getElementById('pinCheckBtn');
    btn.disabled = true;
    btn.textContent = '⏳';
    showPinResult('loading', '🔍 Fetching location…');

    var cityName  = '';
    var stateName = '';
    var areaName  = '';
    var success   = false;

    /* ── 1. India Post API (free, no key needed) ── */
    try {
      var controller = new AbortController();
      var timer = setTimeout(function(){ controller.abort(); }, 5000);
      var resp = await fetch(
        'https://api.postalpincode.in/pincode/' + pin,
        { signal: controller.signal }
      );
      clearTimeout(timer);
      var data = await resp.json();
      if (data && data[0] && data[0].Status === 'Success' &&
          data[0].PostOffice && data[0].PostOffice.length > 0) {
        var po    = data[0].PostOffice[0];
        areaName  = po.Name    || '';
        cityName  = po.District|| po.Division || po.Name || pin;
        stateName = po.State   || '';
        success   = true;

        /* Show a rich card with all post offices */
        var offices = data[0].PostOffice.slice(0, 4);
        var areaList = offices.map(function(o){ return o.Name; }).join(' • ');
        var displayName = cityName + (stateName ? ', ' + stateName : '');

        localStorage.setItem('fk_pincode', pin);
        localStorage.setItem('fk_city', displayName);
        saveRecentPin(pin, displayName);
        updateLocationBar(pin, displayName);

        btn.disabled   = false;
        btn.textContent = 'Check';

        showPinResult('ok',
          '✅  ' + displayName + '\n' +
          '📮  Areas: ' + areaList + '\n' +
          '🚚  Free delivery available!'
        );
        setTimeout(closePinModal, 2000);
        return;
      }
    } catch (e) { /* network error or timeout — fall through */ }

    /* ── 2. Offline fallback lookup ── */
    var fallback = getPincodeInfo(pin);
    if (fallback) {
      cityName  = fallback.city;
      stateName = fallback.state;
      success   = true;
    }

    btn.disabled    = false;
    btn.textContent = 'Check';

    if (success) {
      var displayName = cityName + (stateName ? ', ' + stateName : '');
      localStorage.setItem('fk_pincode', pin);
      localStorage.setItem('fk_city', displayName);
      saveRecentPin(pin, displayName);
      updateLocationBar(pin, displayName);
      showPinResult('ok',
        '✅  ' + displayName + '\n🚚  Free delivery available!'
      );
      setTimeout(closePinModal, 1800);
    } else {
      showPinResult('err', '❌ Pincode not found. Please try another.');
    }
  };

  /* ── Helpers ── */
  function clearResult() {
    var el = document.getElementById('pinResult');
    el.className    = 'pin-result';
    el.textContent  = '';
    el.style.whiteSpace = '';
  }

  function showPinResult(type, msg) {
    var el = document.getElementById('pinResult');
    el.className = 'pin-result' + (type ? ' ' + type : '');
    el.style.whiteSpace = 'pre-line';
    el.textContent = msg;
  }

  function updateLocationBar(pin, city) {
    var el = document.getElementById('loc-label');
    if (el) {
      el.innerHTML = 'Deliver to <strong>' + city + ' ' + pin + '</strong>';
    }
    var link = document.querySelector('.location-link');
    if (link) link.textContent = 'Change ›';
  }

  function saveRecentPin(pin, city) {
    var recents = JSON.parse(localStorage.getItem('fk_recent_pins') || '[]');
    recents = recents.filter(function (r) { return r.pin !== pin; });
    recents.unshift({ pin: pin, city: city });
    recents = recents.slice(0, 4);
    localStorage.setItem('fk_recent_pins', JSON.stringify(recents));
  }

  function renderPinQuick() {
    var recents = JSON.parse(localStorage.getItem('fk_recent_pins') || '[]');
    var wrap = document.getElementById('pinQuick');
    if (!recents.length) { wrap.innerHTML = ''; return; }
    wrap.innerHTML = '<span style="font-size:11px;color:#888;margin-right:6px;align-self:center">Recent:</span>';
    recents.forEach(function (r) {
      var btn = document.createElement('button');
      btn.textContent = r.pin;
      btn.title = r.city;
      btn.addEventListener('click', function () { applyPin(r.pin, r.city); });
      wrap.appendChild(btn);
    });
  }

  window.applyPin = function (pin, city) {
    localStorage.setItem('fk_pincode', pin);
    localStorage.setItem('fk_city', city);
    updateLocationBar(pin, city);
    saveRecentPin(pin, city);
    showPinResult('ok', '✅  ' + city + '\n🚚  Free delivery available!');
    setTimeout(closePinModal, 1200);
  };

  /* ── Keyboard shortcuts ── */
  document.addEventListener('keydown', function (e) {
    var open = document.getElementById('pinOverlay').classList.contains('open');
    if (!open) return;
    if (e.key === 'Escape') closePinModal();
  });

  /* ── Offline pincode → city/state table (all 29 states + UTs) ── */
  function getPincodeInfo(pin) {
    var p = parseInt(pin.substring(0, 3), 10);
    var zones = [
      // Delhi
      {r:[110,110],c:'New Delhi',s:'Delhi'},
      {r:[111,119],c:'Delhi',s:'Delhi'},
      // Haryana
      {r:[121,136],c:'Haryana',s:'Haryana'},
      // Punjab / Chandigarh
      {r:[140,160],c:'Punjab',s:'Punjab'},
      {r:[160,160],c:'Chandigarh',s:'Chandigarh'},
      // Himachal Pradesh
      {r:[171,177],c:'Shimla',s:'Himachal Pradesh'},
      // J&K / Ladakh
      {r:[180,193],c:'Jammu',s:'Jammu & Kashmir'},
      {r:[194,194],c:'Srinagar',s:'Jammu & Kashmir'},
      {r:[195,195],c:'Leh',s:'Ladakh'},
      // Uttar Pradesh
      {r:[201,204],c:'Noida / Ghaziabad',s:'Uttar Pradesh'},
      {r:[205,212],c:'Agra',s:'Uttar Pradesh'},
      {r:[213,221],c:'Allahabad',s:'Uttar Pradesh'},
      {r:[226,226],c:'Lucknow',s:'Uttar Pradesh'},
      {r:[227,246],c:'Uttar Pradesh',s:'Uttar Pradesh'},
      // Uttarakhand
      {r:[246,263],c:'Dehradun',s:'Uttarakhand'},
      // Rajasthan
      {r:[301,345],c:'Jaipur',s:'Rajasthan'},
      // Gujarat
      {r:[360,396],c:'Ahmedabad',s:'Gujarat'},
      {r:[394,396],c:'Surat',s:'Gujarat'},
      // Maharashtra
      {r:[400,410],c:'Mumbai',s:'Maharashtra'},
      {r:[411,412],c:'Pune',s:'Maharashtra'},
      {r:[413,431],c:'Maharashtra',s:'Maharashtra'},
      {r:[440,444],c:'Nagpur',s:'Maharashtra'},
      // Madhya Pradesh
      {r:[450,481],c:'Bhopal',s:'Madhya Pradesh'},
      {r:[462,462],c:'Bhopal',s:'Madhya Pradesh'},
      // Chhattisgarh
      {r:[490,497],c:'Raipur',s:'Chhattisgarh'},
      // Andhra Pradesh / Telangana
      {r:[500,509],c:'Hyderabad',s:'Telangana'},
      {r:[515,535],c:'Andhra Pradesh',s:'Andhra Pradesh'},
      {r:[520,521],c:'Vijayawada',s:'Andhra Pradesh'},
      {r:[530,530],c:'Visakhapatnam',s:'Andhra Pradesh'},
      // Karnataka
      {r:[560,591],c:'Bangalore',s:'Karnataka'},
      {r:[575,577],c:'Mangalore',s:'Karnataka'},
      // Tamil Nadu
      {r:[600,614],c:'Chennai',s:'Tamil Nadu'},
      {r:[620,641],c:'Tamil Nadu',s:'Tamil Nadu'},
      {r:[641,641],c:'Coimbatore',s:'Tamil Nadu'},
      // Kerala
      {r:[670,695],c:'Kerala',s:'Kerala'},
      {r:[682,682],c:'Kochi',s:'Kerala'},
      {r:[695,695],c:'Thiruvananthapuram',s:'Kerala'},
      // Goa
      {r:[403,403],c:'Panaji',s:'Goa'},
      // Odisha
      {r:[751,770],c:'Bhubaneswar',s:'Odisha'},
      // West Bengal
      {r:[700,743],c:'Kolkata',s:'West Bengal'},
      // Bihar
      {r:[800,855],c:'Patna',s:'Bihar'},
      // Jharkhand
      {r:[814,835],c:'Ranchi',s:'Jharkhand'},
      // Assam / Northeast
      {r:[781,788],c:'Guwahati',s:'Assam'},
      {r:[790,798],c:'Northeast India',s:'Arunachal Pradesh'},
      {r:[793,793],c:'Shillong',s:'Meghalaya'},
      {r:[795,795],c:'Imphal',s:'Manipur'},
      {r:[796,796],c:'Aizawl',s:'Mizoram'},
      {r:[797,797],c:'Kohima',s:'Nagaland'},
      {r:[799,799],c:'Agartala',s:'Tripura'},
    ];
    for (var i = 0; i < zones.length; i++) {
      var z = zones[i];
      if (p >= z.r[0] && p <= z.r[1]) return { city: z.c, state: z.s };
    }
    var stateMap = {'1':'North India','2':'Uttar Pradesh','3':'Rajasthan',
      '4':'West India','5':'South India','6':'South India',
      '7':'East India','8':'Bihar / Jharkhand','9':'Northeast India'};
    return { city: 'India', state: stateMap[pin[0]] || 'India' };
  }

})();
</script>
<script>
/* ── Lazy-load off-screen images with IntersectionObserver ── */
(function(){
    if (!('IntersectionObserver' in window)) return;
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if (e.isIntersecting){
                var img = e.target;
                if (img.dataset.src){ img.src = img.dataset.src; delete img.dataset.src; }
                io.unobserve(img);
            }
        });
    }, { rootMargin: '300px' });
    function observe(){ document.querySelectorAll('img[loading="lazy"]').forEach(function(img){ io.observe(img); }); }
    // Run after dynamic cards render
    window.addEventListener('load', observe);
    document.addEventListener('DOMContentLoaded', observe);
    window._reobserveLazy = observe; // call after dynamic render
})();
</script>

    <script src="assets/shield.js" defer></script>
    <script src="assets/shared.js?v=20260320" defer></script>
</body>
</html>
