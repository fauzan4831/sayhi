// /sayhi/javascript/script.js
(function () {
    const body = document.body;

    // ===== Page fade-in
    body.classList.add('page-fade');
    window.addEventListener('DOMContentLoaded', () => requestAnimationFrame(() => {
        body.classList.add('ready');
    }));

    // ===== Topbar helpers (opsional dipakai halaman lain)
    const TOPBAR = document.getElementById('topbar') || (function () {
        const bar = document.createElement('div'); bar.id = 'topbar'; document.body.appendChild(bar); return bar;
    })();
    window.SayHiTopbar = {
        start() { TOPBAR.style.width = '35%'; requestAnimationFrame(() => TOPBAR.style.width = '85%'); },
        done() { TOPBAR.style.width = '100%'; setTimeout(() => TOPBAR.style.width = '0%', 250); }
    };

    // ===== Inject navbar kecuali kalau body.hide-navbar
    const placeholder = document.getElementById('sayhi-navbar');
    if (placeholder && !body.classList.contains('hide-navbar')) {
        fetch('components/navbar.html', { cache: 'no-store' })
            .then(r => r.text())
            .then(html => {
                placeholder.innerHTML = html;

                // set active nav by path
                const path = (location.pathname.split('/').pop() || 'index.html').toLowerCase();
                const map = {
                    'data.html': 'data',
                    'laporan.html': 'laporan',
                    'akun.html': 'akun'
                };
                const key = map[path];
                if (key) {
                    const link = placeholder.querySelector(`a[data-nav="${key}"]`);
                    if (link) link.classList.add('active');
                }

                // enable bootstrap toggler if needed (when loaded after)
                if (typeof bootstrap === 'undefined') return;
            })
            .catch(() => { /* diamkan kalau gagal */ });
    }
})();
