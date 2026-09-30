<?php
page_meta(['title' => 'L’agence', 'description' => 'Découvrez Sargora : une agence marketing qui réunit stratégie, design, technologie et partenariats pour faire grandir les marques.']);
$values = [
    ['sparkles', 'Exigence créative', 'Nous visons l’exceptionnel à chaque détail, du premier croquis à la dernière ligne de code.'],
    ['target', 'Impact mesurable', 'Chaque action est reliée à un objectif chiffré et suivie dans un reporting clair.'],
    ['users', 'Proximité', 'Un interlocuteur dédié, joignable, qui connaît votre marque aussi bien que vous.'],
    ['shield', 'Transparence', 'Des devis clairs, des délais tenus et aucune mauvaise surprise.'],
];
$poles = [
    ['compass', 'Stratégie & conseil', 'Positionnement, études, plans marketing'],
    ['pen', 'Design & direction artistique', 'Identités, interfaces, contenus visuels'],
    ['monitor', 'Web & technologie', 'Sites, e-commerce, plateformes sur mesure'],
    ['trophy', 'Partenariats & événements', 'Sponsoring, activations, influence'],
    ['megaphone', 'Social & performance', 'Contenus, publicité, community management'],
    ['bag', 'Boutique digitale', 'Abonnements et outils pour les pros'],
];
?>
<section class="page-hero page-hero--about">
  <div class="container page-hero__split">
    <div>
      <p class="eyebrow" data-reveal>L’agence</p>
      <h1 class="h1" data-split>Nous allumons des <em>marques.</em></h1>
      <p class="lead page-hero__lead" data-reveal>Sargora est née d’une conviction : une marque bien construite est une flamme qui attire, rassemble et fait grandir. Notre mission est de l’allumer, puis de l’entretenir.</p>
    </div>
    <div class="page-hero__logo" data-reveal>
      <div class="about-logo tilt" data-tilt>
        <img src="<?= asset('img/brand/logo-full.webp') ?>" alt="Logo Sargora — Grow brands. Create impact." width="816" height="740">
        <span class="tilt__glare"></span>
      </div>
    </div>
  </div>
  <div class="page-hero__glow" aria-hidden="true"></div>
</section>

<section class="section manifesto">
  <div class="container">
    <p class="manifesto__text manifesto__text--wide" data-scrub-words>Le bleu de notre emblème, c’est la confiance et la profondeur de la stratégie. L’or, c’est la valeur que nous créons pour chaque client. Ensemble, ils forment une flamme : celle des marques qui grandissent et laissent une empreinte.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head section-head--center">
      <p class="eyebrow" data-reveal>Nos valeurs</p>
      <h2 class="h2" data-split>Ce qui nous <em>anime.</em></h2>
    </div>
    <div class="values">
      <?php foreach ($values as $i => [$ic, $t, $d]): ?>
      <div class="value glass tilt" data-tilt data-reveal style="--d:<?= $i * 80 ?>ms">
        <span class="value__ico"><?= icon($ic) ?></span>
        <h3><?= e($t) ?></h3>
        <p><?= e($d) ?></p>
        <span class="tilt__glare"></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow" data-reveal>Nos pôles</p>
      <h2 class="h2" data-split>Une équipe, <em>six savoir-faire.</em></h2>
    </div>
    <div class="poles">
      <?php foreach ($poles as $i => [$ic, $t, $d]): ?>
      <div class="pole" data-reveal style="--d:<?= ($i % 3) * 80 ?>ms">
        <span class="pole__ico"><?= icon($ic) ?></span>
        <div><h3><?= e($t) ?></h3><p><?= e($d) ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section stats">
  <div class="container">
    <div class="stats__panel" data-reveal>
      <?php foreach (agency_stats() as [$v, $suf, $lab]): ?>
      <div class="stats__item"><strong><span data-count="<?= (int) $v ?>">0</span><?= e($suf) ?></strong><span><?= e($lab) ?></span></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
