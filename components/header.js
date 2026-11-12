// ==========================================
// BUYAPI - DİNAMİK HEADER & MOBİL MENÜ MODÜLÜ
// ==========================================

function getHeaderApiBase() {
    if (typeof window !== 'undefined' && window.location && window.location.origin && !window.location.origin.startsWith('file:')) {
        return window.location.origin;
    }
    return (typeof API_URL !== 'undefined') ? API_URL.replace(/\/api\/?$/, '') : 'http://127.0.0.1:8000';
}

function initHeaderComponent() {
    const target = document.getElementById("site-header");
    if (!target) return;
    if (target.dataset.headerInitialized === "true") return;
    target.dataset.headerInitialized = "true";

    const headerHTML = `
    <header class="header">
        <div class="header-inner">
            <a href="index.html" class="brand-logo">
                <img src="images/l-white.svg" alt="BUYAPI">
            </a>
            
            <nav class="nav desktop-nav" id="desktopNav">
                <a href="index.html" class="nav-link"><span data-i18n="nav.home">ANASAYFA</span></a>
                <a href="kurumsal.html" class="nav-link"><span data-i18n="nav.about">KURUMSAL</span></a>
                <a href="projeler.html" class="nav-link"><span data-i18n="nav.projects">PROJELER</span></a>
                <a href="iletisim.html" class="nav-link"><span data-i18n="nav.contact">İLETİŞİM</span></a>
                <span id="dynamicNavLinks" style="display:contents"></span>
            </nav>

            <div class="header-action desktop-action">
                <!-- DİL SEÇİM MODÜLÜ (DİNAMİK) -->
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
                        <a href="javascript:void(0)" onclick="setLanguage('de')" class="lang-opt-link" data-lang="de"><span class="flag-icon">🇩🇪</span> DEUTSCH (DE)</a>
                    </div>
                </div>

                <a href="kentsel-donusum.html" class="nav-btn btn-kentsel-header" title="Kentsel Dönüşüm Bilgi ve Başvuru">
                    <i class="fa-solid fa-building-shield"></i>
                    <span data-i18n="nav.urban_renewal">KENTSEL DÖNÜŞÜM</span>
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            </div>

            <!-- Mobil Hamburger Butonu -->
            <button class="mobile-toggle-btn" id="mobileToggleBtn" aria-label="Menüyü Aç/Kapat" type="button">
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Mobil Menü Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <button class="mobile-close-btn" id="mobileCloseBtn" aria-label="Menüyü Kapat" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="mobile-menu-content">
            <nav class="mobile-nav">
                <a href="index.html" class="mobile-nav-link" data-i18n="nav.home">ANASAYFA</a>
                <a href="kurumsal.html" class="mobile-nav-link" data-i18n="nav.about">KURUMSAL</a>
                <a href="projeler.html" class="mobile-nav-link" data-i18n="nav.projects">PROJELER</a>
                <a href="iletisim.html" class="mobile-nav-link" data-i18n="nav.contact">İLETİŞİM</a>
                <div id="dynamicMobileLinks" style="display:flex;flex-direction:column;gap:24px;"></div>
                
                <div class="mobile-lang-wrap" id="mobileLangWrap">
                    <a href="javascript:void(0)" onclick="setLanguage('tr')" class="m-lang-btn active" data-lang="tr">TR</a>
                    <a href="javascript:void(0)" onclick="setLanguage('en')" class="m-lang-btn" data-lang="en">EN</a>
                    <a href="javascript:void(0)" onclick="setLanguage('ar')" class="m-lang-btn" data-lang="ar">AR</a>
                    <a href="javascript:void(0)" onclick="setLanguage('de')" class="m-lang-btn" data-lang="de">DE</a>
                </div>

                <a href="kentsel-donusum.html" class="mobile-nav-btn btn-kentsel-mob"><i class="fa-solid fa-building-shield"></i> <span data-i18n="nav.urban_renewal">KENTSEL DÖNÜŞÜM</span> <i class="fa-solid fa-arrow-right"></i></a>
            </nav>
            <div class="mobile-menu-footer">
                <p>&copy; 2026 BUYAPI İNŞAAT A.Ş.</p>
                <a href="tel:+900000000000" id="headerPhoneLink"><i class="fa-solid fa-phone"></i> <span id="headerPhoneText">Telefon</span></a>
            </div>
        </div>
    </div>
    `;

    target.innerHTML = headerHTML;

    // 2. MOBİL HAMBURGER MENÜ MOTORU
    const mobileToggleBtn = document.getElementById("mobileToggleBtn");
    const mobileCloseBtn = document.getElementById("mobileCloseBtn");
    const mobileMenuOverlay = document.getElementById("mobileMenuOverlay");

    function closeMobileMenu() {
        if (mobileMenuOverlay) mobileMenuOverlay.classList.remove("active");
        if (mobileToggleBtn) mobileToggleBtn.classList.remove("active");
        document.body.classList.remove("menu-open");
        document.body.style.overflow = "";
    }

    function openMobileMenu() {
        if (mobileMenuOverlay) mobileMenuOverlay.classList.add("active");
        if (mobileToggleBtn) mobileToggleBtn.classList.add("active");
        document.body.classList.add("menu-open");
        document.body.style.overflow = "hidden";
    }

    function toggleMobileMenu(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (!mobileMenuOverlay) return;
        if (mobileMenuOverlay.classList.contains("active")) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    if (mobileToggleBtn) {
        mobileToggleBtn.addEventListener("click", toggleMobileMenu);
    }

    if (mobileCloseBtn) {
        mobileCloseBtn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            closeMobileMenu();
        });
    }

    if (mobileMenuOverlay) {
        // Overlay dış boşluğuna tıklanınca kapat
        mobileMenuOverlay.addEventListener("click", (e) => {
            if (e.target === mobileMenuOverlay) {
                closeMobileMenu();
            }
        });

        // Menüdeki linklere tıklanınca kapat (dil butonları hariç)
        mobileMenuOverlay.querySelectorAll("a:not(.m-lang-btn)").forEach(link => {
            link.addEventListener("click", () => {
                closeMobileMenu();
            });
        });
    }

    // 3. MASAÜSTÜ DİL DROPDOWN AÇ/KAPAT
    const langBtn = document.getElementById("langDropdownBtn");
    const langMenu = document.getElementById("langDropdownMenu");

    if (langBtn && langMenu) {
        langBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            langMenu.classList.toggle("active");
            langBtn.classList.toggle("open");
        });

        document.addEventListener("click", () => {
            langMenu.classList.remove("active");
            langBtn.classList.remove("open");
        });
    }

    // 4. DİLLERİ API'DEN ÇEK VE YÜKLE
    async function loadDynamicLanguages() {
        try {
            const apiBase = getHeaderApiBase();
            const res = await fetch(`${apiBase}/api/languages?t=${Date.now()}`);
            if (!res.ok) return;
            const langs = await res.json();
            if (!Array.isArray(langs) || langs.length === 0) return;

            const menu = document.getElementById("langDropdownMenu");
            const mWrap = document.getElementById("mobileLangWrap");
            const curLang = localStorage.getItem("selectedLang") || "tr";

            if (menu) {
                menu.innerHTML = langs.map(l => `
                    <a href="javascript:void(0)" onclick="setLanguage('${l.code}')" class="lang-opt-link ${l.code === curLang ? 'active' : ''}" data-lang="${l.code}">
                        <span class="flag-icon">${l.flag || '🌐'}</span> ${l.name.toUpperCase()} (${l.code.toUpperCase()})
                    </a>
                `).join('');
            }

            if (mWrap) {
                mWrap.innerHTML = langs.map(l => `
                    <a href="javascript:void(0)" onclick="setLanguage('${l.code}')" class="m-lang-btn ${l.code === curLang ? 'active' : ''}" data-lang="${l.code}">
                        ${l.code.toUpperCase()}
                    </a>
                `).join('');
            }

            updateActiveLanguageUI();
        } catch (e) {
            console.warn("Dinamik diller yüklenemedi:", e);
        }
    }

    // 5. ÖZEL MENÜ SAYFALARINI API'DEN ÇEK
    window.loadDynamicPages = async function() {
        try {
            const apiBase = getHeaderApiBase();
            const curLang = localStorage.getItem("selectedLang") || "tr";
            const res = await fetch(`${apiBase}/api/pages?menu=1&lang=${curLang}&t=${Date.now()}`);
            if (!res.ok) return;
            const pages = await res.json();
            if (!Array.isArray(pages)) return;

            const coreSlugs = ['anasayfa', 'kurumsal', 'projeler', 'kentsel-donusum', 'iletisim'];
            const customPages = pages.filter(p => !coreSlugs.includes(p.slug));
            
            const dNav = document.getElementById("dynamicNavLinks");
            const mNav = document.getElementById("dynamicMobileLinks");

            if (dNav) dNav.innerHTML = '';
            if (mNav) mNav.innerHTML = '';

            if (customPages.length === 0) return;

            customPages.forEach(p => {
                const pageHref = p.pageType === 'standart' ? `sayfa.html?slug=${p.slug}` : `${p.slug}.html`;
                if (dNav) {
                    const a = document.createElement("a");
                    a.href = pageHref;
                    a.className = "nav-link";
                    a.innerHTML = `<span>${(p.title || p.slug).toUpperCase()}</span>`;
                    dNav.appendChild(a);
                }
                if (mNav) {
                    const ma = document.createElement("a");
                    ma.href = pageHref;
                    ma.className = "mobile-nav-link";
                    ma.textContent = (p.title || p.slug).toUpperCase();
                    ma.addEventListener("click", closeMobileMenu);
                    mNav.appendChild(ma);
                }
            });
        } catch (e) {
            console.warn("Dinamik sayfalar yüklenemedi:", e);
        }
    };

    async function loadHeaderContactSettings() {
        try {
            const apiBase = getHeaderApiBase();
            const curLang = localStorage.getItem("selectedLang") || "tr";
            const res = await fetch(`${apiBase}/api/settings?lang=${curLang}&t=${Date.now()}`);
            if (!res.ok) return;
            const s = await res.json();
            const rawPhone = (s.phone || s.kd_contact_phone || '').toString();
            const digits = rawPhone.replace(/\D/g, '');
            const href = digits ? `tel:+${digits}` : '';

            const phoneLink = document.getElementById('headerPhoneLink');
            const phoneText = document.getElementById('headerPhoneText');
            if (phoneLink && href) phoneLink.href = href;
            if (phoneText && rawPhone) phoneText.textContent = rawPhone;
        } catch (e) {
            console.warn('Header iletişim ayarları yüklenemedi:', e);
        }
    }

    window.loadHeaderContactSettings = loadHeaderContactSettings;
    loadDynamicLanguages();
    window.loadDynamicPages();
    loadHeaderContactSettings();
    updateActiveLanguageUI();

    if (typeof applyTranslations === "function") {
        applyTranslations();
    }
}

