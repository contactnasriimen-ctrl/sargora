<?php
require_admin();
if (is_post()) {
    require_csrf();
    $id = (int) post('id');
    if (post('action') === 'toggle' && $id) {
        db_exec('UPDATE products SET active = 1 - active, updated_at = ? WHERE id = ?', [now(), $id]);
    } elseif (post('action') === 'stock' && $id) {
        db_exec('UPDATE products SET in_stock = 1 - in_stock, updated_at = ? WHERE id = ?', [now(), $id]);
    }
    redirect('/admin/produits' . (get('cat') ? '?cat=' . rawurlencode(get('cat')) : ''));
}
page_meta(['layout' => 'admin', 'title' => 'Produits']);
$cat = get('cat');
$rows = db_all('SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM products p JOIN categories c ON c.id = p.category_id'
    . ($cat !== '' ? ' WHERE c.slug = ?' : '') . ' ORDER BY c.sort, p.sort, p.name', $cat !== '' ? [$cat] : []);
$cats = db_all('SELECT * FROM categories ORDER BY sort');
?>
<div class="toolbar">
  <nav class="tabs">
    <a class="<?= $cat === '' ? 'is-active' : '' ?>" href="/admin/produits">Tous</a>
    <?php foreach ($cats as $c): ?><a class="<?= $cat === $c['slug'] ? 'is-active' : '' ?>" href="/admin/produits?cat=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a><?php endforeach; ?>
  </nav>
  <a class="b b--gold" href="/admin/produits/nouveau"><?= icon('plus') ?> Nouveau produit</a>
</div>
<section class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr><th></th><th>Produit</th><th>Catégorie</th><th>Formules</th><th>En ligne</th><th>Stock</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): $p = hydrate_product($r); ?>
      <tr class="<?= $p['active'] ? '' : 'is-off' ?>">
        <td><span class="thumb th-<?= e($p['theme']) ?>"><?= e((string) $p['glyph']) ?></span></td>
        <td><a href="/admin/produits/<?= (int) $p['id'] ?>"><strong><?= e($p['name']) ?></strong></a><?php if ($p['featured']): ?> <span class="tag">★ Mis en avant</span><?php endif; ?><br><small class="muted"><?= e((string) $p['tagline']) ?></small></td>
        <td><?= e($p['cat_name']) ?></td>
        <td class="muted"><?php foreach ($p['options'] as $o): ?><?= e($o['label']) ?> : <strong><?= money((int) $o['price']) ?></strong><br><?php endforeach; ?></td>
        <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="switch<?= $p['active'] ? ' is-on' : '' ?>" type="submit" aria-label="Basculer la mise en ligne"><i></i></button></form></td>
        <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="stock"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>"><button class="switch<?= $p['in_stock'] ? ' is-on' : '' ?>" type="submit" aria-label="Basculer le stock"><i></i></button></form></td>
        <td class="right"><a class="b b--sm" href="/admin/produits/<?= (int) $p['id'] ?>"><?= icon('edit') ?> Modifier</a> <a class="b b--sm" href="/produit/<?= e($p['slug']) ?>" target="_blank" rel="noopener" title="Voir sur le site"><?= icon('eye') ?></a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
