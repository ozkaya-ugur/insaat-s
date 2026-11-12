<?php
$html = file_get_contents(__DIR__ . '/scraped_example.html');

// Let's strip script and style tags to see the visible text
$clean = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
$clean = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $clean);

// Look for project specific text
$lines = explode("\n", strip_tags($clean));
foreach ($lines as $line) {
    $t = trim($line);
    if (strlen($t) > 2) {
        echo "$t\n";
    }
}
