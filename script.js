// ==========================================
// BUYAPI İNŞAAT - EKSİKSİZ DİNAMİK JS MOTORU
// ==========================================

const API_BASE = (typeof window !== 'undefined' && window.location && window.location.origin && !window.location.origin.startsWith('file:'))
    ? window.location.origin
    : 'http://127.0.0.1:8000';
const API_URL = API_BASE + "/api";
window.API_URL = API_URL;
let currentLang = localStorage.getItem("selectedLang") || "tr";
window.currentLang = currentLang; // Global Kapsama Tanımla
let UI_TRANSLATIONS = {}; // Veritabanındaki `site_settings` tablosundan dinamik dolacak

// Tekrarlı API çağrılarını azaltmak için basit bellek önbelleği
const __apiCache = new Map();
function fetchJsonCached(url, options = {}, { ttlMs = 30000, force = false } = {}) {
    const key = `${url}::${JSON.stringify(options || {})}`;
    const now = Date.now();

    if (!force && __apiCache.has(key)) {
        const entry = __apiCache.get(key);
        if (now - entry.time < ttlMs) {
            return entry.promise;
        }
    }

    const promise = fetch(url, options).then(async (res) => {
        if (!res.ok) throw new Error(`HTTP ${res.status} - ${url}`);
        return res.json();
    });

    __apiCache.set(key, { time: now, promise });
    promise.catch(() => {
        const cur = __apiCache.get(key);
        if (cur?.promise === promise) __apiCache.delete(key);
    });

    return promise;
}

// DİL VE DURUM ÇEVİRİ SÖZLÜKLERİ
const STATUS_TRANSLATIONS = {
    'devam-eden': { tr: 'Devam Ediyor', en: 'Ongoing Project', ar: 'مشروع قيد الإنشاء' },
    'tamamlanan': { tr: 'Tamamlandı', en: 'Completed Project', ar: 'مشروع مكتمل' },
    'yeni-proje': { tr: 'Yeni Proje', en: 'New Project', ar: 'مشروع جديد' }
};

const TYPE_TRANSLATIONS = {
    'konut': { tr: 'Konut', en: 'Residential', ar: 'سكني' },
    'ticari': { tr: 'Ticari', en: 'Commercial', ar: 'تجاري' },
    'sanayi': { tr: 'Sanayi', en: 'Industrial', ar: 'صناعي' },
    'kentsel': { tr: 'Kentsel Dönüşüm', en: 'Urban Transformation', ar: 'التحول الحضري' }
};

function getStatusLabel(statusKey, lang = currentLang) {
    const dict = (window.UI_TRANSLATIONS && (window.UI_TRANSLATIONS[lang] || window.UI_TRANSLATIONS['tr'])) || {};
    const dbKey = `status.${statusKey}`;
    if (dict[dbKey]) return dict[dbKey];
    return STATUS_TRANSLATIONS[statusKey]?.[lang] || STATUS_TRANSLATIONS[statusKey]?.['tr'] || statusKey;
}

function getTypeLabel(typeKey, lang = currentLang) {
    const dict = (window.UI_TRANSLATIONS && (window.UI_TRANSLATIONS[lang] || window.UI_TRANSLATIONS['tr'])) || {};
    const dbKey = `type.${typeKey}`;
    if (dict[dbKey]) return dict[dbKey];
    return TYPE_TRANSLATIONS[typeKey]?.[lang] || TYPE_TRANSLATIONS[typeKey]?.['tr'] || typeKey;
}

// VERİTABANINDAN TÜM MENÜ VE SİTE ÇEVİRİLERİNİ ÇEKME MOTORU
window.loadTranslationsFromDB = async function() {
    try {
        UI_TRANSLATIONS = await fetchJsonCached(`${API_URL}/translations`, {}, { ttlMs: 5 * 60 * 1000, force: false });
        window.UI_TRANSLATIONS = UI_TRANSLATIONS;
    } catch (e) {
        console.error("Veritabanından çeviriler çekilemedi:", e);
    }
    return UI_TRANSLATIONS;
};
async function loadTranslationsFromDB() { return window.loadTranslationsFromDB(); }

// SAYFADAKİ METİNLERİ (data-i18n) VERİTABANINDAN GELEN DİLLE DEĞİŞTİREN MOTOR
window.applyTranslations = function(lang) {
    const cur = lang || window.currentLang || localStorage.getItem("selectedLang") || "tr";
    const dict = (window.UI_TRANSLATIONS && window.UI_TRANSLATIONS[cur]) 
        || (window.UI_TRANSLATIONS && window.UI_TRANSLATIONS['tr']) 
        || UI_TRANSLATIONS[cur] 
        || UI_TRANSLATIONS['tr'] 
        || {};

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (dict[key] !== undefined && dict[key] !== null && dict[key] !== '') {
            if (el.children.length === 0) {
                el.textContent = dict[key];
            } else {
                el.innerHTML = dict[key];
            }
        }
    });

    document.querySelectorAll('[data-i18n-opt]').forEach(el => {
        const key = el.getAttribute('data-i18n-opt');
        if (dict[key]) {
            el.textContent = dict[key];
        }
    });

    document.querySelectorAll('[data-i18n-ph]').forEach(el => {
        const key = el.getAttribute('data-i18n-ph');
        if (dict[key]) {
            el.setAttribute('placeholder', dict[key]);
        }
    });

    document.querySelectorAll('[data-i18n-title]').forEach(el => {
        const key = el.getAttribute('data-i18n-title');
        if (dict[key]) {
            el.setAttribute('title', dict[key]);
        }
    });
};
function applyTranslations(lang) { return window.applyTranslations(lang); }

