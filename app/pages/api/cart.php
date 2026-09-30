<?php
/** API panier : GET = état, POST action=add|set|remove|clear. Sans JavaScript, redirige vers /panier. */
page_meta(['layout' => false]);
$ajax = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

function cart_payload(): array
{
    $c = cart_summary();
    return [
        'ok' => true,
        'count' => $c['count'],
        'total' => money($c['total']),
        'items' => array_map(fn($i) => [
            'key' => $i['key'], 'name' => $i['name'], 'option' => $i['option'], 'qty' => $i['qty'], 'slug' => $i['slug'],
            'theme' => $i['theme'], 'glyph' => $i['glyph'], 'line' => money($i['line']), 'unit' => money($i['unit']),
        ], $c['items']),
    ];
}

if (is_post()) {
    if (!csrf_check()) {
        $ajax ? json_out(['ok' => false, 'error' => 'Session expirée, rechargez la page.'], 403) : redirect('/panier');
    }
    $action = post('action');
    $ok = true;
    if ($action === 'add') {
        $ok = cart_add((int) post('id'), (int) post('opt'), max(1, min(10, (int) post('qty', '1'))));
    } elseif ($action === 'set') {
        cart_set(post('key'), (int) post('qty'));
    } elseif ($action === 'remove') {
        cart_set(post('key'), 0);
    } elseif ($action === 'clear') {
        cart_clear();
    }
    if (!$ajax) {
        flash($ok ? 'success' : 'error', $ok ? 'Produit ajouté au panier.' : 'Ce produit n’est pas disponible.');
        redirect('/panier');
    }
    $out = cart_payload();
    if (!$ok) {
        $out['ok'] = false;
        $out['error'] = 'Ce produit n’est pas disponible.';
    }
    json_out($out);
}

json_out(cart_payload());
