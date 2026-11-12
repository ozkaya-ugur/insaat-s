<?php
function fetchHtml($url) {
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

$listingUrls = [
    'https://buyapi.com.tr/tr-tr/yazilar/projeler-2/',
    'https://buyapi.com.tr/tr-tr/yazilar/projeler-2/konut-3/',
    'https://buyapi.com.tr/tr-tr/yazilar/projeler-2/ticari-4/',
    'https://buyapi.com.tr/tr-tr/yazilar/projeler-2/is-merkezi-5/',
    'https://buyapi.com.tr/tr-TR/Home',
];

$allProjectUrls = [];

foreach ($listingUrls as $lUrl) {
    echo "Fetching: $lUrl\n";
    $html = fetchHtml($lUrl);
    preg_match_all('#href=["\']([^"\']+/sayfalar/projeler-2/[^"\']+)["\']#i', $html, $m);
    if (!empty($m[1])) {
        foreach ($m[1] as $p) {
            $full = (strpos($p, 'http') === 0) ? $p : 'https://buyapi.com.tr' . $p;
            // Clean anchor or query
            $full = strtok($full, '#');
            $full = strtok($full, '?');
            $allProjectUrls[$full] = true;
        }
    }
    
    // Also check for pagination in yazilar
    preg_match_all('#href=["\']([^"\']+/yazilar/projeler-2/[^"\']+)["\']#i', $html, $m2);
    if (!empty($m2[1])) {
        foreach ($m2[1] as $subL) {
            $subFull = (strpos($subL, 'http') === 0) ? $subL : 'https://buyapi.com.tr' . $subL;
            echo "  Found sub-listing: $subFull\n";
        }
    }
}

$allProjectUrls = array_keys($allProjectUrls);
echo "\nTotal distinct project URLs found: " . count($allProjectUrls) . "\n";
foreach ($allProjectUrls as $u) {
    echo "  $u\n";
}
file_put_contents(__DIR__ . '/scraped_project_urls.json', json_encode($allProjectUrls, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
