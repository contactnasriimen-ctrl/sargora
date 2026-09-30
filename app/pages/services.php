<?php
page_meta(['title' => 'Services', 'description' => 'Création de sites web, branding, sponsoring et marketing digital : découvrez les expertises de l’agence Sargora.']);
?>
<section class="page-hero">
  <div class="container">
    <p class="eyebrow" data-reveal>Services</p>
    <h1 class="h1" data-split>Une agence, <em>quatre flammes.</em></h1>
    <p class="lead page-hero__lead" data-reveal>Stratégie, design, technologie et partenariats : nous réunissons tous les métiers qui font grandir une marque, pour un impact cohérent et mesurable.</p>
  </div>
  <div class="page-hero__glow" aria-hidden="true"></div>
</section>

<section class="section svc-rows">
  <div class="container">
    <?php $i = 0; foreach (services() as $slug => $s): $i++; ?>
    <article class="svc-row<?= $i % 2 === 0 ? ' svc-row--rev' : '' ?>">
      <div class="svc-row__visual" data-reveal>
        <div class="orb tilt" data-tilt>
          <span class="orb__ring"></span><span class="orb__ring orb__ring--2"></span>
          <span class="orb__ico"><?= icon($s['icon']) ?></span>
          <span class="orb__num"><?= e($s['num']) ?></span>
          <span class="tilt__glare"></span>
        </div>
      </div>
      <div class="svc-row__copy">
        <p class="eyebrow" data-reveal><?= e($s['num']) ?> — <?= e($s['title']) ?></p>
        <h2 class="h2" data-split><?= e($s['long']) ?></h2>
        <p class="lead" data-reveal><?= e($s['intro']) ?></p>
        <ul class="svc-row__list" data-reveal>
          <?php foreach ($s['deliverables'] as [$d]): ?><li><?= icon('check') ?><?= e($d) ?></li><?php endforeach; ?>
        </ul>
        <a class="btn btn--ghost" href="/services/<?= e($slug) ?>" data-magnetic data-reveal><span>En savoir plus</span><?= icon('arrow-right') ?></a>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="section process">
  <div class="container">
    <div class="section-head section-head--center">
      <p class="eyebrow" data-reveal>Méthode</p>
      <h2 class="h2" data-split>Notre façon de <em>travailler.</em></h2>
    </div>
    <ol class="process__list" data-process>
      <span class="process__line" aria-hidden="true"><span data-process-line></span></span>
      <?php foreach (process_steps() as $i => [$t, $d, $ic]): ?>
      <li class="process__step" data-reveal style="--d:<?= $i * 90 ?>ms">
        <span class="process__dot"><?= icon($ic) ?></span>
        <span class="process__num">0<?= $i + 1 ?></span>
        <h3><?= e($t) ?></h3>
        <p><?= e($d) ?></p>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
