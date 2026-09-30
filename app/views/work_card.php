<?php /** Carte réalisation. Variables : $wslug, $p. */ ?>
<a class="wcard" href="/realisations/<?= e($wslug) ?>" data-work data-cat="<?= e($p['cat']) ?>" data-cursor="Voir le projet" data-reveal>
  <div class="wcard__media"><?= project_cover($wslug, $p) ?></div>
  <div class="wcard__info">
    <div>
      <span class="wcard__cat"><?= e(project_categories()[$p['cat']]) ?> · <?= e($p['year']) ?></span>
      <h3><?= e($p['title']) ?></h3>
      <p><?= e($p['summary']) ?></p>
    </div>
    <span class="wcard__go"><?= icon('arrow-up-right') ?></span>
  </div>
</a>
