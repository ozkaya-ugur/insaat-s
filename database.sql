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
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
  banner_image VARCHAR(255) DEFAULT '',
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
  kind ENUM('hero','render','plan','site','video') NOT NULL,
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
('intense','Buyapı Intense','Buyapi Intense','Teknik İnovasyon & Lüks Konut','Technical Innovation & Luxury Residence','Buyapı Intense, Serdivan''ın en değerli noktasında yükselen anıtsal bir mimarlık eseridir.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','images/banner-1.png','18.500 m²','A++ Avant-Garde','2026',1,1),
('workinn','Buyapı Work Inn','Buyapi Work Inn','Anıtsal İş Merkezi & Plaza','Monumental Business Center & Plaza','İş dünyasına yön veren anıtsal ve vizyoner mimari.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','images/banner-2.png','','','',1,2),
('silence','Buyapı Silence','Buyapi Silence','Doğayla Entegre Lüks Rezidans','Luxury Residence Integrated with Nature','Doğa ile entegre, rafine ve özgün detaylar.','Sakarya','Sapanca / Sakarya','tr-sk','konut','devam-eden','images/banner-3.png','','','',1,3);

INSERT IGNORE INTO sliders (title_tr,subtitle_tr,image_url,link_url,sort_order) VALUES
('BUYAPI INTENSE','Teknik inovasyon ve lüks yaşamın minimalist sentezi.','images/banner-1.png','proje-detay.html?id=intense',1),
('WORK INN','İş dünyasına yön veren anıtsal ve vizyoner mimari.','images/banner-2.png','proje-detay.html?id=workinn',2),
('BUYAPI SILENCE','Doğa ile entegre, rafine ve özgün detaylar.','images/banner-3.png','proje-detay.html?id=silence',3),
('POMEGA ENERJİ','Büyük ölçekli, yüksek mühendislik odaklı sanayi yatırımı.','images/banner-4.png','projeler.html',4);

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
