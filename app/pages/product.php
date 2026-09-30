<?php
$p = product_by_slug((string) $params['slug']);
if (!$p) {
    return 404;
}
page_meta(['title' => $p['name'] . ' — ' . money($p['price_from']), 'description' => $p['tagline'] . ' ' . excerpt($p['description'], 110),
    'head' => '<script type="application/ld+json">' . json_encode([
        '@context' => 'https://schema.org', '@type' => 'Product', 'name' => $p['name'], 'description' => $p['tagline'],
        'image' => abs_url('assets/img/brand/og-image.jpg'), 'brand' => ['@type' => 'Brand', 'name' => SITE_NAME],
        'offers' => ['@type' => 'AggregateOffer', 'priceCurrency' => 'TND', 'lowPrice' => $p['price_from'] / money_factor(),
            'highPrice' => max(array_column($p['options'], 'price')) / money_factor(), 'availability' => $p['in_stock'] ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . '</script>']);
$related = products(['cat' => $p['cat_slug'], 'exclude' => $p['id'], 'limit' => 4]);
$base = $p['options'][0]['price'] ?? 0;
$waText = 'Bonjour Sargora, je souhaite commander : ' . $p['name'] . ' (' . ($p['options'][0]['label'] ?? '') . ').';
?>
<section class="product">
  <div class="container">
    <nav class="crumbs" aria-label="Fil d’Ariane"><a href="/">Accueil</a><?= icon('chevron-right') ?><a href="/boutique">Boutique</a><?= icon('chevron-right') ?><a href="/boutique/<?= e($p['cat_slug']) ?>"><?= e($p['cat_name']) ?></a><?= icon('chevron-right') ?><span><?= e($p['name']) ?></span></nav>
    <div class="product__grid">
      <div class="product__stage" data-reveal>
        <div class="product__float tilt tilt--strong" data-tilt>
          <?= product_card_visual($p, 'lg') ?>
          <span class="tilt__glare"></span>
        </div>
        <div class="product__shadow" aria-hidden="true"></div>
        <ul class="product__perks">
          <li><?= icon('zap') ?><?= e($p['delivery'] ?: 'Livraison rapide') ?></li>
          <li><?= icon('shield') ?>Garantie activation ou remboursement</li>
          <li><?= icon('lock') ?>Paiement en dinars sécurisé</li>
        </ul>
      </div>

      <div class="product__info">
        <span class="ptile__cat"><?= e($p['cat_name']) ?></span>
        <h1 class="product__name"><?= e($p['name']) ?><?php if ($p['badge']): ?> <span class="ptile__badge ptile__badge--inline"><?= e($p['badge']) ?></span><?php endif; ?></h1>
        <p class="product__tagline"><?= e($p['tagline']) ?></p>

        <form class="buy" action="/api/cart" method="post" data-add-to-cart data-buy>
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
          <div class="buy__price"><span data-buy-price><?= money($base) ?></span><small data-buy-label><?= e($p['options'][0]['label'] ?? '') ?></small></div>
          <?php if ($p['multi']): ?>
          <fieldset class="opts">
            <legend>Choisissez votre formule</legend>
            <?php foreach ($p['options'] as $i => $o):
                $save = '';
                if ($i > 0 && preg_match('/^(\d+)\s*mois/u', $o['label'], $m1) && preg_match('/^(\d+)\s*mois/u', $p['options'][0]['label'], $m0) && (int) $m0[1] > 0) {
                    $ref = $base / (int) $m0[1] * (int) $m1[1];
                    $pct = $ref > 0 ? (int) round((1 - $o['price'] / $ref) * 100) : 0;
                    $save = $pct >= 3 ? '-' . $pct . ' %' : '';
                } ?>
            <label class="opt">
              <input type="radio" name="opt" value="<?= $i ?>"<?= $i === 0 ? ' checked' : '' ?> data-price="<?= e(money((int) $o['price'])) ?>" data-label="<?= e($o['label']) ?>">
              <span class="opt__box"><span class="opt__label"><?= e($o['label']) ?></span><span class="opt__price"><?= money((int) $o['price']) ?></span><?php if ($save): ?><span class="opt__save"><?= e($save) ?></span><?php endif; ?></span>
            </label>
            <?php endforeach; ?>
          </fieldset>
          <?php else: ?>
          <input type="hidden" name="opt" value="0">
          <?php endif; ?>
          <div class="buy__row">
            <div class="qty" data-qty>
              <button type="button" data-qty-minus aria-label="Diminuer la quantité"><?= icon('minus') ?></button>
              <input type="number" name="qty" value="1" min="1" max="10" aria-label="Quantité" inputmode="numeric">
              <button type="button" data-qty-plus aria-label="Augmenter la quantité"><?= icon('plus') ?></button>
            </div>
            <?php if ($p['in_stock']): ?>
            <button class="btn btn--gold btn--lg buy__add" type="submit" data-magnetic><?= icon('bag') ?><span>Ajouter au panier</span></button>
            <?php else: ?>
            <span class="btn btn--ghost btn--lg is-disabled">Temporairement indisponible</span>
            <?php endif; ?>
          </div>
          <a class="btn btn--ghost btn--block buy__wa" href="<?= e(whatsapp_link($waText)) ?>" target="_blank" rel="noopener" data-buy-wa data-wa-base="<?= e('Bonjour Sargora, je souhaite commander : ' . $p['name']) ?>"><?= icon('whatsapp') ?><span>Commander via WhatsApp</span></a>
        </form>

        <div class="product__desc">
          <?php foreach (preg_split('/\n\s*\n/', (string) $p['description']) as $para): ?><p><?= e(trim($para)) ?></p><?php endforeach; ?>
        </div>
        <?php if ($p['features']): ?>
        <h2 class="product__h">Ce qui est inclus</h2>
        <ul class="checks checks--cols">
          <?php foreach ($p['features'] as $f): ?><li><?= icon('check') ?><?= e($f) ?></li><?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container faq-wrap">
    <div>
      <p class="eyebrow">Bon à savoir</p>
      <h2 class="h2">Questions <em>fréquentes.</em></h2>
    </div>
    <div class="faq">
      <?php foreach (array_slice(shop_faq(), 0, 4) as [$fq, $fa]): ?>
      <details class="faq__item"><summary><?= e($fq) ?><span class="faq__ico"><?= icon('plus') ?></span></summary><div class="faq__a"><p><?= e($fa) ?></p></div></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section section--tight">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">Dans la même catégorie</p>
      <h2 class="h2">Vous aimerez <em>aussi.</em></h2>
    </div>
    <div class="products-grid">
      <?php foreach ($related as $p) { require ROOT . '/app/views/product_tile.php'; } ?>
    </div>
  </div>
</section>
<?php endif; ?>
