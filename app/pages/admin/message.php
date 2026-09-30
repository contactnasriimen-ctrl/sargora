<?php
require_admin();
$m = db_one('SELECT * FROM messages WHERE id = ?', [(int) $params['id']]);
if (!$m) {
    return 404;
}
if (is_post()) {
    require_csrf();
    if (post('action') === 'delete') {
        db_exec('DELETE FROM messages WHERE id = ?', [$m['id']]);
        flash('success', 'Demande supprimée.');
        redirect('/admin/messages');
    }
    db_exec('UPDATE messages SET is_read = 0 WHERE id = ?', [$m['id']]);
    redirect('/admin/messages');
}
if (!$m['is_read']) {
    db_exec('UPDATE messages SET is_read = 1 WHERE id = ?', [$m['id']]);
}
page_meta(['layout' => 'admin', 'title' => 'Demande de ' . $m['name']]);
$reply = 'Bonjour ' . $m['name'] . ', merci pour votre demande auprès de Sargora.';
?>
<a class="back" href="/admin/messages">← Toutes les demandes</a>
<div class="cols">
  <section class="card pad">
    <h2><?= e($m['services'] ?: 'Demande de contact') ?></h2>
    <p class="note"><?= nl2br(e($m['message'])) ?></p>
    <div class="row">
      <a class="b b--gold" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Votre projet avec Sargora') ?>"><?= icon('mail') ?> Répondre par e-mail</a>
      <?php if ($m['phone']): ?><a class="b" href="https://wa.me/<?= e(preg_replace('/\D+/', '', $m['phone'])) ?>?text=<?= rawurlencode($reply) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp</a><?php endif; ?>
    </div>
  </section>
  <div class="stackc">
    <section class="card pad">
      <h2>Contact</h2>
      <dl class="dl">
        <div><dt>Nom</dt><dd><?= e($m['name']) ?></dd></div>
        <div><dt>Entreprise</dt><dd><?= e($m['company'] ?: '—') ?></dd></div>
        <div><dt>E-mail</dt><dd><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></dd></div>
        <div><dt>Téléphone</dt><dd><?= e($m['phone'] ?: '—') ?></dd></div>
        <div><dt>Budget</dt><dd><?= e($m['budget'] ?: '—') ?></dd></div>
        <div><dt>Délai</dt><dd><?= e($m['timeline'] ?: '—') ?></dd></div>
        <div><dt>Reçue le</dt><dd><?= e(fmt_date($m['created_at'], true)) ?></dd></div>
      </dl>
    </section>
    <form class="card pad row" method="post">
      <?= csrf_field() ?>
      <button class="b" type="submit" name="action" value="unread">Marquer non lue</button>
      <button class="b b--danger" type="submit" name="action" value="delete" data-confirm-btn="Supprimer cette demande ?"><?= icon('trash') ?> Supprimer</button>
    </form>
  </div>
</div>
