# BUYAPI – PHP + MySQL Yönetim Paneli

## Kurulum
1. Bu klasörü PHP 8.1+ çalışan sunucuya yükleyin (Apache, `mod_rewrite` açık). Panel `/admin/`, API `/api/` altında çalışır.
2. `database.sql` dosyasını phpMyAdmin'den içe aktarın (veritabanı: `buyapi`).
3. `config.php` içinde şunları düzenleyin: `DB_*`, `SITE_URL` (ön yüz adresi), `API_PUBLIC_URL` (bu PHP uygulamasının adresi), `CORS_ALLOW_ORIGIN` (canlıda ön yüz alan adınız).
4. Tarayıcıdan `/admin/setup.php` açıp ilk yöneticiyi kendi şifrenizle oluşturun (yönetici varsa bu sayfa kendini kilitler; sonra silebilirsiniz).
5. `uploads/` klasörüne web sunucusu yazabilmeli (`chmod 755` veya sahibi www-data).

## Ön yüzü API'ye bağlama
Mevcut dosyalarda API adresi iki farklı yerde ve iki farklı portla geçiyor (`localhost:5000` ve `localhost:8080`). Hepsini tek adrese çevirin:

- `script.js` satır 5: `const API_URL = "https://SUNUCUNUZ/api";`
- `iletisim.html`: `const API_BASE = "https://SUNUCUNUZ/api";`
- `index.html`, `projeler.html`, `proje-detay.html`: `http://localhost:8080/api/...` → `https://SUNUCUNUZ/api/...`
  (kapak görsellerindeki `http://localhost:8080/${proj.coverImage}` ön ekini de kaldırın; API zaten tam adres döner.)

## Ön yüzde yapılması önerilen 3 küçük düzeltme
1. `projeler.html` konum filtresi seçenekleri: `value="tr-54"` yerine panelin kullandığı harita kodları (`tr-sk`, `tr-ib`, `tr-an`, `tr-kc`) olmalı, yoksa filtre eşleşmez.
2. Durum rozetinde `proj.status` ham haliyle (`devam-eden`) yazılıyor; `proj.statusLabel` kullanın (API ikisini de döndürür).
3. `proje-detay.html` içindeki galeri/plan/şantiye/video bölümleri hâlâ sabit. API her projede `gallery: {render, plan, site, video}` döndürür; isterseniz bu bölümleri de dinamik yaparım. Katalog PDF bağlantısı `proj.catalog`.

## API uç noktaları
`GET /api/projects?lang=tr|en|ar` · `GET /api/projects/featured` · `GET /api/sliders` · `GET /api/translations` · `GET /api/seo` · `GET /api/settings` · `POST /api/messages` (eski `POST /api/admin/settings/messages` de çalışır)

## Güvenlik notları
PDO hazır sorgular, CSRF jetonu, `password_hash`, oturum yenileme, giriş denemesi sınırı, yükleme uzantı/görsel doğrulaması, `uploads/` içinde PHP çalıştırma kapalı, mesaj formunda IP başına hız sınırı + honeypot. Canlıda HTTPS kullanın ve `CORS_ALLOW_ORIGIN` değerini kendi alan adınıza sabitleyin.
