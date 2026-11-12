<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/' || $uri === '') {
    if (is_file(__DIR__ . '/index.html')) {
        header('Content-Type: text/html; charset=utf-8');
        readfile(__DIR__ . '/index.html');
        exit;
    }
}

$filePath = __DIR__ . $uri;

if (is_dir($filePath) && substr($uri, -1) !== '/') {
    header('Location: ' . $uri . '/');
    exit;
}

if (is_file($filePath)) {
    return false; // Serve static file directly
}

if (is_dir($filePath) && is_file(rtrim($filePath, '/') . '/index.php')) {
    $_SERVER['SCRIPT_NAME'] = rtrim($uri, '/') . '/index.php';
    require rtrim($filePath, '/') . '/index.php';
    exit;
}

if (strpos($uri, '/api/') === 0 || $uri === '/api') {
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
    require __DIR__ . '/api/index.php';
    exit;
}

return false;
