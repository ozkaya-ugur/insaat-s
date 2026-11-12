# Favicon Üretim Talimatı (Üretim Ortamı)

**Dayanak:** `buyapi_kod_sartnamesi.md` Bölüm 3/F (`/favicon.ico` 404)  
**Amaç:** Tüm sayfalarda çalışan favicon dosyalarını üretmek ve `<head>` içine bağlamak.

## 1) Kaynak dosya
- Tercih edilen kaynak: şirket logosunun kare versiyonu (`1024x1024`, şeffaf arka plan).
- Önerilen dosya adı: `logo-square.png`.

## 2) Üretilecek dosyalar (`public/` altında)
- `favicon.ico` (16x16, 32x32, 48x48 katmanlı)
- `favicon-16x16.png`
- `favicon-32x32.png`
- `apple-touch-icon.png` (180x180)
- (İsteğe bağlı) `site.webmanifest`

## 3) HTML `<head>` ekleri (tüm sayfalara)
```html
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
```

## 4) Doğrulama
1. `https://<alan-adi>/favicon.ico` HTTP 200 dönmeli.
2. Tarayıcı sekmesinde ikon görünmeli.
3. Hard refresh sonrası da (Ctrl+F5) ikon güncel kalmalı.

## 5) Not
Bu paket kaynak logo üretmez; mevcut kurumsal logo dosyanızdan favicon türetilmelidir.
