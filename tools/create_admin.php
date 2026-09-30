<?php
/**
 * Crée (ou réinitialise) un compte administrateur.
 *   php tools/create_admin.php                      -> admin@sargora.com + mot de passe aléatoire
 *   php tools/create_admin.php email@exemple.tn     -> e-mail choisi + mot de passe aléatoire
 *   php tools/create_admin.php email@exemple.tn MotDePasse
 */
if (PHP_SAPI !== 'cli') { exit; }
require __DIR__ . '/../app/bootstrap.php';
$email = strtolower($argv[1] ?? 'admin@sargora.com');
$pass = $argv[2] ?? random_password();
if (!valid_email($email) || strlen($pass) < 10) { fwrite(STDERR, "E-mail invalide ou mot de passe < 10 caractères.\n"); exit(1); }
$row = db_one('SELECT id FROM admins WHERE email = ?', [$email]);
if ($row) {
    db_exec('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $row['id']]);
    echo "Mot de passe réinitialisé.\n";
} else {
    admin_create($email, 'Administrateur', $pass);
    echo "Compte administrateur créé.\n";
}
echo "  URL      : /admin\n  E-mail   : $email\n  Mot de passe : $pass\n";
