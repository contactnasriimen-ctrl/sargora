<?php
/**
 * Routeur du serveur de développement PHP :  php -S localhost:8090 router.php
 * (en production, Apache utilise .htaccess)
 */
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

if (preg_match('#^/(app|data|sql|tools|_build|tests)(/|$)|/\.|\.(sqlite|sql|md|bat|ps1|log|ini)$#i', $path)) {
    http_response_code(404);
    exit('Not found');
}

$file = __DIR__ . $path;
if ($path !== '/' && is_file($file) && basename($file) !== 'router.php') {
    if (substr($file, -4) === '.php') {
        http_response_code(404);
        exit('Not found');
    }
    $types = [
        'css' => 'text/css; charset=utf-8', 'js' => 'text/javascript; charset=utf-8', 'mjs' => 'text/javascript; charset=utf-8',
        'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp',
        'ico' => 'image/x-icon', 'woff2' => 'font/woff2', 'json' => 'application/json', 'webmanifest' => 'application/manifest+json',
        'txt' => 'text/plain; charset=utf-8', 'xml' => 'application/xml',
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: ' . (isset($_GET['v']) ? 'public, max-age=31536000, immutable' : 'public, max-age=3600'));
    header('Content-Length: ' . filesize($file));
    readfile($file);
    return true;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
