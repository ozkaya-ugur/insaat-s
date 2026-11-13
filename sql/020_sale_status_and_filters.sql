-- 020: "Satışta" proje durumu, satıştaki projeleri öne çıkarma ayarı ve filtre/arama çevirileri
-- Idempotent: tekrar çalıştırılabilir.

-- 1) status ENUM'una 'satista' eklenir
ALTER TABLE `projects`
  MODIFY COLUMN `status` ENUM('devam-eden','tamamlanan','yeni-proje','satista') DEFAULT 'devam-eden';

-- 2) Ayar: satıştaki projeler listede en üste alınsın mı? (0 = verilen sıralama korunur)
INSERT INTO `site_settings` (`s_key`,`s_value`) VALUES ('projects_sale_first','0')
  ON DUPLICATE KEY UPDATE `s_value` = `s_value`;

-- 3) Çeviriler
INSERT INTO `translations` (`lang`,`t_key`,`t_value`) VALUES
  ('tr','status.satista','Satışta'),
  ('en','status.satista','For Sale'),
  ('ar','status.satista','متاح للبيع'),
  ('tr','projects.tab_sale','SATIŞTA'),
  ('en','projects.tab_sale','FOR SALE'),
  ('ar','projects.tab_sale','متاح للبيع'),
  ('tr','projects.search_placeholder','Proje adı, lokasyon veya anahtar kelime ara...'),
  ('en','projects.search_placeholder','Search by project name, location or keyword...'),
  ('ar','projects.search_placeholder','ابحث باسم المشروع أو الموقع أو الكلمة المفتاحية...'),
  ('tr','projects.search_clear','Aramayı temizle'),
  ('en','projects.search_clear','Clear search'),
  ('ar','projects.search_clear','مسح البحث'),
  ('tr','projects.filter_district_all','Tüm İlçeler'),
  ('en','projects.filter_district_all','All Districts'),
  ('ar','projects.filter_district_all','جميع الأحياء'),
  ('tr','projects.filter_location','Lokasyon'),
  ('en','projects.filter_location','Location'),
  ('ar','projects.filter_location','الموقع'),
  ('tr','projects.filter_district','İlçe'),
  ('en','projects.filter_district','District'),
  ('ar','projects.filter_district','الحي'),
  ('tr','projects.filter_type','Yapı Türü'),
  ('en','projects.filter_type','Building Type'),
  ('ar','projects.filter_type','نوع المبنى'),
  ('tr','projects.search_results','"{q}" için {n} proje bulundu'),
  ('en','projects.search_results','{n} projects found for "{q}"'),
  ('ar','projects.search_results','تم العثور على {n} مشروع لـ "{q}"')
ON DUPLICATE KEY UPDATE `t_value` = VALUES(`t_value`);
