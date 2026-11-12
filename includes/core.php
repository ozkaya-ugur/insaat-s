<?php
require_once __DIR__ . '/../config.php';

if (DEBUG) { ini_set('display_errors', 1); error_reporting(E_ALL); }
else { ini_set('display_errors', 0); }

// ---------- Güvenlik başlıkları ----------
if (!headers_sent()) {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: frame-ancestors 'self'; object-src 'none'; base-uri 'self'");
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// mbstring eklentisi kapalı olan sunucular için UTF-8 uyumlu yedek fonksiyonlar
if (!function_exists('mb_substr')) {
    function _u8_chars(string $s): array {
        $r = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);
        return $r === false ? str_split($s) : $r;
    }
    function mb_strlen($s, $enc = null): int { return count(_u8_chars((string)$s)); }
    function mb_substr($s, $start, $length = null, $enc = null): string {
        return implode('', array_slice(_u8_chars((string)$s), (int)$start, $length === null ? null : (int)$length));
    }
}
if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth($s, $start, $width, $trim = '', $enc = null): string {
        $s = mb_substr((string)$s, (int)$start);
        if (mb_strlen($s) <= $width) return $s;
        return mb_substr($s, 0, max(0, $width - mb_strlen($trim))) . $trim;
    }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($s, $enc = null): string {
        return strtolower(strtr((string)$s, ['Ç'=>'ç','Ğ'=>'ğ','İ'=>'i','I'=>'ı','Ö'=>'ö','Ş'=>'ş','Ü'=>'ü']));
    }
}

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
    // Yetki ve aktiflik her istekte veritabanından tazelenir; böylece verilen/alınan
    // yetkiler ve pasife alınan hesap anında etkili olur.
    try {
        $row = q('SELECT id,username,full_name,role,permissions,is_active,must_change_password FROM admins WHERE id=?', [(int)$u['id']])->fetch();
        if (!$row || (int)$row['is_active'] !== 1) {
            $_SESSION = [];
            session_destroy();
            header('Location: /admin/login.php?pasif=1');
            exit;
        }
        $_SESSION['admin'] = [
            'id' => (int)$row['id'], 'username' => $row['username'], 'name' => $row['full_name'],
            'role' => $row['role'] ?: 'staff', 'permissions' => $row['permissions'],
            'must_change_password' => (int)$row['must_change_password'],
        ];
        return $_SESSION['admin'];
    } catch (Throwable $ex) {
        return $u; // tablo/kolon henüz yoksa eski davranış korunur
    }
}

// ---------- Personel yetkileri ----------
// Sayfa => bölümler. Sahip (owner) rolü bu ağacın tamamına otomatik sahiptir.
function admin_perm_tree(): array {
    return [
        'index'         => ['label' => 'Genel Bakış', 'icon' => 'fa-gauge-high', 'sections' => ['view' => 'Görüntüle']],
        'projects'      => ['label' => 'Projeler', 'icon' => 'fa-building', 'sections' => ['view' => 'Listele', 'create' => 'Yeni proje ekle', 'edit' => 'Düzenle', 'delete' => 'Sil']],
        'urban_renewal' => ['label' => 'Kentsel Dönüşüm', 'icon' => 'fa-recycle', 'sections' => ['view' => 'Görüntüle', 'edit' => 'Düzenle']],
        'about'         => ['label' => 'Hakkımızda / Kurumsal', 'icon' => 'fa-landmark', 'sections' => ['view' => 'Görüntüle', 'edit' => 'Düzenle']],
        'pages'         => ['label' => 'Sayfalar', 'icon' => 'fa-file-lines', 'sections' => ['view' => 'Listele', 'create' => 'Yeni sayfa', 'edit' => 'Düzenle', 'delete' => 'Sil']],
        'sliders'       => ['label' => 'Ana Sayfa Slider', 'icon' => 'fa-images', 'sections' => ['view' => 'Görüntüle', 'edit' => 'Düzenle']],
        'messages'      => ['label' => 'Mesajlar', 'icon' => 'fa-envelope', 'sections' => ['view' => 'Görüntüle', 'process' => 'Atama & sonuç işle', 'delete' => 'Sil', 'export' => 'CSV indir']],
        'languages'     => ['label' => 'Diller', 'icon' => 'fa-globe', 'sections' => ['view' => 'Görüntüle', 'edit' => 'Düzenle']],
        'translations'  => ['label' => 'Çeviriler', 'icon' => 'fa-language', 'sections' => ['view' => 'Görüntüle', 'edit' => 'Düzenle']],
        'seo'           => ['label' => 'SEO', 'icon' => 'fa-magnifying-glass-chart', 'sections' => ['view' => 'Görüntüle', 'edit' => 'Düzenle']],
        'settings'      => ['label' => 'Site Ayarları', 'icon' => 'fa-gear', 'sections' => ['view' => 'Görüntüle', 'general' => 'Site & iletişim bilgileri', 'mail' => 'E-posta testi']],
        'staff'         => ['label' => 'Personel & Yetkiler', 'icon' => 'fa-users-gear', 'sections' => ['view' => 'Listele', 'create' => 'Personel ekle', 'edit' => 'Düzenle / şifre belirle', 'delete' => 'Sil', 'permissions' => 'Yetki ver']],
        'account'       => ['label' => 'Hesabım', 'icon' => 'fa-user-shield', 'sections' => ['view' => 'Görüntüle', 'password' => 'Kendi şifresini değiştir']],
    ];
}

