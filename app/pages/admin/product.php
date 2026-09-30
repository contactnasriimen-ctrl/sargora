<?php
require_admin();
$isNew = $params['slug'] === 'nouveau';
$row = $isNew ? null : db_one('SELECT * FROM products WHERE id = ?', [(int) $params['slug']]);
if (!$isNew && !$row) {
    return 404;
}
$themes = ['gold' => 'Sargora (or & marine)', 'emerald' => 'Émeraude', 'obsidian' => 'Obsidienne', 'clay' => 'Terre cuite', 'aurora' => 'Aurore',
    'mono' => 'Monochrome', 'teal' => 'Sarcelle', 'violet' => 'Violet', 'crimson' => 'Cramoisi', 'ink' => 'Encre', 'lime' => 'Citron vert',
    'azure' => 'Azur', 'ruby' => 'Rubis', 'green' => 'Vert', 'scarlet' => 'Écarlate', 'jade' => 'Jade', 'sunset' => 'Couchant', 'mint' => 'Menthe',
    'paper' => 'Papier (clair)', 'ice' => 'Glace', 'cobalt' => 'Cobalt'];
$cats = db_all('SELECT * FROM categories ORDER BY sort');
$errors = [];

$d = $row ?: ['name' => '', 'slug' => '', 'category_id' => $cats[0]['id'] ?? 0, 'tagline' => '', 'description' => '', 'features' => '', 'options' => '[]',
    'badge' => '', 'theme' => 'gold', 'glyph' => '', 'delivery' => 'Activation sous 1 à 6 h (9h – 22h)', 'featured' => 0, 'in_stock' => 1, 'active' => 1, 'sort' => 50];
$optText = implode("\n", array_map(fn($o) => $o['label'] . ' | ' . money((int) $o['price'], false), json_decode((string) $d['options'], true) ?: []));

