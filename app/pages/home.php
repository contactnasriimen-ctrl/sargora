<?php
page_meta(['body' => 'is-home']);
$svcs = services();
$work = projects();
$featured = products(['featured' => true, 'limit' => 8]);
$fan = array_slice($featured, 0, 5);
?>
<section class="hero" data-hero>
  <canvas class="hero__canvas" data-hero3d aria-hidden="true"></canvas>
  <div class="hero__fallback" aria-hidden="true"><img src="<?= asset('img/brand/emblem-640.webp') ?>" alt="" width="640" height="640" fetchpriority="high"></div>
  <div class="hero__veil" aria-hidden="true"></div>
  <div class="hero__inner container">
    <p class="eyebrow hero__eyebrow" data-hero-in>Agence marketing &amp; studio digital</p>
    <h1 class="hero__title" aria-label="Faire grandir les marques, créer l’impact.">
      <span class="line" aria-hidden="true"><span data-hero-line>Faire grandir</span></span>
      <span class="line" aria-hidden="true"><span data-hero-line>les marques,</span></span>
      <span class="line" aria-hidden="true"><span data-hero-line>créer l’<em>impact.</em></span></span>
    </h1>
    <p class="hero__lead" data-hero-in>Sites web qui convertissent, identités qui marquent, sponsorings qui font parler — et une boutique digitale pour équiper votre équipe des meilleurs outils, payables en dinars.</p>
    <div class="hero__cta" data-hero-in>
      <a class="btn btn--gold btn--lg" href="/contact" data-magnetic><span>Démarrer un projet</span><?= icon('arrow-up-right') ?></a>
      <a class="btn btn--ghost btn--lg" href="/boutique" data-magnetic><?= icon('bag') ?><span>Explorer la boutique</span></a>
    </div>
  </div>
  <div class="hero__foot container" data-hero-in>
    <div class="hero__scroll"><span class="hero__scroll-line"></span>Défiler pour découvrir</div>
    <ul class="hero__facts">
      <?php foreach (array_slice(agency_stats(), 0, 3) as [$v, $suf, $lab]): ?>
      <li><strong><?= $v . e($suf) ?></strong><span><?= e($lab) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="hero__side" aria-hidden="true">EST. <?= e(SITE_FOUNDED) ?> — <?= e(mb_strtoupper(explode(',', SITE_ADDRESS)[0])) ?></div>
</section>

<div class="marquee" aria-hidden="true">
  <div class="marquee__track" data-marquee>
    <?php for ($k = 0; $k < 2; $k++): ?>
    <span>Sites web</span><i>✦</i><span class="is-outline">Branding</span><i>✦</i><span>Sponsoring</span><i>✦</i><span class="is-outline">Marketing digital</span><i>✦</i><span>Boutique digitale</span><i>✦</i>
    <?php endfor; ?>
  </div>
</div>

<section class="section manifesto">
  <div class="container manifesto__grid">
    <div class="manifesto__aside" data-reveal>
      <p class="eyebrow">Manifeste</p>
      <div class="manifesto__emblem tilt" data-tilt>
        <img src="<?= asset('img/brand/emblem-256.webp') ?>" alt="Emblème Sargora" width="256" height="256" loading="lazy">
        <span class="tilt__glare"></span>
      </div>
    </div>
    <p class="manifesto__text" data-scrub-words>Chez Sargora, chaque marque est une flamme. Nous l’allumons avec une stratégie claire, la façonnons avec un design d’exception et la faisons rayonner là où votre audience regarde — sur le web, sur le terrain et dans les esprits.</p>
  </div>
</section>

