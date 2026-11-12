<?php
require_once __DIR__ . '/../config.php';

if (DEBUG) { ini_set('display_errors', 1); error_reporting(E_ALL); }
else { ini_set('display_errors', 0); }

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

function q(string $sql, array $p = []): PDOStatement {
    $st = db()->prepare($sql);
    $st->execute($p);
    return $st;
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function slugify(string $s): string {
    $map = ['ı'=>'i','İ'=>'i','ş'=>'s','Ş'=>'s','ğ'=>'g','Ğ'=>'g','ü'=>'u','Ü'=>'u','ö'=>'o','Ö'=>'o','ç'=>'c','Ç'=>'c'];
    $s = strtr($s, $map);
    $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-'));
    return $s ?: 'proje';
}

function setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (q('SELECT s_key,s_value FROM site_settings')->fetchAll() as $r) $cache[$r['s_key']] = $r['s_value'];
    }
    return $cache[$key] ?? $default;
}

// ---------- Oturum / Kimlik ----------
function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0, 'path' => '/', 'httponly' => true,
            'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']),
        ]);
        session_name('buyapi_admin');
        session_start();
    }
}

function csrf_token(): string {
    start_session();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void {
    start_session();
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
    if (!$ok) { http_response_code(419); exit('Oturum doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.'); }
}

function admin_user(): ?array {
    start_session();
    return $_SESSION['admin'] ?? null;
}
function require_login(): array {
    $u = admin_user();
    if (!$u) { header('Location: /admin/login.php'); exit; }
    return $u;
}

function flash(?string $msg = null, string $type = 'ok') {
    start_session();
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}

function redirect(string $url): void { header('Location: ' . $url); exit; }

// ---------- Dosya yükleme ----------
function upload_file(string $field, array $allowedExt, string $sub = ''): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Yükleme hatası (kod ' . $f['error'] . ').');
    if ($f['size'] > MAX_UPLOAD_MB * 1024 * 1024) throw new RuntimeException('Dosya çok büyük (en fazla ' . MAX_UPLOAD_MB . ' MB).');
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) throw new RuntimeException('İzin verilmeyen dosya türü: .' . $ext);

    $imgExt = ['jpg','jpeg','png','webp','gif'];
    if (in_array($ext, $imgExt, true) && @getimagesize($f['tmp_name']) === false)
        throw new RuntimeException('Geçerli bir görsel değil.');

    $videoExt = ['mp4','webm'];
    if (in_array($ext, $videoExt, true)) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $fi ? finfo_file($fi, $f['tmp_name']) : '';
        if ($fi) finfo_close($fi);
        $okVideo = [
            'mp4' => ['video/mp4', 'application/mp4'],
            'webm' => ['video/webm'],
        ];
        if (!in_array((string)$mime, $okVideo[$ext] ?? [], true)) {
            throw new RuntimeException('Geçersiz video dosyası. Sadece gerçek MP4/WEBM dosyaları yüklenebilir.');
        }
    }

    $dir = UPLOAD_DIR . ($sub ? '/' . $sub : '');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) throw new RuntimeException('Yükleme klasörü oluşturulamadı.');
    $name = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) throw new RuntimeException('Dosya kaydedilemedi.');
    return UPLOAD_URL_PATH . ($sub ? '/' . $sub : '') . '/' . $name;
}

function upload_multi(string $field, array $allowedExt, string $sub, callable $each): int {
    if (empty($_FILES[$field]) || !is_array($_FILES[$field]['name'])) return 0;
    $n = 0;
    foreach ($_FILES[$field]['name'] as $i => $nm) {
        if ($_FILES[$field]['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        $_FILES['__one'] = [
            'name' => $nm, 'type' => $_FILES[$field]['type'][$i], 'tmp_name' => $_FILES[$field]['tmp_name'][$i],
            'error' => $_FILES[$field]['error'][$i], 'size' => $_FILES[$field]['size'][$i],
        ];
        $path = upload_file('__one', $allowedExt, $sub);
        if ($path) { $each($path, $nm); $n++; }
    }
    unset($_FILES['__one']);
    return $n;
}

function delete_upload(?string $path): void {
    if (!$path || strpos($path, UPLOAD_URL_PATH . '/') !== 0) return; // yalnızca uploads/ içindekiler silinir
    $full = realpath(__DIR__ . '/../' . $path);
    if ($full && strpos($full, realpath(UPLOAD_DIR)) === 0 && is_file($full)) @unlink($full);
}

function client_ip(): string { return substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45); }

