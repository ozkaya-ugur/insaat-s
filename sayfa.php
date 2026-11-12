<?php
require_once __DIR__ . '/includes/core.php';

function sanitize_page_html(?string $html): string {
    $html = (string)$html;
    if (trim($html) === '') return '';

    $html = preg_replace('#<\s*(script|style)[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html);

    $allowedTags = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><a><blockquote><img>';
    $html = strip_tags($html, $allowedTags);

    // Tüm event/style attribute'larını sök
    $html = preg_replace('/\s+on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

    // <a> sadece href alır
    $html = preg_replace_callback('/<a\b([^>]*)>/i', function($m) {
        $attrs = $m[1] ?? '';
        $href = '';
        if (preg_match('/href\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $hm)) {
            $href = $hm[2] ?: ($hm[3] ?: ($hm[4] ?? ''));
            if (preg_match('/^\s*javascript:/i', $href)) {
                $href = '';
            }
        }
        if ($href === '') return '<a>';
        $safeHref = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
        return '<a href="' . $safeHref . '" rel="noopener noreferrer">';
    }, $html);

    // <img> sadece src ve alt alır
    $html = preg_replace_callback('/<img\b([^>]*)>/i', function($m) {
        $attrs = $m[1] ?? '';
        $src = '';
        $alt = '';

        if (preg_match('/src\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $sm)) {
            $src = $sm[2] ?: ($sm[3] ?: ($sm[4] ?? ''));
        }
        if (preg_match('/alt\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $attrs, $am)) {
            $alt = $am[2] ?: ($am[3] ?: ($am[4] ?? ''));
        }

        if ($src === '' || preg_match('/^\s*javascript:/i', $src)) {
            return '';
        }

        $safeSrc = htmlspecialchars($src, ENT_QUOTES, 'UTF-8');
        $safeAlt = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
        return '<img src="' . $safeSrc . '" alt="' . $safeAlt . '">';
    }, $html);

    return $html;
}

$slug = trim((string)($_GET['slug'] ?? ''));
if ($slug === '') {
    http_response_code(404);
    echo 'Sayfa bulunamadı.';
    exit;
}

$allCodes = get_language_codes();
$cookieLang = trim((string)($_COOKIE['selectedLang'] ?? ''));
$langRaw = trim((string)($_GET['lang'] ?? ($cookieLang ?: 'tr')));
$lang = in_array($langRaw, $allCodes, true) ? $langRaw : 'tr';

$page = q('SELECT * FROM pages WHERE slug=? LIMIT 1', [$slug])->fetch();
if (!$page || (int)($page['is_active'] ?? 0) !== 1) {
    http_response_code(404);
    echo 'Sayfa bulunamadı.';
    exit;
}

$staticFile = trim((string)($page['static_file'] ?? ''));
if ($staticFile !== '') {
    if (!preg_match('#^https?://#i', $staticFile)) {
        $staticFile = '/' . ltrim($staticFile, '/');
    }
    header('Location: ' . $staticFile, true, 302);
    exit;
}

$title = trim((string)($page['title_' . $lang] ?? '')) ?: (string)$page['title_tr'];
$content = trim((string)($page['content_' . $lang] ?? '')) ?: (string)$page['content_tr'];
$contentSafe = sanitize_page_html($content);
$layout = trim((string)($page['layout_template'] ?? 'standart'));
if (!in_array($layout, ['standart','genis','iki-kolon','galeri','iletisim'], true)) {
    $layout = 'standart';
}

$banner = trim((string)($page['banner_image'] ?? ''));
if ($banner !== '' && !preg_match('#^https?://#i', $banner)) {
    $banner = '/' . ltrim($banner, '/');
}

$seoTitle = trim((string)($page['seo_title'] ?? '')) ?: $title;
$seoDesc = trim((string)($page['seo_desc'] ?? ''));
$seoKeywords = trim((string)($page['seo_keywords'] ?? ''));
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>" <?= $lang === 'ar' ? 'dir="rtl"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($seoTitle) ?> | BUYAPI</title>
    <?php if ($seoDesc !== ''): ?><meta name="description" content="<?= e($seoDesc) ?>"><?php endif; ?>
    <?php if ($seoKeywords !== ''): ?><meta name="keywords" content="<?= e($seoKeywords) ?>"><?php endif; ?>
    <?= favicon_links_html('/') ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="db-page layout-<?= e($layout) ?>">
    <div class="fluid-mesh-bg">
        <div class="mesh-orb mesh-1"></div>
        <div class="mesh-orb mesh-2"></div>
        <div class="mesh-orb mesh-3"></div>
    </div>

    <div id="site-header"></div>

    <section class="db-page-banner" style="<?= $banner ? "background-image:url('" . e($banner) . "')" : '' ?>">
        <div class="db-page-banner-overlay"></div>
        <div class="container db-page-banner-inner">
            <span class="section-tag">BUYAPI SAYFA</span>
            <h1 class="page-title"><?= e($title) ?></h1>
        </div>
    </section>

    <main class="db-page-main section-padding">
        <div class="container">
            <article class="db-page-content">
                <?php if ($layout === 'iki-kolon'): ?>
                    <div class="db-layout-grid">
                        <div class="db-layout-primary prose-content"><?= $contentSafe ?></div>
                        <aside class="db-layout-sidebar">
                            <div class="db-side-card">
                                <h3>BUYAPI</h3>
                                <p>Bu sayfa yönetim panelinden çok dilli ve şablonlu olarak yönetilmektedir.</p>
                            </div>
                            <div class="db-side-card">
                                <a class="btn" href="/iletisim.html">Bize Ulaşın</a>
                            </div>
                        </aside>
                    </div>
                <?php elseif ($layout === 'galeri'): ?>
                    <div class="prose-content"><?= $contentSafe ?></div>
                    <div class="db-gallery-placeholder" aria-hidden="true">
                        <div></div><div></div><div></div><div></div>
                    </div>
                <?php elseif ($layout === 'iletisim'): ?>
                    <div class="db-contact-layout">
                        <div class="db-contact-left prose-content"><?= $contentSafe ?></div>
                        <div class="db-contact-right">
                            <div class="db-side-card">
                                <h3>Hızlı İletişim</h3>
                                <p>Detaylı başvuru için iletişim sayfamızı kullanabilirsiniz.</p>
                                <a class="btn" href="/iletisim.html">İletişim Formuna Git</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="prose-content <?= $layout === 'genis' ? 'is-wide' : '' ?>">
                        <?= $contentSafe ?>
                    </div>
                <?php endif; ?>
            </article>
        </div>
    </main>

    <div id="site-footer"></div>

    <script>
      (function(){
        const url = new URL(window.location.href);
        if (!url.searchParams.get('lang')) {
          const localLang = localStorage.getItem('selectedLang');
          if (localLang && ['tr','en','ar'].includes(localLang)) {
            url.searchParams.set('lang', localLang);
            window.location.replace(url.toString());
          }
        }
      })();
    </script>
    <script src="/components/header.js"></script>
    <script src="/components/footer.js?v=202611121918"></script>
    <script src="/script.js"></script>
</body>
</html>
