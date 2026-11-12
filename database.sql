-- BUYAPI Yönetim Paneli - MySQL şeması
-- Kullanım: phpMyAdmin'den içe aktarın veya: mysql -u kullanici -p < database.sql

CREATE DATABASE IF NOT EXISTS buyapi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE buyapi;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) DEFAULT '',
  last_login DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  title_tr VARCHAR(200) NOT NULL,
  title_en VARCHAR(200) DEFAULT '',
  title_ar VARCHAR(200) DEFAULT '',
  title_de VARCHAR(200) DEFAULT '',
  subtitle_tr VARCHAR(255) DEFAULT '',
  subtitle_en VARCHAR(255) DEFAULT '',
  subtitle_ar VARCHAR(255) DEFAULT '',
  subtitle_de VARCHAR(255) DEFAULT '',
  description_tr TEXT,
  description_en TEXT,
  description_ar TEXT,
  description_de TEXT NULL,
  cat VARCHAR(120) DEFAULT '',            -- Şehir adı (ör. Sakarya)
  loc VARCHAR(160) DEFAULT '',            -- Lokasyon (ör. Serdivan / Sakarya)
  city_key VARCHAR(20) DEFAULT 'tr-sk',   -- Highcharts hc-key (tr-sk, tr-ib, tr-an ...)
  type VARCHAR(200) DEFAULT 'konut',      -- virgülle: konut,ticari,sanayi,kentsel
  status ENUM('devam-eden','tamamlanan','yeni-proje') DEFAULT 'devam-eden',
  is_new TINYINT(1) NOT NULL DEFAULT 0,          -- "Yeni Proje" rozeti / Yeni Projeler sekmesi
  is_urban_renewal TINYINT(1) NOT NULL DEFAULT 0, -- Kentsel dönüşüm projesi
  cover_image VARCHAR(255) DEFAULT '',
  area VARCHAR(60) DEFAULT '',
  concept VARCHAR(120) DEFAULT '',
  delivery VARCHAR(60) DEFAULT '',
  catalog_file VARCHAR(255) DEFAULT '',
  street_view_code TEXT NULL,
  google_map_code TEXT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  seo_title VARCHAR(200) DEFAULT '',
  seo_desc VARCHAR(300) DEFAULT '',
  seo_keywords VARCHAR(300) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_projects_is_new (is_new),
  INDEX idx_projects_is_urban_renewal (is_urban_renewal)
) ENGINE=InnoDB;

-- Galeri: render, plan, site (şantiye), video
CREATE TABLE IF NOT EXISTS pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(160) NOT NULL UNIQUE,
  title_tr VARCHAR(200) NOT NULL,
  title_en VARCHAR(200) DEFAULT '',
  title_ar VARCHAR(200) DEFAULT '',
  content_tr MEDIUMTEXT NULL,
  content_en MEDIUMTEXT NULL,
  content_ar MEDIUMTEXT NULL,
  page_type VARCHAR(40) DEFAULT 'standart',
  layout_template VARCHAR(40) NOT NULL DEFAULT 'standart',
  banner_image VARCHAR(255) DEFAULT '',
  static_file VARCHAR(120) DEFAULT NULL,
  show_in_menu TINYINT(1) NOT NULL DEFAULT 1,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT DEFAULT 0,
  seo_title VARCHAR(200) DEFAULT '',
  seo_desc VARCHAR(300) DEFAULT '',
  seo_keywords VARCHAR(300) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  kind ENUM('hero','gallery','render','plan','site','video') NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  caption VARCHAR(160) DEFAULT '',
  sort_order INT DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sliders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title_tr VARCHAR(200) NOT NULL,
  title_en VARCHAR(200) DEFAULT '',
  title_ar VARCHAR(200) DEFAULT '',
  subtitle_tr VARCHAR(255) DEFAULT '',
  subtitle_en VARCHAR(255) DEFAULT '',
  subtitle_ar VARCHAR(255) DEFAULT '',
  image_url VARCHAR(255) NOT NULL,
  link_url VARCHAR(255) DEFAULT 'projeler.html',
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS languages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(10) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL,
  flag VARCHAR(16) DEFAULT '🌐',
  direction ENUM('ltr','rtl') NOT NULL DEFAULT 'ltr',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO languages (code, name, flag, direction, is_default, is_active, sort_order) VALUES
  ('tr', 'Türkçe',  '🇹🇷', 'ltr', 1, 1, 1),
  ('en', 'English', '🇬🇧', 'ltr', 0, 1, 2),
  ('ar', 'العربية', '🇸🇦', 'rtl', 0, 1, 3)
