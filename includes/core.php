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
    'tr-sk' => 'Sakarya', 'tr-ib' => 'İstanbul', 'tr-an' => 'Ankara', 'tr-kc' => 'Kocaeli',
    'tr-gi' => 'Giresun', 'tr-iz' => 'İzmir', 'tr-bu' => 'Bursa', 'tr-an2' => '—',
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
