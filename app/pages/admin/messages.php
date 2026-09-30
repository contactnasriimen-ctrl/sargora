<?php
require_admin();
page_meta(['layout' => 'admin', 'title' => 'Demandes & devis']);
$rows = db_all('SELECT * FROM messages ORDER BY is_read ASC, id DESC LIMIT 300');
?>
<section class="card">
  <?php if (!$rows): ?><p class="muted pad">Aucune demande pour l’instant. Les formulaires de contact et de devis arrivent ici.</p><?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th></th><th>Contact</th><th>Services</th><th>Budget</th><th>Message</th><th>Date</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $m): ?>
      <tr data-href="/admin/messages/<?= (int) $m['id'] ?>" class="<?= $m['is_read'] ? '' : 'is-new' ?>">
        <td><?= $m['is_read'] ? '' : '<i class="dot"></i>' ?></td>
        <td><a href="/admin/messages/<?= (int) $m['id'] ?>"><strong><?= e($m['name']) ?></strong></a><br><small class="muted"><?= e($m['company'] ?: $m['email']) ?></small></td>
        <td><?= e($m['services'] ?: '—') ?></td>
        <td><?= e($m['budget'] ?: '—') ?></td>
        <td class="muted"><?= e(excerpt($m['message'], 70)) ?></td>
        <td class="muted"><?= e(fmt_date($m['created_at'], true)) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
