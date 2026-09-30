<?php
page_meta(['layout' => 'admin', 'title' => 'Connexion', 'bare' => true]);
header('X-Robots-Tag: noindex, nofollow');
if (admin_user()) {
    redirect('/admin');
}
$noAdmin = !(int) db_val('SELECT COUNT(*) FROM admins');
$error = '';
$email = '';
if (is_post()) {
    require_csrf();
    $email = post('email');
    if (login_blocked()) {
        $error = 'Trop de tentatives. Réessayez dans 15 minutes.';
    } elseif (admin_login($email, (string) ($_POST['password'] ?? ''))) {
        $r = get('r');
        redirect(preg_match('#^/admin(/|$|\?)#', $r) ? $r : '/admin');
    } else {
        usleep(400000);
        $error = 'E-mail ou mot de passe incorrect.';
    }
}
?>
<div class="login">
  <form class="login__card" method="post">
    <?= csrf_field() ?>
    <img class="login__logo" src="<?= asset('img/brand/emblem-256.webp') ?>" alt="" width="96" height="96">
    <h1>SARGORA <small>Administration</small></h1>
    <?php if ($noAdmin): ?>
      <div class="alert alert--info">Aucun compte administrateur n’existe encore. Lancez <code>php tools/create_admin.php</code> ou utilisez la page <a href="/admin/installation">d’installation</a>.</div>
    <?php endif; ?>
    <?php if ($error): ?><div class="alert alert--error"><?= e($error) ?></div><?php endif; ?>
    <label class="f"><span>E-mail</span><input type="email" name="email" value="<?= e($email) ?>" required autocomplete="username" autofocus></label>
    <label class="f"><span>Mot de passe</span><input type="password" name="password" required autocomplete="current-password"></label>
    <button class="b b--gold b--block" type="submit">Se connecter</button>
    <a class="login__back" href="/">← Retour au site</a>
  </form>
</div>
