<?php
$html = file_get_contents(__DIR__ . '/scraped_example.html');

// Let's find images under /Admin/Image/Orji/
preg_match_all('#https?://[^"\']*/Admin/Image/Orji/[^"\']+|/Admin/Image/Orji/[^"\']+#i', $html, $imgs);
$projectImages = array_unique($imgs[0]);
echo "Project gallery images: " . count($projectImages) . "\n";
foreach ($projectImages as $img) {
    echo "  $img\n";
}

// Let's find the content container
if (preg_match('#class=["\'][^"\']*(?:project|detail|content)[^"\']*["\']#i', $html)) {
    echo "Found detail classes.\n";
}

// Find title / heading in page
preg_match_all('#<h[1-4][^>]*>(.*?)</h[1-4]>#is', $html, $headings);
foreach ($headings[1] as $idx => $h) {
    echo "H: " . trim(strip_tags($h)) . "\n";
}
