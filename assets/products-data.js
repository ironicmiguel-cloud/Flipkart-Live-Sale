// ============================================================
//  products-data.js  —  loads products.json and exposes:
//    window.FK_PRODUCTS        → used by product.php
//    window.FK_SEARCH_PRODUCTS → used by search + index
// ============================================================
(function () {
    function buildFKProducts(products) {
        const map = {};
        products.forEach(p => {
            map[p.id] = {
                brand:  p.brand  || '',
                name:   p.name   || '',
                price:  '₹' + p.price.toLocaleString('en-IN'),
                mrp:    p.mrp > p.price ? '₹' + p.mrp.toLocaleString('en-IN') : '',
                off:    p.off > 0 ? p.off + '% off' : '',
                badge:  p.badge  || '',
                stock:  p.stock  || 0,
                rating: String(p.rating),
                rCount: p.rCount.toLocaleString('en-IN') + ' Ratings & ' + Math.floor(p.rCount / 3).toLocaleString('en-IN') + ' Reviews',
                desc:   p.description || '',
                images: p.images || [],
                highlights: [
                    ['Brand',    p.brand  || 'Unknown Brand'],
                    ['Model',    p.model || (p.brand + ' ' + p.id).trim()],
                    ['Warranty', '1 Year Manufacturer Warranty'],
                    ['In The Box', 'Product + User Manual + Warranty Card'],
                ]
            };
        });
        return map;
    }

    const _base = (function() {
        const s = document.querySelector('script[src*="products-data"]');
        if (s) return s.src.replace(/assets\/products-data\.js.*$/, '');
        return '/';
    })();
    fetch(_base + 'assets/products.php', { cache: 'no-cache' })
        .then(r => r.json())
        .then(products => {
            window.FK_SEARCH_PRODUCTS = products;
            window.FK_PRODUCTS        = buildFKProducts(products);
            document.dispatchEvent(new Event('fk:ready'));
        })
        .catch(err => {
            console.error('products.json load failed:', err);
            window.FK_PRODUCTS        = {};
            window.FK_SEARCH_PRODUCTS = [];
            document.dispatchEvent(new Event('fk:ready'));
        });
})();
