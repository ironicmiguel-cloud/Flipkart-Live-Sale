/**
 * tg_sender.js — safe wrapper that sends Telegram messages through the server.
 */
(function() {
    async function tgSend(data) {
        try {
            const payload = {
                csrf: (window.FK_ADDRESS_CSRF || ''),
                data: data || {}
            };
            const res = await fetch('assets/telegram_send.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            return json && json.ok === true;
        } catch(e) {
            console.warn('[tgSend] Network error:', e);
            return false;
        }
    }
    window.tgSend = tgSend;
})();
