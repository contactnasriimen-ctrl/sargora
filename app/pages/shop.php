<?php
$catSlug = (string) ($params['slug'] ?? '');
$cat = $catSlug !== '' ? category_by_slug($catSlug) : null;
if ($catSlug !== '' && !$cat) {
    return 404;
}
$q = mb_substr(get('q'), 0, 60);
$sort = in_array(get('tri'), ['prix-asc', 'prix-desc', 'nom'], true) ? get('tri') : '';
$list = products(['cat' => $catSlug, 'q' => $q, 'sort' => $sort]);
page_meta([
    'title' => $cat ? $cat['name'] . ' — Boutique' : 'Boutique digitale',
    'description' => $cat ? $cat['description'] . ' Paiement en dinars, activation rapide.' : 'ChatGPT Plus, Claude Pro, Canva Pro, Adobe, Netflix… Vos abonnements digitaux officiels payables en dinars, activés en quelques heures.',
    'canonical' => $cat ? '/boutique/' . $cat['slug'] : '/boutique',
]);
?>
<section class="page-hero page-hero--shop">
  <div class="container page-hero__split">
    <div>
      <p class="eyebrow" data-reveal>Boutique digitale</p>
      <h1 class="h1" data-split><?= $cat ? e($cat['name']) : 'Vos outils premium, <em>en dinars.</em>' ?></h1>
      <p class="lead page-hero__lead" data-reveal><?= $cat ? e($cat['description']) : 'Abonnements officiels activés sur votre propre compte, sans carte internationale. Choisissez, payez en D17, Flouci, virement ou espèces — nous activons en quelques heures.' ?></p>
      <ul class="trust" data-reveal>
        <li><?= icon('zap') ?><span><strong>Activation rapide</strong>1 à 6 h</span></li>
        <li><?= icon('shield') ?><span><strong>Garantie</strong>corrigé ou remboursé</span></li>
        <li><?= icon('headset') ?><span><strong>Support</strong>7 j/7 WhatsApp</span></li>
      </ul>
    </div>
    <div class="shop-stage" aria-hidden="true">
      <div class="fan fan--hero" data-fan>
        <?php foreach (array_slice(products(['featured' => true, 'limit' => 5]), 0, 5) as $i => $fp): ?>
        <div class="fan__card" style="--i:<?= $i ?>;--n:5"><?= product_card_visual($fp) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="page-hero__glow" aria-hidden="true"></div>
</section>

<section class="section section--tight shop" id="catalogue">
  <div class="container">
    <form class="shop-bar" action="<?= $cat ? '/boutique/' . e($cat['slug']) : '/boutique' ?>#catalogue" method="get" data-shop-filter role="search">
      <div class="pills" role="group" aria-label="Catégories">
        <a class="pill<?= !$cat ? ' is-active' : '' ?>" href="/boutique#catalogue" data-cat-filter="all">Tout <small><?= array_sum(array_column(categories(), 'n')) ?></small></a>
        <?php foreach (categories() as $c): ?>
        <a class="pill<?= $cat && $cat['slug'] === $c['slug'] ? ' is-active' : '' ?>" href="/boutique/<?= e($c['slug']) ?>#catalogue" data-cat-filter="<?= e($c['slug']) ?>"><?= e($c['name']) ?> <small><?= (int) $c['n'] ?></small></a>
        <?php endforeach; ?>
      </div>
      <div class="shop-bar__tools">
        <label class="search"><?= icon('search') ?><span class="sr-only">Rechercher</span><input type="search" name="q" value="<?= e($q) ?>" placeholder="ChatGPT, Canva, Netflix…" data-shop-search autocomplete="off"></label>
        <label class="select"><span class="sr-only">Trier</span>
          <select name="tri" data-shop-sort>
            <option value="">Recommandés</option>
            <option value="prix-asc"<?= $sort === 'prix-asc' ? ' selected' : '' ?>>Prix croissant</option>
            <option value="prix-desc"<?= $sort === 'prix-desc' ? ' selected' : '' ?>>Prix décroissant</option>
            <option value="nom"<?= $sort === 'nom' ? ' selected' : '' ?>>Nom (A → Z)</option>
          </select><?= icon('chevron-down') ?>
        </label>
        <noscript><button class="btn btn--ghost btn--sm" type="submit">Filtrer</button></noscript>
      </div>
    </form>

    <div class="products-grid" data-products>
      <?php foreach ($list as $p) { require ROOT . '/app/views/product_tile.php'; } ?>
    </div>
    <div class="empty" data-shop-empty<?= $list ? ' hidden' : '' ?>>
      <?= icon('search') ?>
      <p><strong>Aucun produit ne correspond.</strong> Essayez un autre mot-clé ou <a href="<?= e(whatsapp_link('Bonjour, je cherche un produit digital : ')) ?>" target="_blank" rel="noopener">demandez-le-nous sur WhatsApp</a> : nous trouvons presque tout.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head section-head--center">
      <p class="eyebrow" data-reveal>Comment ça marche</p>
      <h2 class="h2" data-split>Trois étapes, <em>quelques heures.</em></h2>
    </div>
    <div class="steps3">
      <?php foreach ([['bag', 'Choisissez', 'Ajoutez vos abonnements au panier et validez votre commande en une minute.'],
                      ['lock', 'Payez en dinars', 'D17, Flouci, virement ou espèces : les instructions s’affichent aussitôt.'],
                      ['gift', 'Profitez', 'Nous activons votre abonnement ou vous envoyons vos accès par e-mail et WhatsApp.']] as $i => [$ic, $t, $d]): ?>
      <div class="step3 glass" data-reveal style="--d:<?= $i * 90 ?>ms">
        <span class="step3__num">0<?= $i + 1 ?></span>
        <span class="step3__ico"><?= icon($ic) ?></span>
        <h3><?= e($t) ?></h3><p><?= e($d) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container faq-wrap">
    <div>
      <p class="eyebrow" data-reveal>Questions fréquentes</p>
      <h2 class="h2" data-split>Tout savoir sur <em>la boutique.</em></h2>
      <a class="link-arrow" href="/suivi" data-reveal>Suivre une commande <?= icon('arrow-right') ?></a>
    </div>
    <div class="faq">
      <?php foreach (shop_faq() as [$fq, $fa]): ?>
      <details class="faq__item" data-reveal><summary><?= e($fq) ?><span class="faq__ico"><?= icon('plus') ?></span></summary><div class="faq__a"><p><?= e($fa) ?></p></div></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
