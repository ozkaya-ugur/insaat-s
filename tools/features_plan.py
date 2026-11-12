# -*- coding: utf-8 -*-
"""Projelere tipine göre özellik seti üretir (TR/EN/AR) -> features_plan.json"""
import json

C = {
 'smart_home': ('fa-house-signal', 'Akıllı Ev Sistemi', 'Senaryo ve uzaktan kontrol', 'Smart Home System', 'Scenes and remote control', 'نظام المنزل الذكي', 'مشاهد وتحكم عن بعد'),
 'security': ('fa-shield-halved', '7/24 Güvenlik', 'Kamera ve kontrollü giriş', '24/7 Security', 'CCTV and controlled access', 'أمن 24/7', 'كاميرات ودخول مُدار'),
 'parking_closed': ('fa-square-parking', 'Kapalı Otopark', 'Bağımsız bölümlere ayrılmış yer', 'Indoor Parking', 'Dedicated space per unit', 'موقف مغلق', 'مكان مخصص لكل وحدة'),
 'parking_private': ('fa-car', 'Özel Araç Parkı', 'Villa başına ayrılmış yer', 'Private Parking', 'Dedicated space per villa', 'موقف خاص', 'مكان مخصص لكل فيلا'),
 'pool': ('fa-swimming-pool', 'Yüzme Havuzu', 'Özel veya ortak havuz', 'Swimming Pool', 'Private or shared pool', 'مسبح', 'خاص أو مشترك'),
 'garden': ('fa-tree', 'Peyzaj ve Yeşil Alan', 'Özel bahçe ve çevre düzenlemesi', 'Landscaping', 'Private garden and landscaping', 'تنسيق حدائق', 'حديقة خاصة وتنسيق محيط'),
 'green_building': ('fa-leaf', 'Yeşil Bina Dokusu', 'Doğayla uyumlu tasarım', 'Green Building', 'Nature-friendly design', 'بناء أخضر', 'تصميم متناغم مع الطبيعة'),
 'energy': ('fa-solar-panel', 'Enerji Verimliliği', 'Yalıtım ve tasarruflu sistemler', 'Energy Efficiency', 'Insulation and efficient systems', 'كفاءة الطاقة', 'عزل وأنظمة موفرة'),
 'elevator': ('fa-elevator', 'Asansör', 'Panoramik veya standart asansör', 'Elevator', 'Panoramic or standard elevator', 'مصعد', 'بانورامي أو عادي'),
 'climate': ('fa-snowflake', 'Klima ve Isıtma', 'Isıtma ve soğutma hazır', 'Heating & Cooling', 'Ready HVAC systems', 'تكييف وتدفئة', 'أنظمة تكييف وتدفئة جاهزة'),
 'gas': ('fa-fire-flame-simple', 'Doğalgaz Altyapısı', 'Kurulu doğalgaz tesisatı', 'Natural Gas', 'Installed gas infrastructure', 'غاز طبيعي', 'بنية غاز مثبتة'),
 'fitness': ('fa-dumbbell', 'Spor Salonu', 'Site içi fitness merkezi', 'Fitness Center', 'On-site gym', 'صالة رياضية', 'نادٍ رياضي داخل الموقع'),
 'kids': ('fa-child-reaching', 'Çocuk Oyun Alanı', 'Güvenli oyun parkı', 'Kids Playground', 'Safe play area', 'ملعب أطفال', 'منطقة لعب آمنة'),
 'social': ('fa-city', 'Sosyal Donatı', 'Ortak yaşam ve rekreasyon alanları', 'Social Amenities', 'Shared living and recreation areas', 'مرافق اجتماعية', 'مساحات معيشة وترفيه مشتركة'),
 'charging': ('fa-charging-station', 'Şarj İstasyonu', 'Elektrikli araç hazırlığı', 'EV Charging', 'Electric vehicle ready', 'شحن كهربائي', 'جاهز للسيارات الكهربائية'),
 'fire': ('fa-fire-extinguisher', 'Yangın Güvenliği', 'Algılama ve söndürme sistemleri', 'Fire Safety', 'Detection and suppression systems', 'سلامة من الحرائق', 'أنظمة كشف وإطفاء'),
 'generator': ('fa-bolt', 'Jeneratör Altyapısı', 'Kesintisiz enerji desteği', 'Generator Backup', 'Uninterrupted power support', 'مولد احتياطي', 'دعم طاقة دون انقطاع'),
 'fiber': ('fa-wifi', 'Fiber İnternet Altyapısı', 'Yüksek hızlı bağlantı', 'Fiber Internet', 'High-speed connectivity', 'إنترنت فايبر', 'اتصال عالي السرعة'),
 'cctv': ('fa-video', 'Kamera Sistemi', 'Ortak alan gözetimi', 'CCTV System', 'Common area monitoring', 'نظام كاميرات', 'مراقبة المناطق المشتركة'),
 'retail': ('fa-store', 'Ticari Dükkanlar', 'Zemin kat ticari alanlar', 'Retail Units', 'Ground floor commercial areas', 'وحدات تجارية', 'مساحات تجارية بالطابق الأرضي'),
 'location': ('fa-location-dot', 'Merkezi Konum', 'Ulaşım ve donatılara yakın', 'Central Location', 'Close to transport and amenities', 'موقع مركزي', 'قريب من المواصلات والمرافق'),
 'investment': ('fa-chart-line', 'Yatırım Değeri', 'Bölgede değer artışı', 'Investment Value', 'Value growth in the area', 'قيمة استثمارية', 'نمو القيمة في المنطقة'),
 'prestige': ('fa-star', 'Prestij', 'Sıra dışı mimari ve konfor', 'Prestige', 'Distinctive architecture and comfort', 'هيبة', 'عمارة مميزة وراحة'),
 'steel': ('fa-industry', 'Çelik Konstrüksiyon', 'Geniş açıklıklı taşıyıcı sistem', 'Steel Structure', 'Wide-span structural system', 'هيكل فولاذي', 'نظام إنشائي واسع البحور'),
 'production': ('fa-gears', 'Üretim Alanı', 'Yüksek tavan ve teknik altyapı', 'Production Area', 'High ceiling and technical infrastructure', 'منطقة إنتاج', 'سقف مرتفع وبنية تقنية'),
 'warehouse': ('fa-warehouse', 'Depo ve Stok', 'Raf sistemine uygun alan', 'Warehouse', 'Racking-ready storage space', 'مستودع', 'مساحة جاهزة للأرفف'),
 'logistics': ('fa-truck-fast', 'Sevkiyat ve Lojistik', 'Tır yanaşma ve manevra alanı', 'Logistics', 'Truck access and manoeuvring area', 'لوجستيات', 'منطقة وصول الشاحنات'),
 'osb': ('fa-road', 'OSB Altyapısı', 'Organize sanayi imkanları', 'Industrial Zone', 'Organized industrial zone utilities', 'منطقة صناعية', 'مرافق المنطقة الصناعية'),
 'admin_block': ('fa-building', 'İdari Blok', 'Ofis ve yönetim alanı', 'Admin Block', 'Office and management area', 'مبنى إداري', 'مكاتب وإدارة'),
 'safety': ('fa-helmet-safety', 'İş Güvenliği', 'Şantiye ve tesis standartları', 'Occupational Safety', 'Site and facility standards', 'سلامة مهنية', 'معايير الموقع والمنشأة'),
 'showroom': ('fa-store', 'Showroom', 'Geniş vitrin ve sergi alanı', 'Showroom', 'Wide display and exhibition area', 'صالة عرض', 'واجهة ومعرض واسع'),
 'highway': ('fa-road', 'Ana Yol Cephesi', 'Yüksek görünürlük ve erişim', 'Highway Frontage', 'High visibility and access', 'على الطريق الرئيسي', 'ظهور ووصول مرتفع'),
 'alu': ('fa-building', 'Alüminyum Cephe', 'Modern giydirme cephe sistemi', 'Aluminium Facade', 'Modern curtain wall system', 'واجهة ألمنيوم', 'نظام واجهة حديث'),
 'turnkey': ('fa-key', 'Anahtar Teslim', 'Tasarımdan teslime tek elden', 'Turnkey Delivery', 'Design to handover from one contractor', 'تسليم مفتاح', 'من التصميم إلى التسليم'),
 'fuel': ('fa-industry', 'Akaryakıt Tesisi', 'Kanopi, ada ve tank sahası', 'Fuel Station', 'Canopy, islands and tank farm', 'منشأة وقود', 'مظلة وجزر وخزانات'),
 'stage': ('fa-stairs', 'Etaplı Dönüşüm', 'Kademeli teslim modeli', 'Phased Renewal', 'Staged delivery model', 'تحول مرحلي', 'نموذج تسليم على مراحل'),
 'contract': ('fa-circle-check', 'Şeffaf Sözleşme', 'Hak sahibi güvencesi', 'Transparent Contract', 'Assurance for rights holders', 'عقد شفاف', 'ضمان لأصحاب الحقوق'),
 'value': ('fa-chart-line', 'Değer Artışı', 'Dönüşümle kazanan bölge', 'Value Growth', 'A neighbourhood that gains value', 'نمو القيمة', 'منطقة تكسب قيمة'),
 'daylight': ('fa-sun', 'Gün Işığı', 'Geniş pencereler ve çift cephe', 'Daylight', 'Large windows and dual aspect', 'إضاءة طبيعية', 'نوافذ واسعة وواجهتان'),
 'comfort': ('fa-bed', 'Yüksek Konfor', 'Otel standardında yaşam', 'High Comfort', 'Hotel-standard living', 'راحة عالية', 'معيشة بمستوى فندقي'),
 'accessible': ('fa-door-open', 'Engelsiz Erişim', 'Herkes için ulaşılabilir tasarım', 'Accessible Design', 'Design accessible to everyone', 'تصميم ميسّر', 'تصميم متاح للجميع'),
 'recycle': ('fa-recycle', 'Geri Dönüşüm', 'Atık yönetimi ve ayrıştırma', 'Recycling', 'Waste management and separation', 'إعادة تدوير', 'إدارة وفصل النفايات'),
}

