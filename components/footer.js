async function renderFooterComponent() {
    // 1. SİTEDE SEÇİLİ AKTİF DİLİ GARANTİLİ ŞEKİLDE AL
    let lang = localStorage.getItem("selectedLang") || document.documentElement.getAttribute("lang") || "tr";
    if (typeof window.currentLang !== 'undefined' && window.currentLang) {
        lang = window.currentLang;
    }

    // 2. DİLE GÖRE VARSAYILAN YEDEK METİNLER (API ÇÖKERSE VEYA YANIT VERMEZSE)
    const defaultText = {
        tr: {
            sectionTag: 'PROJE & İŞ BİRLİĞİ',
            ctaTitle: 'Geleceği Şekillendiren Yapılarda <br> İmzanız Olsun.',
            ctaDesc: 'Hayalinizdeki lüks konut, iş merkezi veya endüstriyel proje için mimari ekibimizle doğrudan iletişime geçin.',
            ctaBtnText: 'Projenizi Başlatın',
            startProjectContact: 'PROJENİZİ BAŞLATIN / İLETİŞİM',
            urbanRenewalGuide: 'KENTSEL DÖNÜŞÜM REHBERİ',
            brandDesc: '35 yılı aşkın mühendislik birikimiyle; teknik inovasyonu mimari estetikle buluşturan prestij projeleri üretiyoruz.',
            contactLabel: 'DOĞRUDAN TEMAS',
            phoneLabel: 'MÜŞTERİ HATLARI',
            emailLabel: 'E-POSTA',
            locationsLabel: 'LOKASYONLAR',
            loc1Title: 'Sakarya Ofis',
            loc1Addr: 'Arabacıalanı Mh. Çark Cd. Benli Sk. No: 1/B Serdivan/SAKARYA',
            loc2Title: 'İstanbul Ofis',
            loc2Addr: 'Caddebostan Mh. Cemil Topuzlu Cd. 58 /1/4 Kadıköy/İSTANBUL',
            socialLabel: 'TAKİP EDİN',
            copyText: '&copy; 2026 BUYAPI İNŞAAT A.Ş. • TÜM HAKLARI SAKLIDIR.',
            tagline: 'ARCHITECTURAL PRESTIGE & ENGINEERING',
            backTopText: 'YUKARI DÖN'
        },
        en: {
            sectionTag: 'PROJECT & COLLABORATION',
            ctaTitle: 'Leave Your Mark on the Buildings of Tomorrow.',
            ctaDesc: 'Contact our architectural team directly to bring your dream luxury home, business center, or industrial project to life.',
            ctaBtnText: 'Start your project',
            startProjectContact: 'START YOUR PROJECT / CONTACT',
            urbanRenewalGuide: 'URBAN RENEWAL GUIDE',
            brandDesc: 'With over 35 years of engineering expertise, we create prestigious projects that fuse technical innovation with architectural aesthetics.',
            contactLabel: 'DIRECT CONTACT',
            phoneLabel: 'HOTLINE',
            emailLabel: 'E-MAIL',
            locationsLabel: 'LOCATIONS',
            loc1Title: 'Sakarya Office',
            loc1Addr: 'Arabacıalanı Mh. Çark Cd. Benli Sk. No: 1/B Serdivan/SAKARYA',
            loc2Title: 'Istanbul Office',
            loc2Addr: 'Caddebostan Mh. Cemil Topuzlu Cd. 58 /1/4 Kadıköy/İSTANBUL',
            socialLabel: 'FOLLOW US',
            copyText: '&copy; 2026 BUYAPI İNŞAAT A.Ş. • ALL RIGHTS RESERVED.',
            tagline: 'ARCHITECTURAL PRESTIGE & ENGINEERING',
            backTopText: 'BACK TO TOP'
        },
        ar: {
            sectionTag: 'المشروع والتعاون',
            ctaTitle: 'ليكن لك بصمة في إعمار المستقبل.',
            ctaDesc: 'تواصلوا مباشرة مع فريقنا المعماري من أجل مشروع أحلامكم السكني الفاخر، أو التجاري، أو الصناعي.',
            ctaBtnText: 'ابتدئ مشروعك',
            startProjectContact: 'ابدأ مشروعك / اتصل بنا',
            urbanRenewalGuide: 'دليل التحول الحضري',
            brandDesc: 'مع أكثر من 35 عاماً من الخبرة الهندسية؛ نصنع مشاريع فاخرة تمزج بين الابتكار التكنولوجي والجماليات المعمارية.',
            contactLabel: 'الاتصال المباشر',
            phoneLabel: 'خطوط العملاء',
            emailLabel: 'البريد الإلكتروني',
            locationsLabel: 'المواقع',
            loc1Title: 'مكتب سكاريا',
            loc1Addr: 'Arabacıalanı Mh. Çark Cd. Benli Sk. No: 1/B Serdivan/SAKARYA',
            loc2Title: 'مكتب إسطنبول',
            loc2Addr: 'Caddebostan Mh. Cemil Topuzlu Cd. 58 /1/4 Kadıköy/İSTANBUL',
            socialLabel: 'تابعنا',
            copyText: '&copy; 2026 BUYAPI İNŞAAT A.Ş. • جميع الحقوق محفوظة.',
            tagline: 'الهيبة المعمارية والهندسة',
            backTopText: 'العودة إلى الأعلى'
        },
        de: {
            sectionTag: 'PROJEKT & KOOPERATION',
            ctaTitle: 'Hinterlassen Sie Ihre Handschrift bei den Bauwerken von morgen.',
            ctaDesc: 'Kontaktieren Sie unser Architekturteam direkt, um Ihr Traumhaus, Bürogebäude oder Industrieprojekt zu verwirklichen.',
            ctaBtnText: 'Projekt starten',
            startProjectContact: 'PROJEKT STARTEN / KONTAKT',
            urbanRenewalGuide: 'LEITFADEN STADTERNEUERUNG',
            brandDesc: 'Mit über 35 Jahren Ingenieurserfahrung schaffen wir Prestigeprojekte, die technische Innovation mit architektonischer Ästhetik verbinden.',
            contactLabel: 'DIREKTER KONTAKT',
            phoneLabel: 'HOTLINE',
            emailLabel: 'E-MAIL',
            locationsLabel: 'STANDORTE',
            loc1Title: 'Büro Sakarya',
            loc1Addr: 'Arabacıalanı Mh. Çark Cd. Benli Sk. No: 1/B Serdivan/SAKARYA',
            loc2Title: 'Büro Istanbul',
            loc2Addr: 'Caddebostan Mh. Cemil Topuzlu Cd. 58 /1/4 Kadıköy/İSTANBUL',
            socialLabel: 'FOLGEN SIE UNS',
            copyText: '&copy; 2026 BUYAPI İNŞAAT A.Ş. • ALLE RECHTE VORBEHALTEN.',
            tagline: 'ARCHITECTURAL PRESTIGE & ENGINEERING',
            backTopText: 'NACH OBEN'
        }
    };

    let textData = { ...(defaultText[lang] || defaultText['tr']) };

    let data = {
        ctaBtnLink: 'iletisim.html',
        phone: '444 33 43',
        email: 'info@buyapi.com.tr',
        loc1Map: 'https://maps.app.goo.gl/E8Q1MqJwCP9Dkxhn6',
        loc2Map: 'https://maps.app.goo.gl/7mLbev8XMVHLvqEf8',
        instagram: 'https://www.instagram.com/buyapitr/',
        facebook: 'https://www.facebook.com/buyapiinsaat/',
        linkedin: ''
    };

    const hasValue = (val) => val !== undefined && val !== null && String(val).trim() !== "";

    // 3. REST API'DEN DİNAMİK VERİLERİ ÇEKME
    try {
        const apiBase = (typeof window !== 'undefined' && window.location && window.location.origin && !window.location.origin.startsWith('file:'))
            ? window.location.origin
            : 'http://127.0.0.1:8000';
        const apiUrl = typeof API_URL !== 'undefined' ? API_URL : `${apiBase}/api`;
        const res = await fetch(`${apiUrl}/settings/footer?t=${Date.now()}`);
        const dbData = await res.json();

        if (dbData && Object.keys(dbData).length > 0) {
            // Sabit Genel Ayarlar (Kökteki Veriler)
            if (hasValue(dbData.phone)) data.phone = dbData.phone;
            if (hasValue(dbData.email)) data.email = dbData.email;
            if (hasValue(dbData.ctaBtnLink)) data.ctaBtnLink = dbData.ctaBtnLink;
            if (hasValue(dbData.loc1Map)) data.loc1Map = dbData.loc1Map;
            if (hasValue(dbData.loc2Map)) data.loc2Map = dbData.loc2Map;
            if (hasValue(dbData.instagram)) data.instagram = dbData.instagram;
            if (hasValue(dbData.facebook)) data.facebook = dbData.facebook;
            if (hasValue(dbData.linkedin)) data.linkedin = dbData.linkedin;

            // Seçili Dildeki Verileri Yükle (dbData.tr, dbData.en, dbData.ar)
            const activeLangObj = dbData[lang];
            
            if (activeLangObj && typeof activeLangObj === 'object') {
                if (hasValue(activeLangObj.ctaTitle)) textData.ctaTitle = activeLangObj.ctaTitle;
                if (hasValue(activeLangObj.ctaDesc)) textData.ctaDesc = activeLangObj.ctaDesc;
                if (hasValue(activeLangObj.ctaBtnText)) textData.ctaBtnText = activeLangObj.ctaBtnText;
                if (hasValue(activeLangObj.brandDesc)) textData.brandDesc = activeLangObj.brandDesc;
                if (hasValue(activeLangObj.loc1Title)) textData.loc1Title = activeLangObj.loc1Title;
                if (hasValue(activeLangObj.loc1Addr)) textData.loc1Addr = activeLangObj.loc1Addr;
                if (hasValue(activeLangObj.loc2Title)) textData.loc2Title = activeLangObj.loc2Title;
                if (hasValue(activeLangObj.loc2Addr)) textData.loc2Addr = activeLangObj.loc2Addr;
            }
        }
    } catch (err) {
        console.warn("Footer ayarları çekilemedi, yedekler basılıyor:", err);
    }

    const cleanPhoneLink = data.phone.replace(/\s+/g, '');

    // 4. BİREBİR ORİJİNAL HTML ŞABLONUNUZ
    const footerHTML = `
    <footer class="footer">
        <div class="footer-watermark-logo">
            <img src="images/footer-logo-tr-TR.svg" alt="BUYAPI Siluet Logo">
        </div>
        <div class="container">
            <!-- YÜKSEK PRESTİJ C-SUITE CTA CARD -->
            <div class="footer-top-editorial">
                <div class="f-headline-wrap">
                    <span class="section-tag">${textData.sectionTag}</span>
                    <h2 class="f-big-title">${textData.ctaTitle}</h2>
                    <p class="f-sub-desc">${textData.ctaDesc}</p>
                </div>
                <div class="f-action-wrap">
                    <a href="${data.ctaBtnLink}" class="f-cta-btn">
                        <span>${textData.ctaBtnText}</span>
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
            </div>

            <!-- LÜKS KOLON DÜZENİ -->
            <div class="footer-grid-luxury">
                <div class="f-grid-col f-col-brand">
                    <img src="images/footer-logo-tr-TR.svg" alt="BUYAPI Logo" class="f-editorial-logo">
                    <p class="f-editorial-desc">
                        ${textData.brandDesc}
                    </p>
                    <div class="f-brand-quick-links" style="margin-top:22px;display:flex;flex-direction:column;gap:10px;">
                        <a href="iletisim.html" class="f-quick-action-link" style="display:inline-flex;align-items:center;gap:10px;color:#c5a059;font-weight:700;font-size:0.75rem;letter-spacing:0.2em;text-decoration:none;transition:transform 0.2s ease;">
                            <i class="fa-solid fa-arrow-right"></i> <span data-i18n="footer.start_project_contact">${textData.startProjectContact}</span>
                        </a>
                        <a href="kentsel-donusum.html" class="f-quick-action-link" style="display:inline-flex;align-items:center;gap:10px;color:rgba(255,255,255,0.85);font-weight:600;font-size:0.75rem;letter-spacing:0.2em;text-decoration:none;transition:color 0.2s ease;">
                            <i class="fa-solid fa-building-shield" style="color:#c5a059"></i> <span data-i18n="footer.urban_renewal_guide">${textData.urbanRenewalGuide}</span>
                        </a>
                    </div>
                </div>

                <div class="f-grid-col">
                    <span class="f-col-label">${textData.contactLabel}</span>
                    <div class="f-contact-item">
                        <small>${textData.phoneLabel}</small>
                        <a href="tel:${cleanPhoneLink}" class="f-giant-link">${data.phone}</a>
                    </div>
                    <div class="f-contact-item">
                        <small>${textData.emailLabel}</small>
                        <a href="mailto:${data.email}" class="f-sub-link">${data.email}</a>
                    </div>
                </div>

                <div class="f-grid-col">
                    <span class="f-col-label">${textData.locationsLabel}</span>
                    ${textData.loc1Title ? `
                    <div class="f-loc-item">
                        <strong>${textData.loc1Title}</strong>
                        <p>${textData.loc1Addr}</p>
                    </div>` : ''}

                    ${textData.loc2Title ? `
                    <div class="f-loc-item">
                        <strong>${textData.loc2Title}</strong>
                        <p>${textData.loc2Addr}</p>
                    </div>` : ''}
                </div>

                <div class="f-grid-col">
                    <span class="f-col-label">${textData.socialLabel}</span>
                    <ul class="f-social-editorial">
                        ${data.instagram ? `
                        <li>
                            <a href="${data.instagram}" target="_blank">
                                <span>INSTAGRAM</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </li>` : ''}
                        ${data.facebook ? `
                        <li>
                            <a href="${data.facebook}" target="_blank">
                                <span>FACEBOOK</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </li>` : ''}
                        ${(data.linkedin && data.linkedin !== '#') ? `
                        <li>
                            <a href="${data.linkedin}" target="_blank">
                                <span>LINKEDIN</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </li>` : ''}
                    </ul>
                </div>
            </div>

            <!-- EN ALT EDİTORYAL MİKRO BANT -->
            <div class="footer-bottom-editorial">
                <span class="f-copy">${textData.copyText}</span>
                <span class="f-tagline">${textData.tagline}</span>
                <a href="iletisim.html" class="f-direct-contact-link" style="color:#c5a059;text-decoration:none;font-weight:700;font-size:0.68rem;letter-spacing:0.25em;text-transform:uppercase;"><i class="fa-solid fa-paper-plane" style="margin-right:6px"></i> <span data-i18n="footer.start_project_contact">${textData.startProjectContact}</span></a>
                <a href="#" class="f-back-top" id="footerBackToTop">${textData.backTopText} <i class="fa-solid fa-arrow-up"></i></a>
            </div>
        </div>
    </footer>
    `;

    const target = document.getElementById("site-footer");
    if (target) {
        target.innerHTML = footerHTML;

        // Yukarı Dön Butonu İşlevi
        document.getElementById("footerBackToTop")?.addEventListener("click", (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

// DİL BUTONLARINA DOĞRUDAN DİNLEYİCİ BAĞLAYAN MOTOR
function attachGlobalLanguageListeners() {
    document.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-lang-btn], [data-lang], .lang-btn, .lang-option");
        if (btn) {
            const langCode = btn.getAttribute("data-lang-btn") || btn.getAttribute("data-lang") || btn.textContent.trim().toLowerCase();
            if (langCode) {
                let targetLang = "tr";
                if (langCode.includes("en") || langCode.includes("english")) targetLang = "en";
                else if (langCode.includes("ar") || langCode.includes("عربي")) targetLang = "ar";
                
                localStorage.setItem("selectedLang", targetLang);
                window.currentLang = targetLang;
                document.documentElement.setAttribute("lang", targetLang);
                
                setTimeout(renderFooterComponent, 100);
            }
        }
    });
}

// SAYFA YÜKLENDİĞİNDE ÇALIŞTIR
function initFooterComponent() {
    renderFooterComponent();
    attachGlobalLanguageListeners();
}
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initFooterComponent);
} else {
    initFooterComponent();
}

// EVENT BİLDİRİMİ İLE RE-RENDER ET
window.addEventListener("languageChanged", () => {
    setTimeout(renderFooterComponent, 50);
});