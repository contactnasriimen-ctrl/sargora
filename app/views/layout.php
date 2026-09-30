<?php
/** Gabarit public. Variables : $content (HTML de la page). */
$meta = page_meta();
$title = isset($meta['title']) ? $meta['title'] . ' · ' . SITE_NAME : SITE_NAME . ' — Agence marketing, sites web, branding & boutique digitale';
$desc = $meta['description'] ?? 'Sargora, agence marketing 360° : création de sites web, branding, sponsoring, marketing digital et boutique en ligne d’abonnements digitaux (ChatGPT, Canva, Adobe…) payables en dinars.';
$canonical = abs_url($meta['canonical'] ?? current_path());
$ogImage = abs_url('assets/img/brand/og-image.jpg');
$bodyClass = $meta['body'] ?? '';
$isHome = current_path() === '/';
$count = cart_count();
$svcs = services();
?>
<!doctype html>
<html lang="fr" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<?php if (!empty($meta['robots'])): ?><meta name="robots" content="<?= e($meta['robots']) ?>">
<?php endif; ?>
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#060913">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:locale" content="fr_FR">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" type="image/png" sizes="32x32" href="<?= asset('img/brand/favicon-32.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('img/brand/apple-touch-icon.png') ?>">
<link rel="manifest" href="/assets/site.webmanifest">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Cormorant+Garamond:ital,wght@1,500;1,600&family=Sora:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('css/cards.css') ?>">
<?php if ($isHome): ?><link rel="preload" as="image" href="<?= asset('img/brand/emblem-640.webp') ?>" type="image/webp">
<?php endif; ?>
<script src="<?= asset('js/boot.js') ?>"></script>
<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org', '@type' => 'Organization', 'name' => SITE_NAME, 'slogan' => SITE_TAGLINE,
    'url' => base_url(), 'logo' => abs_url('assets/img/brand/icon-512.png'), 'email' => SITE_EMAIL, 'telephone' => SITE_PHONE,
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => SITE_ADDRESS],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?= $meta['head'] ?? '' ?>
</head>
<body class="<?= e($bodyClass) ?>"<?php if (is_static()): ?> data-static="1" data-base="<?= e((string) STATIC_BASE) ?>"<?php else: ?> data-csrf="<?= e(csrf_token()) ?>"<?php endif; ?> data-wa="<?= e(preg_replace('/\D+/', '', (string) SITE_WHATSAPP)) ?>">
<a class="skip" href="#main">Aller au contenu</a>

<?php if ($isHome): ?>
<div class="loader" data-loader aria-hidden="true">
  <div class="loader__panel loader__panel--top"></div>
  <div class="loader__panel loader__panel--bottom"></div>
  <div class="loader__center">
    <div class="loader__emblem"><img src="<?= asset('img/brand/emblem-256.webp') ?>" alt="" width="160" height="160"><span class="loader__flare"></span></div>
    <div class="loader__word" data-loader-word>SARGORA</div>
    <div class="loader__bar"><span data-loader-bar></span></div>
    <div class="loader__count"><span data-loader-count>0</span>%</div>
  </div>
</div>
<?php endif; ?>
<div class="curtain" aria-hidden="true"><div class="curtain__logo"><img src="<?= asset('img/brand/emblem-96.webp') ?>" alt="" width="64" height="64"></div></div>
<div class="grain" aria-hidden="true"></div>
<div class="cursor" aria-hidden="true"><span class="cursor__dot"></span><span class="cursor__ring"><em class="cursor__label"></em></span></div>

