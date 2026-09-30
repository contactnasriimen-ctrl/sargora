<?php
page_meta(['layout' => false]);
$ajax = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
if (!is_post()) {
    redirect('/');
}
$email = mb_strtolower(post('email'));
if (!csrf_check()) {
    $res = [false, 'Session expirée, rechargez la page.'];
} elseif (!valid_email($email)) {
    $res = [false, 'Adresse e-mail invalide.'];
} else {
    if (!db_val('SELECT 1 FROM subscribers WHERE email = ?', [$email])) {
        db_insert('subscribers', ['email' => $email, 'created_at' => now()]);
    }
    $res = [true, 'Merci ! Vous recevrez nos meilleures offres.'];
}
if ($ajax) {
    json_out(['ok' => $res[0], 'message' => $res[1]], $res[0] ? 200 : 422);
}
flash($res[0] ? 'success' : 'error', $res[1]);
redirect('/');
