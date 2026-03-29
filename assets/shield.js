// ============================================================
//  IronicGuru Shield — Anti-Reverse-Engineering Protection
//  Included via: <script src="assets/shield.js"></script>
//  Apply to: all pages
// ============================================================
(function () {
    'use strict';

    // ── 1. Block right-click context menu ────────────────────
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        return false;
    });

    // ── 2. Block keyboard shortcuts ──────────────────────────
    document.addEventListener('keydown', function (e) {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault(); return false;
        }
        // Ctrl+Shift+I / Cmd+Opt+I (DevTools)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'i')) {
            e.preventDefault(); return false;
        }
        // Ctrl+Shift+J (Console)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'J' || e.key === 'j')) {
            e.preventDefault(); return false;
        }
        // Ctrl+Shift+C (Inspect element)
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'C' || e.key === 'c')) {
            e.preventDefault(); return false;
        }
        // Ctrl+U (View source)
        if ((e.ctrlKey || e.metaKey) && (e.key === 'u' || e.key === 'U')) {
            e.preventDefault(); return false;
        }
        // Ctrl+S (Save page)
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault(); return false;
        }
        // Ctrl+A (Select all) — only block on product pages
        if ((e.ctrlKey || e.metaKey) && (e.key === 'a' || e.key === 'A')) {
            if (document.body.classList.contains('no-select')) {
                e.preventDefault(); return false;
            }
        }
    });

    // ── 3. DevTools detection via size diff ──────────────────
    var _dt = false;
    var _threshold = 160;

    function _checkDevTools() {
        var widthDiff  = window.outerWidth  - window.innerWidth;
        var heightDiff = window.outerHeight - window.innerHeight;
        if (widthDiff > _threshold || heightDiff > _threshold) {
            if (!_dt) {
                _dt = true;
                _onDevToolsOpen();
            }
        } else {
            _dt = false;
        }
    }

    function _onDevToolsOpen() {
        // Clear sensitive DOM data when DevTools opens
        var upiRef = document.getElementById('_upi_ref');
        if (upiRef) {
            upiRef.dataset.val = '';
            upiRef.dataset.mcc = '';
            upiRef.dataset.tr  = '';
        }
        // Blur the page
        document.body.style.filter = 'blur(8px)';
        setTimeout(function () {
            document.body.style.filter = '';
        }, 3000);
    }

    setInterval(_checkDevTools, 1000);

    // ── 4. Debugger trap (slows automated extraction) ────────
    // Runs in a separate function to avoid breaking normal execution
    (function _debuggerTrap() {
        try {
            var _t = new Date();
            // This only fires if someone steps through the debugger
            (function () { /* trap */ })['constructor']('debugger')['call']();
            if (new Date() - _t > 100) {
                document.body.style.filter = 'blur(8px)';
                setTimeout(function () { document.body.style.filter = ''; }, 2000);
            }
        } catch (e) { /* silent */ }
    });
    // Note: trap function is defined but intentionally NOT auto-called
    // to avoid blocking normal page load — only triggered on inspect

    // ── 5. Disable text selection on protected elements ──────
    // Applied via CSS class — see shield.css or inline below
    var style = document.createElement('style');
    style.textContent = [
        '.no-select, .no-select * {',
        '  -webkit-user-select: none !important;',
        '  -moz-user-select: none !important;',
        '  -ms-user-select: none !important;',
        '  user-select: none !important;',
        '}',
        // Block image drag
        'img { -webkit-user-drag: none; user-drag: none; pointer-events: none; }',
        // Allow pointer on product cards and buttons
        '.product-card img, .product-image, button img, a img {',
        '  pointer-events: auto;',
        '}',
    ].join('\n');
    document.head.appendChild(style);

    // ── 6. Block drag-and-drop image theft ───────────────────
    document.addEventListener('dragstart', function (e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });

})();
