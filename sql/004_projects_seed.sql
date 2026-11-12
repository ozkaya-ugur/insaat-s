-- 41 proje ve medya kayıtlarının kalıcı seed dosyası
-- Güvenle tekrar çalıştırılabilir (idempotent).

CREATE TEMPORARY TABLE IF NOT EXISTS tmp_seed_project_slugs (
  slug VARCHAR(160) PRIMARY KEY
) ENGINE=MEMORY;
TRUNCATE TABLE tmp_seed_project_slugs;

INSERT INTO tmp_seed_project_slugs (slug) VALUES ('intense');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('workinn');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('silence');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-altinisik-konutlari-1');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-altinisik-konutlari-2');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('hendek-fabrika');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-palm-is-merkezi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-time-is-merkezi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('gunes-apartmani');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('konak-apartmani');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('kontrolmatik-pomega-enerji');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('yavuz-gida-findik-entegre-tesisleri');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-altinisik-is-merkezi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('mk-moda-atelier');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('kemal-yener-is-merkezi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('saka-su');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('firstgate-is-merkezi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('truva-granit');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('irfan-puskullu-is-merkezi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('honda-plaza');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('hasbil-tarim');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('cark-petrol');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('kia-plaza');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('opera-garden');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('hunerli-orman-urunleri');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-eksen');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-begonvil-apartmani');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-imaj');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-nova-plus');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-centrum');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('sapanca-life');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-vadi');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-sungate');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-uptown');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('begon-villa');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-diamond');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-flex');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-garden');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-prestige');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-prime');
INSERT INTO tmp_seed_project_slugs (slug) VALUES ('buyapi-vision');

