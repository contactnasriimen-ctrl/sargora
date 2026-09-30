<?php
/** Domaine boutique : catalogue, panier (session), commandes. */
declare(strict_types=1);

const ORDER_STATUSES = [
    'pending'   => ['En attente de paiement', 'amber'],
    'paid'      => ['Paiement confirmé', 'blue'],
    'delivered' => ['Livrée', 'green'],
    'cancelled' => ['Annulée', 'red'],
];

/* ---------- Catalogue ---------- */

function categories(): array
{
    static $cache = null;
    return $cache ??= db_all('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.active = 1) AS n
        FROM categories c ORDER BY c.sort, c.name');
}

function category_by_slug(string $slug): ?array
{
    foreach (categories() as $c) {
        if ($c['slug'] === $slug) {
            return $c;
        }
    }
    return null;
}

function hydrate_product(array $p): array
{
    $opts = json_decode((string) $p['options'], true);
    $p['options'] = is_array($opts) ? array_values(array_filter($opts, fn($o) => isset($o['label'], $o['price']))) : [];
    $p['features'] = array_values(array_filter(array_map('trim', explode("\n", (string) $p['features']))));
    $prices = array_column($p['options'], 'price');
    $p['price_from'] = $prices ? (int) min($prices) : 0;
    $p['multi'] = count($p['options']) > 1;
    return $p;
}

function products(array $f = []): array
{
    $where = ['p.active = 1'];
    $args = [];
    if (!empty($f['cat'])) {
        $where[] = 'c.slug = ?';
        $args[] = $f['cat'];
    }
    if (!empty($f['featured'])) {
        $where[] = 'p.featured = 1';
    }
    if (!empty($f['q'])) {
        $where[] = '(LOWER(p.name) LIKE ? OR LOWER(p.tagline) LIKE ?)';
        $q = '%' . mb_strtolower($f['q']) . '%';
        array_push($args, $q, $q);
    }
    if (!empty($f['exclude'])) {
        $where[] = 'p.id <> ?';
        $args[] = (int) $f['exclude'];
    }
    $sql = 'SELECT p.*, c.slug AS cat_slug, c.name AS cat_name FROM products p JOIN categories c ON c.id = p.category_id
            WHERE ' . implode(' AND ', $where) . ' ORDER BY p.featured DESC, p.sort, p.name';
    if (!empty($f['limit'])) {
        $sql .= ' LIMIT ' . (int) $f['limit'];
    }
    $list = array_map('hydrate_product', db_all($sql, $args));
    $sort = $f['sort'] ?? '';
    if ($sort === 'prix-asc' || $sort === 'prix-desc') {
        usort($list, fn($a, $b) => $sort === 'prix-asc' ? $a['price_from'] <=> $b['price_from'] : $b['price_from'] <=> $a['price_from']);
    } elseif ($sort === 'nom') {
        usort($list, fn($a, $b) => strcmp($a['name'], $b['name']));
    }
    return $list;
}

function product_by_slug(string $slug): ?array
{
    $p = db_one('SELECT p.*, c.slug AS cat_slug, c.name AS cat_name FROM products p JOIN categories c ON c.id = p.category_id
        WHERE p.slug = ? AND p.active = 1', [$slug]);
    return $p ? hydrate_product($p) : null;
}

function product_by_id(int $id, bool $onlyActive = true): ?array
{
    $p = db_one('SELECT p.*, c.slug AS cat_slug, c.name AS cat_name FROM products p JOIN categories c ON c.id = p.category_id
        WHERE p.id = ?' . ($onlyActive ? ' AND p.active = 1' : ''), [$id]);
    return $p ? hydrate_product($p) : null;
}

/* ---------- Panier (session) ---------- */

function cart_raw(): array
{
    return is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];
}

function cart_add(int $productId, int $opt, int $qty = 1): bool
{
    $p = product_by_id($productId);
    if (!$p || !$p['in_stock'] || !isset($p['options'][$opt])) {
        return false;
    }
    $key = $productId . ':' . $opt;
    $cart = cart_raw();
    $cart[$key] = min(10, ($cart[$key] ?? 0) + max(1, $qty));
    $_SESSION['cart'] = $cart;
    return true;
}

function cart_set(string $key, int $qty): void
{
    $cart = cart_raw();
    if (!isset($cart[$key])) {
        return;
    }
    if ($qty <= 0) {
        unset($cart[$key]);
    } else {
        $cart[$key] = min(10, $qty);
    }
    $_SESSION['cart'] = $cart;
}

function cart_clear(): void
{
    $_SESSION['cart'] = [];
}

/** Panier détaillé, recalculé depuis la base (les prix ne viennent jamais du client). */
function cart_items(): array
{
    $items = [];
    $cart = cart_raw();
    foreach ($cart as $key => $qty) {
        [$pid, $opt] = array_map('intval', explode(':', (string) $key) + [1 => 0]);
        $p = product_by_id($pid);
        if (!$p || !$p['in_stock'] || !isset($p['options'][$opt])) {
            unset($cart[$key]);
            continue;
        }
        $o = $p['options'][$opt];
        $items[] = [
            'key' => (string) $key, 'id' => $pid, 'opt' => $opt, 'qty' => (int) $qty,
            'name' => $p['name'], 'slug' => $p['slug'], 'option' => $o['label'], 'theme' => $p['theme'], 'glyph' => (string) $p['glyph'],
            'unit' => (int) $o['price'], 'line' => (int) $o['price'] * (int) $qty, 'delivery' => $p['delivery'],
        ];
    }
    $_SESSION['cart'] = $cart;
    return $items;
}

function cart_summary(): array
{
    $items = cart_items();
    return [
        'items' => $items,
        'count' => array_sum(array_column($items, 'qty')),
        'total' => array_sum(array_column($items, 'line')),
    ];
}

function cart_count(): int
{
    return array_sum(array_map('intval', cart_raw()));
}

/* ---------- Commandes ---------- */

function payment_methods(): array
{
    return require __DIR__ . '/data/payments.php';
}

function order_ref(): string
{
    do {
        $ref = ORDER_PREFIX . '-' . date('y') . strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ'), 0, 2)) . random_int(1000, 9999);
    } while (db_val('SELECT 1 FROM orders WHERE ref = ?', [$ref]));
    return $ref;
}

/** @return array{0:?array,1:array} [commande, erreurs] */
function order_create(array $in): array
{
    $cart = cart_summary();
    $errors = [];
    if (!$cart['items']) {
        $errors['cart'] = 'Votre panier est vide.';
    }
    $name = mb_substr(trim($in['name'] ?? ''), 0, 160);
    $email = mb_strtolower(trim($in['email'] ?? ''));
    $phone = trim($in['phone'] ?? '');
    $pay = $in['payment'] ?? '';
    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Indiquez votre nom complet.';
    }
    if (!valid_email($email)) {
        $errors['email'] = 'Adresse e-mail invalide.';
    }
    if (!valid_phone($phone)) {
        $errors['phone'] = 'Numéro de téléphone invalide.';
    }
    if (!isset(payment_methods()[$pay])) {
        $errors['payment'] = 'Choisissez un moyen de paiement.';
    }
    if (empty($in['terms'])) {
        $errors['terms'] = 'Vous devez accepter les conditions générales de vente.';
    }
    if ($errors) {
        return [null, $errors];
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $now = now();
        $ref = order_ref();
        $id = db_insert('orders', [
            'ref' => $ref, 'access_key' => bin2hex(random_bytes(12)), 'status' => 'pending',
            'customer_name' => $name, 'email' => $email, 'phone' => $phone,
            'note' => mb_substr(trim($in['note'] ?? ''), 0, 1000), 'payment_method' => $pay,
            'total' => $cart['total'], 'items_count' => $cart['count'], 'ip' => client_ip(),
            'created_at' => $now, 'updated_at' => $now,
        ]);
        foreach ($cart['items'] as $it) {
            db_insert('order_items', [
                'order_id' => $id, 'product_id' => $it['id'], 'product_name' => $it['name'], 'option_label' => $it['option'],
                'unit_price' => $it['unit'], 'qty' => $it['qty'], 'line_total' => $it['line'],
            ]);
        }
        $pdo->commit();
    } catch (Throwable $ex) {
        $pdo->rollBack();
        throw $ex;
    }
    cart_clear();
    $_SESSION['my_orders'][] = $ref;
    return [order_by_ref($ref), []];
}

function order_by_ref(string $ref): ?array
{
    $o = db_one('SELECT * FROM orders WHERE ref = ?', [$ref]);
    if ($o) {
        $o['items'] = db_all('SELECT * FROM order_items WHERE order_id = ? ORDER BY id', [$o['id']]);
    }
    return $o;
}

function status_label(string $s): string
{
    return ORDER_STATUSES[$s][0] ?? $s;
}

function status_badge(string $s): string
{
    [$label, $tone] = ORDER_STATUSES[$s] ?? [$s, 'grey'];
    return '<span class="badge badge--' . $tone . '">' . e($label) . '</span>';
}

function order_whatsapp_text(array $o): string
{
    $lines = ["Bonjour Sargora, voici ma commande {$o['ref']} :"];
    foreach ($o['items'] as $it) {
        $lines[] = "• {$it['product_name']} ({$it['option_label']}) × {$it['qty']}";
    }
    $lines[] = 'Total : ' . money((int) $o['total']);
    $lines[] = 'Paiement : ' . (payment_methods()[$o['payment_method']]['label'] ?? $o['payment_method']);
    $lines[] = 'Je vous envoie ma preuve de paiement.';
    return implode("\n", $lines);
}

/* ---------- Visuel produit (carte d'accès) ---------- */

function product_card_visual(array $p, string $size = 'md'): string
{
    $glyph = e((string) ($p['glyph'] ?? ''));
    $name = e($p['name']);
    $cat = e($p['cat_name'] ?? '');
    $theme = e(preg_replace('/[^a-z0-9-]/', '', (string) $p['theme']) ?: 'gold');
    return <<<HTML
<div class="pcard pcard--{$size} th-{$theme}" aria-hidden="true">
  <div class="pcard__shine"></div>
  <span class="pcard__glyph">{$glyph}</span>
  <div class="pcard__top"><span class="pcard__brand">SARGORA<i>ACCESS</i></span><span class="pcard__chip"></span></div>
  <div class="pcard__bottom"><span class="pcard__cat">{$cat}</span><strong class="pcard__name">{$name}</strong></div>
</div>
HTML;
}
