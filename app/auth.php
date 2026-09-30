<?php
/** Authentification de l'administration. */
declare(strict_types=1);

function admin_user(): ?array
{
    static $u = false;
    if ($u !== false) {
        return $u;
    }
    $id = (int) ($_SESSION['admin_id'] ?? 0);
    $u = $id ? db_one('SELECT id, email, name FROM admins WHERE id = ?', [$id]) : null;
    return $u;
}

function require_admin(): array
{
    $u = admin_user();
    if (!$u) {
        redirect('/admin/connexion?r=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/admin'));
    }
    header('Cache-Control: no-store');
    header('X-Robots-Tag: noindex, nofollow');
    return $u;
}

function login_blocked(): bool
{
    $since = date('Y-m-d H:i:s', time() - 900);
    db_exec('DELETE FROM login_attempts WHERE at < ?', [date('Y-m-d H:i:s', time() - 86400)]);
    return (int) db_val('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND at >= ?', [client_ip(), $since]) >= 8;
}

function admin_login(string $email, string $password): bool
{
    $u = db_one('SELECT * FROM admins WHERE email = ?', [mb_strtolower(trim($email))]);
    if (!$u || !password_verify($password, $u['password_hash'])) {
        db_insert('login_attempts', ['ip' => client_ip(), 'at' => now()]);
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $u['id'];
    db_exec('UPDATE admins SET last_login = ? WHERE id = ?', [now(), $u['id']]);
    db_exec('DELETE FROM login_attempts WHERE ip = ?', [client_ip()]);
    return true;
}

function admin_logout(): void
{
    unset($_SESSION['admin_id']);
    session_regenerate_id(true);
}

function admin_create(string $email, string $name, string $password): int
{
    return db_insert('admins', [
        'email' => mb_strtolower(trim($email)), 'name' => $name,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'created_at' => now(),
    ]);
}

function random_password(int $len = 14): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789@#%';
    $s = '';
    for ($i = 0; $i < $len; $i++) {
        $s .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $s;
}
