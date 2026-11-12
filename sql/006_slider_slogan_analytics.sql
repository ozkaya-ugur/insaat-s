-- 006: Slider medya iyileştirmeleri, proje slogan alanları, analytics/pixel ayarları
-- İdempotent migration

ALTER TABLE projects
  ADD COLUMN IF NOT EXISTS slogan_tr VARCHAR(255) DEFAULT '' AFTER subtitle_tr,
  ADD COLUMN IF NOT EXISTS slogan_en VARCHAR(255) DEFAULT '' AFTER subtitle_en,
  ADD COLUMN IF NOT EXISTS slogan_ar VARCHAR(255) DEFAULT '' AFTER subtitle_ar;

ALTER TABLE sliders
  ADD COLUMN IF NOT EXISTS media_type ENUM('image','video') NOT NULL DEFAULT 'image' AFTER image_url;

-- Eski seedlerden kalan .png slider yollarını .webp'e çevir
UPDATE sliders SET image_url='images/banner-1.webp' WHERE image_url='images/banner-1.png';
UPDATE sliders SET image_url='images/banner-2.webp' WHERE image_url='images/banner-2.png';
UPDATE sliders SET image_url='images/banner-3.webp' WHERE image_url='images/banner-3.png';
UPDATE sliders SET image_url='images/banner-4.webp' WHERE image_url='images/banner-4.png';

-- Dosya uzantısına göre medya tipini normalleştir
UPDATE sliders
SET media_type = CASE
  WHEN LOWER(image_url) REGEXP '\\.(mp4|webm)(\\?.*)?$' THEN 'video'
  ELSE 'image'
END;

