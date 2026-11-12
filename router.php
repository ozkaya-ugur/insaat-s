<?php
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($uri === '/' || $uri === '') {
    if (is_file(__DIR__ . '/index.html')) {
        header('Content-Type: text/html; charset=utf-8');
        readfile(__DIR__ . '/index.html');
        exit;
    }
}

// /api/* -> api/index.php
if (strpos($uri, '/api/') === 0 || $uri === '/api') {
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
    require __DIR__ . '/api/index.php';
    exit;
}

// /sayfa/{slug} ve /sayfa/{slug}/{lang} -> sayfa.php
if (preg_match('#^/sayfa/([^/]+)(?:/([a-z]{2}))?/?$#i', $uri, $m)) {
    $_GET['slug'] = urldecode($m[1]);
    if (!empty($m[2])) {
        $_GET['lang'] = strtolower($m[2]);
    }
    $_SERVER['SCRIPT_NAME'] = '/sayfa.php';
    require __DIR__ . '/sayfa.php';
    exit;
}

$filePath = __DIR__ . $uri;

if (is_dir($filePath) && substr($uri, -1) !== '/') {
    header('Location: ' . $uri . '/');
    exit;
}

if (is_file($filePath)) {
    return false; // static dosya
}

if (is_dir($filePath) && is_file(rtrim($filePath, '/') . '/index.php')) {
    $_SERVER['SCRIPT_NAME'] = rtrim($uri, '/') . '/index.php';
    require rtrim($filePath, '/') . '/index.php';
    exit;
}

return false;
