<?php
/**
 * Configuration Sargora.
 * Pour la mise en ligne, ne modifiez pas ce fichier : créez app/config.local.php
 * (copie de app/config.local.example.php) — ses valeurs sont prioritaires.
 */
declare(strict_types=1);

$__local = __DIR__ . '/config.local.php';
if (is_file($__local)) {
    require $__local;
}

$__defaults = [
    // Identité
    'SITE_NAME'      => 'Sargora',
    'SITE_TAGLINE'   => 'Grow brands. Create impact.',
    'SITE_URL'       => '',                      // ex. https://sargora.tn (vide = détecté automatiquement)
    'SITE_EMAIL'     => 'contact@sargora.com',   // [à remplacer]
    'SITE_PHONE'     => '+216 00 000 000',       // [à remplacer]
    'SITE_WHATSAPP'  => '21600000000',           // [à remplacer] format international sans + ni espaces
    'SITE_ADDRESS'   => 'Tunis, Tunisie',        // [à remplacer]
    'SITE_HOURS'     => 'Lun – Sam · 9h – 19h',
    'SITE_FOUNDED'   => '2026',
    'SOCIAL_INSTAGRAM' => '#',
    'SOCIAL_FACEBOOK'  => '#',
    'SOCIAL_LINKEDIN'  => '#',
    'SOCIAL_TIKTOK'    => '#',

    // Boutique
    'CURRENCY'          => 'DT',
    'CURRENCY_DECIMALS' => 3,   // DT = 1000 millimes ; les prix sont stockés en unités mineures
    'ORDER_PREFIX'      => 'SG',

    // Base de données : 'sqlite' (aucune installation) ou 'mysql'
    'DB_DRIVER' => 'sqlite',
    'DB_PATH'   => __DIR__ . '/../data/sargora.sqlite',
    'DB_HOST'   => '127.0.0.1',
    'DB_PORT'   => '3306',
    'DB_NAME'   => 'sargora',
    'DB_USER'   => 'root',
    'DB_PASS'   => '',

    // Divers
    'DEBUG'     => false,
    'DEMO_MODE' => true,   // affiche un bandeau « contenu de démonstration » dans l'admin
    'STATIC_BUILD' => false,  // export statique (GitHub Pages) : panier local + commandes via WhatsApp
    'STATIC_BASE'  => '',     // sous-dossier de publication, ex. /sargora
];

foreach ($__defaults as $__k => $__v) {
    if (!defined($__k)) {
        $__env = getenv('SG_' . $__k);
        define($__k, $__env !== false ? $__env : $__v);
    }
}
unset($__defaults, $__k, $__v, $__env, $__local);