// Güvenli çalıştırma (DOM ister yüklenmiş ister yüklenmekte olsun)
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initHeaderComponent);
} else {
    initHeaderComponent();
}

// GLOBAL DİL DEĞİŞTİRME FONKSİYONU
window.setLanguage = async function(langCode) {
    if (!langCode) return;

    localStorage.setItem("selectedLang", langCode);
    window.currentLang = langCode;
    document.documentElement.setAttribute("lang", langCode);

    if (langCode === 'ar' || langCode === 'fa' || langCode === 'ur') {
        document.documentElement.setAttribute("dir", "rtl");
    } else {
        document.documentElement.removeAttribute("dir");
    }

    updateActiveLanguageUI(langCode);

    if (typeof window.changeSiteLanguage === "function") {
        await window.changeSiteLanguage(langCode);
    } else {
        if (typeof window.loadTranslationsFromDB === "function") {
            await window.loadTranslationsFromDB();
        }
        if (typeof window.applyTranslations === "function") {
            window.applyTranslations(langCode);
        }
        window.dispatchEvent(new CustomEvent("languageChanged", { detail: { lang: langCode } }));
    }
};

// AKTİF DİL BUTONLARINI GÜNCELLEYEN FONKSİYON
function updateActiveLanguageUI(lang) {
    const currentLang = lang || window.currentLang || localStorage.getItem("selectedLang") || "tr";
    
    const currentLangText = document.getElementById("currentLangText");
    if (currentLangText) {
        currentLangText.textContent = currentLang.toUpperCase();
    }

    document.querySelectorAll(".lang-opt-link, .m-lang-btn").forEach(btn => {
        const btnLang = btn.getAttribute("data-lang");
        if (btnLang === currentLang) {
            btn.classList.add("active");
        } else {
            btn.classList.remove("active");
        }
    });
}

// DİL DEĞİŞİMİNDE DİNAMİK ALANLARI YENİLE
window.addEventListener("languageChanged", () => {
    updateActiveLanguageUI();
    if (typeof window.loadDynamicPages === "function") {
        window.loadDynamicPages();
    }
    if (typeof window.loadHeaderContactSettings === "function") {
        window.loadHeaderContactSettings();
    }
    if (typeof window.applyTranslations === "function") {
        window.applyTranslations(window.currentLang);
    }
});
