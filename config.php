<?php
// ==========================================================
// BUYAPI - Yapılandırma. Sunucunuza göre düzenleyin.
// ==========================================================
const DB_HOST = 'localhost';
const DB_NAME = 'buyapi';
const DB_USER = 'root';
const DB_PASS = '';

// Ön yüzün (statik HTML sitesi) bulunduğu dizinin web adresi, sonunda / olmadan.
// Görsel yolları (images/banner-1.png gibi) bu adrese göre tamamlanır.
const SITE_URL = 'http://localhost:8000';

// Yüklenen dosyaların fiziksel klasörü ve web yolu
define('UPLOAD_DIR', __DIR__ . '/uploads');
const UPLOAD_URL_PATH = 'uploads'; // API'den giden yol: <API_PUBLIC_URL>/uploads/dosya.jpg

// API'nin dışarıdan görülen kök adresi (uploads için), sonunda / olmadan.
// Örn: https://admin.buyapi.com.tr  veya  http://localhost:8000
const API_PUBLIC_URL = 'http://localhost:8000';

// Ön yüzün API'yi çağırabilmesi için izin verilen kaynaklar (CORS). '*' geliştirme içindir.
const CORS_ALLOW_ORIGIN = '*';

const MAX_UPLOAD_MB = 64;
const DEBUG = true;

date_default_timezone_set('Europe/Istanbul');
