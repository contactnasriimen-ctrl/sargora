<?php
/**
 * Sargora — contrôleur frontal : toutes les URL propres passent par ici.
 */
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$routes = [
    ''                        => 'home',
    'services'                => 'services',
    'services/{slug}'         => 'service',
    'realisations'            => 'work',
    'realisations/{slug}'     => 'project',
    'agence'                  => 'about',
    'boutique'                => 'shop',
    'boutique/{slug}'         => 'shop',
    'produit/{slug}'          => 'product',
    'panier'                  => 'cart',
    'commande'                => 'checkout',
    'commande/{slug}'         => 'order',
    'suivi'                   => 'track',
    'contact'                 => 'contact',
    'mentions-legales'        => 'legal',
    'cgv'                     => 'legal',
    'confidentialite'         => 'legal',
    'sitemap.xml'             => 'sitemap',
    'api/cart'                => 'api/cart',
    'api/newsletter'          => 'api/newsletter',
    'admin'                   => 'admin/dashboard',
    'admin/connexion'         => 'admin/login',
    'admin/deconnexion'       => 'admin/logout',
    'admin/installation'      => 'admin/setup',
    'admin/commandes'         => 'admin/orders',
    'admin/commandes/{id}'    => 'admin/order',
    'admin/produits'          => 'admin/products',
    'admin/produits/{slug}'   => 'admin/product',
    'admin/messages'          => 'admin/messages',
    'admin/messages/{id}'     => 'admin/message',
    'admin/abonnes'           => 'admin/subscribers',
    'admin/compte'            => 'admin/account',
];

$raw = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = trim(rawurldecode($raw), '/');
if ($path === 'index.php') {
    redirect('/', 301);
}
if ($raw !== '/' && substr($raw, -1) === '/') {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    redirect('/' . $path . ($qs !== '' ? '?' . $qs : ''), 301);
}

$page = null;
$params = [];
foreach ($routes as $pattern => $target) {
    $re = '#^' . str_replace(['\{slug\}', '\{id\}'], ['([a-z0-9-]{1,120})', '([0-9]{1,10})'], preg_quote($pattern, '#')) . '$#';
    if (preg_match($re, $path, $m)) {
        $page = $target;
        $params = ['slug' => $m[1] ?? null, 'id' => isset($m[1]) ? (int) $m[1] : 0, 'path' => $path];
        break;
    }
}

function render_404(): void
{
    http_response_code(404);
    page_meta(['title' => 'Page introuvable', 'robots' => 'noindex']);
    ob_start();
    require ROOT . '/app/pages/404.php';
    $content = ob_get_clean();
    require ROOT . '/app/views/layout.php';
    exit;
}

if ($page === null) {
    render_404();
}

try {
    ob_start();
    $result = require ROOT . '/app/pages/' . $page . '.php';
    $content = (string) ob_get_clean();
    if ($result === 404) {
        render_404();
    }
    $meta = page_meta();
    if (($meta['layout'] ?? 'site') === false) {
        echo $content;
    } elseif (($meta['layout'] ?? 'site') === 'admin') {
        require ROOT . '/app/views/admin_layout.php';
    } else {
        require ROOT . '/app/views/layout.php';
    }
} catch (Throwable $ex) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('[sargora] ' . $ex->getMessage() . ' @ ' . $ex->getFile() . ':' . $ex->getLine());
    http_response_code(500);
    if (filter_var(DEBUG, FILTER_VALIDATE_BOOL)) {
        echo '<pre>' . e((string) $ex) . '</pre>';
    } else {
        echo '<!doctype html><meta charset="utf-8"><title>Erreur</title><body style="background:#070b16;color:#e9dcc0;font-family:sans-serif;display:grid;place-items:center;height:100vh;margin:0"><div style="text-align:center"><h1 style="font-weight:500">Un instant…</h1><p>Une erreur est survenue. Réessayez dans quelques secondes.</p><p><a style="color:#d4a84b" href="/">Retour à l’accueil</a></p></div>';
    }
}
