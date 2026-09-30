<?php
$all = projects();
$slug = (string) $params['slug'];
if (!isset($all[$slug])) {
    return 404;
}
$p = $all[$slug];
page_meta(['title' => $p['title'] . ' — Réalisation', 'description' => $p['summary']]);
$keys = array_keys($all);
$nextSlug = $keys[(array_search($slug, $keys, true) + 1) % count($keys)];
$cat = project_categories()[$p['cat']];
?>
<section class="case-hero">
  <div class="container">
    <nav class="crumbs" aria-label="Fil d’Ariane"><a href="/">Accueil</a><?= icon('chevron-right') ?><a href="/realisations">Réalisations</a><?= icon('chevron-right') ?><span><?= e($p['title']) ?></span></nav>
    <p class="eyebrow" data-reveal><?= e($cat) ?> · <?= e($p['year']) ?></p>
    <h1 class="h1 case-hero__title" data-split><?= e($p['title']) ?></h1>
    <p class="lead" data-reveal><?= e($p['summary']) ?></p>
    <dl class="case-meta" data-reveal>
      <div><dt>Client</dt><dd><?= e($p['client']) ?></dd></div>
      <div><dt>Année</dt><dd><?= e($p['year']) ?></dd></div>
      <div><dt>Expertises</dt><dd><?= e(implode(' · ', $p['services'])) ?></dd></div>
    </dl>
  </div>
  <div class="container">
    <div class="case-cover" data-reveal data-parallax><?= project_cover($slug, $p) ?></div>
  </div>
</section>

<section class="section">
  <div class="container case-body">
    <div class="case-block" data-reveal>
      <p class="eyebrow">Le défi</p>
      <p class="case-block__text"><?= e($p['challenge']) ?></p>
    </div>
    <div class="case-block" data-reveal>
      <p class="eyebrow">Notre réponse</p>
      <p class="case-block__text"><?= e($p['solution']) ?></p>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="stats__panel stats__panel--case" data-reveal>
      <?php foreach ($p['results'] as [$v, $l]): ?>
      <div class="stats__item"><strong><?= e($v) ?></strong><span><?= e($l) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section next-link">
  <div class="container">
    <a class="next" href="/realisations/<?= e($nextSlug) ?>" data-cursor="Suivant">
      <span class="eyebrow">Projet suivant</span>
      <strong><?= e($all[$nextSlug]['title']) ?></strong>
      <span class="next__arrow"><?= icon('arrow-right') ?></span>
    </a>
  </div>
</section>
