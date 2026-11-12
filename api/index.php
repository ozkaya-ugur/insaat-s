<?php
require_once __DIR__ . '/../includes/core.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . CORS_ALLOW_ORIGIN);
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }

function out($data, int $code = 200): never {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function normalizeAssetPath(?string $path): string {
    $path = trim((string)$path);
    if ($path === '') return '';

    // Tam URL geldiyse host'u atıp yalnızca path'i al
    if (preg_match('#^https?://#i', $path)) {
        $parts = parse_url($path);
        $path = $parts['path'] ?? '';
    }

    return ltrim($path, '/');
}

function buildPublicAssetUrl(string $relativePath): string {
    if ($relativePath === '') return '';

    // Ortam değişkeni varsa öncelik ver
    $base = getenv('PUBLIC_ASSET_BASE_URL') ?: '';
    if ($base !== '') {
        return rtrim($base, '/') . '/' . ltrim($relativePath, '/');
    }

    // Aynı origin'de çalışacak güvenli yol
    return '/' . ltrim($relativePath, '/');
}

function asset(?string $p): string {
    $relative = normalizeAssetPath($p);
    if ($relative === '') return '';
    return buildPublicAssetUrl($relative);
}

function pick(array $row, string $base, string $lang): string {
    $v = $row[$base . '_' . $lang] ?? '';
    return $v !== '' ? $v : (string)($row[$base . '_tr'] ?? '');
}

// ---- Yol çözümleme: /api/projects/featured -> ['projects','featured']
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$pos  = strpos($uri, '/api/');
$path = $pos !== false ? substr($uri, $pos + 5) : '';
$path = trim(preg_replace('#index\.php/?#', '', $path), '/');
if ($path === '' && isset($_GET['route'])) $path = trim($_GET['route'], '/');
$seg  = $path === '' ? [] : explode('/', $path);
$method = $_SERVER['REQUEST_METHOD'];

$activeCodes = get_language_codes();
$rawLang = $_GET['lang'] ?? 'tr';
$lang = in_array($rawLang, $activeCodes, true) ? $rawLang : 'tr';

try {
    // ---------- AKTİF DİLLER ----------
    if (($seg[0] ?? '') === 'languages' && $method === 'GET') {
        out(get_languages());
    }

    // ---------- PROJELER ----------
    if (($seg[0] ?? '') === 'projects' && $method === 'GET') {
        $featured = ($seg[1] ?? '') === 'featured';
        $sql = 'SELECT * FROM projects WHERE is_active=1' . ($featured ? ' AND is_featured=1' : '') . ' ORDER BY sort_order ASC, id DESC';
        $rows = q($sql)->fetchAll();

        $media = [];
        if ($rows) {
            $ids = implode(',', array_map('intval', array_column($rows, 'id')));
            foreach (q("SELECT * FROM project_media WHERE project_id IN ($ids) ORDER BY sort_order ASC, id ASC")->fetchAll() as $m) {
                $media[$m['project_id']][$m['kind']][] = ['src' => asset($m['file_path']), 'caption' => $m['caption']];
            }
        }

        $list = [];
        foreach ($rows as $r) {
            $g = $media[$r['id']] ?? [];
            $list[] = [
                'id'             => (int)$r['id'],
                'slug'           => $r['slug'],
                'title'          => pick($r, 'title', $lang),
                'subtitle'       => pick($r, 'subtitle', $lang),
                'description'    => pick($r, 'description', $lang),
                'cat'            => $r['cat'],
                'loc'            => $r['loc'],
                'cityKey'        => $r['city_key'],
                'type'           => $r['type'],
                'status'         => $r['status'],
                'isNew'          => (int)($r['is_new'] ?? (($r['status'] ?? '') === 'yeni-proje' ? 1 : 0)),
                'isUrbanRenewal' => (int)($r['is_urban_renewal'] ?? 0),
                'statusLabel'    => PROJECT_STATUS[$r['status']] ?? $r['status'],
                'coverImage'     => asset($r['cover_image']),
                'area'           => $r['area'],
                'concept'        => $r['concept'],
                'delivery'       => $r['delivery'],
                'catalog'        => asset($r['catalog_file']),
                'googleMapCode'  => $r['google_map_code'] ?? '',
                'streetViewCode' => $r['street_view_code'] ?? '',
                'seoTitle'       => $r['seo_title'],
                'seoDesc'        => $r['seo_desc'],
                'seoKeywords'    => $r['seo_keywords'],
                'gallery'        => [
                    'hero'   => $g['hero'] ?? [],
                    'render' => $g['render'] ?? [],
                    'plan'   => $g['plan'] ?? [],
                    'site'   => $g['site'] ?? [],
                    'video'  => $g['video'] ?? [],
                ],
            ];
        }
        out($list);
    }

    // ---------- SLIDER ----------
    if (($seg[0] ?? '') === 'sliders' && $method === 'GET') {
        $res = [];
        foreach (q('SELECT * FROM sliders WHERE is_active=1 ORDER BY sort_order ASC, id ASC')->fetchAll() as $s) {
            $res[] = [
                'id' => (int)$s['id'],
                'title' => json_encode(['tr' => $s['title_tr'], 'en' => pick($s,'title','en'), 'ar' => pick($s,'title','ar')], JSON_UNESCAPED_UNICODE),
                'subtitle' => json_encode(['tr' => $s['subtitle_tr'], 'en' => pick($s,'subtitle','en'), 'ar' => pick($s,'subtitle','ar')], JSON_UNESCAPED_UNICODE),
                'image_url' => asset($s['image_url']),
                'link_url' => $s['link_url'],
            ];
        }
        out($res);
    }

    // ---------- ÇEVİRİLER: {tr:{key:val}, en:{...}, de:{...}} (TÜRKÇE FALLBACK İLE) ----------
    if (($seg[0] ?? '') === 'translations' && $method === 'GET') {
        $raw = [];
        foreach (q('SELECT lang,t_key,t_value FROM translations')->fetchAll() as $t) {
            $raw[$t['lang']][$t['t_key']] = $t['t_value'];
        }
        $trDict = $raw['tr'] ?? [];
        $res = [];
        foreach ($activeCodes as $c) {
            $res[$c] = [];
            foreach ($trDict as $k => $trVal) {
                $v = trim((string)($raw[$c][$k] ?? ''));
                $res[$c][$k] = $v !== '' ? $v : $trVal;
            }
            if (isset($raw[$c])) {
                foreach ($raw[$c] as $k => $v) {
                    if (!isset($res[$c][$k]) && trim((string)$v) !== '') {
                        $res[$c][$k] = $v;
                    }
                }
            }
        }
        out($res);
    }

    // ---------- SEO: TÜRKÇE GERİ DÜŞMELİ (FALLBACK TO TR) ----------
    if (($seg[0] ?? '') === 'seo' && $method === 'GET') {
        $res = [];
        $trSeo = q("SELECT * FROM seo_settings WHERE lang='tr'")->fetch() ?: ['title'=>'','description'=>'','keywords'=>''];
        $allSeo = [];
        foreach (q('SELECT * FROM seo_settings')->fetchAll() as $s) {
            $allSeo[$s['lang']] = $s;
        }

        foreach (get_languages() as $l) {
            $code = $l['code'];
            $s = $allSeo[$code] ?? [];
            $res[$code] = [
                'title'    => !empty($s['title']) ? $s['title'] : ($trSeo['title'] ?? ''),
                'desc'     => !empty($s['description']) ? $s['description'] : ($trSeo['description'] ?? ''),
                'keywords' => !empty($s['keywords']) ? $s['keywords'] : ($trSeo['keywords'] ?? ''),
            ];
        }
        out($res);
    }

    // ---------- SAYFALAR (PAGES) ----------
    if (($seg[0] ?? '') === 'pages' && $method === 'GET') {
        $menuOnly = !empty($_GET['menu']);
        $sql = 'SELECT * FROM pages WHERE is_active=1' . ($menuOnly ? ' AND show_in_menu=1' : '') . ' ORDER BY sort_order ASC, id ASC';
        $rows = q($sql)->fetchAll();
        $list = [];
        foreach ($rows as $r) {
            $list[] = [
                'id'          => (int)$r['id'],
                'slug'        => $r['slug'],
                'title'       => pick($r, 'title', $lang),
                'pageType'    => $r['page_type'],
                'bannerImage' => asset($r['banner_image']),
                'content'     => pick($r, 'content', $lang),
                'showInMenu'  => (bool)$r['show_in_menu'],
                'seoTitle'    => $r['seo_title'],
                'seoDesc'     => $r['seo_desc'],
                'seoKeywords' => $r['seo_keywords'],
            ];
        }
        out($list);
    }

    // ---------- KENTSEL DÖNÜŞÜM AYARLARI, SLIDER, FAYDALAR & DİNAMİK SSS ----------
    if (($seg[0] ?? '') === 'settings' && ($seg[1] ?? '') === 'urban-renewal' && $method === 'GET') {
        $slides = [];
        foreach (q('SELECT * FROM urban_renewal_slides WHERE is_active=1 ORDER BY sort_order ASC, id ASC')->fetchAll() as $sl) {
            $slides[] = [
                'id' => (int)$sl['id'],
                'imageUrl' => asset($sl['image_url']),
                'title' => pick($sl, 'title', $lang),
                'subtitle' => pick($sl, 'subtitle', $lang),
            ];
        }

        $keys = [
            'kd_hero_badge', 'kd_hero_title', 'kd_hero_desc',
            'kd_stat_1_num', 'kd_stat_1_lbl', 'kd_stat_2_num', 'kd_stat_2_lbl',
            'kd_stat_3_num', 'kd_stat_3_lbl', 'kd_stat_4_num', 'kd_stat_4_lbl',
            'kd_law_title', 'kd_law_info', 'kd_contact_phone', 'kd_contact_email',
            'kd_benefit_1_title', 'kd_benefit_1_desc',
            'kd_benefit_2_title', 'kd_benefit_2_desc',
            'kd_benefit_3_title', 'kd_benefit_3_desc',
            'kd_benefit_4_title', 'kd_benefit_4_desc'
        ];
        $settings = [];
        foreach ($keys as $k) {
            // Dil bazlı öncelik: kd_hero_title_en -> kd_hero_title_tr -> kd_hero_title
            $val = setting("{$k}_{$lang}");
            if ($val === '') $val = setting("{$k}_tr");
            if ($val === '') $val = setting($k);
            $settings[$k] = $val;
        }

        // Dinamik SSS (FAQ) Verileri
        $faqs = [];
        try {
            $faqRows = q('SELECT * FROM urban_renewal_faqs WHERE is_active=1 ORDER BY sort_order ASC, id ASC')->fetchAll();
            foreach ($faqRows as $f) {
                $faqs[] = [
                    'id'       => (int)$f['id'],
                    'question' => pick($f, 'question', $lang),
                    'answer'   => pick($f, 'answer', $lang),
                ];
            }
        } catch (Throwable $e) {}

        out([
            'slides'   => $slides,
            'settings' => $settings,
            'faqs'     => $faqs,
        ]);
    }

    // ---------- GENEL AYARLAR ----------
    if (($seg[0] ?? '') === 'settings' && $method === 'GET') {
        $res = [];
        $raw = [];
        foreach (q("SELECT s_key,s_value FROM site_settings WHERE s_key <> 'notify_email'")->fetchAll() as $s) {
            $raw[$s['s_key']] = $s['s_value'];
            $res[$s['s_key']] = $s['s_value'];
        }

        // Seçili dile göre lokalize edilmiş anahtarları öncelikli olarak kök ayarlara eşitle (Fallback: TR)
        foreach ($raw as $k => $v) {
            if ($lang !== 'tr' && str_ends_with($k, "_{$lang}")) {
                $baseKey = substr($k, 0, -(strlen($lang) + 1));
                if ($v !== '') {
                    $res[$baseKey] = $v;
                }
            }
        }
        out($res);
    }

    // ---------- MESAJ / KENTSEL DÖNÜŞÜM GÖNDERME ----------
    $isMsg = ($seg[0] ?? '') === 'messages' ||
             ($seg[0] ?? '') === 'contact' ||
             ($seg[0] ?? '') === 'kentsel-donusum' ||
             ($seg === ['admin','settings','messages']);

    if ($isMsg && $method === 'POST') {
        $in = json_decode(file_get_contents('php://input'), true);
        if (!is_array($in)) $in = $_POST;

        if (!empty($in['website'])) out(['ok' => true, 'message' => 'Mesajınız alındı.']); // honeypot

        $name  = trim((string)($in['fullName'] ?? $in['full_name'] ?? $in['name'] ?? ''));
        $phone = trim((string)($in['phone'] ?? ''));
        $email = trim((string)($in['email'] ?? ''));
        $proj  = trim((string)($in['project'] ?? $in['subject'] ?? ''));
        $src   = trim((string)($in['source'] ?? (($seg[0] ?? '') === 'kentsel-donusum' ? 'kentsel_donusum' : 'iletisim')));
        
        // Kentsel dönüşüm başvuru formu ise detayları mesaj alanına derle
        if ($src === 'kentsel_donusum' || !empty($in['buildingAge']) || !empty($in['district'])) {
            $src = 'kentsel_donusum';
            if ($proj === '') $proj = 'Kentsel Dönüşüm Başvurusu';

            $details = [];
            if (!empty($in['city'])) $details[] = "İl: " . trim($in['city']);
            if (!empty($in['district'])) $details[] = "İlçe: " . trim($in['district']);
            if (!empty($in['neighborhood'])) $details[] = "Mahalle: " . trim($in['neighborhood']);
            if (!empty($in['adaParsel'])) $details[] = "Ada / Parsel: " . trim($in['adaParsel']);
            if (!empty($in['buildingAge'])) $details[] = "Bina Yaşı: " . trim($in['buildingAge']);
            if (!empty($in['floors'])) $details[] = "Kat Sayısı: " . trim($in['floors']);
            if (!empty($in['units'])) $details[] = "Daire / Bağımsız Bölüm: " . trim($in['units']);
            if (!empty($in['landArea'])) $details[] = "Arsa / Taban Alanı: " . trim($in['landArea']) . " m²";
            if (!empty($in['role'])) $details[] = "Binadaki Rolü: " . trim($in['role']);
            if (!empty($in['notes'])) $details[] = "\nEk Açıklama:\n" . trim($in['notes']);
            if (!empty($in['message'])) $details[] = "\nMesaj:\n" . trim($in['message']);

            $msg = implode("\n", $details);
        } else {
            $msg = trim((string)($in['message'] ?? $in['note'] ?? ''));
        }

        if ($name === '' || ($phone === '' && $email === '')) {
            out(['ok' => false, 'error' => 'Ad ve telefon/e-posta zorunludur.'], 422);
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            out(['ok' => false, 'error' => 'Geçersiz e-posta adresi.'], 422);
        }

        // Basit hız sınırı: aynı IP'den 10 dakikada en fazla 5 mesaj
        $cnt = (int)q('SELECT COUNT(*) FROM messages WHERE ip=? AND created_at > (NOW() - INTERVAL 10 MINUTE)', [client_ip()])->fetchColumn();
        if ($cnt >= 6) out(['ok' => false, 'error' => 'Çok fazla istek yapıldı. Lütfen biraz sonra tekrar deneyin.'], 429);

        q('INSERT INTO messages (full_name,phone,email,project,message,source,ip) VALUES (?,?,?,?,?,?,?)', [
            mb_substr($name,0,120), mb_substr($phone,0,40), mb_substr($email,0,160),
            mb_substr($proj,0,160), mb_substr($msg,0,5000), $src, client_ip(),
        ]);

        $notify = setting('notify_email');
        if ($notify && filter_var($notify, FILTER_VALIDATE_EMAIL)) {
            $subj = ($src === 'kentsel_donusum') ? 'BUYAPI - Yeni Kentsel Dönüşüm Başvurusu' : 'BUYAPI - Yeni İletişim Mesajı';
            $body = "Ad: $name\nTelefon: $phone\nE-posta: $email\nKonu / Proje: $proj\nKaynak: $src\n\n$msg";
            @mail($notify, '=?UTF-8?B?' . base64_encode($subj) . '?=', $body,
                "Content-Type: text/plain; charset=UTF-8\r\n" . ($email ? 'Reply-To: ' . str_replace(["\r","\n"], '', $email) . "\r\n" : ''));
        }
        out(['ok' => true, 'message' => 'Mesajınız başarıyla iletildi.']);
    }

    out(['error' => 'Bulunamadı'], 404);
} catch (Throwable $ex) {
    out(['error' => DEBUG ? $ex->getMessage() : 'Sunucu hatası'], 500);
}
