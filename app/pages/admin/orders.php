<?php
require_admin();
page_meta(['layout' => 'admin', 'title' => 'Commandes']);
$status = get('statut');
$q = get('q');
$where = [];
$args = [];
if (isset(ORDER_STATUSES[$status])) {
    $where[] = 'status = ?';
    $args[] = $status;
}
if ($q !== '') {
    $where[] = '(ref LIKE ? OR LOWER(customer_name) LIKE ? OR LOWER(email) LIKE ? OR phone LIKE ?)';
    $like = '%' . mb_strtolower($q) . '%';
    array_push($args, '%' . strtoupper($q) . '%', $like, $like, '%' . $q . '%');
}
$orders = db_all('SELECT * FROM orders' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC LIMIT 300', $args);
$counts = [];
foreach (db_all('SELECT status, COUNT(*) AS n FROM orders GROUP BY status') as $r) {
    $counts[$r['status']] = (int) $r['n'];
}
$pays = payment_methods();
?>
<div class="toolbar">
  <nav class="tabs">
    <a class="<?= $status === '' ? 'is-active' : '' ?>" href="/admin/commandes">Toutes <em><?= array_sum($counts) ?></em></a>
    <?php foreach (ORDER_STATUSES as $k => [$label]): ?>
    <a class="<?= $status === $k ? 'is-active' : '' ?>" href="/admin/commandes?statut=<?= $k ?>"><?= e($label) ?> <em><?= $counts[$k] ?? 0 ?></em></a>
    <?php endforeach; ?>
  </nav>
  <form class="search" method="get"><?php if ($status): ?><input type="hidden" name="statut" value="<?= e($status) ?>"><?php endif; ?><?= icon('search') ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Réf., nom, e-mail, téléphone…"></form>
</div>
<section class="card">
  <?php if (!$orders): ?><p class="muted pad">Aucune commande ne correspond.</p><?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Réf.</th><th>Client</th><th>Contact</th><th>Articles</th><th>Total</th><th>Paiement</th><th>Statut</th><th>Date</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
      <tr data-href="/admin/commandes/<?= (int) $o['id'] ?>">
        <td><a href="/admin/commandes/<?= (int) $o['id'] ?>"><strong><?= e($o['ref']) ?></strong></a></td>
        <td><?= e($o['customer_name']) ?></td>
        <td class="muted"><?= e($o['email']) ?><br><?= e($o['phone']) ?></td>
        <td><?= (int) $o['items_count'] ?></td>
        <td><strong><?= money((int) $o['total']) ?></strong></td>
        <td><?= e($pays[$o['payment_method']]['label'] ?? $o['payment_method']) ?></td>
        <td><?= status_badge($o['status']) ?></td>
        <td class="muted"><?= e(fmt_date($o['created_at'], true)) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