ON DUPLICATE KEY UPDATE name=VALUES(name), flag=VALUES(flag), direction=VALUES(direction),
  is_default=VALUES(is_default), is_active=VALUES(is_active), sort_order=VALUES(sort_order);

CREATE TABLE IF NOT EXISTS translations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lang CHAR(2) NOT NULL,
  t_key VARCHAR(120) NOT NULL,
  t_value TEXT,
  UNIQUE KEY uq_lang_key (lang, t_key)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS seo_settings (
  lang CHAR(2) PRIMARY KEY,
  title VARCHAR(200) DEFAULT '',
  description VARCHAR(300) DEFAULT '',
  keywords VARCHAR(300) DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS site_settings (
  s_key VARCHAR(80) PRIMARY KEY,
  s_value TEXT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS urban_renewal_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_url VARCHAR(255) NOT NULL,
  title_tr VARCHAR(200) DEFAULT '', title_en VARCHAR(200) DEFAULT '', title_ar VARCHAR(200) DEFAULT '',
  subtitle_tr VARCHAR(400) DEFAULT '', subtitle_en VARCHAR(400) DEFAULT '', subtitle_ar VARCHAR(400) DEFAULT '',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS urban_renewal_faqs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_tr VARCHAR(400) DEFAULT '', question_en VARCHAR(400) DEFAULT '', question_ar VARCHAR(400) DEFAULT '',
  answer_tr TEXT NULL, answer_en TEXT NULL, answer_ar TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) DEFAULT '',
  email VARCHAR(160) DEFAULT '',
  project VARCHAR(160) DEFAULT '',
  message TEXT,
  source VARCHAR(30) DEFAULT 'iletisim',
  is_read TINYINT(1) DEFAULT 0,
  ip VARCHAR(45) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Başlangıç verileri ---------------------------------------------------
-- Yönetici hesabı burada oluşturulmaz (hazır şifre güvenlik riskidir).
-- Kurulumdan sonra tarayıcıdan /admin/setup.php açın; ilk yöneticiyi kendi şifrenizle
-- oluşturur ve yönetici varsa kendini kilitler.

INSERT IGNORE INTO projects (slug,title_tr,title_en,subtitle_tr,subtitle_en,description_tr,cat,loc,city_key,type,status,cover_image,area,concept,delivery,is_featured,sort_order) VALUES
('intense','Buyapı Intense','Buyapi Intense','Teknik İnovasyon & Lüks Konut','Technical Innovation & Luxury Residence','Buyapı Intense, Serdivan''ın en değerli noktasında yükselen anıtsal bir mimarlık eseridir.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','images/banner-1.webp','18.500 m²','A++ Avant-Garde','2026',1,1),
('workinn','Buyapı Work Inn','Buyapi Work Inn','Anıtsal İş Merkezi & Plaza','Monumental Business Center & Plaza','İş dünyasına yön veren anıtsal ve vizyoner mimari.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','images/banner-2.webp','','','',1,2),
('silence','Buyapı Silence','Buyapi Silence','Doğayla Entegre Lüks Rezidans','Luxury Residence Integrated with Nature','Doğa ile entegre, rafine ve özgün detaylar.','Sakarya','Sapanca / Sakarya','tr-sk','konut','devam-eden','images/banner-3.webp','','','',1,3);

INSERT IGNORE INTO sliders (title_tr,subtitle_tr,image_url,link_url,sort_order) VALUES
('BUYAPI INTENSE','Teknik inovasyon ve lüks yaşamın minimalist sentezi.','images/banner-1.webp','proje-detay.html?id=intense',1),
('WORK INN','İş dünyasına yön veren anıtsal ve vizyoner mimari.','images/banner-2.webp','proje-detay.html?id=workinn',2),
('BUYAPI SILENCE','Doğa ile entegre, rafine ve özgün detaylar.','images/banner-3.webp','proje-detay.html?id=silence',3),
('POMEGA ENERJİ','Büyük ölçekli, yüksek mühendislik odaklı sanayi yatırımı.','images/banner-4.webp','projeler.html',4);

-- Üst menü ve genel sayfa yönetimi için çekirdek sayfalar
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

INSERT IGNORE INTO seo_settings (lang,title,description,keywords) VALUES
('tr','BUYAPI | Mimari & İnşaat Mühendisliği','Sakarya merkezli prestijli konut, ticari ve endüstriyel projeler.','buyapı, inşaat, sakarya, konut, rezidans'),
('en','BUYAPI | Architecture & Construction Engineering','Prestigious residential, commercial and industrial projects based in Sakarya.','buyapi, construction, sakarya, residence'),
('ar','BUYAPI | الهندسة المعمارية والإنشاءات','مشاريع سكنية وتجارية وصناعية مرموقة في ساكاريا.','بويابي, إنشاءات, ساكاريا');

INSERT IGNORE INTO translations (lang,t_key,t_value) VALUES
('tr','hero.tag','PROJE'),('tr','hero.btn','PROJEYİ İNCELE'),
('en','hero.tag','PROJECT'),('en','hero.btn','VIEW PROJECT'),
('ar','hero.tag','مشروع'),('ar','hero.btn','استعرض المشروع');

INSERT IGNORE INTO site_settings (s_key,s_value) VALUES
('phone','444 33 43'),('whatsapp','904443343'),('email','info@buyapi.com.tr'),
('instagram','https://instagram.com/buyapitr'),('facebook','https://facebook.com/buyapiinsaat'),
('notify_email','');

-- İletişim sayfası ve footer metinleri (panelden düzenlenebilir varsayılanlar)
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_banner_tag','BİZİMLE İLETİŞİME GEÇİN') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_banner_tag_en','GET IN TOUCH WITH US') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_banner_tag_ar','تواصلوا معنا') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_page_title','İletişim & Ofislerimiz') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_page_title_en','Contact & Our Offices') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_page_title_ar','اتصل بنا ومكاتبنا') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_form_title','Projenizi Birlikte Başlatalım') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_form_title_en','Let\'s Start Your Project Together') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_form_title_ar','لنبدأ مشروعكم معاً') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_form_desc','Hayalinizdeki konut, iş merkezi veya mimari yatırımlarınız için ekibimize mesaj bırakabilirsiniz.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_form_desc_en','Leave a message for our team about your dream home, business center, or architectural investment.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('contact_form_desc_ar','يمكنكم ترك رسالة لفريقنا بخصوص منزل أحلامكم أو مركز الأعمال أو استثماركم المعماري.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_section_tag','PROJE & İŞ BİRLİĞİ') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_section_tag_en','PROJECT & COLLABORATION') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_section_tag_ar','المشروع والتعاون') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_title','Geleceği Şekillendiren Yapılarda <br> İmzanız Olsun.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_title_en','Leave Your Mark on the Buildings of Tomorrow.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_title_ar','ليكن لك بصمة في إعمار المستقبل.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_desc','Hayalinizdeki lüks konut, iş merkezi veya endüstriyel proje için mimari ekibimizle doğrudan iletişime geçin.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_desc_en','Contact our architectural team directly to bring your dream luxury home, business center, or industrial project to life.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_desc_ar','تواصلوا مباشرة مع فريقنا المعماري من أجل مشروع أحلامكم السكني الفاخر، أو التجاري، أو الصناعي.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_btn_text','Projenizi Başlatın') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_btn_text_en','Start your project') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_btn_text_ar','ابتدئ مشروعك') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_brand_desc','35 yılı aşkın mühendislik birikimiyle; teknik inovasyonu mimari estetikle buluşturan prestij projeleri üretiyoruz.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_brand_desc_en','With over 35 years of engineering expertise, we create prestigious projects that fuse technical innovation with architectural aesthetics.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_brand_desc_ar','مع أكثر من 35 عاماً من الخبرة الهندسية؛ نصنع مشاريع فاخرة تمزج بين الابتكار التكنولوجي والجماليات المعمارية.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_copy_text','© 2026 BUYAPI İNŞAAT A.Ş. • TÜM HAKLARI SAKLIDIR.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_copy_text_en','© 2026 BUYAPI İNŞAAT A.Ş. • ALL RIGHTS RESERVED.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_copy_text_ar','© 2026 BUYAPI İNŞAAT A.Ş. • جميع الحقوق محفوظة.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_tagline','ARCHITECTURAL PRESTIGE & ENGINEERING') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_tagline_en','ARCHITECTURAL PRESTIGE & ENGINEERING') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_tagline_ar','ARCHITECTURAL PRESTIGE & ENGINEERING') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('footer_cta_btn_link','iletisim.html') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);

