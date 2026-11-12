-- Demo kentsel dönüşüm projeleri (idempotent)

INSERT INTO projects (
  slug, title_tr, title_en, title_ar,
  subtitle_tr, subtitle_en, subtitle_ar,
  description_tr, description_en, description_ar,
  cat, loc, city_key, type, status,
  is_new, is_urban_renewal, cover_image,
  area, concept, delivery, is_featured, is_active, sort_order
) VALUES
(
  'nova-kentsel-serdivan',
  'Nova Kentsel Serdivan', 'Nova Urban Renewal Serdivan', 'نوفا للتجديد الحضري - سرديفان',
  'Mevcut yapı adalarını güvenli ve modern yaşam alanlarına dönüştürüyoruz.',
  'Transforming existing blocks into safe and modern living spaces.',
  'تحويل الجزر العمرانية الحالية إلى مساحات معيشية آمنة وحديثة.',
  'Serdivan merkezde etaplı dönüşüm modeli ile hak sahiplerine güvenli, sosyal donatılı ve değer kazanan yeni bir yaşam alanı sunuyoruz.',
  'In central Serdivan, we offer owners a safe, social, and value-appreciating new living environment with a phased renewal model.',
  'في مركز سرديفان نوفر للمالكين بيئة معيشية جديدة آمنة ومتكاملة عبر نموذج تجديد مرحلي.',
  'Sakarya', 'Serdivan / Sakarya', 'tr-sk', 'kentsel', 'devam-eden',
  1, 1, 'images/banner-1.webp',
  '9.800 m²', 'Güvenli Dönüşüm + Sosyal Donatı', '2027', 0, 1, 901
),
(
  'vadi-kentsel-adapazari',
  'Vadi Kentsel Adapazarı', 'Vadi Urban Renewal Adapazari', 'فادي للتجديد الحضري - أداپازاري',
  'Yıpranmış yapı stoğunu sürdürülebilir mimari ile yeniliyoruz.',
  'Renewing aging building stock with sustainable architecture.',
  'تجديد المخزون العمراني المتقادم عبر عمارة مستدامة.',
  'Adapazarı bölgesinde enerji verimli yapı kabuğu, otopark ve yeşil alanları birlikte planlanan bütüncül bir dönüşüm yaklaşımı uygulanmaktadır.',
  'In Adapazari, a holistic renewal approach combines energy-efficient envelopes, parking, and green areas.',
  'في أداپازاري نطبق نهجاً شاملاً يجمع بين الكفاءة الطاقية والمواقف والمساحات الخضراء.',
  'Sakarya', 'Adapazarı / Sakarya', 'tr-sk', 'kentsel', 'devam-eden',
  0, 1, 'images/banner-2.webp',
  '12.400 m²', 'Sürdürülebilir Mahalle Dönüşümü', '2028', 0, 1, 902
),
(
  'merkez-kentsel-erenler',
  'Merkez Kentsel Erenler', 'Merkez Urban Renewal Erenler', 'مركز للتجديد الحضري - إيرينلر',
  'Deprem güvenliği odaklı, etap etap planlanmış dönüşüm.',
  'Earthquake-safe, phase-by-phase planned renewal.',
  'تجديد مخطط على مراحل مع تركيز على أمان الزلازل.',
  'Erenler aksında etap bazlı ilerleyen projede, hak sahiplerinin yerinde dönüşüm süreci şeffaf sözleşme modeli ile yürütülmektedir.',
  'Along the Erenler axis, owner-occupied renewal is managed with a transparent contract model in phased delivery.',
  'على محور إيرينلر تتم إدارة التجديد في الموقع بعقود شفافة وتسليمات مرحلية.',
  'Sakarya', 'Erenler / Sakarya', 'tr-sk', 'kentsel', 'devam-eden',
  0, 1, 'images/banner-3.webp',
  '8.600 m²', 'Yerinde Dönüşüm & Güvenli Konut', '2027', 0, 1, 903
)
ON DUPLICATE KEY UPDATE
  title_tr = VALUES(title_tr),
  title_en = VALUES(title_en),
  title_ar = VALUES(title_ar),
  subtitle_tr = VALUES(subtitle_tr),
  subtitle_en = VALUES(subtitle_en),
  subtitle_ar = VALUES(subtitle_ar),
  description_tr = VALUES(description_tr),
  description_en = VALUES(description_en),
  description_ar = VALUES(description_ar),
  cat = VALUES(cat),
  loc = VALUES(loc),
  city_key = VALUES(city_key),
  type = VALUES(type),
  status = VALUES(status),
  is_new = VALUES(is_new),
  is_urban_renewal = VALUES(is_urban_renewal),
  cover_image = VALUES(cover_image),
  area = VALUES(area),
  concept = VALUES(concept),
  delivery = VALUES(delivery),
  is_active = VALUES(is_active),
  sort_order = VALUES(sort_order);

INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', p.cover_image, p.title_tr, 1
FROM projects p
WHERE p.slug IN ('nova-kentsel-serdivan', 'vadi-kentsel-adapazari', 'merkez-kentsel-erenler')
  AND NOT EXISTS (
    SELECT 1 FROM project_media pm
    WHERE pm.project_id = p.id AND pm.kind = 'hero' AND pm.sort_order = 1
  );
