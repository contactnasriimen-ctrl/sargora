<?php
page_meta(['title' => 'Réalisations', 'description' => 'Sites web, identités de marque, sponsorings et campagnes : découvrez les projets de l’agence Sargora.']);
$cats = project_categories();
?>
<section class="page-hero">
  <div class="container">
    <p class="eyebrow" data-reveal>Réalisations</p>
    <h1 class="h1" data-split>Des histoires qui <em>brillent.</em></h1>
    <p class="lead page-hero__lead" data-reveal>Chaque projet est une flamme différente. Voici quelques marques que nous avons aidées à grandir.</p>
    <div class="pills" role="group" aria-label="Filtrer les réalisations" data-work-filter data-reveal>
      <button class="pill is-active" type="button" data-filter="all">Tous</button>
      <?php foreach ($cats as $k => $label): ?><button class="pill" type="button" data-filter="<?= e($k) ?>"><?= e($label) ?></button><?php endforeach; ?>
    </div>
  </div>
  <div class="page-hero__glow" aria-hidden="true"></div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="work-grid work-grid--all">
      <?php foreach (projects() as $wslug => $p) { require ROOT . '/app/views/work_card.php'; } ?>
    </div>
  </div>
</section>
