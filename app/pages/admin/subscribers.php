<?php
require_admin();
$rows = db_all('SELECT * FROM subscribers ORDER BY id DESC');
if (get('export') === 'csv') {
    page_meta(['layout' => false]);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="sargora-newsletter-' . date('Y-m-d') . '.csv"');
    echo "\xEF\xBB\xBF" . "email;date\n";
    foreach ($rows as $r) {
        echo str_replace([';', "\n"], '', $r['email']) . ';' . $r['created_at'] . "\n";
    }
    return;
}
page_meta(['layout' => 'admin', 'title' => 'Newsletter']);
?>
<div class="toolbar">
  <p class="muted"><?= count($rows) ?> abonné(s) inscrits via le pied de page du site.</p>
  <?php if ($rows): ?><a class="b" href="/admin/abonnes?export=csv"><?= icon('download') ?> Exporter en CSV</a><?php endif; ?>
</div>
<section class="card">
  <?php if (!$rows): ?><p class="muted pad">Aucun abonné pour l’instant.</p><?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>E-mail</th><th>Inscription</th></tr></thead>
    <tbody><?php foreach ($rows as $r): ?><tr><td><?= e($r['email']) ?></td><td class="muted"><?= e(fmt_date($r['created_at'], true)) ?></td></tr><?php endforeach; ?></tbody>
  </table></div>
  <?php endif; ?>
</section>
