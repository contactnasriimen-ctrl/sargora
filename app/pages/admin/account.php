<?php
$u = require_admin();
$errors = [];
if (is_post()) {
    require_csrf();
    $row = db_one('SELECT * FROM admins WHERE id = ?', [$u['id']]);
    $new = (string) ($_POST['new'] ?? '');
    if (!password_verify((string) ($_POST['current'] ?? ''), $row['password_hash'])) $errors[] = 'Mot de passe actuel incorrect.';
    if (mb_strlen($new) < 10) $errors[] = 'Le nouveau mot de passe doit contenir au moins 10 caractères.';
    if ($new !== (string) ($_POST['confirm'] ?? '')) $errors[] = 'Les deux mots de passe ne correspondent pas.';
    $name = post('name') ?: $row['name'];
    if (!$errors) {
        db_exec('UPDATE admins SET password_hash = ?, name = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $name, $u['id']]);
        session_regenerate_id(true);
        flash('success', 'Mot de passe mis à jour.');
        redirect('/admin/compte');
    }
}
page_meta(['layout' => 'admin', 'title' => 'Mon compte']);
?>
<?php foreach ($errors as $er): ?><div class="alert alert--error"><?= e($er) ?></div><?php endforeach; ?>
<form class="card pad narrow" method="post">
  <?= csrf_field() ?>
  <h2>Identifiants</h2>
  <p class="muted">Connecté en tant que <strong><?= e($u['email']) ?></strong>.</p>
  <label class="f"><span>Nom affiché</span><input type="text" name="name" value="<?= e($u['name']) ?>" maxlength="120"></label>
  <label class="f"><span>Mot de passe actuel</span><input type="password" name="current" required autocomplete="current-password"></label>
  <label class="f"><span>Nouveau mot de passe (10 caractères min.)</span><input type="password" name="new" required minlength="10" autocomplete="new-password"></label>
  <label class="f"><span>Confirmer</span><input type="password" name="confirm" required minlength="10" autocomplete="new-password"></label>
  <button class="b b--gold" type="submit">Mettre à jour</button>
</form>
