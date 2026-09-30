<?php
/**
 * Exporte le site en HTML statique pour GitHub Pages.
 *   php tools/build_static.php [base] [url publique] [dossier de sortie]
 *   ex. php tools/build_static.php /sargora https://moncompte.github.io/sargora dist
 * Le panier fonctionne dans le navigateur ; commandes et devis sont envoyés sur WhatsApp.
 */
if (PHP_SAPI !== 'cli') {
    exit;
}
$root = dirname(__DIR__);
$base = rtrim($argv[1] ?? '/sargora', '/');
$site = rtrim($argv[2] ?? 'https://contactnasriimen-ctrl.github.io/sargora', '/');
$out = $argv[3] ?? 'dist';
$out = preg_match('#^([a-zA-Z]:)?[\\\\/]#', $out) ? $out : $root . '/' . $out;
$port = 8097;

require $root . '/app/bootstrap.php';

function rrmdir(string $dir): void
{
    if (!is_dir($dir)) return;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $f) {
        $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname());
    }
    rmdir($dir);
}

function rcopy(string $src, string $dst, array $skip = []): int
{
    $n = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS)) as $f) {
        $rel = str_replace('\\', '/', substr($f->getPathname(), strlen($src) + 1));
        foreach ($skip as $s) {
            if (strpos($rel, $s) === 0) continue 2;
        }
        $to = $dst . '/' . $rel;
        if (!is_dir(dirname($to))) mkdir(dirname($to), 0775, true);
        copy($f->getPathname(), $to);
        $n++;
    }
    return $n;
}

// 1. Serveur PHP temporaire en mode statique
$env = array_merge(getenv(), ['SG_STATIC_BUILD' => '1', 'SG_STATIC_BASE' => $base, 'SG_SITE_URL' => $site]);
$null = stripos(PHP_OS, 'WIN') === 0 ? 'NUL' : '/dev/null';
$proc = proc_open([PHP_BINARY, '-S', "127.0.0.1:$port", 'router.php'], [0 => ['pipe', 'r'], 1 => ['file', $null, 'w'], 2 => ['file', $null, 'w']], $pipes, $root, $env);
if (!is_resource($proc)) {
    fwrite(STDERR, "Impossible de démarrer le serveur PHP.\n");
    exit(1);
}
$ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 30]]);
for ($i = 0; $i < 50 && @file_get_contents("http://127.0.0.1:$port/robots.txt", false, $ctx) === false; $i++) {
    usleep(100000);
}

// 2. Liste des pages
$paths = ['', 'services', 'realisations', 'agence', 'boutique', 'panier', 'commande', 'suivi', 'contact', 'mentions-legales', 'cgv', 'confidentialite', 'sitemap.xml'];
foreach (array_keys(services()) as $s) $paths[] = 'services/' . $s;
foreach (array_keys(projects()) as $s) $paths[] = 'realisations/' . $s;
foreach (categories() as $c) $paths[] = 'boutique/' . $c['slug'];
foreach (products() as $p) $paths[] = 'produit/' . $p['slug'];

$rewrite = function (string $html) use ($base): string {
    return preg_replace('#\b(href|src|action)=(["\'])/(?!/)#', '$1=$2' . $base . '/', $html) ?? $html;
};

rrmdir($out);
mkdir($out, 0775, true);
$errors = 0;
foreach ($paths as $p) {
    $html = file_get_contents("http://127.0.0.1:$port/" . $p, false, $ctx);
    $status = (int) (preg_match('#HTTP/\S+ (\d{3})#', $http_response_header[0] ?? '', $m) ? $m[1] : 0);
    if ($status !== 200 || $html === false || $html === '') {
        fwrite(STDERR, "  ✗ /$p -> HTTP $status\n");
        $errors++;
        continue;
    }
    $file = $p === '' ? "$out/index.html" : (str_ends_with($p, '.xml') ? "$out/$p" : "$out/$p/index.html");
    if (!is_dir(dirname($file))) mkdir(dirname($file), 0775, true);
    file_put_contents($file, str_ends_with($p, '.xml') ? $html : $rewrite($html));
}
$html404 = file_get_contents("http://127.0.0.1:$port/page-introuvable-sargora", false, $ctx);
file_put_contents("$out/404.html", $rewrite((string) $html404));

proc_terminate($proc);
proc_close($proc);

// 3. Ressources + données de la boutique
$n = rcopy("$root/assets", "$out/assets", ['img/brand/logo-source.png']);
$shop = ['currency' => CURRENCY, 'decimals' => (int) CURRENCY_DECIMALS, 'products' => [], 'payments' => []];
foreach (products() as $p) {
    $shop['products'][(string) $p['id']] = ['name' => $p['name'], 'slug' => $p['slug'], 'theme' => $p['theme'], 'glyph' => (string) $p['glyph'],
        'in_stock' => (bool) $p['in_stock'], 'options' => array_map(fn($o) => ['label' => $o['label'], 'price' => (int) $o['price']], $p['options'])];
}
foreach (payment_methods() as $k => $pm) {
    $shop['payments'][$k] = ['label' => $pm['label'], 'steps' => $pm['steps']];
}
@mkdir("$out/assets/data", 0775, true);
file_put_contents("$out/assets/data/shop.json", json_encode($shop, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
$manifest = json_decode((string) file_get_contents("$root/assets/site.webmanifest"), true);
$manifest['start_url'] = $base . '/';
foreach ($manifest['icons'] as &$ic) {
    $ic['src'] = $base . $ic['src'];
}
unset($ic);
file_put_contents("$out/assets/site.webmanifest", json_encode($manifest, JSON_UNESCAPED_SLASHES));
file_put_contents("$out/robots.txt", "User-agent: *\nAllow: /\nSitemap: $site/sitemap.xml\n");
file_put_contents("$out/.nojekyll", '');

echo count($paths) - $errors . " pages + 404 exportées, $n fichiers de ressources -> $out\n";
exit($errors ? 1 : 0);