<header class="hdr" data-header>
  <div class="hdr__inner container">
    <a class="brand" href="/" aria-label="Sargora — accueil">
      <span class="brand__emblem"><img src="<?= asset('img/brand/emblem-96.webp') ?>" alt="" width="44" height="44"></span>
      <span class="brand__word">SARGORA</span>
    </a>
    <nav class="nav" aria-label="Navigation principale">
      <div class="nav__item nav__item--mega">
        <a class="nav__link<?= is_active('/services') ? ' is-active' : '' ?>" href="/services">Services <?= icon('chevron-down', 'nav__chev') ?></a>
        <div class="mega">
          <div class="mega__grid">
            <?php foreach ($svcs as $slug => $s): ?>
            <a class="mega__card" href="/services/<?= e($slug) ?>">
              <span class="mega__ico"><?= icon($s['icon']) ?></span>
              <span><strong><?= e($s['long']) ?></strong><small><?= e(excerpt($s['short'], 80)) ?></small></span>
            </a>
            <?php endforeach; ?>
          </div>
          <a class="mega__foot" href="/boutique"><span><?= icon('bag') ?> Boutique digitale · ChatGPT, Canva, Adobe… payables en dinars</span><?= icon('arrow-right') ?></a>
        </div>
      </div>
      <a class="nav__link<?= is_active('/realisations') ? ' is-active' : '' ?>" href="/realisations">Réalisations</a>
      <a class="nav__link<?= is_active('/agence') ? ' is-active' : '' ?>" href="/agence">Agence</a>
      <a class="nav__link nav__link--shop<?= is_active('/boutique') || is_active('/produit') ? ' is-active' : '' ?>" href="/boutique">Boutique <span class="nav__dot"></span></a>
      <a class="nav__link<?= is_active('/contact') ? ' is-active' : '' ?>" href="/contact">Contact</a>
    </nav>
    <div class="hdr__actions">
      <a class="cart-btn" href="/panier" data-cart-open aria-label="Ouvrir le panier">
        <?= icon('bag') ?><span class="cart-btn__count<?= $count ? ' is-on' : '' ?>" data-cart-count><?= $count ?></span>
      </a>
      <a class="btn btn--gold btn--sm hdr__cta" href="/contact" data-magnetic><span>Démarrer un projet</span></a>
      <button class="burger" type="button" aria-label="Menu" aria-expanded="false" aria-controls="menu" data-menu-toggle><span></span><span></span></button>
    </div>
  </div>
</header>

<div class="menu" id="menu" data-menu aria-hidden="true">
  <div class="menu__bg"></div>
  <div class="menu__inner container">
    <nav class="menu__nav" aria-label="Menu mobile">
      <?php $mi = 0; foreach (['/' => 'Accueil', '/services' => 'Services', '/realisations' => 'Réalisations', '/agence' => 'Agence', '/boutique' => 'Boutique', '/contact' => 'Contact'] as $href => $label): $mi++; ?>
      <a class="menu__link" href="<?= $href ?>" style="--i:<?= $mi ?>"><sup>0<?= $mi ?></sup><span><?= e($label) ?></span></a>
      <?php endforeach; ?>
    </nav>
    <div class="menu__foot" style="--i:7">
      <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= icon('mail') ?> <?= e(SITE_EMAIL) ?></a>
      <a href="<?= e(whatsapp_link()) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a>
      <a href="/suivi"><?= icon('box') ?> Suivi de commande</a>
    </div>
  </div>
</div>

<main id="main" class="main">
<?php foreach (flashes() as $f): ?>
  <div class="flash flash--<?= e($f['type']) ?> container" role="status"><?= e($f['msg']) ?></div>
<?php endforeach; ?>
<?= $content ?>
</main>

