<?php
require_admin();
page_meta(['layout' => 'admin', 'title' => 'Tableau de bord']);
$monthStart = date('Y-m-01 00:00:00');
$revenue = (int) db_val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('paid','delivered') AND created_at >= ?", [$monthStart]);
$revenueAll = (int) db_val("SELECT COALESCE(SUM(total),0) FROM orders WHERE status IN ('paid','delivered')");
$toPay = (int) db_val("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
$toDeliver = (int) db_val("SELECT COUNT(*) FROM orders WHERE status = 'paid'");
$unread = (int) db_val('SELECT COUNT(*) FROM messages WHERE is_read = 0');
$activeProducts = (int) db_val('SELECT COUNT(*) FROM products WHERE active = 1');
$latest = db_all('SELECT * FROM orders ORDER BY id DESC LIMIT 8');
$msgs = db_all('SELECT * FROM messages ORDER BY id DESC LIMIT 5');
$top = db_all("SELECT oi.product_name, SUM(oi.qty) AS q, SUM(oi.line_total) AS t FROM order_items oi JOIN orders o ON o.id = oi.order_id
               WHERE o.status IN ('paid','delivered') GROUP BY oi.product_name ORDER BY t DESC LIMIT 5");
?>
<div class="kpis">
  <div class="kpi kpi--gold"><span><?= icon('chart') ?> CA encaissé ce mois</span><strong><?= money($revenue) ?></strong><small>Total : <?= money($revenueAll) ?></small></div>
  <a class="kpi" href="/admin/commandes?statut=pending"><span><?= icon('clock') ?> En attente de paiement</span><strong><?= $toPay ?></strong><small>commandes à encaisser</small></a>
  <a class="kpi" href="/admin/commandes?statut=paid"><span><?= icon('zap') ?> À livrer</span><strong><?= $toDeliver ?></strong><small>paiements confirmés</small></a>
  <a class="kpi" href="/admin/messages"><span><?= icon('inbox') ?> Demandes non lues</span><strong><?= $unread ?></strong><small><?= $activeProducts ?> produits en ligne</small></a>
</div>

<div class="cols">
  <section class="card">
    <div class="card__head"><h2>Dernières commandes</h2><a href="/admin/commandes">Tout voir →</a></div>
    <?php if (!$latest): ?><p class="muted pad">Aucune commande pour l’instant. Elles apparaîtront ici dès le premier achat.</p><?php else: ?>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Réf.</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th></tr></thead>
      <tbody>
      <?php foreach ($latest as $o): ?>
        <tr data-href="/admin/commandes/<?= (int) $o['id'] ?>">
          <td><a href="/admin/commandes/<?= (int) $o['id'] ?>"><strong><?= e($o['ref']) ?></strong></a></td>
          <td><?= e($o['customer_name']) ?></td>
          <td><?= money((int) $o['total']) ?></td>
          <td><?= status_badge($o['status']) ?></td>
          <td class="muted"><?= e(fmt_date($o['created_at'], true)) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php endif; ?>
  </section>

  <div class="stackc">
    <section class="card">
      <div class="card__head"><h2>Meilleures ventes</h2></div>
      <?php if (!$top): ?><p class="muted pad">Pas encore de ventes encaissées.</p><?php else: ?>
      <ul class="list">
        <?php foreach ($top as $t): ?><li><span><?= e($t['product_name']) ?> <small class="muted">× <?= (int) $t['q'] ?></small></span><strong><?= money((int) $t['t']) ?></strong></li><?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </section>
    <section class="card">
      <div class="card__head"><h2>Dernières demandes</h2><a href="/admin/messages">Tout voir →</a></div>
      <?php if (!$msgs): ?><p class="muted pad">Aucune demande reçue.</p><?php else: ?>
      <ul class="list">
        <?php foreach ($msgs as $m): ?><li><a href="/admin/messages/<?= (int) $m['id'] ?>"><?= !$m['is_read'] ? '<i class="dot"></i>' : '' ?><?= e($m['name']) ?> <small class="muted"><?= e(excerpt($m['services'] ?: $m['message'], 40)) ?></small></a><small class="muted"><?= e(fmt_date($m['created_at'])) ?></small></li><?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </section>
  </div>
</div>