INSERT INTO projects (slug,title_tr,title_en,title_ar,title_de,subtitle_tr,subtitle_en,subtitle_ar,subtitle_de,description_tr,description_en,description_ar,description_de,cat,loc,city_key,type,status,is_new,is_urban_renewal,cover_image,area,concept,delivery,catalog_file,street_view_code,google_map_code,is_featured,is_active,sort_order,seo_title,seo_desc,seo_keywords) VALUES
('intense','Buyapı Intense','Buyapı Intense','Buyapı Intense','Buyapı Intense','“Huzura Yoğunlaşın”Yeni projemiz “Buyapı INTENSE” ile tanışın.Projemizin ilk etabı olan “INTENSE 12” 12 adet lüks villadan oluşuyor. Yaşamın merkezi S...','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','“Huzura Yoğunlaşın”Yeni projemiz “Buyapı INTENSE” ile tanışın.Projemizin ilk etabı olan “INTENSE 12” 12 adet lüks villadan oluşuyor. Yaşamın merkezi Serdivan’da konforu, sakinliği ve gerçek yaşamı bir arada sunuyor.','Buyapı Intense represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Intense أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Intense verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-intense.webp','12.500 m²','A++ Avant-Garde Rezidans','2026','',NULL,NULL,'1','1','1','','',''),
('workinn','Buyapı Work Inn','Buyapı Work Inn','Buyapı Work Inn','Buyapı Work Inn','Ofis Sayısı : 33Ofis Tipi : 1+1, 2+1Blok Sayısı : 1Toplam Alan : 7500 m2Konumu : SerdivanYapım Yılı : 2017','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Ofis Sayısı : 33Ofis Tipi : 1+1, 2+1Blok Sayısı : 1Toplam Alan : 7500 m2Konumu : SerdivanYapım Yılı : 2017

Serdivan bölgesinin ticari olarak en hareketli bölgelerinden birinde yer alan 7500 m2 kapalı alanlı sıradışı tasarıma sahip projemizde, ofisler,dükkanlar,açık ve kapalı otopark alanları bulunmaktadır.','Buyapı Work Inn represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Work Inn أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Work Inn verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_buyapi-work-inn.webp','7500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'1','1','2','','',''),
('silence','Buyapı Silence','Buyapı Silence','Buyapı Silence','Buyapı Silence','Villa Sayısı : 13Konumu : Sapanca / SakaryaYapım Yılı : 2020','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Villa Sayısı : 13Konumu : Sapanca / SakaryaYapım Yılı : 2020

BUYAPI SILENCE ; SİZİ SESSİZCE MUTLULUĞA ÇAĞIRIYOR

BUYAPI SILENCE, Değişimin Mimarı BUYAPI’dan eşsiz tasarıma sahip yeni bir proje daha hayata geçiyor.

BUYAPI SILENCE adına yakışır şekilde sizi sessizliğin, doğal ortamın ve huzurun üst noktalarına taşırken aynı zamanda modern yaşamın tüm konforunu da kullanımınıza sunuyor. 

13 adet müstakil ve her şeyi ile size özel tasarlanmış villadan oluşan projede her villaya bitişik 2 araçlık otopark ve özel yüzme havuzu standart olarak sunuluyor. Bahçe kotundan düşük tasarladığımız sedir alanı dediğimiz oturma grubu bölümünde kendinizi yeşilin içinde kaybolmuş hissedeceksiniz.

İstanbul’a bir saat, İzmit ve Sakarya’ya sadece 15-20 dakikalık mesafede olan BUYAPI SILENCE projemiz GÖL-ORMAN-DAĞ üçlemesini bünyesinde barındıran, nadir güzellikteki coğrafyanın doğal parçası olacak şekilde tasarlandı.

Hangisi size uygun ? Yüzme, kayak, yürüyüş. Yoksa yeşil denizinin içinde kaybolmak mı ?

Hepsi için BUYAPI-SILENCE sizi bekliyor olacak…','Buyapı Silence represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Silence أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Silence verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Sapanca / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-silence.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','3','','',''),
('buyapi-altinisik-konutlari-1','Buyapı Altınışık Konutları-1','Buyapı Altınışık Konutları-1','Buyapı Altınışık Konutları-1','Buyapı Altınışık Konutları-1','Serdivan’da Avantajlı Bir FırsatAltınışık 1, modern mimarisi, merkezi konumu ve ticari alanlarıyla hem konforlu bir yaşam hem de güçlü bir yatırım fır...','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Serdivan’da Avantajlı Bir FırsatAltınışık 1, modern mimarisi, merkezi konumu ve ticari alanlarıyla hem konforlu bir yaşam hem de güçlü bir yatırım fırsatı sunuyor. Estetik tasarımı ve fonksiyonel planlamasıyla sadece bir yapı değil, yaşam kalitenizi artıracak prestijli bir alan vaat ediyor.

✨ %40 peşinat✨ %0 faiz✨ 12 ay taksit✨ 1+1 ve 2+1 daire seçenekleri

Size uygun ödeme planıyla yeni evinize bir adım daha yaklaşın.','Buyapı Altınışık Konutları-1 represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Altınışık Konutları-1 أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Altınışık Konutları-1 verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-altinisik-konutlari-1.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','1','Buyapı Altınışık Konutları-1 | BUYAPI','Serdivan’da Avantajlı Bir FırsatAltınışık 1, modern mimarisi, merkezi konumu ve ticari alanlarıyla hem konforlu bir yaşam hem de güçlü bir yatırım fır...',''),
('buyapi-altinisik-konutlari-2','Buyapı Altınışık Konutları-2','Buyapı Altınışık Konutları-2','Buyapı Altınışık Konutları-2','Buyapı Altınışık Konutları-2','Sakarya’da Ev Sahibi Olmanın Tam Zamanı!','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Sakarya’da Ev Sahibi Olmanın Tam Zamanı!

Hayalinizdeki eve avantajlı ödeme fırsatlarıyla sahip olun.

Modern mimarisi, ferah yaşam alanları ve özenle planlanan sosyal alanlarıyla Buyapı, konforlu bir yaşamın kapılarını aralıyor.

✨ %40 peşinat✨ %0 faiz✨ 12 ay taksit✨ 1+1 ve 2+1 daire seçenekleri

Size uygun ödeme planıyla yeni evinize bir adım daha yaklaşın.','Buyapı Altınışık Konutları-2 represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Altınışık Konutları-2 أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Altınışık Konutları-2 verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-altinisik-konutlari-2.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','2','Buyapı Altınışık Konutları-2 | BUYAPI','Sakarya’da Ev Sahibi Olmanın Tam Zamanı!',''),
('hendek-fabrika','Hendek Fabrika','Hendek Fabrika','Hendek Fabrika','Hendek Fabrika','Teknik İnovasyon & Prestijli ticari Projesi','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Hendek Fabrika projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Hendek Fabrika represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Hendek Fabrika أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Hendek Fabrika verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Hendek / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_hendek-fabrika.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','3','Hendek Fabrika | BUYAPI','Teknik İnovasyon & Prestijli ticari Projesi',''),
('buyapi-palm-is-merkezi','Buyapı Palm İş Merkezi','Buyapı Palm İş Merkezi','Buyapı Palm İş Merkezi','Buyapı Palm İş Merkezi','Projemiz, 1 adet ticari dükkan 18 adet 1+1 bağımsız daireden oluşmaktadır.Dairelerin tamamında ısıtma ve soğutmayı sağlayan klimalar takılı binamızda ...','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Projemiz, 1 adet ticari dükkan 18 adet 1+1 bağımsız daireden oluşmaktadır.Dairelerin tamamında ısıtma ve soğutmayı sağlayan klimalar takılı binamızda asansör konforu mevcuttur.','Buyapı Palm İş Merkezi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Palm İş Merkezi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Palm İş Merkezi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','devam-eden','0','0','uploads/projects/cover_buyapi-palm-is-merkezi.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'1','1','4','Buyapı Palm İş Merkezi | BUYAPI','Projemiz, 1 adet ticari dükkan 18 adet 1+1 bağımsız daireden oluşmaktadır.Dairelerin tamamında ısıtma ve soğutmayı sağlayan klimalar takılı binamızda ...',''),
('buyapi-time-is-merkezi','Buyapı Time İş Merkezi','Buyapı Time İş Merkezi','Buyapı Time İş Merkezi','Buyapı Time İş Merkezi','Modern mimarisi ve stratejik konumuyla öne çıkan BUYAPI TIME, hem ticari hem de konut yatırımları için ideal bir yaşam alanı sunuyor. Projemizin zemin...','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Modern mimarisi ve stratejik konumuyla öne çıkan BUYAPI TIME, hem ticari hem de konut yatırımları için ideal bir yaşam alanı sunuyor. Projemizin zemin katında 2 adet bağımsız ticari dükkan, 1. ve 2. katlarında ise konforlu bir yaşam için tasarlanmış 14 adet 1+1 bağımsız bölüm (her katta 7 adet) yer almaktadır.

Doğalgaz altyapısı ve asansör konforuyla donatılan BUYAPI TIME, modern şehir hayatının tüm ihtiyaçlarına prestijli bir yanıt veriyor.','Buyapı Time İş Merkezi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Time İş Merkezi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Time İş Merkezi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','devam-eden','0','0','uploads/projects/cover_buyapi-time-is-merkezi.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'1','1','5','Buyapı Time İş Merkezi | BUYAPI','Modern mimarisi ve stratejik konumuyla öne çıkan BUYAPI TIME, hem ticari hem de konut yatırımları için ideal bir yaşam alanı sunuyor. Projemizin zemin...',''),
('gunes-apartmani','Güneş Apartmanı','Güneş Apartmanı','Güneş Apartmanı','Güneş Apartmanı','Operatör Cemil Topuzlu CaddesiKadıköy / İstanbul da yer alıyor.','Prestigious Residential Living in İstanbul','مشروع سكني فاخر في İstanbul','Prestigeträchtiges Wohnen in İstanbul','Operatör Cemil Topuzlu CaddesiKadıköy / İstanbul da yer alıyor.

Projemiz;Bağdat caddesiCaddebostan Sahil yolu60. Yıl parkıÖzgürlük parkıMetro istasyonu ve bir çok özel lokasyona yakınlığı ile dikkat çekiyor

Daire Sayısı : 23Daire Tipi : 3+1Dükkan : 1Ofis: 1Konumu : Kadıköy / İstanbulOperatör Cemil Topuzlu Cad.','Güneş Apartmanı represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Güneş Apartmanı أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Güneş Apartmanı verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','İstanbul','Kadıköy / İstanbul','tr-ib','konut','tamamlanan','0','0','uploads/projects/cover_gunes-apartmani.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','6','Güneş Apartmanı | BUYAPI','Operatör Cemil Topuzlu CaddesiKadıköy / İstanbul da yer alıyor.',''),
('konak-apartmani','Konak Apartmanı','Konak Apartmanı','Konak Apartmanı','Konak Apartmanı','Göztepe’nin yeni göz bebeği ve yepyeni bir lokasyon!','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Göztepe’nin yeni göz bebeği ve yepyeni bir lokasyon!

Daire Sayısı : 24Daire Tipi : 3+1

Konumu : Göztepe / İstanbulTünek Sokak','Konak Apartmanı represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Konak Apartmanı أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Konak Apartmanı verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_konak-apartmani.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','7','Konak Apartmanı | BUYAPI','Göztepe’nin yeni göz bebeği ve yepyeni bir lokasyon!',''),
('kontrolmatik-pomega-enerji','Kontrolmatik Pomega Enerji','Kontrolmatik Pomega Enerji','Kontrolmatik Pomega Enerji','Kontrolmatik Pomega Enerji','Kontrolmatik Teknoloji iştiraklerinden  Pomega Enerji Depolama Teknolojileri Ankara-Polatlı Organize Sanayi Bölgesinde yapacağı Lityum, Demir, Fosfat ...','Modern Commercial & Business Center in Ankara','مركز أعمال وتجاري حديث في Ankara','Modernes Gewerbe- und Geschäftszentrum in Ankara','Kontrolmatik Teknoloji iştiraklerinden  Pomega Enerji Depolama Teknolojileri Ankara-Polatlı Organize Sanayi Bölgesinde yapacağı Lityum, Demir, Fosfat (LiFePO4 ) Batarya hücresi, modül ve enerji depolama sistemleri fabrikasının tüm yapım işinin  BUYAPI tarafından üstlenildiğini gururla duyururuz.

250 MWh kapasiteli olacak İlk fazı 2022 sonunda hizmete girecek olan fabrika,  kalan iki fazın da tamamlanmasıyla 1 GWh kapasiteye ulaşacaktır. ','Kontrolmatik Pomega Enerji represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Kontrolmatik Pomega Enerji أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Kontrolmatik Pomega Enerji verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Ankara','Polatlı / Ankara','tr-an','ticari','tamamlanan','0','0','uploads/projects/cover_kontrolmatik-pomega-enerji.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','9','Kontrolmatik Pomega Enerji | BUYAPI','Kontrolmatik Teknoloji iştiraklerinden  Pomega Enerji Depolama Teknolojileri Ankara-Polatlı Organize Sanayi Bölgesinde yapacağı Lityum, Demir, Fosfat ...',''),
('yavuz-gida-findik-entegre-tesisleri','Yavuz Gıda Fındık Entegre Tesisleri','Yavuz Gıda Fındık Entegre Tesisleri','Yavuz Gıda Fındık Entegre Tesisleri','Yavuz Gıda Fındık Entegre Tesisleri','Blok Sayısı : 2','Modern Commercial & Business Center in Giresun','مركز أعمال وتجاري حديث في Giresun','Modernes Gewerbe- und Geschäftszentrum in Giresun','Blok Sayısı : 2

Toplam Alan : 19.000 m2

Konumu : Giresun / Bulancak 

Yapım Yılı : 2018

Giresun, Bulancak Organize Sanayi Bölgesinde 20.000 m2 arsa üzerinde, 19.000 m2 kapalı alanlı olarak inşa ettiğimiz tesislerin tüm tasarım, çelik konstrüksiyon, betonarme ve çevre düzenleme işleri tarafımızdan anahtar teslimi olarak tamamlanmıştır.','Yavuz Gıda Fındık Entegre Tesisleri represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Yavuz Gıda Fındık Entegre Tesisleri أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Yavuz Gıda Fındık Entegre Tesisleri verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Giresun','Giresun','tr-gi','ticari','tamamlanan','0','0','uploads/projects/cover_yavuz-gida-findik-entegre-tesisleri.webp','19.000 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','10','Yavuz Gıda Fındık Entegre Tesisleri | BUYAPI','Blok Sayısı : 2',''),
('buyapi-altinisik-is-merkezi','Buyapı Altınışık İş Merkezi','Buyapı Altınışık İş Merkezi','Buyapı Altınışık İş Merkezi','Buyapı Altınışık İş Merkezi','Sakarya Serdivan\'da, ticaretin ve yaşamın merkezi olma hedefiyle inşa ettiğimiz Buyapı Altınışık projesi, konumuyla dikkat çekiyor. Şehirdeki önemli a...','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Sakarya Serdivan\'da, ticaretin ve yaşamın merkezi olma hedefiyle inşa ettiğimiz Buyapı Altınışık projesi, konumuyla dikkat çekiyor. Şehirdeki önemli alışveriş merkezleri, hastaneler, okullar ve diğer sosyal donatılara yakın bir mesafede yer alan projemiz, modern yaşamın tüm gereksinimlerini karşılayacak şekilde tasarlandı. Yüksek kaliteli inşaat standartlarıyla inşa edilen Buyapı Altınışık, yatırımcılarına hem prestijli bir yaşam alanı hem de güçlü ticaret fırsatları sunuyor. İş dünyası için ideal olan Buyapı Altınışık, Sakarya\'nın geleceğine değer katacak bir proje olarak öne çıkıyor.','Buyapı Altınışık İş Merkezi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Altınışık İş Merkezi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Altınışık İş Merkezi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_buyapi-altinisik-is-merkezi.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','11','Buyapı Altınışık İş Merkezi | BUYAPI','Sakarya Serdivan\'da, ticaretin ve yaşamın merkezi olma hedefiyle inşa ettiğimiz Buyapı Altınışık projesi, konumuyla dikkat çekiyor. Şehirdeki önemli a...',''),
('mk-moda-atelier','MK Moda Atelier','MK Moda Atelier','MK Moda Atelier','MK Moda Atelier','Blok Sayısı : 3Toplam Alan : 6.800m2Konumu : SakaryaYapım Yılı : 2019Sakarya Söğütlü Organize Sanayi Bölgesi içerisinde bulunan alandaki projemiz, çel...','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 3Toplam Alan : 6.800m2Konumu : SakaryaYapım Yılı : 2019Sakarya Söğütlü Organize Sanayi Bölgesi içerisinde bulunan alandaki projemiz, çelik konstrüksiyon ve betonarme inşaat şeklinde,tasarım ve tüm inşaat işleri tarafımızdan tamamlanarak müşteriye teslim edilmiştir.','MK Moda Atelier represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع MK Moda Atelier أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','MK Moda Atelier verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_mk-moda-atelier.webp','6.800 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','12','MK Moda Atelier | BUYAPI','Blok Sayısı : 3Toplam Alan : 6.800m2Konumu : SakaryaYapım Yılı : 2019Sakarya Söğütlü Organize Sanayi Bölgesi içerisinde bulunan alandaki projemiz, çel...',''),
('kemal-yener-is-merkezi','Kemal Yener İş Merkezi','Kemal Yener İş Merkezi','Kemal Yener İş Merkezi','Kemal Yener İş Merkezi','Ofis Sayısı : 60Ofis Tipi : 1+1Blok Sayısı : 1Toplam Alan : 4200 m2Konumu : Serdivan / SakaryaYapım Yılı : 2017','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Ofis Sayısı : 60Ofis Tipi : 1+1Blok Sayısı : 1Toplam Alan : 4200 m2Konumu : Serdivan / SakaryaYapım Yılı : 2017

4200 m2 alanda, 17 adet dükkan ve 60 adet ofisten oluşan projemiz kapalı otopark ayrıcalığı da sunmaktadır.','Kemal Yener İş Merkezi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Kemal Yener İş Merkezi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Kemal Yener İş Merkezi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_kemal-yener-is-merkezi.webp','4200 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','13','Kemal Yener İş Merkezi | BUYAPI','Ofis Sayısı : 60Ofis Tipi : 1+1Blok Sayısı : 1Toplam Alan : 4200 m2Konumu : Serdivan / SakaryaYapım Yılı : 2017',''),
('saka-su','Saka Su','Saka Su','Saka Su','Saka Su','Teknik İnovasyon & Prestijli ticari Projesi','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Saka Su projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Saka Su represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Saka Su أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Saka Su verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_saka-su.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','15','Saka Su | BUYAPI','Teknik İnovasyon & Prestijli ticari Projesi',''),
('firstgate-is-merkezi','Firstgate İş Merkezi','Firstgate İş Merkezi','Firstgate İş Merkezi','Firstgate İş Merkezi','Blok Sayısı : 1Toplam Alan : 1.000m2Konumu : Serdivan / SakaryaYapım Yılı : 2015','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 1Toplam Alan : 1.000m2Konumu : Serdivan / SakaryaYapım Yılı : 2015

Ofis ve dükkanlardan oluşan iş merkezi inşaatımız özgün mimarisi ile dikkat çekiyor.','Firstgate İş Merkezi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Firstgate İş Merkezi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Firstgate İş Merkezi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_firstgate-is-merkezi.webp','1.000 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','16','Firstgate İş Merkezi | BUYAPI','Blok Sayısı : 1Toplam Alan : 1.000m2Konumu : Serdivan / SakaryaYapım Yılı : 2015',''),
('truva-granit','Truva Granit','Truva Granit','Truva Granit','Truva Granit','Teknik İnovasyon & Prestijli ticari Projesi','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Truva Granit projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Truva Granit represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Truva Granit أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Truva Granit verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_truva-granit.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','17','Truva Granit | BUYAPI','Teknik İnovasyon & Prestijli ticari Projesi',''),
('irfan-puskullu-is-merkezi','İrfan Püsküllü İş Merkezi','İrfan Püsküllü İş Merkezi','İrfan Püsküllü İş Merkezi','İrfan Püsküllü İş Merkezi','Blok Sayısı : 1Toplam Alan : 4.500m2Konumu : Serdivan / SakaryaYapım Yılı : 2016 ','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 1Toplam Alan : 4.500m2Konumu : Serdivan / SakaryaYapım Yılı : 2016 

Şehrin en önemli noktalarından birinde, Serdivan AVM karşısındaki projemiz, toplam 4500 m2 kapalı alanda dükkanlar, ofisler ve kapalı otoparklardan oluşmaktadır.','İrfan Püsküllü İş Merkezi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع İrfan Püsküllü İş Merkezi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','İrfan Püsküllü İş Merkezi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_irfan-puskullu-is-merkezi.webp','4.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','18','İrfan Püsküllü İş Merkezi | BUYAPI','Blok Sayısı : 1Toplam Alan : 4.500m2Konumu : Serdivan / SakaryaYapım Yılı : 2016 ',''),
('honda-plaza','Honda Plaza','Honda Plaza','Honda Plaza','Honda Plaza','Blok Sayısı : 3Toplam Alan : 2.500m2Konumu : Erenler / SakaryaYapım Yılı : 2018D100 karayoluna cepheli plaza, Honda Türkiye tarafından hazırlanmış ola...','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 3Toplam Alan : 2.500m2Konumu : Erenler / SakaryaYapım Yılı : 2018D100 karayoluna cepheli plaza, Honda Türkiye tarafından hazırlanmış olan projelere ve iş programına uygun olarak başarıyla tamamlanmıştır. Tüm betonarme işleri, çelik konstrüksiyon işleri, Alüminyum cephe işleri, iç mekan ince ve kaba işleri sorunsuz şekilde anahtar teslimi olarak tamamlanarak kullanıma sunulmuştur.','Honda Plaza represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Honda Plaza أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Honda Plaza verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_honda-plaza.webp','2.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','19','Honda Plaza | BUYAPI','Blok Sayısı : 3Toplam Alan : 2.500m2Konumu : Erenler / SakaryaYapım Yılı : 2018D100 karayoluna cepheli plaza, Honda Türkiye tarafından hazırlanmış ola...',''),
('hasbil-tarim','Hasbil Tarım','Hasbil Tarım','Hasbil Tarım','Hasbil Tarım','Teknik İnovasyon & Prestijli ticari Projesi','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Hasbil Tarım projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Hasbil Tarım represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Hasbil Tarım أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Hasbil Tarım verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_hasbil-tarim.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','20','Hasbil Tarım | BUYAPI','Teknik İnovasyon & Prestijli ticari Projesi',''),
('cark-petrol','Çark Petrol','Çark Petrol','Çark Petrol','Çark Petrol','Blok Sayısı : 1Toplam Alan : 2.000m2Konumu : SakaryaYapım Yılı : 2018','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 1Toplam Alan : 2.000m2Konumu : SakaryaYapım Yılı : 2018

Petrol Ofisi Türkiye tarafından tarafımıza verilen projelere uygun olarak tüm tesis inşaatı anahtar teslim olarak tarafımızdan tamamlanmıştır.','Çark Petrol represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Çark Petrol أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Çark Petrol verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_cark-petrol.webp','2.000 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','21','Çark Petrol | BUYAPI','Blok Sayısı : 1Toplam Alan : 2.000m2Konumu : SakaryaYapım Yılı : 2018',''),
('kia-plaza','Kia Plaza','Kia Plaza','Kia Plaza','Kia Plaza','Blok Sayısı : 2','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 2

Toplam Alan : 2.500m2 

Konumu : Erenler / Sakarya 

Yapım Yılı : 2013

Kia Türkiye tarafından hazırlanan çizim ve tasarımlara uygun olarak tüm iş kalemleri eksiksiz ve zamanında tamamlanarak kullanıcıya teslim edilmiştir.

Tüm betonarme işleri, çelik konstrüksiyon işleri, Alüminyum cephe işleri, iç mekan ince ve kaba işleri sorunsuz şekilde anahtar teslimi olarak tamamlanarak kullanıma sunulmuştur.','Kia Plaza represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Kia Plaza أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Kia Plaza verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_kia-plaza.webp','2.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','22','Kia Plaza | BUYAPI','Blok Sayısı : 2',''),
('opera-garden','Opera Garden','Opera Garden','Opera Garden','Opera Garden','Teknik İnovasyon & Prestijli ticari Projesi','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Opera Garden projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Opera Garden represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Opera Garden أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Opera Garden verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_opera-garden.webp','12.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','23','Opera Garden | BUYAPI','Teknik İnovasyon & Prestijli ticari Projesi',''),
('hunerli-orman-urunleri','Hünerli Orman Ürünleri','Hünerli Orman Ürünleri','Hünerli Orman Ürünleri','Hünerli Orman Ürünleri','Blok Sayısı : 2','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 2

Toplam Alan : 4.500m2 

Konumu : Sakarya 

Yapım Yılı : 2020

Betonarme temeller üzerine çelik konstrüksiyon olarak inşa edilen yapının tüm tasarım ve inşaat işleri tarafımızdan rekor sürede tamamlanmıştır.','Hünerli Orman Ürünleri represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Hünerli Orman Ürünleri أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Hünerli Orman Ürünleri verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','tamamlanan','0','0','uploads/projects/cover_hunerli-orman-urunleri.webp','4.500 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'0','1','24','Hünerli Orman Ürünleri | BUYAPI','Blok Sayısı : 2',''),
('buyapi-eksen','Buyapı Eksen','Buyapı Eksen','Buyapı Eksen','Buyapı Eksen','Daire Sayısı: 18Daire Tipi : 3+1Toplam Alan : 3.000 m2Konumu : Serdivan / SakaryaHayatın yeni ekseni!','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı: 18Daire Tipi : 3+1Toplam Alan : 3.000 m2Konumu : Serdivan / SakaryaHayatın yeni ekseni!','Buyapı Eksen represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Eksen أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Eksen verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-eksen.webp','3.000 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','25','Buyapı Eksen | BUYAPI','Daire Sayısı: 18Daire Tipi : 3+1Toplam Alan : 3.000 m2Konumu : Serdivan / SakaryaHayatın yeni ekseni!',''),
('buyapi-begonvil-apartmani','Buyapı Begonvil Apartmanı','Buyapı Begonvil Apartmanı','Buyapı Begonvil Apartmanı','Buyapı Begonvil Apartmanı','Teknik İnovasyon & Prestijli konut Projesi','Prestigious Residential Living in İstanbul','مشروع سكني فاخر في İstanbul','Prestigeträchtiges Wohnen in İstanbul','Buyapı Begonvil Apartmanı projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Buyapı Begonvil Apartmanı represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Begonvil Apartmanı أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Begonvil Apartmanı verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','İstanbul','Kadıköy / İstanbul','tr-ib','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-begonvil-apartmani.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','26','Buyapı Begonvil Apartmanı | BUYAPI','Teknik İnovasyon & Prestijli konut Projesi',''),
('buyapi-imaj','Buyapı İmaj','Buyapı İmaj','Buyapı İmaj','Buyapı İmaj','Teknik İnovasyon & Prestijli konut Projesi','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Buyapı İmaj projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Buyapı İmaj represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı İmaj أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı İmaj verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-imaj.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','28','Buyapı İmaj | BUYAPI','Teknik İnovasyon & Prestijli konut Projesi',''),
('buyapi-nova-plus','Buyapı Nova Plus','Buyapı Nova Plus','Buyapı Nova Plus','Buyapı Nova Plus','Teknik İnovasyon & Prestijli konut Projesi','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Buyapı Nova Plus projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.','Buyapı Nova Plus represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Nova Plus أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Nova Plus verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-nova-plus.webp','12.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','29','Buyapı Nova Plus | BUYAPI','Teknik İnovasyon & Prestijli konut Projesi',''),
('buyapi-centrum','Buyapı Centrum','Buyapı Centrum','Buyapı Centrum','Buyapı Centrum','Blok Sayısı : 1Toplam Alan : 1.000m2Konumu : Serdivan / SakaryaYapım Yılı : 2015','Modern Commercial & Business Center in Sakarya','مركز أعمال وتجاري حديث في Sakarya','Modernes Gewerbe- und Geschäftszentrum in Sakarya','Blok Sayısı : 1Toplam Alan : 1.000m2Konumu : Serdivan / SakaryaYapım Yılı : 2015

Şehrin ana giriş arterlerinden biri üzerinde 21 adet ofis ve dükkanlardan oluşan projemiz şehre ve bölgesine ayrı bir estetik ve kalite katmaya devam ediyor.','Buyapı Centrum represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Centrum أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Centrum verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','ticari','devam-eden','0','0','uploads/projects/cover_buyapi-centrum.webp','1.000 m²','Modern Ticari & İş Merkezi','','',NULL,NULL,'1','1','30','Buyapı Centrum | BUYAPI','Blok Sayısı : 1Toplam Alan : 1.000m2Konumu : Serdivan / SakaryaYapım Yılı : 2015',''),
('sapanca-life','Sapanca Life','Sapanca Life','Sapanca Life','Sapanca Life','Villa Sayısı : 6Villa Tipi : 4+1Toplam Alan : 2985 m2Konumu : SapancaYapım Yılı : 2020','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Villa Sayısı : 6Villa Tipi : 4+1Toplam Alan : 2985 m2Konumu : SapancaYapım Yılı : 2020

Sapanca İlçesi , Kırkpınar bölgesi, İpek Yolu üzerinde 6 adet yüzme havuzlu ultra lüks villadan oluşan sitemizde 2020 başında yaşam başladı','Sapanca Life represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Sapanca Life أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Sapanca Life verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Sapanca / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_sapanca-life.webp','2985 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','31','Sapanca Life | BUYAPI','Villa Sayısı : 6Villa Tipi : 4+1Toplam Alan : 2985 m2Konumu : SapancaYapım Yılı : 2020',''),
('buyapi-vadi','Buyapı Vadi','Buyapı Vadi','Buyapı Vadi','Buyapı Vadi','Villa Sayısı : 27Villa Tipi : 4+1Toplam Alan : 6487 m2Konumu : Serdivan / SakaryaYapım Yılı : 2019','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Villa Sayısı : 27Villa Tipi : 4+1Toplam Alan : 6487 m2Konumu : Serdivan / SakaryaYapım Yılı : 2019

27 adet süper lüks villadan oluşan sitemizde 24 saat güvenlikli site girişi,her villa için özel araç otoparkı ve özel bahçe standart olarak sunulmaktadır.','Buyapı Vadi represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Vadi أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Vadi verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-vadi.webp','6487 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','32','Buyapı Vadi | BUYAPI','Villa Sayısı : 27Villa Tipi : 4+1Toplam Alan : 6487 m2Konumu : Serdivan / SakaryaYapım Yılı : 2019',''),
('buyapi-sungate','Buyapı Sungate','Buyapı Sungate','Buyapı Sungate','Buyapı Sungate','Daire Sayısı : 181Daire Tipi : 2+1, 3+1Blok Sayısı : 4Toplam Alan : 17540 m2Konumu : GüneşlerYapım Yılı : 2018','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 181Daire Tipi : 2+1, 3+1Blok Sayısı : 4Toplam Alan : 17540 m2Konumu : GüneşlerYapım Yılı : 2018

Şehrin kuzeydoğu aksında yer alan projemiz 181 adet daireden oluşmaktadır. 17.540 m2 arsa üzerinde 24.906 m2 inşaat alanlı olarak yapımı tamamlanan kat karşılığı konut projemizdir. Sözleşmemizde tamamlanma süresi 30 ay olarak tanımlanan proje 16 ay gibi rekor bir sürede tamamlanarak müşterilerimize teslim edilmiştir. Tüm projenin satışı 10 ayda tamamlanmış olup, müşteri memnuniyeti oranı birebir yapılan görüşmelerde en yüksek seviyede tespit edilmiştir.','Buyapı Sungate represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Sungate أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Sungate verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-sungate.webp','17540 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','33','Buyapı Sungate | BUYAPI','Daire Sayısı : 181Daire Tipi : 2+1, 3+1Blok Sayısı : 4Toplam Alan : 17540 m2Konumu : GüneşlerYapım Yılı : 2018',''),
('buyapi-uptown','Buyapı Uptown','Buyapı Uptown','Buyapı Uptown','Buyapı Uptown','Daire Sayısı : 16Daire Tipi : 3+1Blok Sayısı : 2Toplam Alan : 2857 m2Konumu : Serdivan / SakaryaYapım Yılı : 2016','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 16Daire Tipi : 3+1Blok Sayısı : 2Toplam Alan : 2857 m2Konumu : Serdivan / SakaryaYapım Yılı : 2016

Eşsiz tasarımı ile kaliteli yaşam standartları getiren butik UPTOWN projemizde kapalı otoparklar, panoramik asansörler, yüzme havuzu ve 7/24 güvenlik sistemleri standart olarak site sakinlerinin kullanımına sunulmuştur.','Buyapı Uptown represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Uptown أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Uptown verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-uptown.webp','2857 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','34','Buyapı Uptown | BUYAPI','Daire Sayısı : 16Daire Tipi : 3+1Blok Sayısı : 2Toplam Alan : 2857 m2Konumu : Serdivan / SakaryaYapım Yılı : 2016',''),
('begon-villa','Begon Villa','Begon Villa','Begon Villa','Begon Villa','Villa Sayısı : 3Toplam Alan : 1.500m2Konumu : Serdivan / SakaryaYapım Yılı : 2014','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Villa Sayısı : 3Toplam Alan : 1.500m2Konumu : Serdivan / SakaryaYapım Yılı : 2014

Müstakil özel tasarım villalardan oluşan site sıra dışı mimarisi ve kullanım tarzıyla bölgesine ayrı bir güzellik katıyor','Begon Villa represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Begon Villa أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Begon Villa verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_begon-villa.webp','1.500 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','35','Begon Villa | BUYAPI','Villa Sayısı : 3Toplam Alan : 1.500m2Konumu : Serdivan / SakaryaYapım Yılı : 2014',''),
('buyapi-diamond','Buyapı Diamond','Buyapı Diamond','Buyapı Diamond','Buyapı Diamond','Daire Sayısı : 70Daire Tipi : 2+1, 3+1, 4+1Blok Sayısı : 3Toplam Alan : 9422 m2Konumu : SerdivanYapım Yılı : 2017','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 70Daire Tipi : 2+1, 3+1, 4+1Blok Sayısı : 3Toplam Alan : 9422 m2Konumu : SerdivanYapım Yılı : 2017

70 adet sıra dışı tasarım ve konfora sahip daireden oluşan beş yıldızlı otel konforundaki sitede kapalı otoparklardan direkt dairelere ulaşma imkanı, site içi rekreasyon alanları, çocuk oyun alanları, yüzme havuzu, Fitness Center ve güvenlikli giriş standart olarak sunuldu.','Buyapı Diamond represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Diamond أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Diamond verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-diamond.webp','9422 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','36','Buyapı Diamond | BUYAPI','Daire Sayısı : 70Daire Tipi : 2+1, 3+1, 4+1Blok Sayısı : 3Toplam Alan : 9422 m2Konumu : SerdivanYapım Yılı : 2017',''),
('buyapi-flex','Buyapı Flex','Buyapı Flex','Buyapı Flex','Buyapı Flex','Daire Sayısı : 15Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2656 m2Konumu : SerdivanYapım Yılı : 2017Kendine özgü flexible mimarisi ile hemen göze ç...','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 15Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2656 m2Konumu : SerdivanYapım Yılı : 2017Kendine özgü flexible mimarisi ile hemen göze çarpan sıra dışı projede bahçe katı daireler doğa ile iç içe bir yaşamı şanslı maliklere sunmaya devam ediyor.','Buyapı Flex represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Flex أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Flex verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-flex.webp','2656 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','37','Buyapı Flex | BUYAPI','Daire Sayısı : 15Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2656 m2Konumu : SerdivanYapım Yılı : 2017Kendine özgü flexible mimarisi ile hemen göze ç...',''),
('buyapi-garden','Buyapı Garden','Buyapı Garden','Buyapı Garden','Buyapı Garden','Daire Sayısı : 30Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2642 m2Konumu : Serdivan / SakaryaYapım Yılı : 2016','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 30Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2642 m2Konumu : Serdivan / SakaryaYapım Yılı : 2016

30 adet daire ve dükkanlardan oluşan sitemiz bulunduğu bölgeye yeni bir soluk getirdi ve gelişimine büyük katkıda bulundu.','Buyapı Garden represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Garden أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Garden verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-garden.webp','2642 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','38','Buyapı Garden | BUYAPI','Daire Sayısı : 30Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2642 m2Konumu : Serdivan / SakaryaYapım Yılı : 2016',''),
('buyapi-prestige','Buyapı Prestige','Buyapı Prestige','Buyapı Prestige','Buyapı Prestige','Daire Sayısı : 18Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2515 m2Konumu : AdapazarıYapım Yılı : 2017','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 18Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2515 m2Konumu : AdapazarıYapım Yılı : 2017

18 dairelik sıra dışı konsepte sahip proje, kullanıcılarına doğanın içinde keyifli bir yaşam sunmaya devam ediyor.','Buyapı Prestige represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Prestige أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Prestige verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-prestige.webp','2515 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','39','Buyapı Prestige | BUYAPI','Daire Sayısı : 18Daire Tipi : 3+1Blok Sayısı : 1Toplam Alan : 2515 m2Konumu : AdapazarıYapım Yılı : 2017',''),
('buyapi-prime','Buyapı Prime','Buyapı Prime','Buyapı Prime','Buyapı Prime','Daire Sayısı : 132Daire Tipi : 1+1Blok Sayısı : 2Toplam Alan : 4553 m2Konumu : AdapazarıYapım Yılı : 2019','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı : 132Daire Tipi : 1+1Blok Sayısı : 2Toplam Alan : 4553 m2Konumu : AdapazarıYapım Yılı : 2019

Stüdyo daire tarzını kendine özgü tasarımı ile yeniden biçimlendiren 132 adet (1+1) bağımsız daireden oluşan sitemiz 2019 Eylül ayından beri sakinlerini ağırlamaya devam ediyor.','Buyapı Prime represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Prime أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Prime verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','devam-eden','0','0','uploads/projects/cover_buyapi-prime.webp','4553 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'1','1','40','Buyapı Prime | BUYAPI','Daire Sayısı : 132Daire Tipi : 1+1Blok Sayısı : 2Toplam Alan : 4553 m2Konumu : AdapazarıYapım Yılı : 2019',''),
('buyapi-vision','Buyapı Vision','Buyapı Vision','Buyapı Vision','Buyapı Vision','Daire Sayısı :45','Prestigious Residential Living in Sakarya','مشروع سكني فاخر في Sakarya','Prestigeträchtiges Wohnen in Sakarya','Daire Sayısı :45

Daire Tipi : 3+1

Blok Sayısı : 3

Toplam Alan : 5030 m2

Konumu : Serdivan / Sakarya

Yapım Yılı : 2016

5000 m2 alanda 45 adet keyifli daireden oluşan site 12 ay gibi kısa bir sürede tamamlanarak, sahiplerine teslim edildi.','Buyapı Vision represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.','يقدم مشروع Buyapı Vision أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.','Buyapı Vision verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.','Sakarya','Serdivan / Sakarya','tr-sk','konut','tamamlanan','0','0','uploads/projects/cover_buyapi-vision.webp','5030 m²','A++ Avant-Garde Rezidans','','',NULL,NULL,'0','1','41','Buyapı Vision | BUYAPI','Daire Sayısı :45','')\nON DUPLICATE KEY UPDATE
title_tr=VALUES(title_tr),
title_en=VALUES(title_en),
title_ar=VALUES(title_ar),
title_de=VALUES(title_de),
subtitle_tr=VALUES(subtitle_tr),
subtitle_en=VALUES(subtitle_en),
subtitle_ar=VALUES(subtitle_ar),
subtitle_de=VALUES(subtitle_de),
description_tr=VALUES(description_tr),
description_en=VALUES(description_en),
description_ar=VALUES(description_ar),
description_de=VALUES(description_de),
cat=VALUES(cat),
loc=VALUES(loc),
city_key=VALUES(city_key),
type=VALUES(type),
status=VALUES(status),
is_new=VALUES(is_new),
is_urban_renewal=VALUES(is_urban_renewal),
cover_image=VALUES(cover_image),
area=VALUES(area),
concept=VALUES(concept),
delivery=VALUES(delivery),
catalog_file=VALUES(catalog_file),
street_view_code=VALUES(street_view_code),
google_map_code=VALUES(google_map_code),
is_featured=VALUES(is_featured),
is_active=VALUES(is_active),
sort_order=VALUES(sort_order),
seo_title=VALUES(seo_title),
seo_desc=VALUES(seo_desc),
seo_keywords=VALUES(seo_keywords);

DELETE pm FROM project_media pm
JOIN projects p ON p.id = pm.project_id
JOIN tmp_seed_project_slugs s ON s.slug = p.slug;

INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_begon-villa_0.webp', 'Begon Villa Detay 1', 1
FROM projects p WHERE p.slug = 'begon-villa';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_begon-villa_1.webp', 'Begon Villa Detay 2', 2
FROM projects p WHERE p.slug = 'begon-villa';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_begon-villa_2.webp', 'Begon Villa Detay 3', 3
FROM projects p WHERE p.slug = 'begon-villa';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_begon-villa_3.webp', 'Begon Villa Detay 4', 4
FROM projects p WHERE p.slug = 'begon-villa';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_begon-villa_4.webp', 'Begon Villa Detay 5', 5
FROM projects p WHERE p.slug = 'begon-villa';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_begon-villa_5.webp', 'Begon Villa Detay 6', 6
FROM projects p WHERE p.slug = 'begon-villa';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_0.webp', 'Buyapı Altınışık İş Merkezi Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_1.webp', 'Buyapı Altınışık İş Merkezi Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_2.webp', 'Buyapı Altınışık İş Merkezi Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_3.webp', 'Buyapı Altınışık İş Merkezi Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_4.webp', 'Buyapı Altınışık İş Merkezi Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_5.webp', 'Buyapı Altınışık İş Merkezi Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_6.webp', 'Buyapı Altınışık İş Merkezi Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_7.webp', 'Buyapı Altınışık İş Merkezi Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_8.webp', 'Buyapı Altınışık İş Merkezi Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-is-merkezi_9.webp', 'Buyapı Altınışık İş Merkezi Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-altinisik-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_0.webp', 'Buyapı Altınışık Konutları-1 Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_1.webp', 'Buyapı Altınışık Konutları-1 Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_2.webp', 'Buyapı Altınışık Konutları-1 Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_3.webp', 'Buyapı Altınışık Konutları-1 Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_4.webp', 'Buyapı Altınışık Konutları-1 Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_5.webp', 'Buyapı Altınışık Konutları-1 Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_6.webp', 'Buyapı Altınışık Konutları-1 Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_7.webp', 'Buyapı Altınışık Konutları-1 Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_8.webp', 'Buyapı Altınışık Konutları-1 Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_9.webp', 'Buyapı Altınışık Konutları-1 Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_10.webp', 'Buyapı Altınışık Konutları-1 Detay 11', 11
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_11.webp', 'Buyapı Altınışık Konutları-1 Detay 12', 12
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_12.webp', 'Buyapı Altınışık Konutları-1 Detay 13', 13
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-1_13.webp', 'Buyapı Altınışık Konutları-1 Detay 14', 14
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-1';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_0.webp', 'Buyapı Altınışık Konutları-2 Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_1.webp', 'Buyapı Altınışık Konutları-2 Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_2.webp', 'Buyapı Altınışık Konutları-2 Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_3.webp', 'Buyapı Altınışık Konutları-2 Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_4.webp', 'Buyapı Altınışık Konutları-2 Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_5.webp', 'Buyapı Altınışık Konutları-2 Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-altinisik-konutlari-2_6.webp', 'Buyapı Altınışık Konutları-2 Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-altinisik-konutlari-2';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-begonvil-apartmani_0.webp', 'Buyapı Begonvil Apartmanı Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-begonvil-apartmani_1.webp', 'Buyapı Begonvil Apartmanı Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_2.webp', 'Buyapı Begonvil Apartmanı Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_3.webp', 'Buyapı Begonvil Apartmanı Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_4.webp', 'Buyapı Begonvil Apartmanı Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_5.webp', 'Buyapı Begonvil Apartmanı Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_6.webp', 'Buyapı Begonvil Apartmanı Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_7.webp', 'Buyapı Begonvil Apartmanı Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_8.webp', 'Buyapı Begonvil Apartmanı Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-begonvil-apartmani_9.webp', 'Buyapı Begonvil Apartmanı Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-begonvil-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-centrum_0.webp', 'Buyapı Centrum Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-centrum';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-centrum_1.webp', 'Buyapı Centrum Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-centrum';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-centrum_2.webp', 'Buyapı Centrum Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-centrum';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-centrum_3.webp', 'Buyapı Centrum Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-centrum';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-centrum_4.webp', 'Buyapı Centrum Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-centrum';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-centrum_5.webp', 'Buyapı Centrum Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-centrum';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-diamond_0.webp', 'Buyapı Diamond Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-diamond_1.webp', 'Buyapı Diamond Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_2.webp', 'Buyapı Diamond Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_3.webp', 'Buyapı Diamond Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_4.webp', 'Buyapı Diamond Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_5.webp', 'Buyapı Diamond Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_6.webp', 'Buyapı Diamond Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_7.webp', 'Buyapı Diamond Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_8.webp', 'Buyapı Diamond Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-diamond_9.webp', 'Buyapı Diamond Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-diamond';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-eksen_0.webp', 'Buyapı Eksen Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-eksen_1.webp', 'Buyapı Eksen Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_2.webp', 'Buyapı Eksen Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_3.webp', 'Buyapı Eksen Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_4.webp', 'Buyapı Eksen Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_5.webp', 'Buyapı Eksen Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_6.webp', 'Buyapı Eksen Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_7.webp', 'Buyapı Eksen Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_8.webp', 'Buyapı Eksen Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_9.webp', 'Buyapı Eksen Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-eksen_10.webp', 'Buyapı Eksen Detay 11', 11
FROM projects p WHERE p.slug = 'buyapi-eksen';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-flex_0.webp', 'Buyapı Flex Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-flex_1.webp', 'Buyapı Flex Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-flex_2.webp', 'Buyapı Flex Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-flex_3.webp', 'Buyapı Flex Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-flex_4.webp', 'Buyapı Flex Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-flex_5.webp', 'Buyapı Flex Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-flex_6.webp', 'Buyapı Flex Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-flex_7.webp', 'Buyapı Flex Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-flex';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-garden_0.webp', 'Buyapı Garden Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-garden_1.webp', 'Buyapı Garden Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-garden_2.webp', 'Buyapı Garden Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-garden_3.webp', 'Buyapı Garden Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-garden_4.webp', 'Buyapı Garden Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-garden_5.webp', 'Buyapı Garden Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-garden_6.webp', 'Buyapı Garden Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-imaj_0.webp', 'Buyapı İmaj Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-imaj_1.webp', 'Buyapı İmaj Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_2.webp', 'Buyapı İmaj Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_3.webp', 'Buyapı İmaj Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_4.webp', 'Buyapı İmaj Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_5.webp', 'Buyapı İmaj Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_6.webp', 'Buyapı İmaj Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_7.webp', 'Buyapı İmaj Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_8.webp', 'Buyapı İmaj Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_9.webp', 'Buyapı İmaj Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_10.webp', 'Buyapı İmaj Detay 11', 11
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_11.webp', 'Buyapı İmaj Detay 12', 12
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_12.webp', 'Buyapı İmaj Detay 13', 13
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_13.webp', 'Buyapı İmaj Detay 14', 14
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-imaj_14.webp', 'Buyapı İmaj Detay 15', 15
FROM projects p WHERE p.slug = 'buyapi-imaj';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-nova-plus_0.webp', 'Buyapı Nova Plus Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-nova-plus_1.webp', 'Buyapı Nova Plus Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_2.webp', 'Buyapı Nova Plus Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_3.webp', 'Buyapı Nova Plus Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_4.webp', 'Buyapı Nova Plus Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_5.webp', 'Buyapı Nova Plus Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_6.webp', 'Buyapı Nova Plus Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_7.webp', 'Buyapı Nova Plus Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_8.webp', 'Buyapı Nova Plus Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_9.webp', 'Buyapı Nova Plus Detay 10', 10
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_10.webp', 'Buyapı Nova Plus Detay 11', 11
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_11.webp', 'Buyapı Nova Plus Detay 12', 12
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_12.webp', 'Buyapı Nova Plus Detay 13', 13
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_13.webp', 'Buyapı Nova Plus Detay 14', 14
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_14.webp', 'Buyapı Nova Plus Detay 15', 15
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_15.webp', 'Buyapı Nova Plus Detay 16', 16
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_16.webp', 'Buyapı Nova Plus Detay 17', 17
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_17.webp', 'Buyapı Nova Plus Detay 18', 18
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-nova-plus_18.webp', 'Buyapı Nova Plus Detay 19', 19
FROM projects p WHERE p.slug = 'buyapi-nova-plus';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-palm-is-merkezi_0.webp', 'Buyapı Palm İş Merkezi Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-palm-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-palm-is-merkezi_1.webp', 'Buyapı Palm İş Merkezi Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-palm-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-palm-is-merkezi_2.webp', 'Buyapı Palm İş Merkezi Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-palm-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-palm-is-merkezi_3.webp', 'Buyapı Palm İş Merkezi Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-palm-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-palm-is-merkezi_4.webp', 'Buyapı Palm İş Merkezi Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-palm-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-palm-is-merkezi_5.webp', 'Buyapı Palm İş Merkezi Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-palm-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-prestige_0.webp', 'Buyapı Prestige Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-prestige';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-prestige_1.webp', 'Buyapı Prestige Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-prestige';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prestige_2.webp', 'Buyapı Prestige Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-prestige';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prestige_3.webp', 'Buyapı Prestige Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-prestige';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prestige_4.webp', 'Buyapı Prestige Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-prestige';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prestige_5.webp', 'Buyapı Prestige Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-prestige';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-prime_0.webp', 'Buyapı Prime Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-prime_1.webp', 'Buyapı Prime Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prime_2.webp', 'Buyapı Prime Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prime_3.webp', 'Buyapı Prime Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prime_4.webp', 'Buyapı Prime Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prime_5.webp', 'Buyapı Prime Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prime_6.webp', 'Buyapı Prime Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-prime_7.webp', 'Buyapı Prime Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-prime';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-sungate_0.webp', 'Buyapı Sungate Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-sungate';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-sungate_1.webp', 'Buyapı Sungate Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-sungate';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-sungate_2.webp', 'Buyapı Sungate Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-sungate';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-sungate_3.webp', 'Buyapı Sungate Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-sungate';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-sungate_4.webp', 'Buyapı Sungate Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-sungate';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-sungate_5.webp', 'Buyapı Sungate Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-sungate';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-time-is-merkezi_0.webp', 'Buyapı Time İş Merkezi Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-time-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-time-is-merkezi_1.webp', 'Buyapı Time İş Merkezi Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-time-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-time-is-merkezi_2.webp', 'Buyapı Time İş Merkezi Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-time-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-time-is-merkezi_3.webp', 'Buyapı Time İş Merkezi Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-time-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-time-is-merkezi_4.webp', 'Buyapı Time İş Merkezi Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-time-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-time-is-merkezi_5.webp', 'Buyapı Time İş Merkezi Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-time-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-uptown_0.webp', 'Buyapı Uptown Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-uptown';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-uptown_1.webp', 'Buyapı Uptown Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-uptown';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-uptown_2.webp', 'Buyapı Uptown Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-uptown';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-uptown_3.webp', 'Buyapı Uptown Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-uptown';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-uptown_4.webp', 'Buyapı Uptown Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-uptown';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-vadi_0.webp', 'Buyapı Vadi Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-vadi_1.webp', 'Buyapı Vadi Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_2.webp', 'Buyapı Vadi Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_3.webp', 'Buyapı Vadi Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_4.webp', 'Buyapı Vadi Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_5.webp', 'Buyapı Vadi Detay 6', 6
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_6.webp', 'Buyapı Vadi Detay 7', 7
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_7.webp', 'Buyapı Vadi Detay 8', 8
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vadi_8.webp', 'Buyapı Vadi Detay 9', 9
FROM projects p WHERE p.slug = 'buyapi-vadi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-vision_0.webp', 'Buyapı Vision Detay 1', 1
FROM projects p WHERE p.slug = 'buyapi-vision';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-vision_1.webp', 'Buyapı Vision Detay 2', 2
FROM projects p WHERE p.slug = 'buyapi-vision';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vision_2.webp', 'Buyapı Vision Detay 3', 3
FROM projects p WHERE p.slug = 'buyapi-vision';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vision_3.webp', 'Buyapı Vision Detay 4', 4
FROM projects p WHERE p.slug = 'buyapi-vision';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-vision_4.webp', 'Buyapı Vision Detay 5', 5
FROM projects p WHERE p.slug = 'buyapi-vision';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_cark-petrol_0.webp', 'Çark Petrol Detay 1', 1
FROM projects p WHERE p.slug = 'cark-petrol';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_cark-petrol_1.webp', 'Çark Petrol Detay 2', 2
FROM projects p WHERE p.slug = 'cark-petrol';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_cark-petrol_2.webp', 'Çark Petrol Detay 3', 3
FROM projects p WHERE p.slug = 'cark-petrol';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_cark-petrol_3.webp', 'Çark Petrol Detay 4', 4
FROM projects p WHERE p.slug = 'cark-petrol';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_cark-petrol_4.webp', 'Çark Petrol Detay 5', 5
FROM projects p WHERE p.slug = 'cark-petrol';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_cark-petrol_5.webp', 'Çark Petrol Detay 6', 6
FROM projects p WHERE p.slug = 'cark-petrol';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_firstgate-is-merkezi_0.webp', 'Firstgate İş Merkezi Detay 1', 1
FROM projects p WHERE p.slug = 'firstgate-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_firstgate-is-merkezi_1.webp', 'Firstgate İş Merkezi Detay 2', 2
FROM projects p WHERE p.slug = 'firstgate-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_firstgate-is-merkezi_2.webp', 'Firstgate İş Merkezi Detay 3', 3
FROM projects p WHERE p.slug = 'firstgate-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_firstgate-is-merkezi_3.webp', 'Firstgate İş Merkezi Detay 4', 4
FROM projects p WHERE p.slug = 'firstgate-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_firstgate-is-merkezi_4.webp', 'Firstgate İş Merkezi Detay 5', 5
FROM projects p WHERE p.slug = 'firstgate-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_gunes-apartmani_0.webp', 'Güneş Apartmanı Detay 1', 1
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_gunes-apartmani_1.webp', 'Güneş Apartmanı Detay 2', 2
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_2.webp', 'Güneş Apartmanı Detay 3', 3
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_3.webp', 'Güneş Apartmanı Detay 4', 4
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_4.webp', 'Güneş Apartmanı Detay 5', 5
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_5.webp', 'Güneş Apartmanı Detay 6', 6
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_6.webp', 'Güneş Apartmanı Detay 7', 7
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_7.webp', 'Güneş Apartmanı Detay 8', 8
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_8.webp', 'Güneş Apartmanı Detay 9', 9
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_9.webp', 'Güneş Apartmanı Detay 10', 10
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_gunes-apartmani_10.webp', 'Güneş Apartmanı Detay 11', 11
FROM projects p WHERE p.slug = 'gunes-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_hasbil-tarim_0.webp', 'Hasbil Tarım Detay 1', 1
FROM projects p WHERE p.slug = 'hasbil-tarim';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_hasbil-tarim_1.webp', 'Hasbil Tarım Detay 2', 2
FROM projects p WHERE p.slug = 'hasbil-tarim';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hasbil-tarim_2.webp', 'Hasbil Tarım Detay 3', 3
FROM projects p WHERE p.slug = 'hasbil-tarim';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hasbil-tarim_3.webp', 'Hasbil Tarım Detay 4', 4
FROM projects p WHERE p.slug = 'hasbil-tarim';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hasbil-tarim_4.webp', 'Hasbil Tarım Detay 5', 5
FROM projects p WHERE p.slug = 'hasbil-tarim';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_hendek-fabrika_0.webp', 'Hendek Fabrika Detay 1', 1
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_hendek-fabrika_1.webp', 'Hendek Fabrika Detay 2', 2
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_2.webp', 'Hendek Fabrika Detay 3', 3
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_3.webp', 'Hendek Fabrika Detay 4', 4
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_4.webp', 'Hendek Fabrika Detay 5', 5
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_5.webp', 'Hendek Fabrika Detay 6', 6
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_6.webp', 'Hendek Fabrika Detay 7', 7
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_7.webp', 'Hendek Fabrika Detay 8', 8
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_8.webp', 'Hendek Fabrika Detay 9', 9
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_9.webp', 'Hendek Fabrika Detay 10', 10
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hendek-fabrika_10.webp', 'Hendek Fabrika Detay 11', 11
FROM projects p WHERE p.slug = 'hendek-fabrika';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_honda-plaza_0.webp', 'Honda Plaza Detay 1', 1
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_honda-plaza_1.webp', 'Honda Plaza Detay 2', 2
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_2.webp', 'Honda Plaza Detay 3', 3
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_3.webp', 'Honda Plaza Detay 4', 4
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_4.webp', 'Honda Plaza Detay 5', 5
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_5.webp', 'Honda Plaza Detay 6', 6
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_6.webp', 'Honda Plaza Detay 7', 7
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_7.webp', 'Honda Plaza Detay 8', 8
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_honda-plaza_8.webp', 'Honda Plaza Detay 9', 9
FROM projects p WHERE p.slug = 'honda-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_hunerli-orman-urunleri_0.webp', 'Hünerli Orman Ürünleri Detay 1', 1
FROM projects p WHERE p.slug = 'hunerli-orman-urunleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_hunerli-orman-urunleri_1.webp', 'Hünerli Orman Ürünleri Detay 2', 2
FROM projects p WHERE p.slug = 'hunerli-orman-urunleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hunerli-orman-urunleri_2.webp', 'Hünerli Orman Ürünleri Detay 3', 3
FROM projects p WHERE p.slug = 'hunerli-orman-urunleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_hunerli-orman-urunleri_3.webp', 'Hünerli Orman Ürünleri Detay 4', 4
FROM projects p WHERE p.slug = 'hunerli-orman-urunleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-intense_0.webp', 'Buyapı Intense Detay 1', 1
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-intense_1.webp', 'Buyapı Intense Detay 2', 2
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_2.webp', 'Buyapı Intense Detay 3', 3
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_3.webp', 'Buyapı Intense Detay 4', 4
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_4.webp', 'Buyapı Intense Detay 5', 5
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_5.webp', 'Buyapı Intense Detay 6', 6
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_6.webp', 'Buyapı Intense Detay 7', 7
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_7.webp', 'Buyapı Intense Detay 8', 8
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_8.webp', 'Buyapı Intense Detay 9', 9
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_9.webp', 'Buyapı Intense Detay 10', 10
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_10.webp', 'Buyapı Intense Detay 11', 11
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_11.webp', 'Buyapı Intense Detay 12', 12
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_12.webp', 'Buyapı Intense Detay 13', 13
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-intense_13.webp', 'Buyapı Intense Detay 14', 14
FROM projects p WHERE p.slug = 'intense';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_irfan-puskullu-is-merkezi_0.webp', 'İrfan Püsküllü İş Merkezi Detay 1', 1
FROM projects p WHERE p.slug = 'irfan-puskullu-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_irfan-puskullu-is-merkezi_1.webp', 'İrfan Püsküllü İş Merkezi Detay 2', 2
FROM projects p WHERE p.slug = 'irfan-puskullu-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_irfan-puskullu-is-merkezi_2.webp', 'İrfan Püsküllü İş Merkezi Detay 3', 3
FROM projects p WHERE p.slug = 'irfan-puskullu-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_irfan-puskullu-is-merkezi_3.webp', 'İrfan Püsküllü İş Merkezi Detay 4', 4
FROM projects p WHERE p.slug = 'irfan-puskullu-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_irfan-puskullu-is-merkezi_4.webp', 'İrfan Püsküllü İş Merkezi Detay 5', 5
FROM projects p WHERE p.slug = 'irfan-puskullu-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_irfan-puskullu-is-merkezi_5.webp', 'İrfan Püsküllü İş Merkezi Detay 6', 6
FROM projects p WHERE p.slug = 'irfan-puskullu-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_kemal-yener-is-merkezi_0.webp', 'Kemal Yener İş Merkezi Detay 1', 1
FROM projects p WHERE p.slug = 'kemal-yener-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_kemal-yener-is-merkezi_1.webp', 'Kemal Yener İş Merkezi Detay 2', 2
FROM projects p WHERE p.slug = 'kemal-yener-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kemal-yener-is-merkezi_2.webp', 'Kemal Yener İş Merkezi Detay 3', 3
FROM projects p WHERE p.slug = 'kemal-yener-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kemal-yener-is-merkezi_3.webp', 'Kemal Yener İş Merkezi Detay 4', 4
FROM projects p WHERE p.slug = 'kemal-yener-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kemal-yener-is-merkezi_4.webp', 'Kemal Yener İş Merkezi Detay 5', 5
FROM projects p WHERE p.slug = 'kemal-yener-is-merkezi';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_kia-plaza_0.webp', 'Kia Plaza Detay 1', 1
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_kia-plaza_1.webp', 'Kia Plaza Detay 2', 2
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kia-plaza_2.webp', 'Kia Plaza Detay 3', 3
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kia-plaza_3.webp', 'Kia Plaza Detay 4', 4
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kia-plaza_4.webp', 'Kia Plaza Detay 5', 5
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kia-plaza_5.webp', 'Kia Plaza Detay 6', 6
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kia-plaza_6.webp', 'Kia Plaza Detay 7', 7
FROM projects p WHERE p.slug = 'kia-plaza';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_konak-apartmani_0.webp', 'Konak Apartmanı Detay 1', 1
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_konak-apartmani_1.webp', 'Konak Apartmanı Detay 2', 2
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_2.webp', 'Konak Apartmanı Detay 3', 3
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_3.webp', 'Konak Apartmanı Detay 4', 4
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_4.webp', 'Konak Apartmanı Detay 5', 5
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_5.webp', 'Konak Apartmanı Detay 6', 6
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_6.webp', 'Konak Apartmanı Detay 7', 7
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_7.webp', 'Konak Apartmanı Detay 8', 8
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_konak-apartmani_8.webp', 'Konak Apartmanı Detay 9', 9
FROM projects p WHERE p.slug = 'konak-apartmani';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_0.webp', 'Kontrolmatik Pomega Enerji Detay 1', 1
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_1.webp', 'Kontrolmatik Pomega Enerji Detay 2', 2
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_2.webp', 'Kontrolmatik Pomega Enerji Detay 3', 3
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_3.webp', 'Kontrolmatik Pomega Enerji Detay 4', 4
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_4.webp', 'Kontrolmatik Pomega Enerji Detay 5', 5
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_5.webp', 'Kontrolmatik Pomega Enerji Detay 6', 6
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_6.webp', 'Kontrolmatik Pomega Enerji Detay 7', 7
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_7.webp', 'Kontrolmatik Pomega Enerji Detay 8', 8
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_8.webp', 'Kontrolmatik Pomega Enerji Detay 9', 9
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_9.webp', 'Kontrolmatik Pomega Enerji Detay 10', 10
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_10.webp', 'Kontrolmatik Pomega Enerji Detay 11', 11
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_11.webp', 'Kontrolmatik Pomega Enerji Detay 12', 12
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_kontrolmatik-pomega-enerji_12.webp', 'Kontrolmatik Pomega Enerji Detay 13', 13
FROM projects p WHERE p.slug = 'kontrolmatik-pomega-enerji';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_mk-moda-atelier_0.webp', 'MK Moda Atelier Detay 1', 1
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_mk-moda-atelier_1.webp', 'MK Moda Atelier Detay 2', 2
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_mk-moda-atelier_2.webp', 'MK Moda Atelier Detay 3', 3
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_mk-moda-atelier_3.webp', 'MK Moda Atelier Detay 4', 4
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_mk-moda-atelier_4.webp', 'MK Moda Atelier Detay 5', 5
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_mk-moda-atelier_5.webp', 'MK Moda Atelier Detay 6', 6
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_mk-moda-atelier_6.webp', 'MK Moda Atelier Detay 7', 7
FROM projects p WHERE p.slug = 'mk-moda-atelier';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_opera-garden_0.webp', 'Opera Garden Detay 1', 1
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_opera-garden_1.webp', 'Opera Garden Detay 2', 2
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_opera-garden_2.webp', 'Opera Garden Detay 3', 3
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_opera-garden_3.webp', 'Opera Garden Detay 4', 4
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_opera-garden_4.webp', 'Opera Garden Detay 5', 5
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_opera-garden_5.webp', 'Opera Garden Detay 6', 6
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_opera-garden_6.webp', 'Opera Garden Detay 7', 7
FROM projects p WHERE p.slug = 'opera-garden';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_saka-su_0.webp', 'Saka Su Detay 1', 1
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_saka-su_1.webp', 'Saka Su Detay 2', 2
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_2.webp', 'Saka Su Detay 3', 3
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_3.webp', 'Saka Su Detay 4', 4
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_4.webp', 'Saka Su Detay 5', 5
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_5.webp', 'Saka Su Detay 6', 6
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_6.webp', 'Saka Su Detay 7', 7
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_7.webp', 'Saka Su Detay 8', 8
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_8.webp', 'Saka Su Detay 9', 9
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_9.webp', 'Saka Su Detay 10', 10
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_saka-su_10.webp', 'Saka Su Detay 11', 11
FROM projects p WHERE p.slug = 'saka-su';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_sapanca-life_0.webp', 'Sapanca Life Detay 1', 1
FROM projects p WHERE p.slug = 'sapanca-life';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_sapanca-life_1.webp', 'Sapanca Life Detay 2', 2
FROM projects p WHERE p.slug = 'sapanca-life';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_sapanca-life_2.webp', 'Sapanca Life Detay 3', 3
FROM projects p WHERE p.slug = 'sapanca-life';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_sapanca-life_3.webp', 'Sapanca Life Detay 4', 4
FROM projects p WHERE p.slug = 'sapanca-life';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_sapanca-life_4.webp', 'Sapanca Life Detay 5', 5
FROM projects p WHERE p.slug = 'sapanca-life';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_sapanca-life_5.webp', 'Sapanca Life Detay 6', 6
FROM projects p WHERE p.slug = 'sapanca-life';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-silence_0.webp', 'Buyapı Silence Detay 1', 1
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-silence_1.webp', 'Buyapı Silence Detay 2', 2
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_2.webp', 'Buyapı Silence Detay 3', 3
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_3.webp', 'Buyapı Silence Detay 4', 4
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_4.webp', 'Buyapı Silence Detay 5', 5
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_5.webp', 'Buyapı Silence Detay 6', 6
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_6.webp', 'Buyapı Silence Detay 7', 7
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_7.webp', 'Buyapı Silence Detay 8', 8
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_8.webp', 'Buyapı Silence Detay 9', 9
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_9.webp', 'Buyapı Silence Detay 10', 10
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_10.webp', 'Buyapı Silence Detay 11', 11
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_11.webp', 'Buyapı Silence Detay 12', 12
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_12.webp', 'Buyapı Silence Detay 13', 13
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_13.webp', 'Buyapı Silence Detay 14', 14
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_14.webp', 'Buyapı Silence Detay 15', 15
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_15.webp', 'Buyapı Silence Detay 16', 16
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_16.webp', 'Buyapı Silence Detay 17', 17
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_17.webp', 'Buyapı Silence Detay 18', 18
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_18.webp', 'Buyapı Silence Detay 19', 19
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_19.webp', 'Buyapı Silence Detay 20', 20
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_20.webp', 'Buyapı Silence Detay 21', 21
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_21.webp', 'Buyapı Silence Detay 22', 22
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_22.webp', 'Buyapı Silence Detay 23', 23
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_23.webp', 'Buyapı Silence Detay 24', 24
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_24.webp', 'Buyapı Silence Detay 25', 25
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_25.webp', 'Buyapı Silence Detay 26', 26
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-silence_26.webp', 'Buyapı Silence Detay 27', 27
FROM projects p WHERE p.slug = 'silence';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_truva-granit_0.webp', 'Truva Granit Detay 1', 1
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_truva-granit_1.webp', 'Truva Granit Detay 2', 2
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_truva-granit_2.webp', 'Truva Granit Detay 3', 3
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_truva-granit_3.webp', 'Truva Granit Detay 4', 4
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_truva-granit_4.webp', 'Truva Granit Detay 5', 5
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_truva-granit_5.webp', 'Truva Granit Detay 6', 6
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_truva-granit_6.webp', 'Truva Granit Detay 7', 7
FROM projects p WHERE p.slug = 'truva-granit';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-work-inn_0.webp', 'Buyapı Work Inn Detay 1', 1
FROM projects p WHERE p.slug = 'workinn';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_buyapi-work-inn_1.webp', 'Buyapı Work Inn Detay 2', 2
FROM projects p WHERE p.slug = 'workinn';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-work-inn_2.webp', 'Buyapı Work Inn Detay 3', 3
FROM projects p WHERE p.slug = 'workinn';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-work-inn_3.webp', 'Buyapı Work Inn Detay 4', 4
FROM projects p WHERE p.slug = 'workinn';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_buyapi-work-inn_4.webp', 'Buyapı Work Inn Detay 5', 5
FROM projects p WHERE p.slug = 'workinn';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_yavuz-gida-findik-entegre-tesisleri_0.webp', 'Yavuz Gıda Fındık Entegre Tesisleri Detay 1', 1
FROM projects p WHERE p.slug = 'yavuz-gida-findik-entegre-tesisleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'hero', 'uploads/projects/gallery_yavuz-gida-findik-entegre-tesisleri_1.webp', 'Yavuz Gıda Fındık Entegre Tesisleri Detay 2', 2
FROM projects p WHERE p.slug = 'yavuz-gida-findik-entegre-tesisleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_yavuz-gida-findik-entegre-tesisleri_2.webp', 'Yavuz Gıda Fındık Entegre Tesisleri Detay 3', 3
FROM projects p WHERE p.slug = 'yavuz-gida-findik-entegre-tesisleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_yavuz-gida-findik-entegre-tesisleri_3.webp', 'Yavuz Gıda Fındık Entegre Tesisleri Detay 4', 4
FROM projects p WHERE p.slug = 'yavuz-gida-findik-entegre-tesisleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_yavuz-gida-findik-entegre-tesisleri_4.webp', 'Yavuz Gıda Fındık Entegre Tesisleri Detay 5', 5
FROM projects p WHERE p.slug = 'yavuz-gida-findik-entegre-tesisleri';
INSERT INTO project_media (project_id, kind, file_path, caption, sort_order)
SELECT p.id, 'render', 'uploads/projects/gallery_yavuz-gida-findik-entegre-tesisleri_5.webp', 'Yavuz Gıda Fındık Entegre Tesisleri Detay 6', 6
FROM projects p WHERE p.slug = 'yavuz-gida-findik-entegre-tesisleri';

DROP TEMPORARY TABLE IF EXISTS tmp_seed_project_slugs;