<section class="section services-home" id="services">
  <div class="container services-home__grid">
    <div class="services-home__intro">
      <div class="sticky-col">
        <p class="eyebrow" data-reveal>Nos expertises</p>
        <h2 class="h2" data-split>Tout ce qu’il faut pour <em>briller.</em></h2>
        <p class="lead" data-reveal>Quatre pôles complémentaires, une seule équipe. Nous pensons votre marque comme un tout, de la stratégie jusqu’aux résultats.</p>
        <a class="link-arrow" href="/services" data-reveal>Tous nos services <?= icon('arrow-right') ?></a>
      </div>
    </div>
    <div class="stack">
      <?php $si = 0; foreach ($svcs as $slug => $s): $si++; ?>
      <article class="stack__card" style="--i:<?= $si ?>" data-stack-card>
        <a class="svc-card tilt" href="/services/<?= e($slug) ?>" data-tilt data-cursor="Découvrir">
          <span class="svc-card__num"><?= e($s['num']) ?></span>
          <span class="svc-card__ico"><?= icon($s['icon']) ?></span>
          <h3 class="svc-card__title"><?= e($s['long']) ?></h3>
          <p class="svc-card__text"><?= e($s['short']) ?></p>
          <ul class="svc-card__tags">
            <?php foreach (array_slice($s['deliverables'], 0, 4) as [$d]): ?><li><?= e($d) ?></li><?php endforeach; ?>
          </ul>
          <span class="svc-card__go"><?= icon('arrow-up-right') ?></span>
          <span class="tilt__glare"></span>
        </a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section work-home" data-hscroll-section>
  <div class="container work-home__head">
    <div>
      <p class="eyebrow" data-reveal>Réalisations</p>
      <h2 class="h2" data-split>Des marques qui <em>rayonnent.</em></h2>
    </div>
    <a class="btn btn--ghost" href="/realisations" data-magnetic><span>Toutes les réalisations</span><?= icon('arrow-right') ?></a>
  </div>
  <div class="hscroll" data-hscroll>
    <div class="hscroll__track" data-hscroll-track>
      <?php $wi = 0; foreach ($work as $slug => $p): if (++$wi > 6) break; ?>
      <a class="work-card" href="/realisations/<?= e($slug) ?>" data-cursor="Voir le projet">
        <div class="work-card__media"><?= project_cover($slug, $p) ?></div>
        <div class="work-card__info">
          <span class="work-card__cat"><?= e(project_categories()[$p['cat']]) ?> · <?= e($p['year']) ?></span>
          <h3><?= e($p['title']) ?></h3>
          <p><?= e($p['summary']) ?></p>
        </div>
      </a>
      <?php endforeach; ?>
      <a class="work-card work-card--more" href="/realisations">
        <span class="work-card__more-ico"><?= icon('arrow-up-right') ?></span>
        <strong>Voir tous<br>les projets</strong>
      </a>
    </div>
  </div>
</section>

<section class="section process">
  <div class="container">
    <div class="section-head section-head--center">
      <p class="eyebrow" data-reveal>Méthode</p>
      <h2 class="h2" data-split>De l’étincelle à <em>l’impact.</em></h2>
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

<section class="section stats">
  <div class="container">
    <div class="stats__panel" data-reveal>
      <?php foreach (agency_stats() as [$v, $suf, $lab]): ?>
      <div class="stats__item">
        <strong><span data-count="<?= (int) $v ?>">0</span><?= e($suf) ?></strong>
        <span><?= e($lab) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section shop-home">
  <div class="container shop-home__grid">
    <div class="shop-home__copy">
      <p class="eyebrow" data-reveal>Boutique digitale</p>
      <h2 class="h2" data-split>Vos outils premium, <em>payés en dinars.</em></h2>
      <p class="lead" data-reveal>ChatGPT, Claude, Canva, Adobe, Netflix… Nous activons vos abonnements officiels sur votre propre compte, sans carte internationale, en quelques heures.</p>
      <ul class="checks" data-reveal>
        <li><?= icon('check') ?> Activation sur votre compte personnel</li>
        <li><?= icon('check') ?> D17, Flouci, virement ou espèces</li>
        <li><?= icon('check') ?> Support 7 j/7 sur WhatsApp</li>
      </ul>
      <a class="btn btn--gold btn--lg" href="/boutique" data-magnetic data-reveal><span>Voir la boutique</span><?= icon('arrow-right') ?></a>
    </div>
    <div class="fan" data-fan aria-hidden="true">
      <?php foreach ($fan as $i => $p): ?>
      <div class="fan__card" style="--i:<?= $i ?>;--n:<?= count($fan) ?>"><?= product_card_visual($p) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="container">
    <div class="products-grid products-grid--home">
      <?php foreach (array_slice($featured, 0, 4) as $p) { require ROOT . '/app/views/product_tile.php'; } ?>
    </div>
  </div>
</section>

<section class="section testimonials">
  <div class="container">
    <div class="quotes" data-quotes>
      <p class="eyebrow">Ils nous font confiance</p>
      <div class="quotes__stage">
        <?php foreach (testimonials() as $i => [$q, $n, $r]): ?>
        <figure class="quote<?= $i === 0 ? ' is-active' : '' ?>" data-quote>
          <blockquote><p>« <?= e($q) ?> »</p></blockquote>
          <figcaption><span class="quote__avatar"><?= e(mb_substr($n, 0, 1)) ?></span><span><strong><?= e($n) ?></strong><small><?= e($r) ?></small></span></figcaption>
        </figure>
        <?php endforeach; ?>
      </div>
      <div class="quotes__nav">
        <button class="icon-btn" type="button" data-quote-prev aria-label="Témoignage précédent"><?= icon('arrow-left') ?></button>
        <div class="quotes__dots" data-quote-dots></div>
        <button class="icon-btn" type="button" data-quote-next aria-label="Témoignage suivant"><?= icon('arrow-right') ?></button>
      </div>
    </div>
  </div>
</section>
