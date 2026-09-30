<?php
/**
 * Création du premier administrateur depuis le navigateur (hébergement sans terminal).
 * Active uniquement si aucun admin n'existe ET si SETUP_KEY est défini dans app/config.local.php.
 */
if ((int) db_val('SELECT COUNT(*) FROM admins') > 0) {
    return 404;
}
page_meta(['layout' => 'admin', 'title' => 'Installation', 'bare' => true]);
$key = defined('SETUP_KEY') ? (string) SETUP_KEY : '';
$errors = [];
if (is_post() && $key !== '') {
    require_csrf();
    if (!hash_equals($key, post('key'))) $errors[] = 'Code d’installation incorrect.';
    if (!valid_email(post('email'))) $errors[] = 'E-mail invalide.';
    if (mb_strlen((string) ($_POST['password'] ?? '')) < 10) $errors[] = 'Le mot de passe doit contenir au moins 10 caractères.';
    if (!$errors) {
        admin_create(post('email'), post('name') ?: 'Administrateur', (string) $_POST['password']);
        admin_login(post('email'), (string) $_POST['password']);
        flash('success', 'Compte administrateur créé. Bienvenue !');
        redirect('/admin');
    }
}
?>
<div class="login">
  <form class="login__card" method="post">
    <?= csrf_field() ?>
    <img class="login__logo" src="<?= asset('img/brand/emblem-256.webp') ?>" alt="" width="96" height="96">
    <h1>SARGORA <small>Installation</small></h1>
    <?php if ($key === ''): ?>
      <div class="alert alert--info">Pour activer cette page, ajoutez <code>define('SETUP_KEY', 'un-code-secret');</code> dans <code>app/config.local.php</code>, puis rechargez.</div>
    <?php else: ?>
      <?php foreach ($errors as $er): ?><div class="alert alert--error"><?= e($er) ?></div><?php endforeach; ?>
      <label class="f"><span>Code d’installation</span><input type="password" name="key" required></label>
      <label class="f"><span>Nom</span><input type="text" name="name" value="<?= e(post('name')) ?>" placeholder="Administrateur"></label>
      <label class="f"><span>E-mail</span><input type="email" name="email" value="<?= e(post('email')) ?>" required></label>
      <label class="f"><span>Mot de passe (10 caractères min.)</span><input type="password" name="password" required minlength="10" autocomplete="new-password"></label>
      <button class="b b--gold b--block" type="submit">Créer le compte</button>
    <?php endif; ?>
  </form>
</div>
