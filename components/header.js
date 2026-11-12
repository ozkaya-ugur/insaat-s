// ==========================================
// BUYAPI - DİNAMİK HEADER & MOBİL MENÜ MODÜLÜ
// ==========================================

function getHeaderApiBase() {
    if (typeof window !== 'undefined' && window.location && window.location.origin && !window.location.origin.startsWith('file:')) {
        return window.location.origin;
    }
    return (typeof API_URL !== 'undefined') ? API_URL.replace(/\/api\/?$/, '') : 'http://127.0.0.1:8000';
}

function normalizeHref(href) {
    if (!href) return '#';
    if (/^https?:\/\//i.test(href)) return href;
    if (href.startsWith('/')) return href;
    return `/${href}`;
}

function getHardcodedMenu() {
    return [
        { slug: 'anasayfa', title: 'ANASAYFA', url: '/index.html' },
        { slug: 'kurumsal', title: 'KURUMSAL', url: '/kurumsal.html' },
        { slug: 'projeler', title: 'PROJELER', url: '/projeler.html' },
        { slug: 'iletisim', title: 'İLETİŞİM', url: '/iletisim.html' },
    ];
}

function markActiveLinks() {
    const current = (window.location.pathname || '/').replace(/\/$/, '');
    document.querySelectorAll('#desktopNav a.nav-link, #mobileNav a.mobile-nav-link').forEach((a) => {
        const href = (a.getAttribute('href') || '').replace(/\/$/, '');
        if (!href) return;
        if (href === current || (href === '/index.html' && (current === '' || current === '/'))) {
            a.classList.add('active');
        } else {
            a.classList.remove('active');
        }
    });
}

function buildMenuLinks(menuItems) {
    const desktopNav = document.getElementById('desktopNavLinks');
    const mobileNav = document.getElementById('mobileNavLinks');
    if (!desktopNav || !mobileNav) return;

    desktopNav.innerHTML = '';
    mobileNav.innerHTML = '';

    menuItems.forEach((item) => {
        const href = normalizeHref(item.url);

        const dLink = document.createElement('a');
        dLink.href = href;
        dLink.className = 'nav-link';
        dLink.innerHTML = `<span>${(item.title || item.slug || '').toUpperCase()}</span>`;
        desktopNav.appendChild(dLink);

        const mLink = document.createElement('a');
        mLink.href = href;
        mLink.className = 'mobile-nav-link';
        mLink.textContent = (item.title || item.slug || '').toUpperCase();
        mobileNav.appendChild(mLink);
    });

    markActiveLinks();
}

async function loadHeaderMenu() {
    const curLang = localStorage.getItem('selectedLang') || 'tr';
    const apiBase = getHeaderApiBase();
    const current = (window.location.pathname || '/').replace(/^\//, '');

    try {
        const res = await fetch(`${apiBase}/api/pages?menu=1&lang=${encodeURIComponent(curLang)}&current=${encodeURIComponent(current)}&t=${Date.now()}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const pages = await res.json();
        if (!Array.isArray(pages) || pages.length === 0) throw new Error('Boş menü');
        buildMenuLinks(pages);
    } catch (e) {
        console.warn('Dinamik menü yüklenemedi, fallback kullanılacak:', e);
        buildMenuLinks(getHardcodedMenu());
    }
}

function initHeaderComponent() {
    const target = document.getElementById('site-header');
    if (!target) return;
    if (target.dataset.headerInitialized === 'true') return;
    target.dataset.headerInitialized = 'true';

    target.innerHTML = `
    <header class="header">
        <div class="header-inner">
            <a href="/index.html" class="brand-logo">
                <img src="/images/l-white.svg" alt="BUYAPI">
            </a>

            <nav class="nav desktop-nav" id="desktopNav">
                <span id="desktopNavLinks" style="display:contents"></span>
            </nav>

            <div class="header-action desktop-action">
                <div class="lang-selector-dropdown">
                    <button type="button" class="lang-curr-btn" id="langDropdownBtn">
                        <i class="fa-solid fa-globe"></i>
                        <span id="currentLangText">TR</span>
                        <i class="fa-solid fa-chevron-down lang-arrow"></i>
                    </button>
                    <div class="lang-dropdown-menu" id="langDropdownMenu">
                        <a href="javascript:void(0)" onclick="setLanguage('tr')" class="lang-opt-link active" data-lang="tr"><span class="flag-icon">🇹🇷</span> TÜRKÇE (TR)</a>
                        <a href="javascript:void(0)" onclick="setLanguage('en')" class="lang-opt-link" data-lang="en"><span class="flag-icon">🇬🇧</span> ENGLISH (EN)</a>
                        <a href="javascript:void(0)" onclick="setLanguage('ar')" class="lang-opt-link" data-lang="ar"><span class="flag-icon">🇸🇦</span> العربية (AR)</a>
                    </div>
                </div>

                <a href="/kentsel-donusum.html" class="nav-btn btn-kentsel-header" title="Kentsel Dönüşüm Bilgi ve Başvuru">
                    <i class="fa-solid fa-building-shield"></i>
                    <span data-i18n="nav.urban_renewal">KENTSEL DÖNÜŞÜM</span>
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>

            <button class="mobile-toggle-btn" id="mobileToggleBtn" aria-label="Menüyü Aç/Kapat" type="button">
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <button class="mobile-close-btn" id="mobileCloseBtn" aria-label="Menüyü Kapat" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="mobile-menu-content">
            <nav class="mobile-nav" id="mobileNav">
                <div id="mobileNavLinks" style="display:flex;flex-direction:column;gap:24px;"></div>

                <div class="mobile-lang-wrap" id="mobileLangWrap">
                    <a href="javascript:void(0)" onclick="setLanguage('tr')" class="m-lang-btn active" data-lang="tr">TR</a>
                    <a href="javascript:void(0)" onclick="setLanguage('en')" class="m-lang-btn" data-lang="en">EN</a>
                    <a href="javascript:void(0)" onclick="setLanguage('ar')" class="m-lang-btn" data-lang="ar">AR</a>
                </div>

                <a href="/kentsel-donusum.html" class="mobile-nav-btn btn-kentsel-mob"><i class="fa-solid fa-building-shield"></i> <span data-i18n="nav.urban_renewal">KENTSEL DÖNÜŞÜM</span> <i class="fa-solid fa-arrow-right"></i></a>
            </nav>
            <div class="mobile-menu-footer">
                <p>&copy; 2026 BUYAPI İNŞAAT A.Ş.</p>
                <a href="tel:+900000000000" id="headerPhoneLink"><i class="fa-solid fa-phone"></i> <span id="headerPhoneText">Telefon</span></a>
            </div>
        </div>
    </div>
    `;

    const mobileToggleBtn = document.getElementById('mobileToggleBtn');
    const mobileCloseBtn = document.getElementById('mobileCloseBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    function closeMobileMenu() {
        mobileMenuOverlay?.classList.remove('active');
        mobileToggleBtn?.classList.remove('active');
        document.body.classList.remove('menu-open');
        document.body.style.overflow = '';
    }

    function openMobileMenu() {
        mobileMenuOverlay?.classList.add('active');
        mobileToggleBtn?.classList.add('active');
        document.body.classList.add('menu-open');
        document.body.style.overflow = 'hidden';
    }

    mobileToggleBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (mobileMenuOverlay?.classList.contains('active')) closeMobileMenu();
        else openMobileMenu();
    });

    mobileCloseBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        closeMobileMenu();
    });

    mobileMenuOverlay?.addEventListener('click', (e) => {
        if (e.target === mobileMenuOverlay) closeMobileMenu();
    });

    document.addEventListener('click', (e) => {
        const langBtn = document.getElementById('langDropdownBtn');
        const langMenu = document.getElementById('langDropdownMenu');
        if (!langBtn || !langMenu) return;

        if (langBtn.contains(e.target)) {
            langMenu.classList.toggle('active');
            langBtn.classList.toggle('open');
            return;
        }

        langMenu.classList.remove('active');
        langBtn.classList.remove('open');
    });

    async function loadDynamicLanguages() {
        try {
            const apiBase = getHeaderApiBase();
            const res = await fetch(`${apiBase}/api/languages?t=${Date.now()}`);
            if (!res.ok) return;
            const langs = await res.json();
            if (!Array.isArray(langs) || langs.length === 0) return;

            const menu = document.getElementById('langDropdownMenu');
            const mWrap = document.getElementById('mobileLangWrap');
            const curLang = localStorage.getItem('selectedLang') || 'tr';

            if (menu) {
                menu.innerHTML = langs.map((l) => `
                    <a href="javascript:void(0)" onclick="setLanguage('${l.code}')" class="lang-opt-link ${l.code === curLang ? 'active' : ''}" data-lang="${l.code}">
                        <span class="flag-icon">${l.flag || '🌐'}</span> ${l.name.toUpperCase()} (${l.code.toUpperCase()})
                    </a>
                `).join('');
            }

            if (mWrap) {
                mWrap.innerHTML = langs.map((l) => `
                    <a href="javascript:void(0)" onclick="setLanguage('${l.code}')" class="m-lang-btn ${l.code === curLang ? 'active' : ''}" data-lang="${l.code}">${l.code.toUpperCase()}</a>
                `).join('');
            }

            updateActiveLanguageUI();
        } catch (e) {
            console.warn('Dinamik diller yüklenemedi:', e);
        }
    }

    async function loadHeaderContactSettings() {
        try {
            const apiBase = getHeaderApiBase();
            const curLang = localStorage.getItem('selectedLang') || 'tr';
            const res = await fetch(`${apiBase}/api/settings?lang=${curLang}&t=${Date.now()}`);
            if (!res.ok) return;
            const s = await res.json();
            const rawPhone = (s.phone || s.kd_contact_phone || '').toString();
            const digits = rawPhone.replace(/\D/g, '');
            const href = !digits ? '' : (digits.length >= 12 && digits.startsWith('90') ? `tel:+${digits}` : `tel:${digits}`);

            const phoneLink = document.getElementById('headerPhoneLink');
            const phoneText = document.getElementById('headerPhoneText');
            if (phoneLink && href) phoneLink.href = href;
            if (phoneText && rawPhone) phoneText.textContent = rawPhone;
        } catch (e) {
            console.warn('Header iletişim ayarları yüklenemedi:', e);
        }
    }

    window.loadHeaderContactSettings = loadHeaderContactSettings;
    window.loadDynamicPages = loadHeaderMenu;

    loadDynamicLanguages();
    loadHeaderMenu();
    loadHeaderContactSettings();
    updateActiveLanguageUI();

    if (typeof applyTranslations === 'function') {
        applyTranslations();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeaderComponent);
} else {
    initHeaderComponent();
}

window.setLanguage = async function(langCode) {
    if (!langCode) return;

    localStorage.setItem('selectedLang', langCode);
    window.currentLang = langCode;
    document.documentElement.setAttribute('lang', langCode);

    if (langCode === 'ar' || langCode === 'fa' || langCode === 'ur') {
        document.documentElement.setAttribute('dir', 'rtl');
    } else {
        document.documentElement.removeAttribute('dir');
    }

    updateActiveLanguageUI(langCode);

    if (typeof window.changeSiteLanguage === 'function') {
        await window.changeSiteLanguage(langCode);
    } else {
        if (typeof window.loadTranslationsFromDB === 'function') {
            await window.loadTranslationsFromDB();
        }
        if (typeof window.applyTranslations === 'function') {
            window.applyTranslations(langCode);
        }
        window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang: langCode } }));
    }
};

function updateActiveLanguageUI(lang) {
    const currentLang = lang || window.currentLang || localStorage.getItem('selectedLang') || 'tr';

    const currentLangText = document.getElementById('currentLangText');
    if (currentLangText) currentLangText.textContent = currentLang.toUpperCase();

    document.querySelectorAll('.lang-opt-link, .m-lang-btn').forEach((btn) => {
        const btnLang = btn.getAttribute('data-lang');
        if (btnLang === currentLang) btn.classList.add('active');
        else btn.classList.remove('active');
    });
}

window.addEventListener('languageChanged', () => {
    updateActiveLanguageUI();
    if (typeof window.loadDynamicPages === 'function') {
        window.loadDynamicPages();
    }
    if (typeof window.loadHeaderContactSettings === 'function') {
        window.loadHeaderContactSettings();
    }
    if (typeof window.applyTranslations === 'function') {
        window.applyTranslations(window.currentLang);
    }
});

// ---------- Google Analytics 4 & Meta Pixel (admin panelden; boşsa hiçbir kod yüklenmez) ----------
(function () {
    if (window.__buyapiTrackingLoaded) return;
    window.__buyapiTrackingLoaded = true;
    const base = (location.origin && !location.origin.startsWith('file:')) ? location.origin : '';
    fetch(`${base}/api/settings?lang=tr`).then(r => r.ok ? r.json() : {}).then(s => {
        const ga = String(s.google_analytics_id || '').trim().toUpperCase();
        const px = String(s.meta_pixel_id || '').trim();
        if (/^G-[A-Z0-9]{4,15}$/.test(ga)) {
            const sc = document.createElement('script');
            sc.async = true; sc.id = 'ga4-loader';
            sc.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(ga)}`;
            document.head.appendChild(sc);
            window.dataLayer = window.dataLayer || [];
            window.gtag = function () { window.dataLayer.push(arguments); };
            window.gtag('js', new Date());
            window.gtag('config', ga);
        }
        if (/^\d{8,20}$/.test(px)) {
            !function (f, b, e, v, n, t, s2) {
                if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
                t = b.createElement(e); t.async = !0; t.id = 'meta-pixel-loader'; t.src = v;
                s2 = b.getElementsByTagName(e)[0]; s2.parentNode.insertBefore(t, s2);
            }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
            window.fbq('init', px);
            window.fbq('track', 'PageView');
        }
    }).catch(() => {});
})();
