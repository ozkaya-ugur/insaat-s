-- Sayfa yönetimi: layout şablonu + statik dosya eşleme + başlangıç sayfaları

ALTER TABLE pages
  ADD COLUMN IF NOT EXISTS layout_template VARCHAR(40) NOT NULL DEFAULT 'standart' AFTER page_type,
  ADD COLUMN IF NOT EXISTS static_file VARCHAR(120) DEFAULT NULL AFTER banner_image;

-- Eski kayıtlarda boş/NULL gelebilecek alanları normalize et
UPDATE pages
SET layout_template = 'standart'
WHERE layout_template IS NULL OR layout_template = '';

-- Mevcut statik sayfaları idempotent biçimde seed et
INSERT INTO pages (
    slug, title_tr, title_en, title_ar,
    content_tr, content_en, content_ar,
    page_type, layout_template, banner_image, static_file,
    show_in_menu, is_active, sort_order,
    seo_title, seo_desc, seo_keywords
) VALUES
('anasayfa', 'Anasayfa', 'Home', 'الصفحة الرئيسية', '', '', '', 'standart', 'standart', '', 'index.html', 1, 1, 1, '', '', ''),
('projeler', 'Projeler', 'Projects', 'المشاريع', '', '', '', 'projeler', 'genis', '', 'projeler.html', 1, 1, 2, '', '', ''),
('kurumsal', 'Kurumsal', 'Corporate', 'المؤسسية', '', '', '', 'hakkimizda', 'iki-kolon', '', 'kurumsal.html', 1, 1, 3, '', '', ''),
('kentsel-donusum', 'Kentsel Dönüşüm', 'Urban Renewal', 'التجديد الحضري', '', '', '', 'kentsel_donusum', 'iletisim', '', 'kentsel-donusum.html', 1, 1, 4, '', '', ''),
('iletisim', 'İletişim', 'Contact', 'اتصل بنا', '', '', '', 'iletisim', 'iletisim', '', 'iletisim.html', 1, 1, 5, '', '', ''),
('harita', 'Harita', 'Map', 'الخريطة', '', '', '', 'ozel', 'genis', '', 'harita.html', 0, 1, 6, '', '', '')
ON DUPLICATE KEY UPDATE
    title_tr = VALUES(title_tr),
    title_en = VALUES(title_en),
    title_ar = VALUES(title_ar),
    page_type = VALUES(page_type),
    layout_template = VALUES(layout_template),
    static_file = VALUES(static_file),
    show_in_menu = VALUES(show_in_menu),
    is_active = VALUES(is_active),
    sort_order = VALUES(sort_order);