-- Projeler ve kentsel dönüşüm sayfa metinleri (panelden düzenlenebilir varsayılanlar)
INSERT INTO site_settings (s_key,s_value) VALUES ('projects_banner_tag','MİMARİ PORTFÖY') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('projects_banner_tag_en','ARCHITECTURAL PORTFOLIO') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('projects_banner_tag_ar','المحفظة المعمارية') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('projects_banner_title','Proje Kataloğu') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('projects_banner_title_en','Project Catalogue') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('projects_banner_title_ar','كتالوج المشاريع') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_tag','YASAL MEVZUAT VE DESTEKLER') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_tag_en','LEGAL FRAMEWORK AND INCENTIVES') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_tag_ar','التشريعات والحوافز') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_title','6306 Sayılı Kanun Kapsamında Haklarınız') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_title_en','Your Rights Under Law No. 6306') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_title_ar','حقوقكم في إطار القانون رقم 6306') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_projects_tag','PRESTİJ VE DEĞER YARATAN İMZALAR') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_projects_tag_en','SIGNATURES THAT CREATE PRESTIGE AND VALUE') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_projects_tag_ar','بصمات تصنع الهيبة والقيمة') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_projects_title','Kentsel Dönüşüm Projelerimiz') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_projects_title_en','Our Urban Renewal Projects') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_projects_title_ar','مشاريعنا في التحول الحضري') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_compare_tag','NEDEN YENİLEMELİSİNİZ?') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_compare_tag_en','WHY SHOULD YOU RENEW?') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_compare_tag_ar','لماذا يجب التجديد؟') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_compare_title','Eski Yapı vs. BUYAPI Kentsel Dönüşüm') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_compare_title_en','Old Building vs. BUYAPI Urban Renewal') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_compare_title_ar','المبنى القديم مقابل التحول الحضري من BUYAPI') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_faq_tag','MERAK EDİLENLER') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_faq_tag_en','FREQUENTLY ASKED') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_faq_tag_ar','الأسئلة الشائعة') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_faq_title','Sıkça Sorulan Sorular') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_faq_title_en','Frequently Asked Questions') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_faq_title_ar','الأسئلة المتكررة') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_form_tag','KENTSEL DÖNÜŞÜM ÖN BAŞVURU') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_form_tag_en','URBAN RENEWAL PRE-APPLICATION') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_form_tag_ar','الطلب المسبق للتحول الحضري') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_form_title','Binanızı Yenilemek İçin İlk Adımı Atın') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_form_title_en','Take the First Step to Renew Your Building') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_form_title_ar','اتخذوا الخطوة الأولى لتجديد مبناكم') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);