// DİNAMİK ÇOK DİLLİ SEO METATAG ENGINE
async function applySeoMetaTags(lang = currentLang) {
    // Detay sayfasında başlık/seo proje bazlı akıştan yönetiliyor.
    if (document.body?.classList.contains('detail-page-body')) return;

    try {
        const path = (location.pathname.split('/').pop() || 'index.html').toLowerCase();
        const slug = path.replace('.html', '') || 'index';
        const isHome = slug === 'index';

        const staticTitle = document.querySelector('head > title')?.textContent?.trim() || document.title;

        const [seoData, pages] = await Promise.all([
            fetchJsonCached(`${API_URL}/seo`, {}, { ttlMs: 60 * 1000 }),
            fetchJsonCached(`${API_URL}/pages?menu=1&lang=${encodeURIComponent(lang)}`, {}, { ttlMs: 60 * 1000 }).catch(() => [])
        ]);

        const currentSeo = seoData?.[lang] || seoData?.tr || {};
        const pageSeo = (pages || []).find((p) => (p.slug || '').toLowerCase() === slug) || null;

        // 1) Sayfaya özel SEO varsa önceliklidir
        if (pageSeo?.seoTitle) {
            document.title = pageSeo.seoTitle;
        } else if (isHome && currentSeo.title) {
            // 2) Genel SEO yalnızca ana sayfada title'ı ezebilir
            document.title = currentSeo.title;
        } else if (staticTitle) {
            // 3) Diğer sayfalarda statik title korunur
            document.title = staticTitle;
        }

        let metaDesc = document.querySelector('meta[name="description"]');
        if (!metaDesc) {
            metaDesc = document.createElement('meta');
            metaDesc.setAttribute('name', 'description');
            document.head.appendChild(metaDesc);
        }

        if (pageSeo?.seoDesc) {
            metaDesc.setAttribute('content', pageSeo.seoDesc);
        } else if (isHome && currentSeo.desc) {
            metaDesc.setAttribute('content', currentSeo.desc);
        }

        if (isHome && currentSeo.keywords) {
            let metaKey = document.querySelector('meta[name="keywords"]');
            if (!metaKey) {
                metaKey = document.createElement('meta');
                metaKey.setAttribute('name', 'keywords');
                document.head.appendChild(metaKey);
            }
            metaKey.setAttribute('content', currentSeo.keywords);
        }
    } catch (err) {
        console.error("SEO metatagleri uygulanamadı:", err);
    }
}

// Mobil Tarayıcının Adres Çubuğu Değişiminde Slider'ın Büyüyüp Küçülmesini Engeller
function lockMobileHeroHeight() {
    if (window.innerWidth <= 768) {
        const initialHeight = window.innerHeight + 'px';
        document.documentElement.style.setProperty('--hero-height', initialHeight);
    }
}
window.addEventListener('DOMContentLoaded', lockMobileHeroHeight);

// Canonical etiketi yoksa üret
function ensureCanonicalTag() {
    let canonical = document.querySelector('link[rel="canonical"]');
    if (!canonical) {
        canonical = document.createElement('link');
        canonical.setAttribute('rel', 'canonical');
        document.head.appendChild(canonical);
    }
    canonical.setAttribute('href', window.location.href.split('#')[0]);
}

