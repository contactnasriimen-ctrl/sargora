<?php
if (is_post()) {
    require_csrf();
    foreach ((array) ($_POST['qty'] ?? []) as $key => $qty) {
        cart_set((string) $key, (int) $qty);
    }
    if (post('remove') !== '') {
        cart_set(post('remove'), 0);
    }
    redirect('/panier');
}
page_meta(['title' => 'Panier', 'robots' => 'noindex', 'no_cta' => true]);
$cart = cart_summary();
?>
<section class="page-hero page-hero--compact">
  <div class="container">
    <p class="eyebrow">Boutique</p>
    <h1 class="h1">Votre <em>panier.</em></h1>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <?php if (is_static()): ?>
    <div data-static-cart><p class="muted">Chargement du panier…</p></div>
    <?php elseif (!$cart['items']): ?>
    <div class="empty empty--big">
      <?= icon('bag') ?>
      <p><strong>Votre panier est vide.</strong> Découvrez nos abonnements digitaux payables en dinars.</p>
      <a class="btn btn--gold" href="/boutique"><span>Explorer la boutique</span><?= icon('arrow-right') ?></a>
    </div>
    <?php else: ?>
    <form class="cart" method="post" action="/panier" data-cart-page>
      <?= csrf_field() ?>
      <div class="cart__list">
        <?php foreach ($cart['items'] as $it): ?>
        <div class="cart__row">
          <a class="pthumb th-<?= e($it['theme']) ?>" href="/produit/<?= e($it['slug']) ?>" aria-hidden="true" tabindex="-1"><span><?= e($it['glyph']) ?></span></a>
          <div class="cart__info">
            <a class="cart__name" href="/produit/<?= e($it['slug']) ?>"><?= e($it['name']) ?></a>
            <span class="cart__opt"><?= e($it['option']) ?> · <?= money($it['unit']) ?></span>
          </div>
          <label class="qty qty--sm"><span class="sr-only">Quantité</span><input type="number" name="qty[<?= e($it['key']) ?>]" value="<?= (int) $it['qty'] ?>" min="0" max="10" data-autosubmit></label>
          <strong class="cart__line"><?= money($it['line']) ?></strong>
          <button class="icon-btn" type="submit" name="remove" value="<?= e($it['key']) ?>" aria-label="Retirer <?= e($it['name']) ?>"><?= icon('trash') ?></button>
        </div>
        <?php endforeach; ?>
        <div class="cart__actions"><a class="link-arrow" href="/boutique"><?= icon('arrow-left') ?> Continuer mes achats</a><button class="btn btn--ghost btn--sm" type="submit">Mettre à jour</button></div>
      </div>
      <aside class="summary glass">
        <h2>Récapitulatif</h2>
        <div class="summary__row"><span>Articles</span><span><?= (int) $cart['count'] ?></span></div>
        <div class="summary__row"><span>Livraison digitale</span><span>Offerte</span></div>
        <div class="summary__total"><span>Total</span><strong><?= money($cart['total']) ?></strong></div>
        <a class="btn btn--gold btn--block btn--lg" href="/commande" data-magnetic><span>Passer la commande</span><?= icon('arrow-right') ?></a>
        <p class="summary__note"><?= icon('lock') ?> Paiement en dinars : D17, Flouci, virement ou espèces.</p>
      </aside>
    </form>
    <?php endif; ?>
  </div>
</section>
