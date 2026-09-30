<?php
$o = order_by_ref(strtoupper((string) $params['slug']));
$mine = $o && in_array($o['ref'], (array) ($_SESSION['my_orders'] ?? []), true);
if (!$o || (!$mine && !hash_equals($o['access_key'], get('k')))) {
    return 404;
}
page_meta(['title' => 'Commande ' . $o['ref'], 'robots' => 'noindex', 'no_cta' => true]);
$pm = payment_methods()[$o['payment_method']] ?? null;
$steps = ['pending' => 1, 'paid' => 2, 'delivered' => 3, 'cancelled' => 0];
$cur = $steps[$o['status']] ?? 1;
$fresh = $mine && (time() - strtotime($o['created_at'])) < 1800;
?>
<section class="order-hero">
  <div class="container order-hero__inner">
    <?php if ($o['status'] === 'cancelled'): ?>
      <span class="order-hero__icon order-hero__icon--off"><?= icon('close') ?></span>
      <h1 class="h1">Commande <em>annulée.</em></h1>
      <p class="lead">Cette commande a été annulée. Une question ? Écrivez-nous sur WhatsApp.</p>
    <?php else: ?>
      <span class="order-hero__icon<?= $fresh ? ' is-celebrate' : '' ?>" data-celebrate="<?= $fresh ? '1' : '0' ?>"><svg viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="m15 27 7.5 7.5L38 19"/></svg></span>
      <h1 class="h1"><?= $o['status'] === 'delivered' ? 'Commande <em>livrée.</em>' : ($o['status'] === 'paid' ? 'Paiement <em>confirmé.</em>' : 'Merci, commande <em>reçue !</em>') ?></h1>
      <p class="lead"><?= $o['status'] === 'pending' ? 'Réglez votre commande en suivant les instructions ci-dessous : nous la livrons dès réception du paiement.' : ($o['status'] === 'paid' ? 'Nous préparons votre activation. Vous serez notifié(e) dès qu’elle est prête.' : 'Vos accès sont disponibles ci-dessous. Bonne utilisation !') ?></p>
    <?php endif; ?>
    <div class="order-ref">
      <span>Référence</span>
      <strong data-copy-text><?= e($o['ref']) ?></strong>
      <button class="icon-btn icon-btn--sm" type="button" data-copy aria-label="Copier la référence"><?= icon('copy') ?></button>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container order-grid">
    <div class="order-main">
      <?php if ($o['status'] !== 'cancelled'): ?>
      <ol class="timeline">
        <?php foreach (['Commande reçue', 'Paiement confirmé', 'Livrée'] as $i => $label): ?>
        <li class="<?= $i + 1 < $cur ? 'is-done' : ($i + 1 === $cur ? 'is-current' : '') ?>"><span class="timeline__dot"><?= icon('check') ?></span><?= e($label) ?></li>
        <?php endforeach; ?>
      </ol>
      <?php endif; ?>

      <?php if ($o['status'] === 'delivered' && trim((string) $o['delivery_info']) !== ''): ?>
      <div class="panel glass panel--gold">
        <h2 class="panel__title"><?= icon('gift') ?> Vos accès &amp; instructions</h2>
        <div class="delivery"><?= nl2br(e((string) $o['delivery_info'])) ?></div>
      </div>
      <?php endif; ?>

      <?php if ($o['status'] === 'pending' && $pm): ?>
      <div class="panel glass">
        <h2 class="panel__title"><?= icon($pm['icon']) ?> Payer par <?= e($pm['label']) ?></h2>
        <p class="pay-amount">Montant à régler : <strong><?= money((int) $o['total']) ?></strong></p>
        <ol class="pay-steps">
          <?php foreach ($pm['steps'] as $st): ?><li><?= e($st) ?></li><?php endforeach; ?>
        </ol>
        <a class="btn btn--gold btn--lg" href="<?= e(whatsapp_link(order_whatsapp_text($o))) ?>" target="_blank" rel="noopener" data-magnetic><?= icon('whatsapp') ?><span>Envoyer ma preuve de paiement</span></a>
      </div>
      <?php endif; ?>

      <p class="order-help"><?= icon('eye') ?> Conservez ce lien ou retrouvez votre commande à tout moment sur la page <a href="/suivi">Suivi de commande</a> avec votre référence et votre e-mail.</p>
    </div>

    <aside class="summary glass">
      <h2>Détail</h2>
      <ul class="summary__items">
        <?php foreach ($o['items'] as $it): ?>
        <li><span class="summary__name"><?= e($it['product_name']) ?><small><?= e((string) $it['option_label']) ?> × <?= (int) $it['qty'] ?></small></span><strong><?= money((int) $it['line_total']) ?></strong></li>
        <?php endforeach; ?>
      </ul>
      <div class="summary__total"><span>Total</span><strong><?= money((int) $o['total']) ?></strong></div>
      <dl class="summary__meta">
        <div><dt>Statut</dt><dd><?= status_badge($o['status']) ?></dd></div>
        <div><dt>Date</dt><dd><?= e(fmt_date($o['created_at'], true)) ?></dd></div>
        <div><dt>Paiement</dt><dd><?= e($pm['label'] ?? $o['payment_method']) ?></dd></div>
        <div><dt>Client</dt><dd><?= e($o['customer_name']) ?></dd></div>
      </dl>
    </aside>
  </div>
</section>