// Haritada Şehre Tıklandığında Alt Panele Yumuşak Kaydırma (Smooth Scroll)
window.scrollToMapProjects = function(cityKey) {
    if (typeof renderBottomMapProjects === "function") {
        renderBottomMapProjects(cityKey);
    }
    const panel = document.getElementById("mapProjectsPanel");
    if (panel) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

document.addEventListener("DOMContentLoaded", () => {
    // 1. GSAP PRELOADER
    const loaderProgress = document.querySelector(".loader-progress");
    const loader = document.querySelector(".loader");
    
    if (loaderProgress && typeof gsap !== "undefined") {
        const tl = gsap.timeline({
            onComplete: () => {
                if (loader) {
                    loader.style.display = "none";
                    loader.style.pointerEvents = "none";
                }
            }
        });
        tl.to(".loader-progress", { width: "100%", duration: 1.0, ease: "power2.inOut" })
          .to(".loader", { y: "-100%", duration: 0.8, ease: "power4.inOut" });
    } else if (loader) {
        loader.style.display = "none";
        loader.style.pointerEvents = "none";
    }
    setTimeout(() => {
        if (loader) {
            loader.style.display = "none";
            loader.style.pointerEvents = "none";
        }
    }, 2000);

    initApp();
});

async function initApp() {
    // 1. ÖNCE ÇEVİRİLERİ VE SEO METATAGLERİNİ MYSQL VERİTABANINDAN ÇEK VE UYGULA
    await loadTranslationsFromDB();
    applyTranslations(currentLang);
    ensureCanonicalTag();
    await applySeoMetaTags(currentLang);

    // 2. AKTİF MENÜ VURGULAMA
    const currentPath = window.location.pathname.split("/").pop() || "index.html";
    const navLinks = document.querySelectorAll(".desktop-nav .nav-link, .mobile-nav-link");
    navLinks.forEach(link => {
        if (link.getAttribute("href") === currentPath) {
            link.classList.add("active");
        }
    });

    // 3. MOBİL MENÜ YÖNETİMİ (components/header.js tarafından kontrol edilir)

    // 4. HEADER SCROLL EFEKTİ
    window.addEventListener("scroll", () => {
        const header = document.querySelector(".header");
        if (header) {
            if (window.scrollY > 50) header.classList.add("scrolled");
            else header.classList.remove("scrolled");
        }
    });

    // 5. API DİNAMİK VERİ ÇEKİMLERİ
    try { await fetchSliders(); } catch (e) { console.error("Slider çekme hatası:", e); }
    try { await fetchProjectsAndMapData(); } catch (e) { console.error("Proje çekme hatası:", e); }

    // 6. PROJELER SAYFASI GÖRÜNÜM BUTONLARI
    const viewButtons = document.querySelectorAll(".view-btn");
    const viewContainers = document.querySelectorAll(".view-container");

    if (viewButtons.length > 0) {
        viewButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const targetView = btn.getAttribute("data-view");
                viewButtons.forEach(b => b.classList.remove("active"));
                viewContainers.forEach(c => c.classList.remove("active"));
                btn.classList.add("active");

                if (targetView === "grid") {
                    document.getElementById("viewGrid")?.classList.add("active");
                } else if (targetView === "list") {
                    document.getElementById("viewList")?.classList.add("active");
                } else if (targetView === "map") {
                    document.getElementById("viewMap")?.classList.add("active");
                    if (typeof initTurkeyMap === "function") initTurkeyMap();
                }
            });
        });
    }

    // 7. PROJE DETAY HERO SLIDER
    const detailSlides = document.querySelectorAll(".detail-hero-slider .detail-slide");
    const detailPrevBtn = document.getElementById("detailPrevSlide");
    const detailNextBtn = document.getElementById("detailNextSlide");
    const detailCurrentNum = document.getElementById("detailCurrentNum");
    let currentDetailSlide = 0;

    if (detailSlides.length > 0) {
        function changeDetailSlide(direction) {
            detailSlides[currentDetailSlide].classList.remove("active");
            if (direction === "next") {
                currentDetailSlide = (currentDetailSlide + 1) % detailSlides.length;
            } else if (direction === "prev") {
                currentDetailSlide = (currentDetailSlide - 1 + detailSlides.length) % detailSlides.length;
            }
            detailSlides[currentDetailSlide].classList.add("active");
            if (detailCurrentNum) detailCurrentNum.textContent = `0${currentDetailSlide + 1}`;
        }

        if (detailNextBtn) detailNextBtn.addEventListener("click", () => changeDetailSlide("next"));
        if (detailPrevBtn) detailPrevBtn.addEventListener("click", () => changeDetailSlide("prev"));
        setInterval(() => changeDetailSlide("next"), 6000);
    }

    // 8. DİKEY SEKME PANELİ (mobilde akordiyon: içerik tıklanan başlığın hemen altında açılır)
    const vTabButtons = document.querySelectorAll(".v-tab-btn");
    const mediaTabContents = document.querySelectorAll(".media-tab-content");
    const vTabsNav = document.querySelector(".vertical-tabs-nav");
    const vTabsWrap = document.querySelector(".vertical-tabs-content-wrap");
    const vTabsMobile = window.matchMedia("(max-width: 1024px)");
    const vTabsOrder = Array.from(mediaTabContents);

    function vTabsIsMobile() {
        return vTabsMobile.matches;
    }

    function vTabsCloseAll() {
        vTabButtons.forEach(b => b.classList.remove("active"));
        mediaTabContents.forEach(c => c.classList.remove("active"));
    }

    // Mobilde aktif paneli ilgili butonun altına taşır, masaüstünde yerine geri koyar.
    function vTabsPlacePanel(scrollToPanel) {
        if (!vTabsNav || !vTabsWrap) return;
        const activeBtn = vTabsNav.querySelector(".v-tab-btn.active");
        const activePanel = document.querySelector(".media-tab-content.active");

        if (vTabsIsMobile()) {
            if (activeBtn && activePanel && activePanel.previousElementSibling !== activeBtn) {
                activeBtn.insertAdjacentElement("afterend", activePanel);
            }
            if (scrollToPanel && activePanel) {
                const top = activePanel.getBoundingClientRect().top + window.pageYOffset - 90;
                window.scrollTo({ top: Math.max(top, 0), behavior: "smooth" });
            }
        } else if (vTabsWrap.querySelectorAll(".media-tab-content").length !== vTabsOrder.length) {
            vTabsOrder.forEach(panel => vTabsWrap.appendChild(panel));
        }
    }

    if (vTabButtons.length > 0) {
        vTabButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const targetTab = btn.getAttribute("data-tab");
                const isAlreadyOpen = btn.classList.contains("active");

                if (vTabsIsMobile() && isAlreadyOpen) {
                    vTabsCloseAll();
                    return;
                }

                vTabsCloseAll();
                btn.classList.add("active");
                const panel = document.getElementById(targetTab);
                if (panel) panel.classList.add("active");
                vTabsPlacePanel(true);
            });
        });

        const hasActiveOnLoad = !!document.querySelector(".v-tab-btn.active") && !!document.querySelector(".media-tab-content.active");
        if (vTabsIsMobile()) {
            // İstek gereği mobilde başlangıçta hiçbir sekme açık gelmesin.
            vTabsCloseAll();
        } else {
            if (!hasActiveOnLoad && vTabButtons[0]) {
                vTabButtons[0].classList.add("active");
                const firstPanel = document.getElementById(vTabButtons[0].getAttribute("data-tab"));
                if (firstPanel) firstPanel.classList.add("active");
            }
            vTabsPlacePanel(false);
        }

        const onViewportChange = () => {
            if (vTabsIsMobile()) {
                vTabsCloseAll();
            } else {
                if (!document.querySelector(".v-tab-btn.active") && vTabButtons[0]) {
                    vTabButtons[0].classList.add("active");
                    const firstPanel = document.getElementById(vTabButtons[0].getAttribute("data-tab"));
                    if (firstPanel) firstPanel.classList.add("active");
                }
                vTabsPlacePanel(false);
            }
        };

        if (vTabsMobile.addEventListener) {
            vTabsMobile.addEventListener("change", onViewportChange);
        } else if (vTabsMobile.addListener) {
            vTabsMobile.addListener(onViewportChange);
        }
    }

    // 9. LIGHTBOX RESİM MOTORU
    const lightboxModal = document.getElementById("lightboxModal");
    const lightboxImage = document.getElementById("lightboxImage");
    const lightboxClose = document.getElementById("lightboxClose");
    const lbPrevBtn = document.getElementById("lbPrevBtn");
    const lbNextBtn = document.getElementById("lbNextBtn");
    const lightboxCounter = document.getElementById("lightboxCounter");

    let currentGalleryImages = [];
    let currentGalleryIndex = 0;

    function updateLightboxImage() {
        if (currentGalleryImages.length === 0 || !lightboxImage) return;
        const currentSrc = currentGalleryImages[currentGalleryIndex].getAttribute("data-src") || currentGalleryImages[currentGalleryIndex].querySelector("img")?.getAttribute("src");
        if (currentSrc) lightboxImage.setAttribute("src", currentSrc);

        if (lightboxCounter) {
            lightboxCounter.textContent = `${currentGalleryIndex + 1} / ${currentGalleryImages.length}`;
        }
    }

    document.addEventListener("click", (e) => {
        const item = e.target.closest(".gallery-item:not(.video-gallery-item)");
        if (item && lightboxModal) {
            const activeTabContent = item.closest(".media-tab-content") || document;
            currentGalleryImages = Array.from(activeTabContent.querySelectorAll(".gallery-item:not(.video-gallery-item)"));
            currentGalleryIndex = currentGalleryImages.indexOf(item);

            if (currentGalleryIndex !== -1) {
                updateLightboxImage();
                lightboxModal.classList.add("active");
                document.body.style.overflow = "hidden";
            }
        }
    });

    if (lbNextBtn) lbNextBtn.addEventListener("click", () => {
        if (currentGalleryImages.length === 0) return;
        currentGalleryIndex = (currentGalleryIndex + 1) % currentGalleryImages.length;
        updateLightboxImage();
    });

    if (lbPrevBtn) lbPrevBtn.addEventListener("click", () => {
        if (currentGalleryImages.length === 0) return;
        currentGalleryIndex = (currentGalleryIndex - 1 + currentGalleryImages.length) % currentGalleryImages.length;
        updateLightboxImage();
    });

    if (lightboxClose) lightboxClose.addEventListener("click", () => {
        if (lightboxModal) {
            lightboxModal.classList.remove("active");
            document.body.style.overflow = "";
        }
    });

    // 10. İLETİŞİM HARİTASI VE FORMLAR
    const locationCards = document.querySelectorAll(".office-location-card");
    const mapIframe = document.getElementById("contactMapIframe");
    const projectInquiryForm = document.getElementById("projectInquiryForm");

    const mapUrls = {
        sakarya: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.1119794695914!2d30.379201576550734!3d40.770549936052725!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14ccb2fb167ca9f1%3A0x6b6c0852d43cb8d1!2sSerdivan%2C%20Sakarya!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str",
        istanbul: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3011.6420188219!2d29.0560555!3d40.9670!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cac79294246813%3A0x33b4d24177d612e6!2sCaddebostan%2C%20Kad%C4%B1k%C3%B6y%2F%C4%B0stanbul!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str"
    };

    if (locationCards.length > 0 && mapIframe) {
        locationCards.forEach(card => {
            card.addEventListener("click", () => {
                locationCards.forEach(c => c.classList.remove("active"));
                card.classList.add("active");
                const mapKey = card.getAttribute("data-map");
                if (mapUrls[mapKey]) mapIframe.setAttribute("src", mapUrls[mapKey]);
            });
        });
    }

    if (projectInquiryForm) {
        projectInquiryForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const nameEl = document.getElementById("inquiryName");
            const phoneEl = document.getElementById("inquiryPhone");
            const noteEl = document.getElementById("inquiryNote");
            const submitBtn = projectInquiryForm.querySelector("button[type='submit']");
            const submitText = submitBtn ? submitBtn.querySelector("span") : null;
            const originalText = submitText ? submitText.textContent : "SATIŞ DANIŞMANI ARASIN";

            const fullName = (nameEl?.value || "").trim();
            const phone = (phoneEl?.value || "").trim();
            const note = (noteEl?.value || "").trim();
            const projectTitle = (document.getElementById("detailProjectTitle")?.textContent || "").trim();

            if (!fullName || !phone) {
                alert("Lütfen ad soyad ve telefon alanlarını doldurun.");
                return;
            }

            const payload = {
                source: "proje_detay",
                fullName,
                phone,
                project: projectTitle || "Proje Detay Talebi",
                note
            };

            try {
                if (submitBtn) submitBtn.disabled = true;
                if (submitText) submitText.textContent = "GÖNDERİLİYOR...";

                const response = await fetch(`${API_URL}/contact`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });
                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data?.ok) {
                    throw new Error(data?.error || "Mesaj gönderilemedi.");
                }

                const lang = localStorage.getItem("selectedLang") || window.currentLang || "tr";
                const q = new URLSearchParams({ tip: "proje-detay", lang });
                if (data?.id) q.set("ref", String(data.id));
                window.location.href = `tesekkur.html?${q.toString()}`;
            } catch (err) {
                alert(err?.message || "Mesaj gönderilirken bir hata oluştu.");
            } finally {
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.textContent = originalText;
            }
        });
    }
}

