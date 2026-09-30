<?php
$svcs = services();
$budgets = ['< 1 000 DT', '1 000 – 3 000 DT', '3 000 – 7 000 DT', '7 000 DT +', 'À définir'];
$timelines = ['Urgent (< 2 semaines)', 'Dans le mois', '1 à 3 mois', 'Pas encore défini'];
$errors = [];
$sent = false;
$in = ['name' => '', 'email' => '', 'phone' => '', 'company' => '', 'budget' => '', 'timeline' => '', 'message' => ''];
$picked = array_filter([get('service')], fn($s) => isset($svcs[$s]));

if (is_post()) {
    require_csrf();
    foreach (array_keys($in) as $k) {
        $in[$k] = post($k);
    }
    $picked = array_values(array_filter((array) ($_POST['services'] ?? []), fn($s) => is_string($s) && isset($svcs[$s])));
    if (post('website') !== '') {
        redirect('/contact');
    }
    if (mb_strlen($in['name']) < 2) $errors['name'] = 'Indiquez votre nom.';
    if (!valid_email($in['email'])) $errors['email'] = 'Adresse e-mail invalide.';
    if ($in['phone'] !== '' && !valid_phone($in['phone'])) $errors['phone'] = 'Numéro invalide.';
    if (mb_strlen($in['message']) < 10) $errors['message'] = 'Décrivez votre projet en quelques mots (10 caractères minimum).';
    $recent = (int) db_val('SELECT COUNT(*) FROM messages WHERE ip = ? AND created_at >= ?', [client_ip(), date('Y-m-d H:i:s', time() - 3600)]);
    if ($recent >= 5) $errors['message'] = 'Trop de messages envoyés. Réessayez plus tard ou contactez-nous sur WhatsApp.';
    if (!$errors) {
        db_insert('messages', [
            'kind' => 'devis', 'name' => mb_substr($in['name'], 0, 160), 'email' => mb_strtolower($in['email']), 'phone' => mb_substr($in['phone'], 0, 40),
            'company' => mb_substr($in['company'], 0, 160), 'services' => implode(', ', array_map(fn($s) => $svcs[$s]['long'], $picked)),
            'budget' => in_array($in['budget'], $budgets, true) ? $in['budget'] : '', 'timeline' => in_array($in['timeline'], $timelines, true) ? $in['timeline'] : '',
            'message' => mb_substr($in['message'], 0, 5000), 'ip' => client_ip(), 'created_at' => now(),
        ]);
        $sent = true;
    }
}
page_meta(['title' => 'Contact & devis', 'description' => 'Parlez-nous de votre projet de site web, branding, sponsoring ou marketing digital : devis gratuit sous 24 h.', 'no_cta' => true]);
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field__err">' . e($errors[$k]) . '</span>' : '';
?>
<section class="page-hero page-hero--compact">
  <div class="container">
    <p class="eyebrow" data-reveal>Contact</p>
    <h1 class="h1" data-split>Allumons votre <em>projet.</em></h1>
    <p class="lead page-hero__lead" data-reveal>Racontez-nous votre ambition : nous revenons vers vous sous 24 h avec des premières idées et un devis gratuit.</p>
  </div>
  <div class="page-hero__glow" aria-hidden="true"></div>
</section>