PLAN = {
 # --- Villa siteleri ---
 1:  ['smart_home','security','parking_private','garden','green_building','energy','prestige','investment'],
 3:  ['pool','parking_private','garden','smart_home','security','green_building','energy','prestige'],
 31: ['pool','parking_private','garden','security','smart_home','energy','prestige','investment'],
 32: ['security','parking_private','garden','smart_home','green_building','energy','charging','prestige'],
 35: ['garden','parking_private','smart_home','security','green_building','energy','comfort','prestige'],
 # --- Apartman / daire konut ---
 4:  ['retail','location','elevator','parking_closed','security','energy','investment','smart_home'],
 5:  ['social','elevator','parking_closed','security','kids','garden','energy','investment'],
 9:  ['location','retail','elevator','parking_closed','security','energy','investment','prestige'],
 10: ['elevator','parking_closed','security','location','energy','investment','smart_home','comfort'],
 26: ['elevator','parking_closed','security','energy','smart_home','location','investment','garden'],
 27: ['elevator','parking_closed','security','energy','smart_home','garden','investment','comfort'],
 28: ['elevator','parking_closed','security','energy','smart_home','garden','investment','comfort'],
 29: ['elevator','parking_closed','security','energy','smart_home','garden','kids','investment'],
 33: ['social','kids','garden','elevator','parking_closed','security','fitness','investment'],
 34: ['parking_closed','elevator','pool','security','fitness','smart_home','garden','prestige'],
 36: ['parking_closed','pool','fitness','kids','social','security','elevator','comfort','prestige'],
 37: ['garden','smart_home','parking_closed','security','energy','elevator','comfort','green_building'],
 38: ['retail','garden','elevator','parking_closed','security','kids','energy','investment'],
 39: ['garden','elevator','parking_closed','security','energy','smart_home','comfort','prestige'],
 40: ['smart_home','elevator','parking_closed','security','fiber','investment','location','social'],
 41: ['elevator','parking_closed','security','kids','garden','energy','investment','social'],
 # --- Ofis / iş merkezi ---
 2:  ['retail','parking_closed','elevator','fiber','cctv','fire','generator','location','prestige'],
 7:  ['retail','climate','elevator','parking_closed','security','fiber','investment','location'],
 8:  ['retail','gas','elevator','parking_closed','security','fiber','location','investment'],
 13: ['retail','location','parking_closed','elevator','fiber','security','investment','prestige'],
 15: ['retail','parking_closed','elevator','fiber','cctv','fire','generator','location'],
 17: ['retail','elevator','fiber','security','location','prestige','parking_closed','fire'],
 19: ['retail','parking_closed','elevator','location','fiber','security','fire','investment'],
 30: ['retail','location','fiber','elevator','security','parking_closed','investment','prestige'],
 # --- Sanayi / üretim tesisleri ---
 6:  ['steel','production','warehouse','logistics','osb','fire','safety','admin_block','energy'],
 11: ['steel','production','warehouse','osb','energy','fire','safety','logistics','admin_block'],
 12: ['steel','production','warehouse','logistics','osb','admin_block','fire','safety','garden'],
 14: ['steel','production','warehouse','osb','logistics','admin_block','fire','safety'],
 16: ['production','steel','warehouse','logistics','osb','fire','safety','energy'],
 18: ['production','steel','warehouse','logistics','osb','fire','safety','admin_block'],
 21: ['production','warehouse','logistics','osb','energy','fire','safety','garden'],
 24: ['retail','garden','elevator','parking_closed','security','fiber','location','prestige'],
 25: ['steel','production','warehouse','logistics','fire','safety','admin_block','osb'],
 # --- Plaza / otomotiv ---
 20: ['showroom','highway','steel','alu','turnkey','parking_closed','admin_block','fire'],
 23: ['showroom','highway','steel','alu','turnkey','parking_closed','admin_block','fire'],
 # --- Akaryakıt ---
 22: ['fuel','retail','fire','turnkey','highway','safety','garden','generator'],
 # --- Kentsel dönüşüm ---
 45: ['stage','contract','social','garden','parking_closed','energy','value','security'],
 46: ['energy','parking_closed','garden','stage','contract','social','value','green_building'],
 47: ['stage','contract','social','parking_closed','garden','energy','value','security'],
}

out = {}
for pid, keys in PLAN.items():
    rows = []
    for k in keys:
        icon, t_tr, s_tr, t_en, s_en, t_ar, s_ar = C[k]
        rows.append({'icon': icon, 'title_tr': t_tr, 'sub_tr': s_tr, 'title_en': t_en,
                     'sub_en': s_en, 'title_ar': t_ar, 'sub_ar': s_ar})
    out[str(pid)] = rows

json.dump(out, open('/home/ubuntu/features_plan.json', 'w'), ensure_ascii=False, indent=1)
print('proje:', len(out), 'ozellik:', sum(len(v) for v in out.values()))
