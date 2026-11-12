<?php
function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $html = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['html' => $html, 'code' => $info['http_code'], 'url' => $info['url']];
}

$res = fetchUrl('https://buyapi.com.tr/');
echo "Root URL: " . $res['url'] . " | Code: " . $res['code'] . " | Length: " . strlen($res['html']) . "\n";
file_put_contents(__DIR__ . '/scraped_root.html', $res['html']);

$example = fetchUrl('https://buyapi.com.tr/tr-tr/sayfalar/projeler-2/konut-3/buyapi-altinisik-konutlari-1-47/');
echo "Example URL: " . $example['url'] . " | Code: " . $example['code'] . " | Length: " . strlen($example['html']) . "\n";
if ($example['code'] == 200) {
    file_put_contents(__DIR__ . '/scraped_example.html', $example['html']);
}
