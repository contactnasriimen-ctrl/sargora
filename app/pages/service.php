<?php
$all = services();
$slug = (string) $params['slug'];
if (!isset($all[$slug])) {
    return 404;
}
$s = $all[$slug];
page_meta(['title' => $s['long'], 'description' => $s['short']]);
$related = array_filter(projects(), fn($p) => ($slug === 'site-web' && $p['cat'] === 'web') || ($slug === 'branding' && $p['cat'] === 'branding')
    || ($slug === 'sponsoring' && $p['cat'] === 'sponsoring') || ($slug === 'marketing-digital' && $p['cat'] === 'marketing'));
$keys = array_keys($all);
$next = $keys[(array_search($slug, $keys, true) + 1) % count($keys)];
?>
<section class="page-hero page-hero--svc">
  <div class="container page-hero__split">
    <div>
      <nav class="crumbs" aria-label="Fil d’Ariane"><a href="/">Accueil</a><?= icon('chevron-right') ?><a href="/services">Services</a><?= icon('chevron-right') ?><span><?= e($s['title']) ?></span></nav>
      <p class="eyebrow" data-reveal><?= e($s['num']) ?> — Expertise</p>
      <h1 class="h1" data-split><?= e($s['long']) ?></h1>
      <p class="lead page-hero__lead" data-reveal><?= e($s['intro']) ?></p>
      <div class="hero__cta" data-reveal>
        <a class="btn btn--gold btn--lg" href="/contact?service=<?= e($slug) ?>" data-magnetic><span>Demander un devis</span><?= icon('arrow-up-right') ?></a>
        <a class="btn btn--ghost btn--lg" href="#formules"><span>Voir les formules</span></a>
      </div>
    </div>
    <div class="page-hero__orb" data-reveal>
      <div class="orb orb--xl tilt" data-tilt>
        <span class="orb__ring"></span><span class="orb__ring orb__ring--2"></span>
        <span class="orb__ico"><?= icon($s['icon']) ?></span>
        <span class="orb__num"><?= e($s['num']) ?></span>
        <span class="tilt__glare"></span>
      </div>
    </div>
  </div>
  <div class="page-hero__glow" aria-hidden="true"></div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-reveal>Ce que nous livrons</p>
      <h2 class="h2" data-split>Un savoir-faire <em>complet.</em></h2>
    </div>
    <div class="deliv-grid">
      <?php foreach ($s['deliverables'] as $i => [$t, $d]): ?>
      <div class="deliv glass" data-reveal style="--d:<?= ($i % 3) * 80 ?>ms">
        <span class="deliv__num">0<?= $i + 1 ?></span>
        <h3><?= e($t) ?></h3>
        <p><?= e($d) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="formules">
  <div class="container">
    <div class="section-head section-head--center">
      <p class="eyebrow" data-reveal>Formules</p>
      <h2 class="h2" data-split>Choisissez votre <em>élan.</em></h2>
      <p class="lead" data-reveal>Des tarifs indicatifs pour vous situer : chaque projet fait l’objet d’un devis précis et gratuit.</p>
    </div>
    <div class="plans">
      <?php foreach ($s['packages'] as $i => $pk): $hot = !empty($pk[3]); ?>
      <div class="plan<?= $hot ? ' plan--hot' : '' ?> tilt" data-tilt data-reveal style="--d:<?= $i * 90 ?>ms">
        <?php if ($hot): ?><span class="plan__flag">Le plus choisi</span><?php endif; ?>
        <h3 class="plan__name"><?= e($pk[0]) ?></h3>
        <p class="plan__price"><?= e($pk[1]) ?></p>
        <ul><?php foreach ($pk[2] as $f): ?><li><?= icon('check') ?><?= e($f) ?></li><?php endforeach; ?></ul>
        <a class="btn <?= $hot ? 'btn--gold' : 'btn--ghost' ?> btn--block" href="/contact?service=<?= e($slug) ?>&amp;formule=<?= e(slugify($pk[0])) ?>"><span>Choisir</span><?= icon('arrow-right') ?></a>
        <span class="tilt__glare"></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-reveal>Réalisations</p>
      <h2 class="h2" data-split>Ils nous ont fait <em>confiance.</em></h2>
    </div>
    <div class="work-grid">
      <?php foreach ($related as $wslug => $p) { require ROOT . '/app/views/work_card.php'; } ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section">
  <div class="container faq-wrap">
    <div>
      <p class="eyebrow" data-reveal>Questions fréquentes</p>
      <h2 class="h2" data-split>Vous vous <em>demandez…</em></h2>
    </div>
    <div class="faq">
      <?php foreach ($s['faq'] as [$q, $a]): ?>
      <details class="faq__item" data-reveal><summary><?= e($q) ?><span class="faq__ico"><?= icon('plus') ?></span></summary><div class="faq__a"><p><?= e($a) ?></p></div></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section next-link">
  <div class="container">
    <a class="next" href="/services/<?= e($next) ?>" data-cursor="Suivant">
      <span class="eyebrow">Service suivant</span>
      <strong><?= e($all[$next]['long']) ?></strong>
      <span class="next__arrow"><?= icon('arrow-right') ?></span>
    </a>
  </div>
</section>
