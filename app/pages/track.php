<?php
$error = '';
$ref = strtoupper(get('ref'));
$email = '';
if (is_post()) {
    require_csrf();
    $ref = strtoupper(post('ref'));
    $email = mb_strtolower(post('email'));
    usleep(250000);
    $o = $ref !== '' ? db_one('SELECT ref, email, access_key FROM orders WHERE ref = ?', [$ref]) : null;
    if ($o && hash_equals(mb_strtolower($o['email']), $email)) {
        redirect('/commande/' . strtolower($o['ref']) . '?k=' . $o['access_key']);
    }
    $error = 'Aucune commande ne correspond à cette référence et cet e-mail.';
}
page_meta(['title' => 'Suivi de commande', 'description' => 'Suivez votre commande Sargora et retrouvez vos accès avec votre référence et votre e-mail.', 'no_cta' => true]);
if (is_static()): ?>
<section class="page-hero page-hero--compact">
  <div class="container narrow">
    <p class="eyebrow">Boutique</p>
    <h1 class="h1">Suivi de <em>commande.</em></h1>
    <p class="lead">Envoyez-nous votre référence de commande (ex. SG-26AB1234) sur WhatsApp : nous vous répondons avec son statut et vos accès.</p>
  </div>
</section>
<section class="section section--tight">
  <div class="container narrow">
    <div class="panel glass track">
      <a class="btn btn--gold btn--lg btn--block" href="<?= e(whatsapp_link('Bonjour Sargora, où en est ma commande ? Référence : ')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?><span>Suivre ma commande sur WhatsApp</span></a>
      <p class="panel__note"><?= icon('mail') ?> Ou par e-mail : <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
    </div>
  </div>
</section>
<?php return; endif; ?>
<section class="page-hero page-hero--compact">
  <div class="container narrow">
    <p class="eyebrow">Boutique</p>
    <h1 class="h1">Suivi de <em>commande.</em></h1>
    <p class="lead">Entrez la référence reçue lors de votre commande (ex. SG-26AB1234) et l’e-mail utilisé.</p>
  </div>
</section>
<section class="section section--tight">
  <div class="container narrow">
    <form class="panel glass track" method="post" action="/suivi">
      <?= csrf_field() ?>
      <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
      <div class="fields">
        <label class="field"><span>Référence</span><input type="text" name="ref" value="<?= e($ref) ?>" required placeholder="SG-…" autocomplete="off" maxlength="30"></label>
        <label class="field"><span>E-mail</span><input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email"></label>
      </div>
      <button class="btn btn--gold btn--lg btn--block" type="submit"><?= icon('search') ?><span>Retrouver ma commande</span></button>
      <p class="panel__note"><?= icon('whatsapp') ?> Référence perdue ? <a href="<?= e(whatsapp_link('Bonjour, je souhaite retrouver ma commande.')) ?>" target="_blank" rel="noopener">Écrivez-nous sur WhatsApp</a>.</p>
    </form>
  </div>
</section>