function admin_is_owner(?array $u = null): bool {
    $u = $u ?? admin_user();
    return $u !== null && ($u['role'] ?? '') === 'owner';
}

function admin_perms(?array $u = null): array {
    $u = $u ?? admin_user();
    if (!$u) return [];
    $raw = $u['permissions'] ?? '';
    if (is_array($raw)) return $raw;
    if (!is_string($raw) || $raw === '') return [];
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

function admin_can(string $page, string $section = 'view', ?array $u = null): bool {
    $u = $u ?? admin_user();
    if (!$u) return false;
    if (($u['role'] ?? '') === 'owner') return true;
    // Her personel kendi hesap sayfasını ve şifresini yönetebilir.
    if ($page === 'account' && in_array($section, ['view', 'password'], true)) return true;
    $p = admin_perms($u);
    if (!isset($p[$page]) || !is_array($p[$page])) return false;
    return in_array($section, $p[$page], true);
}

function admin_can_any(string $page, ?array $u = null): bool {
    $u = $u ?? admin_user();
    if (!$u) return false;
    if (($u['role'] ?? '') === 'owner') return true;
    if ($page === 'account') return true;
    $p = admin_perms($u);
    return !empty($p[$page]) && is_array($p[$page]);
}

// Kullanıcının yetkili olduğu ilk sayfa (giriş sonrası yönlendirme için)
function admin_home_url(?array $u = null): string {
    $u = $u ?? admin_user();
    foreach (admin_perm_tree() as $page => $meta) {
        if (admin_can_any($page, $u)) return '/admin/' . $page . '.php';
    }
    return '/admin/account.php';
}

function admin_deny(): void {
    http_response_code(403);
    ?><!DOCTYPE html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow"><title>Yetki yok | BUYAPI Panel</title>
    <link rel="stylesheet" href="/admin/assets/admin.css"></head><body>
    <div class="login-wrap"><div class="login-box" style="text-align:center">
      <h1 style="font-size:22px">Yetkiniz yok</h1>
      <p class="sub" style="margin:10px 0 18px">Bu sayfaya veya bu işleme erişim yetkiniz bulunmuyor. Yetki için yöneticinize başvurun.</p>
      <a class="btn" href="<?= e(admin_home_url()) ?>" style="justify-content:center;width:100%">Panele dön</a>
    </div></div></body></html><?php
    exit;
}

// Salt görüntüleme modu: sayfa açılır, form alanları kilitlenir.
function admin_readonly(?bool $set = null): bool {
    static $ro = false;
    if ($set !== null) $ro = $set;
    return $ro;
}

function require_perm(string $page, string $section = 'view'): array {
    $u = require_login();
    if (!admin_can($page, $section, $u)) admin_deny();
    return $u;
}

function flash(?string $msg = null, string $type = 'ok') {
    start_session();
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}

function redirect(string $url): void { header('Location: ' . $url); exit; }


// ---------- E-posta gönderimi (SMTP) ----------
// Sunucuda sendmail_path=/bin/true olduğu için PHP mail() sessizce hiçbir şey yapmıyor.
// Bu yüzden mesajlar doğrudan SMTP ile teslim edilir: önce yerel aktarıcı (127.0.0.1:25),
// reddedilirse alıcı alan adının MX sunucusu denenir.
function smtp_read($fp): string {
    $out = '';
    while (($line = fgets($fp, 2048)) !== false) {
        $out .= $line;
        if (strlen($line) < 4 || $line[3] === ' ') break;
    }
    return $out;
}

function smtp_cmd($fp, string $cmd): string {
    fwrite($fp, $cmd . "\r\n");
    return smtp_read($fp);
}

function smtp_code(string $resp): string { return substr(trim($resp), 0, 3); }

function smtp_deliver(string $host, int $port, string $from, string $to, string $data, int $timeout = 12): array {
    $errno = 0; $errstr = '';
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$fp) return ['ok' => false, 'code' => 'conn', 'response' => "Bağlantı kurulamadı ($host:$port) $errstr"];
    stream_set_timeout($fp, $timeout);
    $banner = smtp_read($fp);
    if (smtp_code($banner) !== '220') { fclose($fp); return ['ok' => false, 'code' => smtp_code($banner), 'response' => trim($banner)]; }
    $ehlo = smtp_cmd($fp, 'EHLO ' . (parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost'));
    if (smtp_code($ehlo) !== '250') { $ehlo = smtp_cmd($fp, 'HELO ' . (parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost')); }
    $r = smtp_cmd($fp, 'MAIL FROM:<' . $from . '>');
    if (smtp_code($r) !== '250') { smtp_cmd($fp, 'QUIT'); fclose($fp); return ['ok' => false, 'code' => smtp_code($r), 'response' => trim($r)]; }
    $r = smtp_cmd($fp, 'RCPT TO:<' . $to . '>');
    if (!in_array(smtp_code($r), ['250', '251'], true)) { smtp_cmd($fp, 'QUIT'); fclose($fp); return ['ok' => false, 'code' => smtp_code($r), 'response' => trim($r)]; }
    $r = smtp_cmd($fp, 'DATA');
    if (smtp_code($r) !== '354') { smtp_cmd($fp, 'QUIT'); fclose($fp); return ['ok' => false, 'code' => smtp_code($r), 'response' => trim($r)]; }
    $r = smtp_cmd($fp, $data . "\r\n.");
    smtp_cmd($fp, 'QUIT');
    fclose($fp);
    return ['ok' => smtp_code($r) === '250', 'code' => smtp_code($r), 'response' => trim($r)];
}

function mx_hosts(string $domain): array {
    $hosts = [];
    if (function_exists('dns_get_record')) {
        foreach ((array)@dns_get_record($domain, DNS_MX) as $mx) {
            if (!empty($mx['target'])) $hosts[(int)($mx['pri'] ?? 0)] = rtrim($mx['target'], '.');
        }
    }
    if (!$hosts && function_exists('getmxrr')) {
        $list = []; $weights = [];
        if (@getmxrr($domain, $list, $weights)) foreach ($list as $i => $h) $hosts[(int)($weights[$i] ?? 0)] = rtrim($h, '.');
    }
    ksort($hosts);
    return array_values($hosts);
}

function mail_log_write(string $to, string $subject, array $res, string $transport): void {
    try {
        q('INSERT INTO mail_log (to_email,subject,status,transport,response,ip) VALUES (?,?,?,?,?,?)', [
            mb_substr($to, 0, 190), mb_substr($subject, 0, 190),
            $res['ok'] ? 'sent' : 'failed', mb_substr($transport, 0, 120),
            mb_substr((string)($res['response'] ?? ''), 0, 500), client_ip(),
        ]);
    } catch (Throwable $ex) { /* tablo yoksa e-posta akışını engelleme */ }
}

function send_mail(string $to, string $subject, string $body, string $replyTo = '', string $fromName = 'BUYAPI'): array {
    $to = trim($to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return ['ok' => false, 'response' => 'Geçersiz alıcı adresi'];

    $from = setting('email', 'info@buyapi.com.tr');
    if (!filter_var($from, FILTER_VALIDATE_EMAIL)) $from = 'info@buyapi.com.tr';
    $host = parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost';
    $domain = preg_replace('/^www\./', '', $host);

    $headers = [
        'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $from . '>',
        'To: <' . $to . '>',
        'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
        'Date: ' . date('r'),
        'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $domain . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: <' . str_replace(["\r", "\n"], '', $replyTo) . '>';
    }
    $data = implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\n", "\r\n", $body);

    $res = smtp_deliver('127.0.0.1', 25, $from, $to, $data);
    $transport = 'relay:127.0.0.1';
    if (!$res['ok']) {
        $mxDomain = substr(strrchr($to, '@') ?: '', 1);
        foreach (mx_hosts($mxDomain) as $mx) {
            $res = smtp_deliver($mx, 25, $from, $to, $data);
            $transport = 'mx:' . $mx;
            if ($res['ok']) break;
        }
    }
    if (!$res['ok'] && function_exists('mail')) {
        $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers[0] . "\r\nContent-Type: text/plain; charset=UTF-8");
        $res = ['ok' => (bool)$ok, 'response' => 'mail() fallback'];
        $transport = 'mail()';
    }
    mail_log_write($to, $subject, $res, $transport);
    return $res + ['transport' => $transport];
}

// ---------- Basit istek sınırlama (IP bazlı) ----------
function throttle_count(string $key, int $windowSec): int {
    try {
        $w = max(1, (int)$windowSec);
        return (int)q("SELECT COUNT(*) FROM login_attempts WHERE k=? AND created_at > (NOW() - INTERVAL $w SECOND)", [$key])->fetchColumn();
    } catch (Throwable $ex) { return 0; }
}
function throttle_hit(string $key): void {
    try {
        q('INSERT INTO login_attempts (k,ip) VALUES (?,?)', [$key, client_ip()]);
        if (random_int(1, 20) === 1) q('DELETE FROM login_attempts WHERE created_at < (NOW() - INTERVAL 1 DAY)');
    } catch (Throwable $ex) { }
}
function throttle_clear(string $key): void {
    try { q('DELETE FROM login_attempts WHERE k=?', [$key]); } catch (Throwable $ex) { }
}

// ---------- Favicon ----------
const FAVICON_FILES = ['favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png'];

/** Yüklenen görselden favicon setini üretir; kök dizindeki dosyaları (yedekleyerek) günceller. */
function favicon_generate(string $srcFile, string $ext): array {
    if (!function_exists('imagecreatefromstring')) throw new RuntimeException('Sunucuda GD eklentisi yok, favicon üretilemedi.');
    $dir = UPLOAD_DIR . '/favicon';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) throw new RuntimeException('Favicon klasörü oluşturulamadı.');
    $ver = date('YmdHis');
    $out = [];

    if ($ext === 'ico') {
        if (!copy($srcFile, "$dir/favicon-$ver.ico")) throw new RuntimeException('ICO dosyası kaydedilemedi.');
        $out['favicon.ico'] = "$dir/favicon-$ver.ico";
    } else {
        $img = @imagecreatefromstring((string)file_get_contents($srcFile));
        if (!$img) throw new RuntimeException('Görsel okunamadı (PNG, JPG veya WEBP yükleyin).');
        $w = imagesx($img); $h = imagesy($img); $side = min($w, $h);
        $sx = (int)(($w - $side) / 2); $sy = (int)(($h - $side) / 2);
        $pngs = [];
        foreach (['favicon-16x16.png' => 16, 'favicon-32x32.png' => 32, 'apple-touch-icon.png' => 180, 'favicon-48.png' => 48] as $name => $size) {
            $c = imagecreatetruecolor($size, $size);
            imagealphablending($c, false); imagesavealpha($c, true);
            imagefill($c, 0, 0, imagecolorallocatealpha($c, 0, 0, 0, 127));
            imagecopyresampled($c, $img, 0, 0, $sx, $sy, $size, $size, $side, $side);
            ob_start(); imagepng($c, null, 9); $pngs[$size] = ob_get_clean();
            imagedestroy($c);
            if ($size !== 48) { $p = "$dir/" . pathinfo($name, PATHINFO_FILENAME) . "-$ver.png"; file_put_contents($p, $pngs[$size]); $out[$name] = $p; }
        }
        imagedestroy($img);
        // PNG gömülü ICO (16, 32, 48)
        $sizes = [16, 32, 48];
        $ico = pack('vvv', 0, 1, count($sizes));
        $offset = 6 + 16 * count($sizes);
        $data = '';
        foreach ($sizes as $s) {
            $ico .= pack('CCCCvvVV', $s, $s, 0, 0, 1, 32, strlen($pngs[$s]), $offset);
            $offset += strlen($pngs[$s]); $data .= $pngs[$s];
        }
        file_put_contents("$dir/favicon-$ver.ico", $ico . $data);
        $out['favicon.ico'] = "$dir/favicon-$ver.ico";
    }

    // Kök dizindeki standart dosyaları güncelle (ilk seferde orijinaller yedeklenir)
    $root = realpath(__DIR__ . '/..');
    $rootUpdated = true;
    foreach (FAVICON_FILES as $name) {
        $target = "$root/$name";
        if (!isset($out[$name])) continue;
        $bak = "$dir/default-$name";
        if (is_file($target) && !is_file($bak)) @copy($target, $bak);
        if (!@copy($out[$name], $target)) $rootUpdated = false;
    }

    $rel = fn($p) => $p ? UPLOAD_URL_PATH . '/favicon/' . basename($p) : '';
    $vals = [
        'favicon_ico'   => $rel($out['favicon.ico'] ?? ''),
        'favicon_32'    => $rel($out['favicon-32x32.png'] ?? ''),
        'favicon_16'    => $rel($out['favicon-16x16.png'] ?? ''),
        'favicon_apple' => $rel($out['apple-touch-icon.png'] ?? ''),
        'favicon_version' => $ver,
    ];
    foreach ($vals as $k => $v) q('INSERT INTO site_settings (s_key,s_value) VALUES (?,?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)', [$k, $v]);
    // Eski üretilmiş dosyaları temizle
    foreach (glob("$dir/*-*.{png,ico}", GLOB_BRACE) ?: [] as $f) {
        $b = basename($f);
        if (strpos($b, 'default-') === 0 || strpos($b, $ver) !== false) continue;
        @unlink($f);
    }
    return ['root_updated' => $rootUpdated, 'version' => $ver] + $vals;
}

/** Varsayılan favicon setine döner. */
function favicon_reset(): void {
    $dir = UPLOAD_DIR . '/favicon';
    $root = realpath(__DIR__ . '/..');
    foreach (FAVICON_FILES as $name) {
        if (is_file("$dir/default-$name")) @copy("$dir/default-$name", "$root/$name");
    }
    foreach (glob("$dir/*-*.{png,ico}", GLOB_BRACE) ?: [] as $f) {
        if (strpos(basename($f), 'default-') !== 0) @unlink($f);
    }
    q("DELETE FROM site_settings WHERE s_key IN ('favicon_ico','favicon_32','favicon_16','favicon_apple')");
    q("INSERT INTO site_settings (s_key,s_value) VALUES ('favicon_version',?) ON DUPLICATE KEY UPDATE s_value=VALUES(s_value)", [date('YmdHis')]);
}

/** <head> için favicon link etiketleri (sunucu tarafı render eden sayfalar). */
function favicon_links_html(string $prefix = '/'): string {
    $v = setting('favicon_version', '');
    $q = $v !== '' ? '?v=' . rawurlencode($v) : '';
    $pick = fn($key, $def) => ($p = setting($key, '')) !== '' ? $prefix . ltrim($p, '/') : $prefix . $def;
    return '<link rel="icon" type="image/x-icon" href="' . e($pick('favicon_ico', 'favicon.ico') . $q) . '">' . "\n"
        . '    <link rel="icon" type="image/png" sizes="32x32" href="' . e($pick('favicon_32', 'favicon-32x32.png') . $q) . '">' . "\n"
        . '    <link rel="icon" type="image/png" sizes="16x16" href="' . e($pick('favicon_16', 'favicon-16x16.png') . $q) . '">' . "\n"
        . '    <link rel="apple-touch-icon" sizes="180x180" href="' . e($pick('favicon_apple', 'apple-touch-icon.png') . $q) . '">';
}

// ---------- Medya kütüphanesi ----------
/** uploads/ ve images/ altındaki görsel/video dosyalarını listeler. */
function media_library(string $search = '', string $type = 'all'): array {
    $root = realpath(__DIR__ . '/..');
    $imgExt = ['jpg','jpeg','png','webp','gif'];
    $vidExt = ['mp4','webm'];
    $allowed = $type === 'image' ? $imgExt : ($type === 'video' ? $vidExt : array_merge($imgExt, $vidExt));
    $items = [];
    foreach (['uploads', 'images'] as $base) {
        $dir = "$root/$base";
        if (!is_dir($dir)) continue;
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            $ext = strtolower($f->getExtension());
            if (!in_array($ext, $allowed, true)) continue;
            $rel = $base . '/' . str_replace('\\', '/', substr($f->getPathname(), strlen($dir) + 1));
            if (strpos($rel, 'uploads/favicon/') === 0) continue;
            if ($search !== '' && stripos($rel, $search) === false) continue;
            $items[] = ['path' => $rel, 'type' => in_array($ext, $vidExt, true) ? 'video' : 'image', 'size' => $f->getSize(), 'mtime' => $f->getMTime()];
        }
    }
    usort($items, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
    return $items;
}

/** Göreli bir medya yolunun güvenli ve mevcut olduğunu doğrular. */
function media_path_valid(string $path, array $allowedExt): bool {
    $path = ltrim(trim($path), '/');
    if ($path === '' || strpos($path, '..') !== false || !preg_match('#^(uploads|images)/[A-Za-z0-9_\-./ ]+$#', $path)) return false;
    if (!in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $allowedExt, true)) return false;
    $root = realpath(__DIR__ . '/..');
    $full = realpath("$root/$path");
    return $full && strpos($full, $root . DIRECTORY_SEPARATOR) === 0 && is_file($full);
}

/** Bir dosya başka bir kayıtta kullanılıyor mu (silmeden önce kontrol). */
function upload_in_use(string $path, int $exceptSliderId = 0): bool {
    if ($path === '') return false;
    if ((int)q('SELECT COUNT(*) FROM sliders WHERE image_url=? AND id<>?', [$path, $exceptSliderId])->fetchColumn()) return true;
    if ((int)q('SELECT COUNT(*) FROM projects WHERE cover_image=?', [$path])->fetchColumn()) return true;
    if ((int)q('SELECT COUNT(*) FROM project_media WHERE file_path=?', [$path])->fetchColumn()) return true;
    if ((int)q('SELECT COUNT(*) FROM site_settings WHERE s_value=?', [$path])->fetchColumn()) return true;
    return false;
}

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

require_once __DIR__ . '/il-ilce.php';

/**
 * Bir ilin ilçe listesi.
 */
function districts_of(string $cityKey): array
{
    return TR_DISTRICTS[$cityKey] ?? [];
}

const PROJECT_FEATURE_ICONS = [
    'fa-house-signal'      => 'Akıllı ev',
    'fa-shield-halved'     => 'Güvenlik',
    'fa-square-parking'    => 'Otopark',
    'fa-leaf'              => 'Yeşil bina',
    'fa-solar-panel'       => 'Güneş enerjisi',
    'fa-bolt'              => 'Elektrik / enerji',
    'fa-fire-flame-simple' => 'Isıtma',
    'fa-snowflake'         => 'Soğutma / iklimlendirme',
    'fa-water'             => 'Su / havuz',
    'fa-swimming-pool'     => 'Yüzme havuzu',
    'fa-dumbbell'          => 'Spor salonu',
    'fa-spa'               => 'Spa / sağlıklı yaşam',
    'fa-tree'              => 'Peyzaj',
    'fa-seedling'          => 'Sürdürülebilirlik',
    'fa-elevator'          => 'Asansör',
    'fa-stairs'            => 'Merdiven',
    'fa-building'          => 'Bina',
    'fa-city'              => 'Şehir / karma kullanım',
    'fa-warehouse'         => 'Depo / lojistik',
    'fa-industry'          => 'Sanayi / üretim',
    'fa-truck-fast'        => 'Sevkiyat',
    'fa-boxes-stacked'     => 'Stok alanı',
    'fa-gears'             => 'Teknik donanım',
    'fa-wifi'              => 'İnternet altyapısı',
    'fa-video'             => 'Kamera sistemi',
    'fa-bell-concierge'    => 'Resepsiyon',
    'fa-concierge-bell'    => 'Danışma',
    'fa-car'               => 'Araç',
    'fa-charging-station'  => 'Şarj istasyonu',
    'fa-road'              => 'Yol / ulaşım',
    'fa-location-dot'      => 'Konum',
    'fa-map'               => 'Harita',
    'fa-ruler-combined'    => 'Metraj',
    'fa-compass-drafting'  => 'Mimari tasarım',
    'fa-helmet-safety'     => 'Şantiye güvenliği',
    'fa-hard-hat'          => 'İş güvenliği',
    'fa-fire-extinguisher' => 'Yangın güvenliği',
    'fa-door-open'         => 'Giriş',
    'fa-key'               => 'Anahtar / kilit',
    'fa-lock'              => 'Kilit',
    'fa-child-reaching'    => 'Çocuk alanı',
    'fa-futbol'            => 'Spor sahası',
    'fa-store'             => 'Ticari alan',
    'fa-utensils'          => 'Kafe / restoran',
    'fa-bed'               => 'Konfor',
    'fa-sun'               => 'Gün ışığı',
    'fa-wind'              => 'Havalandırma',
    'fa-recycle'           => 'Geri dönüşüm',
    'fa-chart-line'        => 'Yatırım değeri',
    'fa-star'              => 'Prestij',
    'fa-circle-check'      => 'Genel',
];

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