<section class="section section--tight">
  <div class="container contact">
    <aside class="contact__aside">
      <a class="contact__card glass" href="mailto:<?= e(SITE_EMAIL) ?>" data-reveal><span class="contact__ico"><?= icon('mail') ?></span><span><small>E-mail</small><strong><?= e(SITE_EMAIL) ?></strong></span></a>
      <a class="contact__card glass" href="<?= e(whatsapp_link('Bonjour Sargora !')) ?>" target="_blank" rel="noopener" data-reveal><span class="contact__ico"><?= icon('whatsapp') ?></span><span><small>WhatsApp</small><strong><?= e(SITE_PHONE) ?></strong></span></a>
      <a class="contact__card glass" href="tel:<?= e(preg_replace('/[^0-9+]/', '', SITE_PHONE)) ?>" data-reveal><span class="contact__ico"><?= icon('phone') ?></span><span><small>Téléphone</small><strong><?= e(SITE_PHONE) ?></strong></span></a>
      <div class="contact__card glass" data-reveal><span class="contact__ico"><?= icon('pin') ?></span><span><small>Adresse</small><strong><?= e(SITE_ADDRESS) ?></strong><small><?= e(SITE_HOURS) ?></small></span></div>
      <div class="contact__promise" data-reveal>
        <p class="eyebrow">Notre promesse</p>
        <ul class="checks">
          <li><?= icon('check') ?> Réponse sous 24 h ouvrées</li>
          <li><?= icon('check') ?> Devis clair et gratuit</li>
          <li><?= icon('check') ?> Confidentialité de vos informations</li>
        </ul>
      </div>
    </aside>

    <div class="contact__main">
    <?php if ($sent): ?>
      <div class="panel glass sent" data-reveal>
        <span class="order-hero__icon is-celebrate" data-celebrate="1"><svg viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="m15 27 7.5 7.5L38 19"/></svg></span>
        <h2 class="h2">Message <em>bien reçu.</em></h2>
        <p class="lead">Merci <?= e($in['name']) ?> ! Un membre de l’équipe vous répond sous 24 h ouvrées. Pour aller plus vite, écrivez-nous aussi sur WhatsApp.</p>
        <a class="btn btn--ghost" href="<?= e(whatsapp_link('Bonjour Sargora, je viens d’envoyer une demande de devis.')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?><span>Continuer sur WhatsApp</span></a>
      </div>
    <?php else: ?>
      <form class="panel glass devis" method="post" action="/contact" novalidate<?= is_static() ? ' data-static-contact' : '' ?>>
        <?= csrf_field() ?>
        <div class="hp" aria-hidden="true"><label>Site web <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <fieldset class="devis__group">
          <legend>De quoi avez-vous besoin ?</legend>
          <div class="chips">
            <?php foreach ($svcs as $slug => $s): ?>
            <label class="chip"><input type="checkbox" name="services[]" value="<?= e($slug) ?>"<?= in_array($slug, $picked, true) ? ' checked' : '' ?>><span><?= icon($s['icon']) ?><?= e($s['long']) ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <fieldset class="devis__group">
          <legend>Budget envisagé</legend>
          <div class="chips">
            <?php foreach ($budgets as $b): ?><label class="chip chip--sm"><input type="radio" name="budget" value="<?= e($b) ?>"<?= $in['budget'] === $b ? ' checked' : '' ?>><span><?= e($b) ?></span></label><?php endforeach; ?>
          </div>
        </fieldset>
        <fieldset class="devis__group">
          <legend>Délai souhaité</legend>
          <div class="chips">
            <?php foreach ($timelines as $t): ?><label class="chip chip--sm"><input type="radio" name="timeline" value="<?= e($t) ?>"<?= $in['timeline'] === $t ? ' checked' : '' ?>><span><?= e($t) ?></span></label><?php endforeach; ?>
          </div>
        </fieldset>
        <div class="fields">
          <label class="field<?= isset($errors['name']) ? ' has-err' : '' ?>"><span>Nom complet *</span><input type="text" name="name" value="<?= e($in['name']) ?>" required autocomplete="name" maxlength="160"><?= $err('name') ?></label>
          <label class="field"><span>Entreprise</span><input type="text" name="company" value="<?= e($in['company']) ?>" autocomplete="organization" maxlength="160"></label>
          <label class="field<?= isset($errors['email']) ? ' has-err' : '' ?>"><span>E-mail *</span><input type="email" name="email" value="<?= e($in['email']) ?>" required autocomplete="email" maxlength="190"><?= $err('email') ?></label>
          <label class="field<?= isset($errors['phone']) ? ' has-err' : '' ?>"><span>Téléphone</span><input type="tel" name="phone" value="<?= e($in['phone']) ?>" autocomplete="tel" maxlength="20"><?= $err('phone') ?></label>
          <label class="field field--full<?= isset($errors['message']) ? ' has-err' : '' ?>"><span>Votre projet *</span><textarea name="message" rows="5" required maxlength="5000" placeholder="Objectifs, public, inspirations, échéances…"><?= e($in['message']) ?></textarea><?= $err('message') ?></label>
        </div>
        <button class="btn btn--gold btn--lg" type="submit" data-magnetic><span>Envoyer ma demande</span><?= icon('arrow-up-right') ?></button>
      </form>
    <?php endif; ?>
    </div>
  </div>
</section>