const CITY_KEYS = [
    'tr-aa' => '01 Adana',
    'tr-ad' => '02 Adıyaman',
    'tr-af' => '03 Afyonkarahisar',
    'tr-ag' => '04 Ağrı',
    'tr-am' => '05 Amasya',
    'tr-an' => '06 Ankara',
    'tr-al' => '07 Antalya',
    'tr-av' => '08 Artvin',
    'tr-ay' => '09 Aydın',
    'tr-bk' => '10 Balıkesir',
    'tr-bc' => '11 Bilecik',
    'tr-bg' => '12 Bingöl',
    'tr-bt' => '13 Bitlis',
    'tr-bl' => '14 Bolu',
    'tr-bd' => '15 Burdur',
    'tr-bu' => '16 Bursa',
    'tr-ck' => '17 Çanakkale',
    'tr-ci' => '18 Çankırı',
    'tr-cm' => '19 Çorum',
    'tr-dn' => '20 Denizli',
    'tr-dy' => '21 Diyarbakır',
    'tr-ed' => '22 Edirne',
    'tr-eg' => '23 Elazığ',
    'tr-en' => '24 Erzincan',
    'tr-em' => '25 Erzurum',
    'tr-es' => '26 Eskişehir',
    'tr-ga' => '27 Gaziantep',
    'tr-gi' => '28 Giresun',
    'tr-gu' => '29 Gümüşhane',
    'tr-hk' => '30 Hakkari',
    'tr-ht' => '31 Hatay',
    'tr-ip' => '32 Isparta',
    'tr-ic' => '33 Mersin',
    'tr-ib' => '34 İstanbul',
    'tr-iz' => '35 İzmir',
    'tr-ka' => '36 Kars',
    'tr-ks' => '37 Kastamonu',
    'tr-ky' => '38 Kayseri',
    'tr-kl' => '39 Kırklareli',
    'tr-kh' => '40 Kırşehir',
    'tr-kc' => '41 Kocaeli',
    'tr-ko' => '42 Konya',
    'tr-ku' => '43 Kütahya',
    'tr-ml' => '44 Malatya',
    'tr-mn' => '45 Manisa',
    'tr-km' => '46 Kahramanmaraş',
    'tr-mr' => '47 Mardin',
    'tr-mg' => '48 Muğla',
    'tr-ms' => '49 Muş',
    'tr-nv' => '50 Nevşehir',
    'tr-ng' => '51 Niğde',
    'tr-or' => '52 Ordu',
    'tr-ri' => '53 Rize',
    'tr-sk' => '54 Sakarya',
    'tr-ss' => '55 Samsun',
    'tr-si' => '56 Siirt',
    'tr-sp' => '57 Sinop',
    'tr-sv' => '58 Sivas',
    'tr-tg' => '59 Tekirdağ',
    'tr-tt' => '60 Tokat',
    'tr-tb' => '61 Trabzon',
    'tr-tc' => '62 Tunceli',
    'tr-su' => '63 Şanlıurfa',
    'tr-us' => '64 Uşak',
    'tr-va' => '65 Van',
    'tr-yz' => '66 Yozgat',
    'tr-zo' => '67 Zonguldak',
    'tr-ak' => '68 Aksaray',
    'tr-bb' => '69 Bayburt',
    'tr-kr' => '70 Karaman',
    'tr-kk' => '71 Kırıkkale',
    'tr-bm' => '72 Batman',
    'tr-sr' => '73 Şırnak',
    'tr-br' => '74 Bartın',
    'tr-ar' => '75 Ardahan',
    'tr-ig' => '76 Iğdır',
    'tr-yl' => '77 Yalova',
    'tr-kb' => '78 Karabük',
    'tr-ki' => '79 Kilis',
    'tr-os' => '80 Osmaniye',
    'tr-du' => '81 Düzce',
];
const PROJECT_TYPES = [
    'konut' => 'Konut & Rezidans', 'ticari' => 'İş Merkezi & Ticari',
    'sanayi' => 'Sanayi & Endüstriyel Tesis', 'kentsel' => 'Kentsel Dönüşüm',
];
const PROJECT_STATUS = ['devam-eden' => 'Devam Ediyor', 'tamamlanan' => 'Tamamlandı', 'yeni-proje' => 'Yeni Proje'];

const PAGE_TYPES = [
    'standart'        => 'Standart Bilgi Sayfası',
    'hakkimizda'      => 'Hakkımızda / Kurumsal',
    'iletisim'        => 'İletişim',
    'projeler'        => 'Projeler Listesi',
    'kentsel_donusum' => 'Kentsel Dönüşüm',
    'ozel'            => 'Özel İçerik / Şablon',
];

// ---------- Dinamik Dil Yardımcıları ----------
function get_languages(bool $all = false): array {
    static $cacheActive = null;
    static $cacheAll = null;
    if ($all && $cacheAll !== null) return $cacheAll;
    if (!$all && $cacheActive !== null) return $cacheActive;

    try {
        $sql = 'SELECT * FROM languages ' . ($all ? '' : 'WHERE is_active = 1 ') . 'ORDER BY sort_order ASC, id ASC';
        $rows = q($sql)->fetchAll();
    } catch (Throwable $e) {
        $rows = [
            ['code' => 'tr', 'name' => 'Türkçe', 'flag' => '🇹🇷', 'direction' => 'ltr', 'is_default' => 1, 'is_active' => 1],
            ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'direction' => 'ltr', 'is_default' => 0, 'is_active' => 1],
            ['code' => 'ar', 'name' => 'العربية', 'flag' => '🇸🇦', 'direction' => 'rtl', 'is_default' => 0, 'is_active' => 1],
        ];
    }
    if ($all) $cacheAll = $rows;
    else $cacheActive = $rows;
    return $rows;
}

function get_language_codes(bool $all = false): array {
    return array_column(get_languages($all), 'code');
}

function clean_embed_src(?string $val): string {
    if (!$val) return '';
    $val = trim($val);
    if (preg_match('#src=["\']([^"\']+)["\']#i', $val, $m)) {
        return $m[1];
    }
    return $val;
}