-- Kentsel dönüşüm hero/istatistik/yasa/avantaj metinleri ve varsayılan slider+SSS kayıtları
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_badge','6306 SAYILI KANUN GÜVENCESİ') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_badge_en','ASSURANCE UNDER LAW NO. 6306') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_badge_ar','ضمان بموجب القانون رقم 6306') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_title','Geleceğe Güvenle Bakan
Kentsel Dönüşüm Projeleri') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_title_en','Urban Renewal Projects
Built with Confidence for the Future') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_title_ar','مشاريع التحول الحضري
بثقة نحو المستقبل') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_desc','BUYAPI, riskli ve yıpranmış yapıları 35 yılı aşkın ileri mühendislik tecrübesi, şeffaf hukuki danışmanlık ve yüksek mimari estetikle depreme dayanıklı, çağdaş prestij eserlerine dönüştürüyor.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_desc_en','BUYAPI transforms risky and worn-out buildings into earthquake-resistant, contemporary prestige landmarks through more than 35 years of advanced engineering expertise, transparent legal consulting, and high architectural aesthetics.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_hero_desc_ar','تحوّل BUYAPI المباني المتهالكة وعالية المخاطر إلى معالم معاصرة مقاومة للزلازل، عبر أكثر من 35 عاماً من الخبرة الهندسية المتقدمة، والاستشارات القانونية الشفافة، والجمالية المعمارية الرفيعة.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_1_num','35+') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_1_num_en','35+') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_1_num_ar','35+') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_1_lbl','Yıllık Mühendislik Deneyimi') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_1_lbl_en','Years of Engineering Experience') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_1_lbl_ar','سنة من الخبرة الهندسية') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_2_num','%100') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_2_num_en','100%') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_2_num_ar','100%') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_2_lbl','Hukuki ve Yasal Güvence') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_2_lbl_en','Legal and Regulatory Assurance') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_2_lbl_ar','ضمان قانوني وتنظيمي') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_3_num','0 Risk') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_3_num_en','0 Risk') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_3_num_ar','0 مخاطر') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_3_lbl','Hak Sahipleri Koruma Modeli') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_3_lbl_en','Rights-Holder Protection Model') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_3_lbl_ar','نموذج حماية أصحاب الحقوق') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_4_num','A++') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_4_num_en','A++') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_4_num_ar','A++') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_4_lbl','Statik ve Deprem Güvenliği') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_4_lbl_en','Structural and Earthquake Safety') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_stat_4_lbl_ar','السلامة الإنشائية ومقاومة الزلازل') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_info','6306 Sayılı Kanun\'da yapılan son düzenlemeler uyarınca, kentsel dönüşüm kararı almak için artık 2/3 çoğunluk aranmamaktadır. Hissedarların %50+1 (salt çoğunluk) oyu ile binanızın yenilenmesine başlanabilir. Ayrıca tüm noter harçları, tapu devir masrafları ve belediye harçlarından %100 muafiyet sağlanmaktadır.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_info_en','Under the latest amendments to Law No. 6306, a 2/3 majority is no longer required to decide on urban renewal. Renovation can start with a 50%+1 simple majority vote of shareholders. In addition, 100% exemption is provided for notary fees, title deed transfer costs, and municipal charges.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_law_info_ar','وفقاً لأحدث التعديلات على القانون رقم 6306، لم يعد اشتراط أغلبية الثلثين ضرورياً لاتخاذ قرار التحول الحضري. يمكن البدء بالتجديد بأغلبية بسيطة 1+50% من أصوات المالكين. كما يُمنح إعفاء كامل بنسبة 100% من رسوم الكاتب بالعدل وتكاليف نقل سند الملكية والرسوم البلدية.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_contact_phone','444 33 43') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_contact_phone_en','444 33 43') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_contact_phone_ar','444 33 43') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_contact_email','kentsel@buyapi.com.tr') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_contact_email_en','kentsel@buyapi.com.tr') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_contact_email_ar','kentsel@buyapi.com.tr') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_1_title','%100 Harç & Vergi Muafiyeti') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_1_title_en','100% Fee & Tax Exemption') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_1_title_ar','إعفاء 100% من الرسوم والضرائب') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_1_desc','Tapu kadastro harcı, noter masrafı, damga vergisi, veraset-intikal ve belediye ruhsat harçlarının tamamından muaf olarak yüzbinlerce liralık tasarruf sağlarsınız.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_1_desc_en','You save substantial amounts through full exemption from title deed fees, notary costs, stamp tax, inheritance-transfer charges, and municipal permit fees.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_1_desc_ar','تحققون وفورات كبيرة بفضل الإعفاء الكامل من رسوم السجل العقاري، وتكاليف الكاتب بالعدل، وضريبة الدمغة، ورسوم الإرث/الانتقال، ورسوم تراخيص البلدية.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_2_title','Düzenli Kira Yardımı Desteği') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_2_title_en','Regular Rental Assistance Support') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_2_title_ar','دعم منتظم لمساعدة الإيجار') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_2_desc','İnşaat süresince Çevre, Şehircilik ve İklim Değişikliği Bakanlığı tarafından hak sahiplerine karşılıksız aylık kira yardımı sağlanır. Başvuru işlemlerini BUYAPI takip eder.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_2_desc_en','During construction, rights holders receive non-refundable monthly rental assistance from the Ministry of Environment, Urbanization and Climate Change. BUYAPI follows all application procedures.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_2_desc_ar','خلال فترة الإنشاء، يحصل أصحاب الحقوق على دعم إيجار شهري غير مسترد من وزارة البيئة والتخطيط العمراني وتغير المناخ. وتتولى BUYAPI متابعة إجراءات التقديم.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_3_title','Düşük Faizli Dönüşüm Kredisi') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_3_title_en','Low-Interest Renewal Loan') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_3_title_ar','قرض تحويل منخفض الفائدة') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_3_desc','Kamu ve anlaşmalı bankalar aracılığıyla devlet faiz destekli, 120 aya varan vadelerle uygun koşullu kentsel dönüşüm kredisi imkanından yararlanabilirsiniz.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_3_desc_en','You can benefit from government-interest-supported urban renewal loans through public and partner banks, with suitable terms up to 120 months.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_3_desc_ar','يمكنكم الاستفادة من قروض التحول الحضري المدعومة بفائدة حكومية عبر البنوك الحكومية والمتعاقدة، بشروط مناسبة وآجال تصل إلى 120 شهراً.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_4_title','Noter Tasdikli Şeffaf Sözleşme') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_4_title_en','Notary-Certified Transparent Contract') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_4_title_ar','عقد شفاف موثق لدى الكاتب بالعدل') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_4_desc','Bağımsız bölümlerin büyüklüğü, şerefiye payları ve malzeme teknik şartnamesi noter güvencesiyle kayıt altına alınır; hiçbir hak kaybı riski oluşmaz.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_4_desc_en','Unit sizes, premium shares, and technical material specifications are recorded under notary assurance; no risk of rights loss arises.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO site_settings (s_key,s_value) VALUES ('kd_benefit_4_desc_ar','يتم توثيق مساحات الوحدات المستقلة، وحصص التقييم، والمواصفات الفنية للمواد بضمان الكاتب بالعدل؛ ولا ينشأ أي خطر لفقدان الحقوق.') ON DUPLICATE KEY UPDATE s_value=VALUES(s_value);
INSERT INTO urban_renewal_faqs (question_tr,answer_tr,question_en,answer_en,question_ar,answer_ar,is_active,sort_order) SELECT 'Kentsel dönüşüm kararı için hangi çoğunluk gerekir?','Yeni düzenlemeyle birlikte, kentsel dönüşüm süreci için hissedarların %50+1 salt çoğunluk oyu yeterlidir.','What majority is required for an urban renewal decision?','With the new regulation, a simple majority of 50%+1 shareholder votes is sufficient to start the urban renewal process.','ما نسبة الأغلبية المطلوبة لاتخاذ قرار التحول الحضري؟','مع التنظيم الجديد، تكفي أغلبية بسيطة بنسبة 1+50% من أصوات المالكين لبدء عملية التحول الحضري.',1,(SELECT COALESCE(MAX(sort_order),0)+1 FROM urban_renewal_faqs) WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_faqs WHERE question_tr='Kentsel dönüşüm kararı için hangi çoğunluk gerekir?');
INSERT INTO urban_renewal_faqs (question_tr,answer_tr,question_en,answer_en,question_ar,answer_ar,is_active,sort_order) SELECT 'Dönüşüm sürecinde devlet destekleri nelerdir?','Hak sahipleri için kira yardımı, harç ve vergi muafiyetleri ile faiz destekli dönüşüm kredisi imkanları bulunmaktadır.','What state supports are available during the renewal process?','Rights holders can benefit from rental assistance, fee/tax exemptions, and interest-supported renewal loan opportunities.','ما هي أشكال الدعم الحكومي خلال عملية التحول؟','يمكن لأصحاب الحقوق الاستفادة من دعم الإيجار، وإعفاءات الرسوم/الضرائب، وفرص القروض المدعومة بالفائدة.',1,(SELECT COALESCE(MAX(sort_order),0)+1 FROM urban_renewal_faqs) WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_faqs WHERE question_tr='Dönüşüm sürecinde devlet destekleri nelerdir?');
INSERT INTO urban_renewal_faqs (question_tr,answer_tr,question_en,answer_en,question_ar,answer_ar,is_active,sort_order) SELECT 'Başvuru ve resmi süreçleri kim takip ediyor?','Teknik inceleme, hak sahipleri koordinasyonu ve resmi başvuru süreçleri BUYAPI uzman ekipleri tarafından yürütülür.','Who manages applications and official procedures?','Technical assessments, rights-holder coordination, and official application procedures are carried out by BUYAPI expert teams.','من يتابع طلبات التقديم والإجراءات الرسمية؟','تتولى فرق BUYAPI المتخصصة أعمال الفحص الفني، وتنسيق أصحاب الحقوق، وإجراءات التقديم الرسمية.',1,(SELECT COALESCE(MAX(sort_order),0)+1 FROM urban_renewal_faqs) WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_faqs WHERE question_tr='Başvuru ve resmi süreçleri kim takip ediyor?');
INSERT INTO urban_renewal_faqs (question_tr,answer_tr,question_en,answer_en,question_ar,answer_ar,is_active,sort_order) SELECT 'Proje sonunda hangi güvenceler sağlanır?','Sözleşme kapsamı, teknik şartname, bağımsız bölüm dağılımı ve teslim taahhütleri noter ve mevzuat güvencesiyle kayıt altına alınır.','What assurances are provided at project completion?','Contract scope, technical specifications, unit allocation, and delivery commitments are recorded under notary and legal assurance.','ما الضمانات المتوفرة عند اكتمال المشروع؟','يتم توثيق نطاق العقد، والمواصفات الفنية، وتوزيع الوحدات، والتزامات التسليم بضمان قانوني وتوثيق الكاتب بالعدل.',1,(SELECT COALESCE(MAX(sort_order),0)+1 FROM urban_renewal_faqs) WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_faqs WHERE question_tr='Proje sonunda hangi güvenceler sağlanır?');
INSERT INTO urban_renewal_slides (image_url,title_tr,title_en,title_ar,subtitle_tr,subtitle_en,subtitle_ar,is_active,sort_order) SELECT 'images/banner-1.webp','Geleceğe Güvenle Bakan Kentsel Dönüşüm','Urban Renewal with Confidence for the Future','تحول حضري بثقة نحو المستقبل','Depreme dayanıklı yeni yaşam alanları','Earthquake-resistant new living spaces','مساحات سكنية جديدة مقاومة للزلازل',1,1 WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_slides WHERE image_url='images/banner-1.webp');
INSERT INTO urban_renewal_slides (image_url,title_tr,title_en,title_ar,subtitle_tr,subtitle_en,subtitle_ar,is_active,sort_order) SELECT 'images/banner-4.webp','Yüksek Mühendislik ve Hukuki Güvence','Advanced Engineering and Legal Assurance','هندسة متقدمة وضمان قانوني','Şeffaf süreç, güçlü teslim modeli','Transparent process, strong delivery model','عملية شفافة ونموذج تسليم قوي',1,2 WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_slides WHERE image_url='images/banner-4.webp');
INSERT INTO urban_renewal_slides (image_url,title_tr,title_en,title_ar,subtitle_tr,subtitle_en,subtitle_ar,is_active,sort_order) SELECT 'images/banner-2.webp','Prestij ve Değer Üreten Dönüşüm','Renewal that Creates Prestige and Value','تحول يصنع الهيبة والقيمة','Hak sahiplerine odaklı sürdürülebilir dönüşüm','Sustainable renewal focused on rights holders','تحول مستدام يركز على أصحاب الحقوق',1,3 WHERE NOT EXISTS (SELECT 1 FROM urban_renewal_slides WHERE image_url='images/banner-2.webp');
-- Menü (nav) çevirileri: dil değişince üst menü ve mobil menü de değişsin.
INSERT INTO translations (lang, t_key, t_value) VALUES
('tr','nav.home','ANASAYFA'),
('tr','nav.about','KURUMSAL'),
('tr','nav.projects','PROJELER'),
('tr','nav.contact','İLETİŞİM'),
('tr','nav.urban_renewal','KENTSEL DÖNÜŞÜM'),
('tr','nav.map','HARİTA'),
('tr','nav.offices','OFİSLERİMİZ'),
('en','nav.home','HOME'),
('en','nav.about','CORPORATE'),
('en','nav.projects','PROJECTS'),
('en','nav.contact','CONTACT'),
('en','nav.urban_renewal','URBAN RENEWAL'),
('en','nav.map','MAP'),
('en','nav.offices','OUR OFFICES'),
('ar','nav.home','الرئيسية'),
('ar','nav.about','الشركة'),
('ar','nav.projects','المشاريع'),
('ar','nav.contact','اتصل بنا'),
('ar','nav.urban_renewal','التجديد الحضري'),
('ar','nav.map','الخريطة'),
('ar','nav.offices','مكاتبنا'),
('de','nav.home','STARTSEITE'),
('de','nav.about','UNTERNEHMEN'),
('de','nav.projects','PROJEKTE'),
('de','nav.contact','KONTAKT'),
('de','nav.urban_renewal','STADTERNEUERUNG'),
('de','nav.map','KARTE'),
('de','nav.offices','UNSERE BÜROS')
ON DUPLICATE KEY UPDATE t_value = VALUES(t_value);


-- Ek SQL seed dosyaları (idempotent)
SOURCE sql/004_projects_seed.sql;
SOURCE sql/005_i18n_ui.sql;

SOURCE sql/006_slider_slogan_analytics.sql;
SOURCE sql/007_slogan_translations.sql;
SOURCE sql/008_corp_content.sql;
SOURCE sql/010_urban_demo_projects.sql;