-- Proje sloganlarını sadece boş alanlar için örnek metinlerle doldur (TR/EN/AR)
UPDATE projects
SET
  slogan_tr = CASE
    WHEN COALESCE(TRIM(slogan_tr),'') <> '' THEN slogan_tr
    WHEN slug='intense' THEN 'Serdivan’da modern yaşamı prestijle buluşturan seçkin proje.'
    WHEN slug='workinn' THEN 'İş hayatının merkezinde güçlü kurumsal kimlik sunan yeni nesil plaza.'
    WHEN slug='silence' THEN 'Doğa ve lüksü aynı çizgide buluşturan sakin yaşam deneyimi.'
    WHEN slug='buyapi-altinisik-konutlari-1' THEN 'Avantajlı ödeme modeliyle şehir merkezinde erişilebilir konfor.'
    WHEN slug='buyapi-altinisik-konutlari-2' THEN 'Yatırım değeri yüksek, dengeli planlanmış çağdaş yaşam alanı.'
    WHEN slug='hendek-fabrika' THEN 'Hendek sanayi hattında üretim verimini artıran mühendislik çözümü.'
    WHEN slug='buyapi-palm-is-merkezi' THEN 'Ticaret ve günlük yaşamı tek yapıda bir araya getiren akıllı tasarım.'
    WHEN slug='buyapi-time-is-merkezi' THEN 'Zamana değer katan merkezi konum ve fonksiyonel bağımsız bölümler.'
    WHEN slug='gunes-apartmani' THEN 'Kadıköy’de prestijli lokasyonda nitelikli aile yaşamı sunar.'
    WHEN slug='konak-apartmani' THEN 'Göztepe’nin ritmine uyumlu seçkin ve güvenli konut yaklaşımı.'
    WHEN slug='kontrolmatik-pomega-enerji' THEN 'Yüksek kapasiteli enerji üretimine hizmet eden stratejik sanayi yatırımı.'
    WHEN slug='yavuz-gida-findik-entegre-tesisleri' THEN 'Entegre üretim zinciri için dayanıklı ve ölçeklenebilir tesis altyapısı.'
    WHEN slug='buyapi-altinisik-is-merkezi' THEN 'Serdivan’da ticari hareketliliği büyüten prestijli iş adresi.'
    WHEN slug='mk-moda-atelier' THEN 'Organize sanayide verimli üretim için bütüncül yapı çözümü.'
    WHEN slug='kemal-yener-is-merkezi' THEN 'Ofis ve ticareti dengeli bir kurgu ile buluşturan iş merkezi.'
    WHEN slug='saka-su' THEN 'Endüstriyel süreçlere özel teknik güvenlik odaklı üretim tesisi.'
    WHEN slug='firstgate-is-merkezi' THEN 'Kompakt ölçekte yüksek görünürlük sunan modern ticari yapı.'
    WHEN slug='truva-granit' THEN 'Malzeme üretiminde süreklilik sağlayan güçlü tesis planlaması.'
    WHEN slug='irfan-puskullu-is-merkezi' THEN 'Merkezi lokasyonda prestijli ofis deneyimi sunan iş yapısı.'
    WHEN slug='honda-plaza' THEN 'Marka gücünü yansıtan fonksiyonel showroom ve servis altyapısı.'
    WHEN slug='hasbil-tarim' THEN 'Tarım operasyonlarında verimliliği artıran dayanıklı tesis mimarisi.'
    WHEN slug='cark-petrol' THEN 'Akaryakıt ve hizmet operasyonlarını optimize eden modern istasyon.'
    WHEN slug='kia-plaza' THEN 'Otomotiv perakendesinde güçlü görünürlük sağlayan kurumsal plaza.'
    WHEN slug='opera-garden' THEN 'Yaşam konforunu estetik peyzajla tamamlayan özel konut projesi.'
    WHEN slug='hunerli-orman-urunleri' THEN 'Endüstriyel ahşap üretiminde sürdürülebilir tesis yaklaşımı.'
    WHEN slug='buyapi-eksen' THEN 'Şehrin gelişim ekseninde yatırım değerini güçlendiren yaşam alanı.'
    WHEN slug='buyapi-begonvil-apartmani' THEN 'Mahalle dokusuna uyumlu, modern detaylarla zenginleşen konut.'
    WHEN slug='buyapi-imaj' THEN 'Prestijli cephe çizgisiyle öne çıkan çağdaş iş-konut çözümü.'
    WHEN slug='buyapi-nova-plus' THEN 'Yeni nesil şehir yaşamına uyum sağlayan esnek planlama.'
    WHEN slug='buyapi-centrum' THEN 'Kente yakın, günlük yaşama entegre güçlü bir merkez projesi.'
    WHEN slug='sapanca-life' THEN 'Sapanca doğasında huzur ve konfor dengesini kuran yaşam alanı.'
    WHEN slug='buyapi-vadi' THEN 'Doğal vadi perspektifinde ferah ve değer odaklı konut kurgusu.'
    WHEN slug='buyapi-sungate' THEN 'Gün ışığını merkeze alan fonksiyonel ve seçkin mimari yaklaşım.'
    WHEN slug='buyapi-uptown' THEN 'Şehir temposuna uyumlu, modern çizgide üst segment yaşam.'
    WHEN slug='begon-villa' THEN 'Müstakil yaşam ayrıcalığını kalite standartlarıyla birleştirir.'
    WHEN slug='buyapi-diamond' THEN 'Prestij odaklı detaylarıyla yatırımcısına değer katan proje.'
    WHEN slug='buyapi-flex' THEN 'Değişen ihtiyaçlara uyum sağlayan dinamik yaşam ve çalışma alanı.'
    WHEN slug='buyapi-garden' THEN 'Yeşil alan odaklı planıyla konforlu aile yaşamını destekler.'
    WHEN slug='buyapi-prestige' THEN 'Kurumsal duruşu güçlendiren modern mimari ve yüksek işlevsellik.'
    WHEN slug='buyapi-prime' THEN 'Serdivan/Sakarya’da kalite standartlarını yükselten prestijli imza.'
    WHEN slug='buyapi-vision' THEN 'Gelecek odaklı tasarım diliyle sürdürülebilir değer üretir.'
    ELSE CONCAT(COALESCE(title_tr,'Proje'), ' için örnek slogan metni.')
  END,
  slogan_en = CASE
    WHEN COALESCE(TRIM(slogan_en),'') <> '' THEN slogan_en
    ELSE CONCAT(COALESCE(title_en, title_tr, 'Project'), ' delivers a distinctive benchmark of modern quality.')
  END,
  slogan_ar = CASE
    WHEN COALESCE(TRIM(slogan_ar),'') <> '' THEN slogan_ar
    ELSE CONCAT('يقدم مشروع ', COALESCE(title_ar, title_tr, 'المشروع'), ' معياراً مميزاً للجودة العصرية.')
  END;

INSERT INTO site_settings (s_key, s_value) VALUES
  ('google_analytics_id', ''),
  ('meta_pixel_id', '')
ON DUPLICATE KEY UPDATE s_value = s_value;
