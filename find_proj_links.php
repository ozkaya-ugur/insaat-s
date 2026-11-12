<?php
$html = file_get_contents(__DIR__ . '/scraped_root.html');
preg_match_all('#href=["\']([^"\']+)["\']#i', $html, $matches);
$links = array_unique($matches[1]);
$projLinks = [];
foreach ($links as $l) {
    if (stripos($l, 'proje') !== false) {
        $projLinks[] = $l;
    }
}
echo "Found " . count($projLinks) . " links with 'proje':\n";
foreach ($projLinks as $l) {
    echo "  $l\n";
}

// Let's also check sitemap.xml
$ch = curl_init('https://buyapi.com.tr/sitemap.xml');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$sitemap = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Sitemap HTTP code: $code | Length: " . strlen($sitemap) . "\n";
if ($code == 200 && strlen($sitemap) > 0) {
    file_put_contents(__DIR__ . '/scraped_sitemap.xml', $sitemap);
}
