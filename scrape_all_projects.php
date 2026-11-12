<?php
require_once __DIR__ . '/includes/core.php';

set_time_limit(600);
ini_set('memory_limit', '512M');

echo "=== BUYAPI.COM.TR TÜM PROJELERİ İÇERİ AKTARMA MOTORU ===\n";

$urlsJson = file_get_contents(__DIR__ . '/scraped_project_urls.json');
$projectUrls = json_decode($urlsJson, true) ?: [];

echo "Toplam " . count($projectUrls) . " proje URL'si işlenecek.\n\n";

$uploadProjectsDir = UPLOAD_DIR . '/projects';
if (!is_dir($uploadProjectsDir)) {
    @mkdir($uploadProjectsDir, 0777, true);
}

function fetchWebPage($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function downloadImageLocally($remoteUrl, $destFilename) {
    global $uploadProjectsDir;
    $targetPath = $uploadProjectsDir . '/' . $destFilename;
    if (file_exists($targetPath) && filesize($targetPath) > 1000) {
        return 'uploads/projects/' . $destFilename;
    }
    $ch = curl_init($remoteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code == 200 && strlen($data) > 1000) {
        file_put_contents($targetPath, $data);
        return 'uploads/projects/' . $destFilename;
    }
    return $remoteUrl; // fallback to remote URL
}

$processed = 0;
$inserted = 0;
$updated = 0;

foreach ($projectUrls as $idx => $url) {
    $processed++;
    echo "[$processed/" . count($projectUrls) . "] İndiriliyor: $url ...\n";

    $html = fetchWebPage($url);
    if (!$html) {
        echo "  HATA: Sayfa içeriği alınamadı.\n";
        continue;
    }

    // Başlık
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
    if (!$title) {
        $slugPart = basename($url);
        $title = ucwords(str_replace(['-', '_'], ' ', preg_replace('/-\d+$/', '', $slugPart)));
    }
    $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Slug
    $slug = slugify($title);
    if (!$slug) $slug = 'proje-' . $processed;

    // Tür (type)
    $type = 'konut';
    if (strpos($url, 'ticari') !== false) $type = 'ticari';
    elseif (strpos($url, 'is-merkezi') !== false) $type = 'ticari';
    elseif (strpos($url, 'kentsel') !== false) $type = 'kentsel';

    // Görseller
    preg_match_all('#https?://[^"\']*/Admin/Image/Orji/[^"\']+|/Admin/Image/Orji/[^"\']+#i', $html, $imgM);
    $images = [];
    foreach ($imgM[0] as $img) {
        $img = (strpos($img, 'http') === 0) ? $img : 'https://buyapi.com.tr' . $img;
        $images[$img] = true;
    }
    $images = array_keys($images);

    // Açıklama
    $paragraphs = [];
    if (preg_match_all('#<p[^>]*>(.*?)</p>#is', $html, $pM)) {
        foreach ($pM[1] as $p) {
            $text = trim(strip_tags($p));
            if (strlen($text) > 15 && stripos($text, 'cookies') === false && stripos($text, 'copyright') === false && stripos($text, 'faaliyete başladığımız') === false) {
                $paragraphs[] = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
    }
    $desc = implode("\n\n", $paragraphs);
    if (!$desc) {
        $desc = "$title projemiz, Buyapı İnşaat güvencesiyle yüksek mühendislik ve mimari estetiği buluşturmaktadır.";
    }

    // Alt Başlık / Spot (ilk paragraf veya özet)
    $subtitle = '';
    if (!empty($paragraphs[0])) {
        $subtitle = mb_substr($paragraphs[0], 0, 150, 'UTF-8') . (mb_strlen($paragraphs[0], 'UTF-8') > 150 ? '...' : '');
    } else {
        $subtitle = "Teknik İnovasyon & Prestijli $type Projesi";
    }

    // Lokasyon & İl Kodu Tespiti
    $loc = 'Serdivan / Sakarya';
    $cat = 'Sakarya';
    $cityKey = 'tr-sk';
    $lowerText = mb_strtolower($title . ' ' . $desc . ' ' . $url, 'UTF-8');

    if (strpos($lowerText, 'erenköy') !== false || strpos($lowerText, 'istanbul') !== false || strpos($lowerText, 'kadıköy') !== false) {
        $loc = 'Kadıköy / İstanbul';
        $cat = 'İstanbul';
        $cityKey = 'tr-ib';
    } elseif (strpos($lowerText, 'polatlı') !== false || strpos($lowerText, 'ankara') !== false) {
        $loc = 'Polatlı / Ankara';
        $cat = 'Ankara';
        $cityKey = 'tr-an';
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

    // Durum (Status)
    $status = 'tamamlanan';
    if (strpos($lowerText, 'satış') !== false || strpos($lowerText, 'devam') !== false || strpos($lowerText, 'intense') !== false || strpos($lowerText, 'altinisik-konutlari-1') !== false || strpos($lowerText, 'altinisik-konutlari-2') !== false || strpos($lowerText, 'palm') !== false || strpos($lowerText, 'time') !== false) {
        $status = 'devam-eden';
    }

    // Kapak Görseli
    $cover = '';
    if (!empty($images[0])) {
        $ext = pathinfo(parse_url($images[0], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
        $cover = downloadImageLocally($images[0], "cover_{$slug}.{$ext}");
    }

    // İngilizce & Almanca & Arapça Çeviriler (Yabancı diller için akıllı başlık/altbaşlık)
    $titleEn = $title;
    $subtitleEn = ($type === 'konut') ? "Prestigious Residential Living in $cat" : "Modern Commercial & Business Center in $cat";
    $descEn = "$title represents high architectural standards, structural safety, and prime location developed by BUYAPI Construction.";

    $titleDe = $title;
    $subtitleDe = ($type === 'konut') ? "Prestigeträchtiges Wohnen in $cat" : "Modernes Gewerbe- und Geschäftszentrum in $cat";
    $descDe = "$title verbindet erstklassige Ingenieurkunst und moderne Architektur unter der Garantie von BUYAPI Bau.";

    $titleAr = $title;
    $subtitleAr = ($type === 'konut') ? "مشروع سكني فاخر في $cat" : "مركز أعمال وتجاري حديث في $cat";
    $descAr = "يقدم مشروع $title أعلى معايير الجودة المعمارية والهندسية تحت إشراف شركة بويابي للإنشاءات.";

    // Alan / Konsept
    $area = '12.500 m²';
    if (preg_match('/(\d+(?:\.\d+)?)\s*(?:m2|m²)/i', $desc, $aM)) {
        $area = $aM[1] . ' m²';
    }
    $concept = ($type === 'konut') ? 'A++ Avant-Garde Rezidans' : 'Modern Ticari & İş Merkezi';

    // Veritabanında kontrol et (slug veya title benzerliği)
    $existing = q('SELECT id, cover_image FROM projects WHERE slug = ? OR title_tr = ?', [$slug, $title])->fetch();

    if ($existing) {
        $pId = (int)$existing['id'];
        $finalCover = (!empty($existing['cover_image']) && !strpos($existing['cover_image'], 'banner-')) ? $existing['cover_image'] : $cover;
        
        q('UPDATE projects SET 
            title_tr = ?, title_en = ?, title_ar = ?, title_de = ?,
            subtitle_tr = ?, subtitle_en = ?, subtitle_ar = ?, subtitle_de = ?,
            description_tr = ?, description_en = ?, description_ar = ?, description_de = ?,
            cat = ?, loc = ?, city_key = ?, type = ?, status = ?,
            cover_image = ?, area = ?, concept = ?, is_active = 1
           WHERE id = ?', [
            $title, $titleEn, $titleAr, $titleDe,
            $subtitle, $subtitleEn, $subtitleAr, $subtitleDe,
            $desc, $descEn, $descAr, $descDe,
            $cat, $loc, $cityKey, $type, $status,
            $finalCover, $area, $concept,
            $pId
        ]);
        $updated++;
        echo "  -> Güncellendi (ID: $pId): $title\n";
    } else {
        q('INSERT INTO projects (
            slug, title_tr, title_en, title_ar, title_de,
            subtitle_tr, subtitle_en, subtitle_ar, subtitle_de,
            description_tr, description_en, description_ar, description_de,
            cat, loc, city_key, type, status, cover_image, area, concept,
            is_featured, is_active, sort_order, seo_title, seo_desc
        ) VALUES (
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?,
            ?, 1, ?, ?, ?
        )', [
            $slug, $title, $titleEn, $titleAr, $titleDe,
            $subtitle, $subtitleEn, $subtitleAr, $subtitleDe,
            $desc, $descEn, $descAr, $descDe,
            $cat, $loc, $cityKey, $type, $status, $cover, $area, $concept,
            ($status === 'devam-eden' ? 1 : 0), $processed, "$title | BUYAPI", $subtitle
        ]);
        $pId = (int)db()->lastInsertId();
        $inserted++;
        echo "  -> Eklendi (ID: $pId): $title\n";
    }

    // Galeri Görsellerini `project_media` tablosuna işle
    if (!empty($images) && $pId > 0) {
        // Mevcut medyaları temizlemeden önce kontrol et
        $existingMediaCount = (int)q('SELECT COUNT(*) FROM project_media WHERE project_id = ?', [$pId])->fetchColumn();
        if ($existingMediaCount === 0) {
            foreach ($images as $mIdx => $imgUrl) {
                $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                $localMedia = downloadImageLocally($imgUrl, "gallery_{$slug}_{$mIdx}.{$ext}");
                $kind = ($mIdx < 2) ? 'hero' : 'render';
                q('INSERT INTO project_media (project_id, kind, file_path, caption, sort_order) VALUES (?, ?, ?, ?, ?)', [
                    $pId, $kind, $localMedia, "$title Detay " . ($mIdx + 1), $mIdx + 1
                ]);
            }
            echo "     " . count($images) . " adet galeri görseli kaydedildi.\n";
        }
    }
}

echo "\n============================================\n";
echo "TAMAMLANDI! Toplam İşlenen: $processed | Yeni Eklenen: $inserted | Güncellenen: $updated\n";
$totalInDb = q('SELECT COUNT(*) FROM projects')->fetchColumn();
echo "Veritabanındaki Toplam Proje Sayısı: $totalInDb\n";
echo "============================================\n";