<footer class="ftr">
  <?php if (empty($meta['no_cta'])): ?>
  <section class="ftr__cta container" data-reveal>
    <p class="eyebrow">Un projet en tête ?</p>
    <h2 class="ftr__title">Parlons de votre <em>prochaine flamme.</em></h2>
    <div class="ftr__cta-row">
      <a class="btn btn--gold btn--lg" href="/contact" data-magnetic><span>Demander un devis gratuit</span><?= icon('arrow-up-right') ?></a>
      <a class="btn btn--ghost btn--lg" href="<?= e(whatsapp_link('Bonjour Sargora, j’aimerais parler de mon projet.')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?><span>Écrire sur WhatsApp</span></a>
    </div>
  </section>
  <?php endif; ?>
  <div class="ftr__main container">
    <div class="ftr__brand">
      <a class="brand" href="/" aria-label="Sargora — accueil">
        <span class="brand__emblem"><img src="<?= asset('img/brand/emblem-96.webp') ?>" alt="" width="44" height="44" loading="lazy"></span>
        <span class="brand__word">SARGORA</span>
      </a>
      <p class="ftr__tag"><?= e(SITE_TAGLINE) ?></p>
      <p class="ftr__text">Agence marketing 360° : sites web, branding, sponsoring, marketing digital et boutique d’outils digitaux payables en dinars.</p>
      <?php if (!is_static()): ?>
      <form class="news" data-newsletter action="/api/newsletter" method="post">
        <?= csrf_field() ?>
        <label class="sr-only" for="news-email">Votre e-mail</label>
        <input id="news-email" type="email" name="email" placeholder="Votre e-mail pour nos bons plans" required autocomplete="email">
        <button class="news__btn" type="submit" aria-label="S’inscrire à la newsletter"><?= icon('arrow-right') ?></button>
        <p class="news__msg" data-newsletter-msg role="status"></p>
      </form>
      <?php endif; ?>
    </div>
    <div class="ftr__col">
      <h3>Services</h3>
      <?php foreach ($svcs as $slug => $s): ?><a href="/services/<?= e($slug) ?>"><?= e($s['long']) ?></a><?php endforeach; ?>
    </div>
    <div class="ftr__col">
      <h3>Boutique</h3>
      <?php foreach (categories() as $c): ?><a href="/boutique/<?= e($c['slug']) ?>"><?= e($c['name']) ?></a><?php endforeach; ?>
      <a href="/suivi">Suivi de commande</a>
    </div>
    <div class="ftr__col">
      <h3>Contact</h3>
      <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>
      <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a>
      <span><?= e(SITE_ADDRESS) ?></span>
      <span><?= e(SITE_HOURS) ?></span>
      <div class="socials">
        <a href="<?= e(SOCIAL_INSTAGRAM) ?>" aria-label="Instagram"><?= icon('instagram') ?></a>
        <a href="<?= e(SOCIAL_FACEBOOK) ?>" aria-label="Facebook"><?= icon('facebook') ?></a>
        <a href="<?= e(SOCIAL_LINKEDIN) ?>" aria-label="LinkedIn"><?= icon('linkedin') ?></a>
        <a href="<?= e(SOCIAL_TIKTOK) ?>" aria-label="TikTok"><?= icon('tiktok') ?></a>
      </div>
    </div>
  </div>
  <div class="ftr__giant" aria-hidden="true">SARGORA</div>
  <div class="ftr__bottom container">
    <span>© <?= date('Y') ?> <?= e(SITE_NAME) ?>. Tous droits réservés.</span>
    <nav aria-label="Liens légaux"><a href="/mentions-legales">Mentions légales</a><a href="/cgv">CGV</a><a href="/confidentialite">Confidentialité</a></nav>
  </div>
</footer>

<aside class="drawer" id="cart-drawer" data-drawer aria-hidden="true" aria-label="Panier">
  <div class="drawer__backdrop" data-drawer-close></div>
  <div class="drawer__panel" role="dialog" aria-modal="true" aria-labelledby="drawer-title">
    <div class="drawer__head">
      <h2 id="drawer-title">Votre panier <span data-cart-count-label></span></h2>
      <button class="icon-btn" type="button" data-drawer-close aria-label="Fermer le panier"><?= icon('close') ?></button>
    </div>
    <div class="drawer__body" data-cart-items><p class="drawer__empty">Chargement…</p></div>
    <div class="drawer__foot" data-cart-foot hidden>
      <div class="drawer__total"><span>Total</span><strong data-cart-total>0</strong></div>
      <a class="btn btn--gold btn--block" href="/commande"><span>Commander</span><?= icon('arrow-right') ?></a>
      <a class="drawer__link" href="/panier">Voir le panier détaillé</a>
    </div>
  </div>
</aside>

<a class="wa-float" href="<?= e(whatsapp_link('Bonjour Sargora !')) ?>" target="_blank" rel="noopener" aria-label="Nous écrire sur WhatsApp"><?= icon('whatsapp') ?></a>

<script defer src="<?= asset('vendor/gsap/gsap.min.js') ?>"></script>
<script defer src="<?= asset('vendor/gsap/ScrollTrigger.min.js') ?>"></script>
<script defer src="<?= asset('vendor/lenis/lenis.min.js') ?>"></script>
<?php if (is_static()): ?><script defer src="<?= asset('js/static.js') ?>"></script>
<?php endif; ?>
<script defer src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
