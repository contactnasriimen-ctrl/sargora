<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

require __DIR__ . '/config.php';

date_default_timezone_set('Africa/Tunis');
mb_internal_encoding('UTF-8');

$debug = filter_var(DEBUG, FILTER_VALIDATE_BOOL);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/shop.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/data/agency.php';
require __DIR__ . '/covers.php';

if (PHP_SAPI !== 'cli') {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('sargora');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax']);
    ini_set('session.use_strict_mode', '1');
    session_start();

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'");
    if ($secure) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}
