<?php
$html = file_get_contents(__DIR__ . '/scraped_example.html');

// Title
if (preg_match('#<h1[^>]*>(.*?)</h1>#is', $html, $m)) {
    echo "H1: " . trim(strip_tags($m[1])) . "\n";
}
if (preg_match('#<title>(.*?)</title>#is', $html, $m)) {
    echo "Title: " . trim($m[1]) . "\n";
}

// Meta description
if (preg_match('#<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']#is', $html, $m)) {
    echo "Meta Desc: " . trim($m[1]) . "\n";
}

// Images
preg_match_all('#<img[^>]+src=["\']([^"\']+)["\']#is', $html, $imgM);
echo "Images found: " . count($imgM[1]) . "\n";
foreach (array_slice(array_unique($imgM[1]), 0, 10) as $img) {
    echo "  IMG: $img\n";
}

// Text content / article body
if (preg_match('#<article[^>]*>(.*?)</article>#is', $html, $artM)) {
    echo "Article length: " . strlen($artM[1]) . "\n";
} else {
    echo "No article tag. Checking main content...\n";
}
