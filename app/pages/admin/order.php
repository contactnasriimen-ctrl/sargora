<?php
require_admin();
$o = db_one('SELECT * FROM orders WHERE id = ?', [(int) $params['id']]);
if (!$o) {
    return 404;
}
if (is_post()) {
    require_csrf();
    if (post('action') === 'delete') {
        db_exec('DELETE FROM order_items WHERE order_id = ?', [$o['id']]);
        db_exec('DELETE FROM orders WHERE id = ?', [$o['id']]);
        flash('success', 'Commande ' . $o['ref'] . ' supprimée.');
        redirect('/admin/commandes');
    }
    $status = post('status');
    db_update('orders', [
        'status' => isset(ORDER_STATUSES[$status]) ? $status : $o['status'],
        'delivery_info' => mb_substr(post('delivery_info'), 0, 5000),
        'admin_note' => mb_substr(post('admin_note'), 0, 5000),
        'updated_at' => now(),
    ], (int) $o['id']);
    flash('success', 'Commande mise à jour.');
    redirect('/admin/commandes/' . $o['id']);
}
$o = order_by_ref($o['ref']);
page_meta(['layout' => 'admin', 'title' => 'Commande ' . $o['ref']]);
$pm = payment_methods()[$o['payment_method']] ?? ['label' => $o['payment_method']];
$clientUrl = abs_url('commande/' . strtolower($o['ref']) . '?k=' . $o['access_key']);
$waMsg = "Bonjour {$o['customer_name']}, votre commande {$o['ref']} chez Sargora ";
$waMsg .= $o['status'] === 'delivered' ? "est livrée ! Retrouvez vos accès ici : $clientUrl" : ($o['status'] === 'paid' ? 'est confirmée, nous préparons votre activation.' : 'est bien enregistrée. Montant à régler : ' . money((int) $o['total']) . '.');
$waLink = 'https://wa.me/' . preg_replace('/\D+/', '', $o['phone']) . '?text=' . rawurlencode($waMsg);
?>
<a class="back" href="/admin/commandes">← Toutes les commandes</a>
<div class="cols">
  <div class="stackc">
    <section class="card">
      <div class="card__head"><h2>Articles</h2><?= status_badge($o['status']) ?></div>
      <div class="table-wrap"><table class="table">
        <thead><tr><th>Produit</th><th>Formule</th><th>Prix unit.</th><th>Qté</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($o['items'] as $it): ?>
          <tr><td><strong><?= e($it['product_name']) ?></strong></td><td><?= e((string) $it['option_label']) ?></td><td><?= money((int) $it['unit_price']) ?></td><td><?= (int) $it['qty'] ?></td><td><?= money((int) $it['line_total']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><td colspan="4">Total</td><td><strong><?= money((int) $o['total']) ?></strong></td></tr></tfoot>
      </table></div>
    </section>

    <form class="card pad" method="post">
      <?= csrf_field() ?>
      <h2>Traitement</h2>
      <div class="status-pick">
        <?php foreach (ORDER_STATUSES as $k => [$label, $tone]): ?>
        <label class="sp sp--<?= $tone ?>"><input type="radio" name="status" value="<?= $k ?>"<?= $o['status'] === $k ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
        <?php endforeach; ?>
      </div>
      <label class="f"><span>Accès &amp; instructions de livraison <small>(visibles par le client sur sa page de commande quand le statut est « Livrée »)</small></span>
        <textarea name="delivery_info" rows="6" placeholder="Ex. : Abonnement activé sur votre compte xxx@gmail.com jusqu’au 28/10. Identifiants : …"><?= e((string) $o['delivery_info']) ?></textarea></label>
      <label class="f"><span>Note interne <small>(jamais visible par le client)</small></span><textarea name="admin_note" rows="3"><?= e((string) $o['admin_note']) ?></textarea></label>
      <div class="row">
        <button class="b b--gold" type="submit">Enregistrer</button>
        <a class="b" href="<?= e($waLink) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> Prévenir le client</a>
      </div>
    </form>
  </div>

  <div class="stackc">
    <section class="card pad">
      <h2>Client</h2>
      <dl class="dl">
        <div><dt>Nom</dt><dd><?= e($o['customer_name']) ?></dd></div>
        <div><dt>E-mail</dt><dd><a href="mailto:<?= e($o['email']) ?>"><?= e($o['email']) ?></a></dd></div>
        <div><dt>Téléphone</dt><dd><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $o['phone'])) ?>"><?= e($o['phone']) ?></a></dd></div>
        <div><dt>Paiement</dt><dd><?= e($pm['label']) ?></dd></div>
        <div><dt>Passée le</dt><dd><?= e(fmt_date($o['created_at'], true)) ?></dd></div>
        <div><dt>Mise à jour</dt><dd><?= e(fmt_date($o['updated_at'], true)) ?></dd></div>
      </dl>
      <?php if (trim((string) $o['note']) !== ''): ?><h3>Précisions du client</h3><p class="note"><?= nl2br(e((string) $o['note'])) ?></p><?php endif; ?>
    </section>
    <section class="card pad">
      <h2>Lien client</h2>
      <p class="muted">Page de suivi privée du client (statut, instructions de paiement, accès livrés) :</p>
      <div class="copy"><input type="text" readonly value="<?= e($clientUrl) ?>" data-copy-input><button class="b" type="button" data-copy-btn><?= icon('copy') ?></button></div>
    </section>
    <form class="card pad danger" method="post" data-confirm="Supprimer définitivement cette commande ?">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete">
      <button class="b b--danger" type="submit"><?= icon('trash') ?> Supprimer la commande</button>
    </form>
  </div>
</div>
