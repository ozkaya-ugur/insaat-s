<?php
function parseProjectPage($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) return null;

    // Title
    $title = '';
    if (preg_match('#<h1[^>]*>(.*?)</h1>#is', $html, $m)) {
        $title = trim(strip_tags($m[1]));
    }
    if (!$title && preg_match('#<title>(.*?)</title>#is', $html, $m)) {
        $t = trim($m[1]);
        $t = preg_replace('#^BUYAPI\s*-\s*#i', '', $t);
        $t = preg_replace('#\s*\|\s*Değişimin Mimarı.*$#i', '', $t);
        $title = trim($t);
    }

    // Type from URL
    $type = 'konut';
    if (strpos($url, 'ticari') !== false) $type = 'ticari';
    elseif (strpos($url, 'is-merkezi') !== false) $type = 'ticari';
    elseif (strpos($url, 'kentsel') !== false) $type = 'kentsel';

    // Images under /Admin/Image/Orji/
    preg_match_all('#https?://[^"\']*/Admin/Image/Orji/[^"\']+|/Admin/Image/Orji/[^"\']+#i', $html, $imgM);
    $images = [];
    foreach ($imgM[0] as $img) {
        $img = (strpos($img, 'http') === 0) ? $img : 'https://buyapi.com.tr' . $img;
        $images[$img] = true;
    }
    $images = array_keys($images);

    // Meta description
    $metaDesc = '';
    if (preg_match('#<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']#is', $html, $m)) {
        $metaDesc = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // Description text
    // Look for content paragraphs
    $desc = '';
    if (preg_match_all('#<p[^>]*>(.*?)</p>#is', $html, $pM)) {
        $paragraphs = [];
        foreach ($pM[1] as $p) {
            $text = trim(strip_tags($p));
            if (strlen($text) > 15 && stripos($text, 'cookies') === false && stripos($text, 'copyright') === false) {
                $paragraphs[] = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        $desc = implode("\n\n", $paragraphs);
    }

    // Location detection
    $loc = 'Serdivan / Sakarya';
    $cat = 'Sakarya';
    $cityKey = 'tr-sk';
    $lowerText = mb_strtolower($title . ' ' . $desc . ' ' . $url, 'UTF-8');
    if (strpos($lowerText, 'erenköy') !== false || strpos($lowerText, 'istanbul') !== false || strpos($lowerText, 'kadıköy') !== false) {
        $loc = 'Kadıköy / İstanbul';
        $cat = 'İstanbul';
        $cityKey = 'tr-ib';
    } elseif (strpos($lowerText, 'sapanca') !== false) {
        $loc = 'Sapanca / Sakarya';
        $cat = 'Sakarya';
        $cityKey = 'tr-sk';
    } elseif (strpos($lowerText, 'hendek') !== false) {
        $loc = 'Hendek / Sakarya';
        $cat = 'Sakarya';
        $cityKey = 'tr-sk';
    } elseif (strpos($lowerText, 'giresun') !== false) {
        $loc = 'Giresun';
        $cat = 'Giresun';
        $cityKey = 'tr-gi';
    } elseif (strpos($lowerText, 'kocaeli') !== false || strpos($lowerText, 'izmit') !== false) {
        $loc = 'İzmit / Kocaeli';
        $cat = 'Kocaeli';
        $cityKey = 'tr-kc';
    }

    // Status detection
    $status = 'tamamlanan';
    if (strpos($lowerText, 'satış') !== false || strpos($lowerText, 'devam') !== false || strpos($lowerText, 'intense') !== false || strpos($lowerText, 'altinisik-konutlari-1') !== false || strpos($lowerText, 'altinisik-konutlari-2') !== false || strpos($lowerText, 'palm') !== false || strpos($lowerText, 'time') !== false) {
        $status = 'devam-eden';
    }

    return [
        'url' => $url,
        'title' => $title,
        'type' => $type,
        'cat' => $cat,
        'loc' => $loc,
        'cityKey' => $cityKey,
        'status' => $status,
        'desc' => $desc,
        'images' => $images,
    ];
}

$testUrls = [
    'https://buyapi.com.tr/tr-tr/sayfalar/projeler-2/konut-3/buyapi-altinisik-konutlari-1-47/',
    'https://buyapi.com.tr/tr-tr/sayfalar/projeler-2/ticari-4/kontrolmatik-pomega-enerji-37/',
    'https://buyapi.com.tr/tr-tr/sayfalar/projeler-2/is-merkezi-5/buyapi-palm-is-merkezi-45/'
];

foreach ($testUrls as $u) {
    echo "===================================\n";
    $p = parseProjectPage($u);
    echo "Title: {$p['title']}\n";
    echo "Type: {$p['type']} | Loc: {$p['loc']} | Status: {$p['status']}\n";
    echo "Images count: " . count($p['images']) . "\n";
    echo "Desc preview: " . substr(str_replace("\n", " ", $p['desc']), 0, 150) . "...\n";
}
