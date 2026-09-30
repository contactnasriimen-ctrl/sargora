<?php
$cart = cart_summary();
$errors = [];
$in = ['name' => '', 'email' => '', 'phone' => '', 'note' => '', 'payment' => 'd17', 'terms' => ''];
if (is_post()) {
    require_csrf();
    foreach (array_keys($in) as $k) {
        $in[$k] = post($k);
    }
    if (post('website') !== '') {           // pot de miel anti-robots
        redirect('/');
    }
    [$order, $errors] = order_create($in);
    if ($order) {
        redirect('/commande/' . strtolower($order['ref']) . '?k=' . $order['access_key']);
    }
}
if (!is_static() && !$cart['items'] && !$errors) {
    redirect('/panier');
}
page_meta(['title' => 'Commande', 'robots' => 'noindex', 'no_cta' => true]);
$pays = payment_methods();
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field__err">' . e($errors[$k]) . '</span>' : '';
?>
<section class="page-hero page-hero--compact">
  <div class="container">
    <p class="eyebrow">Paiement sécurisé en dinars</p>
    <h1 class="h1">Finaliser la <em>commande.</em></h1>
    <ol class="progress-steps" aria-label="Étapes"><li class="is-done">Panier</li><li class="is-current">Coordonnées &amp; paiement</li><li>Confirmation</li></ol>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <?php if (isset($errors['cart'])): ?><div class="flash flash--error"><?= e($errors['cart']) ?> <a href="/boutique">Retour à la boutique</a></div><?php endif; ?>
    <form class="checkout" method="post" action="/commande" novalidate data-checkout<?= is_static() ? ' data-static-checkout' : '' ?>>
      <?= csrf_field() ?>
      <div class="hp" aria-hidden="true"><label>Site web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <div class="checkout__main">
        <fieldset class="panel glass">
          <legend class="panel__title"><span>1</span> Vos coordonnées</legend>
          <div class="fields">
            <label class="field field--full<?= isset($errors['name']) ? ' has-err' : '' ?>"><span>Nom complet</span><input type="text" name="name" value="<?= e($in['name']) ?>" required autocomplete="name" maxlength="160"><?= $err('name') ?></label>
            <label class="field<?= isset($errors['email']) ? ' has-err' : '' ?>"><span>E-mail</span><input type="email" name="email" value="<?= e($in['email']) ?>" required autocomplete="email" maxlength="190"><?= $err('email') ?></label>
            <label class="field<?= isset($errors['phone']) ? ' has-err' : '' ?>"><span>Téléphone / WhatsApp</span><input type="tel" name="phone" value="<?= e($in['phone']) ?>" required autocomplete="tel" placeholder="+216 …" maxlength="20"><?= $err('phone') ?></label>
            <label class="field field--full"><span>Précisions (optionnel)</span><textarea name="note" rows="3" maxlength="1000" placeholder="E-mail du compte à activer, préférences…"><?= e($in['note']) ?></textarea></label>
          </div>
        </fieldset>

        <fieldset class="panel glass">
          <legend class="panel__title"><span>2</span> Moyen de paiement</legend>
          <?= $err('payment') ?>
          <div class="paygrid">
            <?php foreach ($pays as $k => $pm): ?>
            <label class="pay">
              <input type="radio" name="payment" value="<?= e($k) ?>"<?= $in['payment'] === $k ? ' checked' : '' ?>>
              <span class="pay__box"><span class="pay__ico"><?= icon($pm['icon']) ?></span><span><strong><?= e($pm['label']) ?></strong><small><?= e($pm['desc']) ?></small></span><span class="pay__check"><?= icon('check') ?></span></span>
            </label>
            <?php endforeach; ?>
          </div>
          <p class="panel__note"><?= icon('shield') ?> Les instructions de paiement s’affichent après validation. Votre commande est livrée dès réception du paiement.</p>
        </fieldset>

        <label class="terms<?= isset($errors['terms']) ? ' has-err' : '' ?>">
          <input type="checkbox" name="terms" value="1"<?= $in['terms'] ? ' checked' : '' ?> required>
          <span class="terms__box"><?= icon('check') ?></span>
          <span>J’accepte les <a href="/cgv" target="_blank">conditions générales de vente</a> et je demande l’exécution immédiate de la prestation digitale.</span>
        </label>
        <?= $err('terms') ?>
      </div>

      <aside class="summary glass">
        <h2>Votre commande</h2>
        <ul class="summary__items" data-static-items>
          <?php foreach ($cart['items'] as $it): ?>
          <li><span class="pthumb pthumb--xs th-<?= e($it['theme']) ?>"><span><?= e($it['glyph']) ?></span></span><span class="summary__name"><?= e($it['name']) ?><small><?= e($it['option']) ?> × <?= (int) $it['qty'] ?></small></span><strong><?= money($it['line']) ?></strong></li>
          <?php endforeach; ?>
        </ul>
        <div class="summary__row"><span>Livraison digitale</span><span>Offerte</span></div>
        <div class="summary__total"><span>Total</span><strong data-static-total><?= money($cart['total']) ?></strong></div>
        <button class="btn btn--gold btn--block btn--lg" type="submit" data-magnetic><?= icon('lock') ?><span>Valider la commande</span></button>
        <p class="summary__note">Aucun prélèvement automatique : vous réglez à l’étape suivante.</p>
      </aside>
    </form>
  </div>
</section>