// API'DEN DİNAMİK SLIDER VE KONTROL BUTONLARINI RENDER EDEN MOTOR
async function fetchSliders() {
    const sliderContainer = document.querySelector(".hero-slider");
    if (!sliderContainer) return;

    try {
        const res = await fetch(`${API_URL}/sliders`);
        const sliders = await res.json();
        if (!sliders || !sliders.length) return;

        // Veritabanından gelen çevirilere göre etiketler
        const dict = UI_TRANSLATIONS[currentLang] || {};
        const tagText = dict['hero.tag'] || 'PROJE';
        const btnText = dict['hero.btn'] || 'PROJEYİ İNCELE';

        const slidesHTML = sliders.map((slide, i) => {
            let title = slide.title, subtitle = slide.subtitle;
            try { const pT = JSON.parse(slide.title); title = pT[currentLang] || pT['tr'] || ''; } catch (e) {}
            try { const pS = JSON.parse(slide.subtitle); subtitle = pS[currentLang] || pS['tr'] || ''; } catch (e) {}

            let rawImgPath = slide.image_url.trim().replace(/^\/+/, '');
            const finalImgSrc = rawImgPath.startsWith('http') ? rawImgPath : `${window.location.protocol}//${window.location.host}/${rawImgPath}`;

            return `
                <div class="slide ${i === 0 ? 'active' : ''}">
                    ${(slide.media_type === 'video' || /\.(mp4|webm)(\?|$)/i.test(finalImgSrc))
                        ? `<div class="slide-bg slide-bg-video"><video src="${finalImgSrc}" autoplay muted loop playsinline preload="metadata"></video></div>`
                        : `<div class="slide-bg" style="background-image: url('${finalImgSrc}');"></div>`}
                    <div class="slide-overlay"></div>
                    <div class="slide-content-wrap">
                        <div class="container">
                            <div class="slide-content">
                                <span class="hero-tag">${tagText}</span>
                                <h1 class="hero-title">${title}</h1>
                                <p class="hero-desc">${subtitle}</p>
                                ${slide.link_url ? `
                                    <a href="${slide.link_url}" class="hero-btn">
                                        <span>${btnText}</span>
                                        <i class="fa-solid fa-arrow-right-long"></i>
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        const dotsHTML = sliders.map((_, i) => `<div class="dot ${i === 0 ? 'active' : ''}"></div>`).join('');

        const controlsHTML = `
            <div class="slider-controls">
                <div class="container">
                    <div class="controls-inner">
                        <div class="slide-counter">
                            <span id="currentNum">01</span> / 0${sliders.length}
                        </div>
                        <div class="slider-dots">${dotsHTML}</div>
                        <div class="slider-arrows">
                            <button id="prevSlide" aria-label="Önceki"><i class="fa-solid fa-arrow-left"></i></button>
                            <button id="nextSlide" aria-label="Sonraki"><i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        sliderContainer.innerHTML = slidesHTML + controlsHTML;
        initHeroSliderControl();

        // Panelden girilen ana sayfa metinleri/görselleri slider yeniden çizildikten sonra tekrar uygulanır
        if (typeof window.applyHomeContentSettings === 'function') {
            await window.applyHomeContentSettings();
        }
    } catch (err) {
        console.error("Slider çekilirken hata:", err);
    }
}

function initHeroSliderControl() {
    const slides = document.querySelectorAll(".hero-slider .slide");
    const dots = document.querySelectorAll(".dot");
    const prevBtn = document.getElementById("prevSlide");
    const nextBtn = document.getElementById("nextSlide");
    const currentNum = document.getElementById("currentNum");
    let currentSlide = 0;

    if (!slides.length) return;

    function goToSlide(index) {
        slides.forEach(s => s.classList.remove("active"));
        dots.forEach(d => d.classList.remove("active"));
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add("active");
        if (dots[currentSlide]) dots[currentSlide].classList.add("active");
        if (currentNum) currentNum.textContent = `0${currentSlide + 1}`;
    }

    if (nextBtn) nextBtn.onclick = (e) => { e.preventDefault(); goToSlide(currentSlide + 1); restart(); };
    if (prevBtn) prevBtn.onclick = (e) => { e.preventDefault(); goToSlide(currentSlide - 1); restart(); };
    dots.forEach((dot, index) => dot.onclick = () => { goToSlide(index); restart(); });

    // Otomatik geçiş (video slaytlar dahil)
    function restart() {
        clearInterval(window.__heroAutoTimer);
        if (slides.length > 1) window.__heroAutoTimer = setInterval(() => goToSlide(currentSlide + 1), 7000);
    }
    restart();
}

// API'DEN PROJELERİ ÇEKİP HARİTA VE LİSTELEME İLE SENKRONİZE EDEN MOTOR
async function fetchProjectsAndMapData() {
    // Projeler/map bileşeni olmayan sayfalarda gereksiz çağrı yapmayalım.
    const needsProjectsData = document.getElementById('highchartsTurkeyMap')
        || document.getElementById('projectGridContainer')
        || document.getElementById('projectListContainer');
    if (!needsProjectsData) return;

    try {
        const projects = await fetchJsonCached(`${API_URL}/projects?lang=${encodeURIComponent(currentLang)}`, {}, { ttlMs: 60 * 1000 });
        if (!projects) return;

        let cityProjectsData = {};
        projects.forEach(p => {
            const cKey = p.cityKey || 'tr-sk';
            if (!cityProjectsData[cKey]) {
                cityProjectsData[cKey] = {
                    name: p.cat || 'Şehir',
                    title: `${p.cat} Projelerimiz`,
                    tag: `LOKASYON: ${p.cat.toUpperCase()}`,
                    count: 0,
                    projects: []
                };
            }
            cityProjectsData[cKey].count++;
            cityProjectsData[cKey].projects.push({
                name: p.title,
                cat: p.cat,
                link: `proje-detay.html?slug=${p.slug}`,
                status: p.status,
                type: p.type,
                loc: p.loc
            });
        });

        window.cityProjectsData = cityProjectsData;
        initFiltersAndMap(cityProjectsData);
    } catch (err) {
        console.error("Projeler çekilemedi:", err);
    }
}

function initFiltersAndMap(cityProjectsData) {
    const statusTabs = document.querySelectorAll(".status-tab");
    const locationSelect = document.getElementById("filterLocation");
    const typeSelect = document.getElementById("filterType");
    const projectItems = document.querySelectorAll(".project-item");
    const pins = document.querySelectorAll(".map-pin");

    function applyFilters() {
        const activeStatus = document.querySelector(".status-tab.active")?.getAttribute("data-status") || "all";
        const selectedLocation = locationSelect ? locationSelect.value : "all";
        const selectedType = typeSelect ? typeSelect.value : "all";

        projectItems.forEach(item => {
            const matchStatus = (activeStatus === "all" || item.getAttribute("data-status") === activeStatus);
            const matchLocation = (selectedLocation === "all" || item.getAttribute("data-location") === selectedLocation);
            const matchType = (selectedType === "all" || item.getAttribute("data-type") === selectedType);
            item.classList.toggle("filtered-out", !(matchStatus && matchLocation && matchType));
        });

        pins.forEach(pin => {
            const city = pin.getAttribute("data-city");
            const cityData = cityProjectsData[city];
            const badgeSpan = pin.querySelector(".pin-count");

            if (cityData && badgeSpan) {
                const matchingCount = cityData.projects.filter(p => {
                    const matchStatus = (activeStatus === "all" || p.status === activeStatus);
                    const matchLocation = (selectedLocation === "all" || p.loc === selectedLocation);
                    const matchType = (selectedType === "all" || p.type === selectedType);
                    return matchStatus && matchLocation && matchType;
                }).length;

                badgeSpan.textContent = matchingCount;
                pin.classList.toggle("disabled-pin", matchingCount === 0);
            }
        });

        const activePin = document.querySelector(".map-pin.active:not(.disabled-pin)") || document.querySelector(".map-pin:not(.disabled-pin)");
        window.renderBottomMapProjects(activePin ? activePin.getAttribute("data-city") : "tr-sk");
    }

    window.renderBottomMapProjects = function(cityKey) {
        const cardsContainer = document.getElementById("panelCardsContainer");
        const cityTitle = document.getElementById("panelCityTitle");
        const cityTag = document.getElementById("panelCityTag");
        if (!cardsContainer) return;

        const data = cityProjectsData[cityKey];
        if (!cityKey || !data) {
            if (cityTitle) cityTitle.textContent = "Proje Bulunamadı";
            if (cityTag) cityTag.textContent = "LOKASYON: MAVİ BÖLGE";
            cardsContainer.innerHTML = `<p style="color: var(--text-dim); grid-column: 1/-1;">Bu şehirde henüz aktif bir projemiz bulunmamaktadır.</p>`;
            return;
        }

        if (cityTitle) cityTitle.textContent = data.title;
        if (cityTag) cityTag.textContent = data.tag;

        cardsContainer.innerHTML = data.projects.map(proj => `
            <a href="${proj.link}" class="map-mini-card-bottom">
                <div class="mini-card-top-head">
                    <span class="status-badge ${proj.status}">${getStatusLabel(proj.status)}</span>
                    <i class="fa-solid fa-arrow-up-right-from-square mini-card-arrow"></i>
                </div>
                <div class="mini-card-info">
                    <span class="mini-card-cat">${proj.cat}</span>
                    <h4>${proj.name}</h4>
                </div>
            </a>
        `).join('');
    };

    function initTurkeyMap() {
        const container = document.getElementById("highchartsTurkeyMap");
        if (!container || typeof Highcharts === "undefined" || !Highcharts.maps?.['countries/tr/tr-all']) return;

        const mapData = Highcharts.maps['countries/tr/tr-all'].features.map(feature => {
            const hcKey = feature.properties['hc-key'];
            const hasData = cityProjectsData[hcKey];
            return {
                'hc-key': hcKey,
                name: feature.properties.name,
                value: hasData ? hasData.count : 0,
                color: hasData ? '#c5a059' : '#121824'
            };
        });

        Highcharts.mapChart('highchartsTurkeyMap', {
            chart: { map: 'countries/tr/tr-all', backgroundColor: 'transparent' },
            title: { text: null },
            mapNavigation: { enabled: false },
            plotOptions: {
                map: {
                    borderColor: 'rgba(255, 255, 255, 0.08)',
                    borderWidth: 0.8,
                    states: { hover: { color: '#dfb775', borderColor: '#ffffff' } },
                    dataLabels: { enabled: true, format: '{point.name}', style: { color: 'rgba(255,255,255,0.4)', fontSize: '9px', fontWeight: '300', textOutline: 'none' } }
                }
            },
            tooltip: {
                formatter: function () {
                    return `<b>${this.point.name}</b><br/>${this.point.value > 0 ? this.point.value + ' Aktif Proje' : 'Proje Bulunmuyor'}`;
                }
            },
            series: [{
                data: mapData,
                name: 'Türkiye',
                cursor: 'pointer',
                point: { events: { click: function () { window.scrollToMapProjects(this['hc-key']); } } }
            }]
        });
    }

    initTurkeyMap();

    statusTabs.forEach(tab => {
        tab.onclick = (e) => {
            e.preventDefault();
            statusTabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");
            applyFilters();
        };
    });

    if (locationSelect) locationSelect.onchange = applyFilters;
    if (typeSelect) typeSelect.onchange = applyFilters;

    pins.forEach(pin => {
        pin.onclick = (e) => {
            e.preventDefault();
            pins.forEach(p => p.classList.remove("active"));
            pin.classList.add("active");
            window.scrollToMapProjects(pin.getAttribute("data-city"));
        };
    });

    applyFilters();
}

// SWIPER SLIDER MOBİL DOKUNMATİK ENTEGRASYONU
if (typeof Swiper !== "undefined" && document.querySelector('.projects-slider')) {
    new Swiper('.projects-slider', {
        slidesPerView: 1.15,
        spaceBetween: 15,
        loop: false,
        grabCursor: true,
        touchEventsTarget: 'wrapper',
        simulateTouch: true,
        allowTouchMove: true,
        navigation: {
            nextEl: '.projects-section .swiper-button-next',
            prevEl: '.projects-section .swiper-button-prev',
        },
        breakpoints: {
            768: { slidesPerView: 3, spaceBetween: 30 }
        }
    });
}

// FOOTER ARAMA MOTORU
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('footerSearchInput');
    const searchBtn = document.getElementById('footerSearchBtn');

    function executeFooterSearch() {
        const query = searchInput?.value.trim();
        if (query) {
            window.location.href = `projeler.html?search=${encodeURIComponent(query)}`;
        }
    }

    searchBtn?.addEventListener('click', executeFooterSearch);
    searchInput?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') executeFooterSearch();
    });
});

// ==========================================
// DİL DEĞİŞTİRME MOTORU & EVENT LISTENERS
// ==========================================

window.changeSiteLanguage = async function(langCode) {
    currentLang = langCode;
    window.currentLang = langCode; // Global Kapsam Eşitlemesi
    localStorage.setItem("selectedLang", langCode);
    
    document.documentElement.setAttribute("lang", langCode);
    if (langCode === 'ar' || langCode === 'fa' || langCode === 'ur') {
        document.documentElement.setAttribute("dir", "rtl");
    } else {
        document.documentElement.removeAttribute("dir");
    }

    document.querySelectorAll("[data-lang-btn]").forEach(btn => {
        btn.classList.toggle("active", btn.getAttribute("data-lang-btn") === langCode);
    });

    // 1. Veritabanından gelen yeni dil çevirilerini ve SEO metataglerini uygula
    if (!window.UI_TRANSLATIONS || Object.keys(window.UI_TRANSLATIONS).length === 0) {
        await window.loadTranslationsFromDB();
    }
    window.applyTranslations(langCode);
    await applySeoMetaTags(langCode);

    // 2. Dinamik Slider ve Projeleri Yeniden Çek (eğer anasayfadaysak)
    if (typeof fetchSliders === "function") await fetchSliders();
    if (typeof fetchProjectsAndMapData === "function") await fetchProjectsAndMapData();

    // 3. Proje detay sayfasındaysak anında yeni dille güncelle
    if (typeof window.forceLoadProjectDetails === "function") {
        await window.forceLoadProjectDetails();
    } else if (typeof initProjectDetailPage === "function" && document.body.classList.contains("detail-page-body")) {
        await initProjectDetailPage();
    }

    // 4. FOOTER V.B. BİLEŞENLERE DİL DEĞİŞTİ HABERİ BİLDİR
    window.dispatchEvent(new CustomEvent("languageChanged", { detail: { lang: langCode } }));
};

document.addEventListener("DOMContentLoaded", () => {
    const savedLang = localStorage.getItem("selectedLang") || "tr";
    if (savedLang !== "tr") {
        window.changeSiteLanguage(savedLang);
    }

    document.querySelectorAll("[data-lang-btn]").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const selectedLang = btn.getAttribute("data-lang-btn");
            window.changeSiteLanguage(selectedLang);
        });
    });
});

// PROJE DETAY SAYFASI ÖZEL SEO METATAG ENGINE
function applySingleProjectSEO(projectData) {
    if (!projectData) return;

    // 1. Tarayıcı Başlığını (Title) Güncelle
    const titleText = projectData.seoTitle || projectData.title;
    if (titleText) {
        document.title = `${titleText} | BUYAPI`;
    }

    // 2. Meta Description (Açıklama) Güncelle
    const descText = projectData.seoDesc || projectData.description || projectData.subtitle;
    if (descText) {
        let metaDesc = document.querySelector('meta[name="description"]');
        if (!metaDesc) {
            metaDesc = document.createElement('meta');
            metaDesc.setAttribute('name', 'description');
            document.head.appendChild(metaDesc);
        }
        metaDesc.setAttribute('content', descText);
    }

    // 3. Meta Keywords (Anahtar Kelimeler) Güncelle
    if (projectData.seoKeywords) {
        let metaKey = document.querySelector('meta[name="keywords"]');
        if (!metaKey) {
            metaKey = document.createElement('meta');
            metaKey.setAttribute('name', 'keywords');
            document.head.appendChild(metaKey);
        }
        metaKey.setAttribute('content', projectData.seoKeywords);
    }
}

// ==========================================
// PROJE DETAY SAYFASI DİNAMİK YÜKLEME VE SEO MOTORU
// ==========================================

document.addEventListener("DOMContentLoaded", async () => {
    // Sadece proje detay sayfasındaysak çalıştır
    if (document.body.classList.contains("detail-page-body")) {
        await initProjectDetailPage();
    }
});

async function initProjectDetailPage() {
    if (typeof window.forceLoadProjectDetails === 'function') {
        return window.forceLoadProjectDetails();
    }
    const urlParams = new URLSearchParams(window.location.search);
    const slug = urlParams.get('slug') || urlParams.get('id');
    if (!slug) return;

    try {
        // API'den mevcut dildeki projeleri çek
        const projects = await fetchJsonCached(`${API_URL}/projects?lang=${encodeURIComponent(currentLang)}`, {}, { ttlMs: 60 * 1000 });
        
        // URL'deki slug veya id ile eşleşen projeyi bul
        const project = projects.find(p => p.slug === slug || String(p.id) === String(slug));

        if (project) {
            // 1. DİNAMİK SEO METATAGLERİNİ İŞLE
            applySingleProjectSEO(project);

            // 2. SAYFA BAŞLIK VE METİNLERİNİ DİNAMİK DOLDUR
            const titleElem = document.querySelector(".project-detail-title");
            const subtitleElem = document.querySelector(".project-detail-subtitle");
            const categoryElem = document.querySelector(".hero-text-wrap .section-tag");
            const leadTextElem = document.querySelector(".project-description-section .lead-text");

            if (titleElem) titleElem.textContent = (project.title || "PROJE DETAYI").toUpperCase();
            if (subtitleElem) subtitleElem.textContent = project.slogan || project.subtitle || "";
            if (categoryElem) {
                const typeText = typeof getTypeLabel === 'function' ? getTypeLabel(project.type, currentLang) : (project.type || 'KONUT');
                categoryElem.textContent = `${(project.loc || project.cat || 'SAKARYA').toUpperCase()} • ${typeText.toUpperCase()}`;
            }
            if (leadTextElem && project.description) leadTextElem.textContent = project.description;
        }
    } catch (err) {
        console.error("Proje detay verisi ve SEO bilgileri yüklenemedi:", err);
    }
}