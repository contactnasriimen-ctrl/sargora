<?php
$meta = page_meta();
$u = admin_user();
$bare = !empty($meta['bare']);
$pending = $u ? (int) db_val("SELECT COUNT(*) FROM orders WHERE status IN ('pending','paid')") : 0;
$unread = $u ? (int) db_val('SELECT COUNT(*) FROM messages WHERE is_read = 0') : 0;
$nav = [
    ['/admin', 'grid', 'Tableau de bord', null],
    ['/admin/commandes', 'box', 'Commandes', $pending],
    ['/admin/produits', 'bag', 'Produits', null],
    ['/admin/messages', 'inbox', 'Demandes & devis', $unread],
    ['/admin/abonnes', 'users', 'Newsletter', null],
    ['/admin/compte', 'user', 'Mon compte', null],
];
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e(($meta['title'] ?? 'Administration') . ' · Sargora Admin') ?></title>
<link rel="icon" type="image/png" href="<?= asset('img/brand/favicon-32.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&family=Sora:wght@300;400;500;600&display=swap">
<link rel="stylesheet" href="<?= asset('css/cards.css') ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="adm<?= $bare ? ' adm--bare' : '' ?>">
<?php if (!$bare && $u): ?>
<aside class="side" id="side">
  <a class="side__brand" href="/admin"><img src="<?= asset('img/brand/emblem-96.webp') ?>" alt="" width="36" height="36"><span>SARGORA</span><small>Admin</small></a>
  <nav class="side__nav">
    <?php foreach ($nav as [$href, $ic, $label, $n]):
        $active = $href === '/admin' ? current_path() === '/admin' : is_active($href); ?>
    <a class="<?= $active ? 'is-active' : '' ?>" href="<?= $href ?>"><?= icon($ic) ?><span><?= e($label) ?></span><?php if ($n): ?><em><?= (int) $n ?></em><?php endif; ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="side__foot">
    <a href="/" target="_blank" rel="noopener"><?= icon('external') ?><span>Voir le site</span></a>
    <a href="/admin/deconnexion"><?= icon('logout') ?><span>Déconnexion</span></a>
  </div>
</aside>
<?php endif; ?>
<div class="adm__main">
  <?php if (!$bare && $u): ?>
  <header class="top">
    <button class="top__burger" type="button" data-side-toggle aria-label="Menu" aria-controls="side"><?= icon('menu') ?></button>
    <h1><?= e($meta['title'] ?? 'Administration') ?></h1>
    <span class="top__user"><?= icon('user') ?> <?= e($u['name']) ?></span>
  </header>
  <?php if (filter_var(DEMO_MODE, FILTER_VALIDATE_BOOL)): ?><div class="demo">Mode démonstration : pensez à remplacer les coordonnées de paiement, les réalisations et les témoignages d’exemple avant la mise en ligne.</div><?php endif; ?>
  <?php endif; ?>
  <?php foreach (flashes() as $f): ?><div class="alert alert--<?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endforeach; ?>
  <?= $content ?>
</div>
<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
