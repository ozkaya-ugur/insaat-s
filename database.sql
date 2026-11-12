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
  subtitle_tr VARCHAR(255) DEFAULT '',
  subtitle_en VARCHAR(255) DEFAULT '',
  subtitle_ar VARCHAR(255) DEFAULT '',
  description_tr TEXT,
  description_en TEXT,
  description_ar TEXT,
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
CREATE TABLE IF NOT EXISTS project_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  kind ENUM('render','plan','site','video') NOT NULL,
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
