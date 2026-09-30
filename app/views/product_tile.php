<?php /** Tuile produit. Variable : $p (produit hydraté). */ ?>
<article class="ptile" data-product data-cat="<?= e($p['cat_slug']) ?>" data-name="<?= e(mb_strtolower($p['name'] . ' ' . $p['tagline'])) ?>" data-price="<?= (int) $p['price_from'] ?>" data-sort="<?= (int) $p['sort'] ?>">
  <a class="ptile__visual tilt" href="/produit/<?= e($p['slug']) ?>" data-tilt tabindex="-1" aria-hidden="true">
    <?= product_card_visual($p) ?>
    <?php if ($p['badge']): ?><span class="ptile__badge"><?= e($p['badge']) ?></span><?php endif; ?>
    <span class="tilt__glare"></span>
  </a>
  <div class="ptile__body">
    <span class="ptile__cat"><?= e($p['cat_name']) ?></span>
    <h3 class="ptile__name"><a href="/produit/<?= e($p['slug']) ?>"><?= e($p['name']) ?></a></h3>
    <p class="ptile__tag"><?= e($p['tagline']) ?></p>
    <div class="ptile__foot">
      <span class="price"><?php if ($p['multi']): ?><small>dès</small> <?php endif; ?><?= money($p['price_from']) ?></span>
      <?php if (!$p['in_stock']): ?>
        <span class="badge badge--grey">Rupture</span>
      <?php else: ?>
      <form action="/api/cart" method="post" data-add-to-cart>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><input type="hidden" name="opt" value="0">
        <button class="add-btn" type="submit" aria-label="Ajouter <?= e($p['name']) ?> (<?= e($p['options'][0]['label'] ?? '') ?>) au panier"><?= icon('plus') ?><span>Ajouter</span></button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</article>
