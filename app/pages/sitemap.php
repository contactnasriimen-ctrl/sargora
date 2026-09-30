<?php
page_meta(['layout' => false]);
header('Content-Type: application/xml; charset=utf-8');
$urls = ['', 'services', 'realisations', 'agence', 'boutique', 'contact', 'suivi', 'mentions-legales', 'cgv', 'confidentialite'];
foreach (array_keys(services()) as $s) $urls[] = 'services/' . $s;
foreach (array_keys(projects()) as $s) $urls[] = 'realisations/' . $s;
foreach (categories() as $c) $urls[] = 'boutique/' . $c['slug'];
foreach (products() as $p) $urls[] = 'produit/' . $p['slug'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . e(abs_url($u)) . '</loc></url>' . "\n";
}
echo '</urlset>';