if (is_post()) {
    require_csrf();
    if (post('action') === 'delete' && $row) {
        db_exec('DELETE FROM products WHERE id = ?', [$row['id']]);
        flash('success', 'Produit supprimé. Les commandes passées conservent leur historique.');
        redirect('/admin/produits');
    }
    $d = array_merge($d, [
        'name' => post('name'), 'slug' => slugify(post('slug') ?: post('name')), 'category_id' => (int) post('category_id'),
        'tagline' => post('tagline'), 'description' => post('description'), 'features' => post('features'),
        'badge' => post('badge'), 'theme' => isset($themes[post('theme')]) ? post('theme') : 'gold', 'glyph' => mb_substr(post('glyph'), 0, 6),
        'delivery' => post('delivery'), 'featured' => post('featured') ? 1 : 0, 'in_stock' => post('in_stock') ? 1 : 0, 'active' => post('active') ? 1 : 0,
        'sort' => (int) post('sort'),
    ]);
    $optText = post('options');
    $opts = [];
    foreach (preg_split('/\R/', $optText) as $n => $line) {
        if (trim($line) === '') continue;
        $parts = array_map('trim', explode('|', $line));
        $price = isset($parts[1]) ? parse_money($parts[1]) : null;
        if ($parts[0] === '' || $price === null) {
            $errors[] = 'Ligne ' . ($n + 1) . ' des formules invalide : utilisez « Libellé | prix » (ex. 1 mois | 69).';
            continue;
        }
        $opts[] = ['label' => mb_substr($parts[0], 0, 80), 'price' => $price];
    }
    if (mb_strlen($d['name']) < 2) $errors[] = 'Le nom est obligatoire.';
    if (!$opts) $errors[] = 'Ajoutez au moins une formule avec un prix.';
    if (!array_filter($cats, fn($c) => (int) $c['id'] === $d['category_id'])) $errors[] = 'Catégorie invalide.';
    if (db_val('SELECT 1 FROM products WHERE slug = ? AND id <> ?', [$d['slug'], (int) ($row['id'] ?? 0)])) $errors[] = 'Ce lien (slug) est déjà utilisé par un autre produit.';
    if (!$errors) {
        $data = $d;
        unset($data['id'], $data['created_at']);
        $data['options'] = json_encode($opts, JSON_UNESCAPED_UNICODE);
        $data['updated_at'] = now();
        $data = array_intersect_key($data, array_flip(['name', 'slug', 'category_id', 'tagline', 'description', 'features', 'options', 'badge', 'theme', 'glyph', 'delivery', 'featured', 'in_stock', 'active', 'sort', 'updated_at']));
        if ($row) {
            db_update('products', $data, (int) $row['id']);
            $id = (int) $row['id'];
        } else {
            $data['created_at'] = now();
            $id = db_insert('products', $data);
        }
        flash('success', 'Produit enregistré.');
        redirect('/admin/produits/' . $id);
    }
}
page_meta(['layout' => 'admin', 'title' => $isNew ? 'Nouveau produit' : 'Modifier : ' . $d['name']]);
$preview = array_merge($d, ['cat_name' => array_values(array_filter($cats, fn($c) => (int) $c['id'] === (int) $d['category_id']))[0]['name'] ?? '']);
?>
<a class="back" href="/admin/produits">← Tous les produits</a>
<?php foreach ($errors as $er): ?><div class="alert alert--error"><?= e($er) ?></div><?php endforeach; ?>
<form class="cols" method="post">
  <?= csrf_field() ?>
  <div class="stackc">
    <section class="card pad">
      <h2>Informations</h2>
      <div class="grid2">
        <label class="f"><span>Nom du produit *</span><input type="text" name="name" value="<?= e($d['name']) ?>" required maxlength="160"></label>
        <label class="f"><span>Lien (slug)</span><input type="text" name="slug" value="<?= e($d['slug']) ?>" placeholder="généré automatiquement" maxlength="120"></label>
        <label class="f"><span>Catégorie</span><select name="category_id"><?php foreach ($cats as $c): ?><option value="<?= (int) $c['id'] ?>"<?= (int) $c['id'] === (int) $d['category_id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
        <label class="f"><span>Badge <small>(ex. Best-seller, Nouveau)</small></span><input type="text" name="badge" value="<?= e((string) $d['badge']) ?>" maxlength="40"></label>
      </div>
      <label class="f"><span>Accroche</span><input type="text" name="tagline" value="<?= e((string) $d['tagline']) ?>" maxlength="255"></label>
      <label class="f"><span>Description <small>(laissez une ligne vide entre les paragraphes)</small></span><textarea name="description" rows="6"><?= e((string) $d['description']) ?></textarea></label>
      <label class="f"><span>Ce qui est inclus <small>(un point par ligne)</small></span><textarea name="features" rows="5"><?= e((string) $d['features']) ?></textarea></label>
    </section>
    <section class="card pad">
      <h2>Formules &amp; prix</h2>
      <label class="f"><span>Une formule par ligne : <code>Libellé | prix en <?= e(CURRENCY) ?></code></span><textarea name="options" rows="4" class="mono" placeholder="1 mois | 69&#10;3 mois | 195&#10;12 mois | 749"><?= e($optText) ?></textarea></label>
      <label class="f"><span>Délai de livraison affiché</span><input type="text" name="delivery" value="<?= e((string) $d['delivery']) ?>" maxlength="160"></label>
    </section>
  </div>
  <div class="stackc">
    <section class="card pad">
      <h2>Aperçu de la carte</h2>
      <div class="preview"><?= product_card_visual($preview) ?></div>
      <div class="grid2">
        <label class="f"><span>Thème visuel</span><select name="theme" data-theme-select><?php foreach ($themes as $k => $label): ?><option value="<?= $k ?>"<?= $d['theme'] === $k ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
        <label class="f"><span>Monogramme <small>(2-4 car.)</small></span><input type="text" name="glyph" value="<?= e((string) $d['glyph']) ?>" maxlength="6" data-glyph-input></label>
      </div>
    </section>
    <section class="card pad">
      <h2>Publication</h2>
      <label class="chk"><input type="checkbox" name="active" value="1"<?= $d['active'] ? ' checked' : '' ?>> En ligne sur la boutique</label>
      <label class="chk"><input type="checkbox" name="in_stock" value="1"<?= $d['in_stock'] ? ' checked' : '' ?>> Disponible (en stock)</label>
      <label class="chk"><input type="checkbox" name="featured" value="1"<?= $d['featured'] ? ' checked' : '' ?>> Mis en avant (accueil)</label>
      <label class="f"><span>Ordre d’affichage</span><input type="number" name="sort" value="<?= (int) $d['sort'] ?>"></label>
      <button class="b b--gold b--block" type="submit">Enregistrer</button>
    </section>
    <?php if ($row): ?>
    <section class="card pad danger">
      <button class="b b--danger" type="submit" name="action" value="delete" formnovalidate data-confirm-btn="Supprimer définitivement ce produit ?"><?= icon('trash') ?> Supprimer le produit</button>
    </section>
    <?php endif; ?>
  </div>
</form>
