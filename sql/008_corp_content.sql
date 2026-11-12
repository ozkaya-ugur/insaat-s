-- 008: Hakkımızda (kurumsal) içerik ve istatistik renkleri — TR/EN/AR (idempotent)
SET NAMES utf8mb4;

INSERT INTO site_settings (s_key, s_value) VALUES
  ('corp_banner_tag', 'BUYAPI MİMARİSİ'),
  ('corp_banner_title', 'Kurumsal Felsefemiz'),
  ('corp_section_tag', 'GELECEĞİ İNŞA EDİYORUZ'),
  ('corp_title', 'Sıradışı ve prestijli projeleri hayata geçiriyoruz.'),
  ('corp_lead', 'Faaliyete başladığımız günden itibaren pek çok sıra dışı projeyi hayata geçirme başarısını göstererek sektörde ilklerin öncüsü olduk.'),
  ('corp_body', 'BUYAPI inşaatın sistematik ve kurumsal yapısına yakışır şekilde; proje aşamasından başlayarak teknik inovasyon, ürün geliştirme, mimari ve yapım-üretim dahil yüksek kalitede, anahtar teslim işlerin yanı sıra ofis, işyeri, plazalar, konut ve endüstriyel tesis projelerinde öne çıkıyoruz.'),
  ('corp_banner_tag_en', 'BUYAPI ARCHITECTURE'),
  ('corp_banner_title_en', 'Our Corporate Philosophy'),
  ('corp_section_tag_en', 'WE BUILD THE FUTURE'),
  ('corp_title_en', 'We bring extraordinary and prestigious projects to life.'),
  ('corp_lead_en', 'Since the day we started operating, we have successfully delivered many extraordinary projects and become a pioneer of firsts in our sector.'),
  ('corp_body_en', 'In line with BUYAPI’s systematic and corporate structure, we stand out in high-quality turnkey works — from project stage through technical innovation, product development, architecture and construction — as well as in office, workplace, plaza, residential and industrial facility projects.'),
  ('corp_banner_tag_ar', 'معمارية بويايبي'),
  ('corp_banner_title_ar', 'فلسفتنا المؤسسية'),
  ('corp_section_tag_ar', 'نبني المستقبل'),
  ('corp_title_ar', 'نحوّل المشاريع الاستثنائية والمرموقة إلى واقع.'),
  ('corp_lead_ar', 'منذ يوم انطلاقنا نجحنا في تنفيذ العديد من المشاريع الاستثنائية وأصبحنا رائداً في مجالات السباقة بقطاعنا.'),
  ('corp_body_ar', 'بما يتوافق مع البنية المنهجية والمؤسسية لبويايبي، نتميز في الأعمال الجاهزة عالية الجودة — من مرحلة المشروع مروراً بالابتكار التقني وتطوير المنتج والعمارة والتنفيذ — إضافة إلى مشاريع المكاتب وأماكن العمل والبلازات والسكن والمنشآت الصناعية.'),
  ('corp_stat_1_num', '35'), ('corp_stat_1_sym', '+'), ('corp_stat_1_lbl', 'YILLIK MİMARİ MİRAS'),
  ('corp_stat_1_lbl_en', 'YEARS OF ARCHITECTURAL HERITAGE'), ('corp_stat_1_lbl_ar', 'عاماً من الإرث المعماري'),
  ('corp_stat_2_num', '40'), ('corp_stat_2_sym', '+'), ('corp_stat_2_lbl', 'TAMAMLANAN İMZA ESER'),
  ('corp_stat_2_lbl_en', 'COMPLETED SIGNATURE WORKS'), ('corp_stat_2_lbl_ar', 'أعمال مميزة منجزة'),
  ('corp_stat_3_num', '100'), ('corp_stat_3_sym', '%'), ('corp_stat_3_lbl', 'TEKNİK MÜKEMMELLİK'),
  ('corp_stat_3_lbl_en', 'TECHNICAL EXCELLENCE'), ('corp_stat_3_lbl_ar', 'التميز التقني'),
  ('corp_stat_1_color', '#c5a059'), ('corp_stat_2_color', '#c5a059'), ('corp_stat_3_color', '#c5a059')
ON DUPLICATE KEY UPDATE s_value = VALUES(s_value);
