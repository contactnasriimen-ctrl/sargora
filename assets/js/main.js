/* ==========================================================================
   SARGORA — interactions & mise en scène
   ========================================================================== */
(function () {
  'use strict';

  var html = document.documentElement;
  var body = document.body;
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var reduced = html.classList.contains('reduced');
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var hasGsap = typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined';
  var csrf = body.getAttribute('data-csrf') || '';
  var lenis = null;
  var BASE = body.getAttribute('data-base') || '';
  var localPath = function (p) { return (p.indexOf(BASE) === 0 ? p.slice(BASE.length) : p).replace(/\/+$/, '') || '/'; };

  if (hasGsap) gsap.registerPlugin(ScrollTrigger);

  /* ---------- Défilement fluide (Lenis) ---------- */
  if (!reduced && finePointer && typeof window.Lenis !== 'undefined') {
    lenis = new Lenis({ duration: 1.15, easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); }, smoothWheel: true });
    if (hasGsap) {
      lenis.on('scroll', ScrollTrigger.update);
      gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
      gsap.ticker.lagSmoothing(0);
    } else {
      (function raf(t) { lenis.raf(t); requestAnimationFrame(raf); })(0);
    }
    $$('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var id = a.getAttribute('href');
        if (id.length > 1 && $(id)) { e.preventDefault(); lenis.scrollTo(id, { offset: -90 }); }
      });
    });
  }

  /* ---------- Découpage des titres en mots ---------- */
  $$('[data-split]').forEach(function (el) {
    var i = 0;
    (function walk(node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (n) {
        if (n.nodeType === 3) {
          var frag = document.createDocumentFragment();
          n.textContent.split(/(\s+)/).forEach(function (part) {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(' ')); return; }
            var w = document.createElement('span'); w.className = 'w';
            var s = document.createElement('span'); s.textContent = part; s.style.setProperty('--wi', i++);
            w.appendChild(s); frag.appendChild(w);
          });
          n.parentNode.replaceChild(frag, n);
        } else if (n.nodeType === 1 && n.tagName === 'EM') {
          var w2 = document.createElement('span'); w2.className = 'w';
          var s2 = document.createElement('span'); s2.style.setProperty('--wi', i++);
          n.parentNode.replaceChild(w2, n); s2.appendChild(n); w2.appendChild(s2);
        } else if (n.nodeType === 1) { walk(n); }
      });
    })(el);
  });

  /* ---------- Mots éclairés au défilement ---------- */
  $$('[data-scrub-words]').forEach(function (el) {
    var words = el.textContent.trim().split(/\s+/);
    el.innerHTML = words.map(function (w) {
      var gold = /flamme|stratégie|design|rayonner|confiance|valeur|grandissent/i.test(w);
      return '<span class="sw' + (gold ? ' sw--g' : '') + '">' + w.replace(/[<>&]/g, '') + '</span>';
    }).join(' ');
    var spans = $$('.sw', el);
    var update = function () {
      var r = el.getBoundingClientRect();
      var vh = window.innerHeight;
      var p = Math.min(1, Math.max(0, (vh * 0.85 - r.top) / (r.height + vh * 0.35)));
      var lit = Math.round(p * spans.length);
      spans.forEach(function (s, k) {
        s.classList.toggle('is-lit', k < lit || reduced);
        s.classList.toggle('is-gold', (k < lit || reduced) && s.classList.contains('sw--g'));
      });
    };
    window.addEventListener('scroll', update, { passive: true });
    update();
  });

  /* ---------- Apparitions ---------- */
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (en) {
      if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
  $$('[data-reveal], [data-split]').forEach(function (el) { io.observe(el); });

  /* ---------- Compteurs ---------- */
  var cio = new IntersectionObserver(function (entries) {
    entries.forEach(function (en) {
      if (!en.isIntersecting) return;
      var el = en.target, to = parseInt(el.getAttribute('data-count'), 10) || 0, t0 = null;
      cio.unobserve(el);
      if (reduced) { el.textContent = to; return; }
      (function step(t) {
        if (!t0) t0 = t;
        var p = Math.min(1, (t - t0) / 1800), e = 1 - Math.pow(1 - p, 4);
        el.textContent = Math.round(to * e);
        if (p < 1) requestAnimationFrame(step);
      })(performance.now());
    });
  }, { threshold: 0.5 });
  $$('[data-count]').forEach(function (el) { cio.observe(el); });

  /* ---------- En-tête ---------- */
  var hdr = $('[data-header]'), lastY = window.scrollY;
  var onScroll = function () {
    var y = window.scrollY;
    if (hdr) {
      hdr.classList.toggle('is-scrolled', y > 30);
      hdr.classList.toggle('is-hidden', y > 500 && y > lastY + 2 && !body.classList.contains('menu-open'));
      if (y < lastY - 2) hdr.classList.remove('is-hidden');
    }
    lastY = y;
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Menu plein écran ---------- */
  var menuBtn = $('[data-menu-toggle]'), menu = $('[data-menu]');
  var setMenu = function (open) {
    body.classList.toggle('menu-open', open);
    html.classList.toggle('menu-open', open);
    if (menuBtn) menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (menu) menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (lenis) open ? lenis.stop() : lenis.start();
  };
  if (menuBtn) menuBtn.addEventListener('click', function () { setMenu(!body.classList.contains('menu-open')); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); closeDrawer(); } });

  /* ---------- Curseur ---------- */
  var cursor = $('.cursor');
  if (cursor && finePointer && !reduced) {
    var dot = $('.cursor__dot', cursor), ring = $('.cursor__ring', cursor), label = $('.cursor__label', cursor);
    var mx = -100, my = -100, rx = -100, ry = -100;
    window.addEventListener('pointermove', function (e) { mx = e.clientX; my = e.clientY; cursor.classList.remove('is-hidden'); }, { passive: true });
    document.addEventListener('pointerleave', function () { cursor.classList.add('is-hidden'); });
    (function loop() {
      rx += (mx - rx) * 0.18; ry += (my - ry) * 0.18;
      dot.style.transform = 'translate3d(' + mx + 'px,' + my + 'px,0)';
      ring.style.transform = 'translate3d(' + rx + 'px,' + ry + 'px,0)';
      requestAnimationFrame(loop);
    })();
    document.addEventListener('pointerover', function (e) {
      var t = e.target.closest ? e.target.closest('a, button, label, input, select, textarea, [data-cursor]') : null;
      var lab = t && t.getAttribute('data-cursor');
      cursor.classList.toggle('is-label', !!lab);
      cursor.classList.toggle('is-hover', !!t && !lab);
      label.textContent = lab || '';
    });
  }

  /* ---------- Boutons magnétiques ---------- */
  if (finePointer && !reduced) {
    $$('[data-magnetic]').forEach(function (el) {
      el.addEventListener('pointermove', function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - r.left - r.width / 2) * 0.28, y = (e.clientY - r.top - r.height / 2) * 0.35;
        el.style.transform = 'translate(' + x + 'px,' + y + 'px)';
      });
      el.addEventListener('pointerleave', function () { el.style.transform = ''; });
    });
  }

  /* ---------- Inclinaison 3D ---------- */
  if (finePointer && !reduced) {
    $$('[data-tilt]').forEach(function (el) {
      var strong = el.classList.contains('tilt--strong');
      var max = strong ? 16 : 9;
      el.addEventListener('pointermove', function (e) {
        var r = el.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
        el.classList.add('is-tilting');
        el.style.setProperty('--ry', ((px - 0.5) * max * 2).toFixed(2) + 'deg');
        el.style.setProperty('--rx', ((0.5 - py) * max * 1.6).toFixed(2) + 'deg');
        el.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
        el.style.setProperty('--my', (py * 100).toFixed(1) + '%');
      });
      el.addEventListener('pointerleave', function () {
        el.classList.remove('is-tilting');
        el.style.removeProperty('--rx'); el.style.removeProperty('--ry');
      });
    });
    // Éventail de cartes : suit la souris
    $$('[data-fan]').forEach(function (fan) {
      var zone = fan.closest('section') || fan;
      zone.addEventListener('pointermove', function (e) {
        var r = fan.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width - 0.5, py = (e.clientY - r.top) / r.height - 0.5;
        fan.style.transform = 'rotateY(' + (px * 14).toFixed(2) + 'deg) rotateX(' + (-py * 10).toFixed(2) + 'deg)';
      });
      zone.addEventListener('pointerleave', function () { fan.style.transform = ''; });
      fan.style.transition = 'transform .8s cubic-bezier(.22,1,.36,1)';
    });
  }

  /* ---------- Transitions de page ---------- */
  document.addEventListener('click', function (e) {
    var a = e.target.closest ? e.target.closest('a') : null;
    if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
    var href = a.getAttribute('href');
    if (!href || href.charAt(0) === '#' || a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-cart-open')) return;
    var url;
    try { url = new URL(a.href, location.href); } catch (err) { return; }
    if (url.origin !== location.origin || /^\/(admin|api)/.test(localPath(url.pathname))) return;
    if (url.pathname === location.pathname && url.hash) return;
    if (reduced) return;
    e.preventDefault();
    try { sessionStorage.setItem('sg-nav', '1'); } catch (err) {}
    html.classList.add('is-leaving');
    setTimeout(function () { location.href = url.href; }, 520);
  });
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) html.classList.remove('is-leaving', 'is-arriving');
  });
  setTimeout(function () { html.classList.remove('is-arriving'); }, 1200);

  /* ---------- Intro (accueil) + hero ---------- */
  var hero = $('[data-hero]');
  var loader = $('[data-loader]');
  var hero3d = null;
  var startHero = function () {
    if (!hero) return;
    hero.classList.add('is-in');
    if (hero3d && hero3d.intro) hero3d.intro();
  };

  var canvas = $('[data-hero3d]');
  var wantsGL = canvas && !reduced && (function () {
    try { var c = document.createElement('canvas'); return !!(c.getContext('webgl2') || c.getContext('webgl')); } catch (e) { return false; }
  })() && !(navigator.connection && navigator.connection.saveData);
  var glReady = wantsGL ? import('./hero3d.js').then(function (m) {
    hero3d = m.initHero(canvas, { mobile: !finePointer || window.innerWidth < 900 });
    return hero3d.ready;
  }).then(function () { hero.classList.add('is-3d'); }).catch(function (err) { console.warn('3D indisponible', err); }) : Promise.resolve();

  if (loader && !html.classList.contains('intro-seen')) {
    var word = $('[data-loader-word]', loader);
    word.innerHTML = word.textContent.split('').map(function (ch, i) { return '<span style="animation-delay:' + (0.25 + i * 0.07) + 's">' + ch + '</span>'; }).join('');
    var bar = $('[data-loader-bar]', loader), count = $('[data-loader-count]', loader);
    var t0 = performance.now(), minDur = reduced ? 300 : 2100, done = false, loaded = false;
    Promise.race([Promise.all([glReady, new Promise(function (r) { if (document.readyState === 'complete') r(); else window.addEventListener('load', r); })]), new Promise(function (r) { setTimeout(r, 5000); })])
      .then(function () { loaded = true; });
    if (lenis) lenis.stop();
    (function tick(t) {
      var p = Math.min(1, (t - t0) / minDur);
      var shown = loaded ? p : Math.min(p, 0.9);
      bar.style.transform = 'scaleX(' + shown + ')';
      count.textContent = Math.round(shown * 100);
      if (shown >= 1 && !done) {
        done = true;
        try { sessionStorage.setItem('sg-intro', '1'); } catch (e) {}
        loader.classList.add('is-done');
        setTimeout(startHero, 450);
        setTimeout(function () { loader.classList.add('is-gone'); if (lenis) lenis.start(); }, 1500);
        return;
      }
      requestAnimationFrame(tick);
    })(t0);
  } else if (hero) {
    requestAnimationFrame(function () { setTimeout(startHero, html.classList.contains('is-arriving') ? 350 : 60); });
  }

  // Progression du hero -> scène 3D
  if (hero) {
    var heroProg = function () {
      var r = hero.getBoundingClientRect();
      var p = Math.min(1, Math.max(0, -r.top / (r.height || 1)));
      if (hero3d && hero3d.setProgress) hero3d.setProgress(p);
    };
    window.addEventListener('scroll', heroProg, { passive: true });
    if (finePointer) window.addEventListener('pointermove', function (e) {
      if (hero3d && hero3d.setPointer) hero3d.setPointer(e.clientX / window.innerWidth * 2 - 1, e.clientY / window.innerHeight * 2 - 1);
    }, { passive: true });
  }

  /* ---------- Cartes de services empilées ---------- */
  if (hasGsap && !reduced) {
    var cards = $$('[data-stack-card]');
    cards.forEach(function (card, i) {
      if (i === cards.length - 1) return;
      gsap.to(card.firstElementChild, {
        scale: 0.93, filter: 'brightness(.55)', ease: 'none',
        scrollTrigger: { trigger: cards[i + 1], start: 'top 70%', end: 'top 20%', scrub: true }
      });
    });
  }

  /* ---------- Galerie horizontale épinglée ---------- */
  var hs = $('[data-hscroll]');
  if (hs && hasGsap && !reduced && window.matchMedia('(min-width: 900px)').matches) {
    var track = $('[data-hscroll-track]', hs);
    hs.classList.add('is-pinned');
    var dist = function () { return Math.max(0, track.scrollWidth - window.innerWidth); };
    gsap.to(track, {
      x: function () { return -dist(); }, ease: 'none',
      scrollTrigger: { trigger: hs.closest('[data-hscroll-section]') || hs, start: 'top top', end: function () { return '+=' + dist(); }, pin: true, scrub: 0.8, invalidateOnRefresh: true, anticipatePin: 1 }
    });
  }

  /* ---------- Ligne du processus ---------- */
  $$('[data-process]').forEach(function (list) {
    var line = $('[data-process-line]', list);
    if (!line) return;
    var upd = function () {
      var r = list.getBoundingClientRect(), vh = window.innerHeight;
      var p = Math.min(1, Math.max(0, (vh * 0.8 - r.top) / (r.height * 0.9)));
      line.style.setProperty('--p', reduced ? 1 : p.toFixed(3));
    };
    window.addEventListener('scroll', upd, { passive: true });
    upd();
  });

  /* ---------- Parallaxe légère ---------- */
  if (hasGsap && !reduced) {
    $$('[data-parallax]').forEach(function (el) {
      gsap.fromTo(el.firstElementChild || el, { yPercent: -6, scale: 1.08 }, { yPercent: 6, scale: 1.08, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true } });
    });
  }

  /* ---------- Témoignages ---------- */
  $$('[data-quotes]').forEach(function (wrap) {
    var items = $$('[data-quote]', wrap), dots = $('[data-quote-dots]', wrap), cur = 0, timer;
    if (items.length < 2) return;
    items.forEach(function (_, i) {
      var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Témoignage ' + (i + 1));
      b.addEventListener('click', function () { go(i); });
      dots.appendChild(b);
    });
    var go = function (i) {
      cur = (i + items.length) % items.length;
      items.forEach(function (q, k) { q.classList.toggle('is-active', k === cur); });
      $$('button', dots).forEach(function (d, k) { d.classList.toggle('is-active', k === cur); });
      clearInterval(timer);
      if (!reduced) timer = setInterval(function () { go(cur + 1); }, 6500);
    };
    $('[data-quote-prev]', wrap).addEventListener('click', function () { go(cur - 1); });
    $('[data-quote-next]', wrap).addEventListener('click', function () { go(cur + 1); });
    go(0);
  });

  /* ---------- Filtres réalisations ---------- */
  $$('[data-work-filter]').forEach(function (bar) {
    bar.addEventListener('click', function (e) {
      var b = e.target.closest('[data-filter]'); if (!b) return;
      var f = b.getAttribute('data-filter');
      $$('[data-filter]', bar).forEach(function (x) { x.classList.toggle('is-active', x === b); });
      $$('[data-work]').forEach(function (w) {
        var show = f === 'all' || w.getAttribute('data-cat') === f;
        w.classList.toggle('is-hidden', !show);
        if (show) { w.classList.remove('is-in'); requestAnimationFrame(function () { w.classList.add('is-in'); }); }
      });
      if (hasGsap) ScrollTrigger.refresh();
    });
  });

  /* ---------- Boutique : filtres instantanés ---------- */
  var shopForm = $('[data-shop-filter]');
  if (shopForm) {
    var grid = $('[data-products]'), tiles = $$('[data-product]', grid), empty = $('[data-shop-empty]');
    var search = $('[data-shop-search]', shopForm), sort = $('[data-shop-sort]', shopForm);
    var currentCat = ($('.pill.is-active', shopForm) || {}).getAttribute ? $('.pill.is-active', shopForm).getAttribute('data-cat-filter') : 'all';
    var norm = function (s) { return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); };
    var apply = function () {
      var q = norm(search.value.trim()), n = 0, k = 0;
      var sorted = tiles.slice();
      var mode = sort.value;
      sorted.sort(function (a, b) {
        if (mode === 'prix-asc') return a.dataset.price - b.dataset.price;
        if (mode === 'prix-desc') return b.dataset.price - a.dataset.price;
        if (mode === 'nom') return a.dataset.name.localeCompare(b.dataset.name);
        return 0;
      });
      if (mode) sorted.forEach(function (t) { grid.appendChild(t); });
      sorted.forEach(function (t) {
        var ok = (currentCat === 'all' || t.dataset.cat === currentCat) && (!q || norm(t.dataset.name).indexOf(q) !== -1);
        var was = !t.classList.contains('is-hidden');
        t.classList.toggle('is-hidden', !ok);
        if (ok) { n++; if (!was) { t.style.setProperty('--k', k++); t.classList.remove('is-entering'); void t.offsetWidth; t.classList.add('is-entering'); } }
      });
      empty.hidden = n > 0;
      var params = new URLSearchParams();
      if (search.value.trim()) params.set('q', search.value.trim());
      if (mode) params.set('tri', mode);
      var path = BASE + (currentCat === 'all' ? '/boutique' : '/boutique/' + currentCat);
      history.replaceState(null, '', path + (params.toString() ? '?' + params : '') + '#catalogue');
    };
    // Sur une page catégorie, le serveur n'a rendu que cette catégorie : on ne filtre alors que localement.
    var serverCat = /^\/boutique\/[a-z0-9-]+$/.test(localPath(location.pathname));
    $$('[data-cat-filter]', shopForm).forEach(function (p) {
      p.addEventListener('click', function (e) {
        if (serverCat) return;
        e.preventDefault();
        currentCat = p.getAttribute('data-cat-filter');
        $$('[data-cat-filter]', shopForm).forEach(function (x) { x.classList.toggle('is-active', x === p); });
        apply();
      });
    });
    search.addEventListener('input', apply);
    sort.addEventListener('change', apply);
    shopForm.addEventListener('submit', function (e) { e.preventDefault(); apply(); });
    var qs = new URLSearchParams(location.search);
    if (!search.value && qs.get('q')) search.value = qs.get('q');
    if (!sort.value && qs.get('tri')) sort.value = qs.get('tri');
    if (search.value || sort.value) apply();
  }

  /* ---------- Fiche produit : formules & quantité ---------- */
  $$('[data-buy]').forEach(function (form) {
    var priceBox = $('.buy__price', form), price = $('[data-buy-price]', form), lab = $('[data-buy-label]', form), wa = $('[data-buy-wa]', form);
    $$('input[name="opt"][type="radio"]', form).forEach(function (r) {
      r.addEventListener('change', function () {
        price.textContent = r.getAttribute('data-price');
        lab.textContent = r.getAttribute('data-label');
        priceBox.classList.remove('is-bump'); void priceBox.offsetWidth; priceBox.classList.add('is-bump');
        if (wa) {
          var u = new URL(wa.href);
          u.searchParams.set('text', wa.getAttribute('data-wa-base') + ' (' + r.getAttribute('data-label') + ').');
          wa.href = u.toString();
        }
      });
    });
  });
  $$('[data-qty]').forEach(function (q) {
    var input = $('input', q);
    var set = function (d) { input.value = Math.max(1, Math.min(10, (parseInt(input.value, 10) || 1) + d)); };
    $('[data-qty-minus]', q).addEventListener('click', function () { set(-1); });
    $('[data-qty-plus]', q).addEventListener('click', function () { set(1); });
  });
  $$('[data-autosubmit]').forEach(function (i) { i.addEventListener('change', function () { i.form.submit(); }); });

  /* ---------- Panier (tiroir + ajout animé) ---------- */
  var drawer = $('[data-drawer]');
  var countEls = $$('[data-cart-count]');
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var api = function (data) {
    var opts = { headers: { 'Accept': 'application/json', 'X-CSRF-Token': csrf }, credentials: 'same-origin' };
    if (data) { opts.method = 'POST'; opts.body = data; }
    if (window.SGStore) return window.SGStore.api(data);
    return fetch(BASE + '/api/cart', opts).then(function (r) { return r.json(); });
  };
  var ico = function (name) {
    var p = { minus: '<path d="M5 12h14"/>', plus: '<path d="M12 5v14M5 12h14"/>', trash: '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>' }[name];
    return '<svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + p + '</svg>';
  };
  var render = function (c) {
    countEls.forEach(function (el) { el.textContent = c.count; el.classList.toggle('is-on', c.count > 0); });
    if (!drawer) return;
    var box = $('[data-cart-items]', drawer), foot = $('[data-cart-foot]', drawer);
    $('[data-cart-count-label]', drawer).textContent = c.count ? '(' + c.count + ')' : '';
    if (!c.items.length) {
      box.innerHTML = '<div class="drawer__empty"><p>Votre panier est vide.</p><a class="btn btn--ghost btn--sm" href="' + BASE + '/boutique"><span>Découvrir la boutique</span></a></div>';
      foot.hidden = true;
      return;
    }
    box.innerHTML = c.items.map(function (it) {
      return '<div class="ditem" data-key="' + esc(it.key) + '">' +
        '<a class="pthumb th-' + esc(it.theme) + '" href="' + BASE + '/produit/' + esc(it.slug) + '" tabindex="-1" aria-hidden="true"><span>' + esc(it.glyph) + '</span></a>' +
        '<div><a class="ditem__name" href="' + BASE + '/produit/' + esc(it.slug) + '">' + esc(it.name) + '</a><span class="ditem__opt">' + esc(it.option) + ' · ' + esc(it.unit) + '</span></div>' +
        '<div class="ditem__side"><strong class="ditem__line">' + esc(it.line) + '</strong>' +
        '<span class="ditem__qty"><button type="button" data-q="-1" aria-label="Diminuer">' + ico(it.qty > 1 ? 'minus' : 'trash') + '</button><span>' + it.qty + '</span><button type="button" data-q="1" aria-label="Augmenter">' + ico('plus') + '</button></span></div>' +
        '</div>';
    }).join('');
    $('[data-cart-total]', drawer).textContent = c.total;
    foot.hidden = false;
  };
  var openDrawer = function () {
    if (!drawer) return;
    drawer.classList.add('is-open'); drawer.setAttribute('aria-hidden', 'false');
    if (lenis) lenis.stop();
    api().then(render);
    setTimeout(function () { var b = $('[data-drawer-close].icon-btn', drawer); if (b) b.focus(); }, 300);
  };
  function closeDrawer() {
    if (!drawer || !drawer.classList.contains('is-open')) return;
    drawer.classList.remove('is-open'); drawer.setAttribute('aria-hidden', 'true');
    if (lenis) lenis.start();
  }
  $$('[data-cart-open]').forEach(function (b) { b.addEventListener('click', function (e) { e.preventDefault(); openDrawer(); }); });
  $$('[data-drawer-close]').forEach(function (b) { b.addEventListener('click', closeDrawer); });
  if (drawer) drawer.addEventListener('click', function (e) {
    var b = e.target.closest('[data-q]'); if (!b) return;
    var row = b.closest('[data-key]'), qty = parseInt($('.ditem__qty span', row).textContent, 10) + parseInt(b.getAttribute('data-q'), 10);
    var fd = new FormData(); fd.append('action', 'set'); fd.append('key', row.getAttribute('data-key')); fd.append('qty', qty); fd.append('_csrf', csrf);
    api(fd).then(render);
  });

  var fly = function (from) {
    var target = $('[data-cart-open]');
    if (!from || !target || reduced) return;
    var a = from.getBoundingClientRect(), b = target.getBoundingClientRect();
    var el = document.createElement('div'); el.className = 'fly';
    el.style.left = (a.left + a.width / 2 - 30) + 'px'; el.style.top = (a.top + a.height / 2 - 30) + 'px';
    body.appendChild(el);
    var dx = b.left + b.width / 2 - (a.left + a.width / 2), dy = b.top + b.height / 2 - (a.top + a.height / 2);
    el.animate([
      { transform: 'translate(0,0) scale(1) rotate(0)', opacity: 1 },
      { transform: 'translate(' + dx * 0.5 + 'px,' + (dy - 120) + 'px) scale(.8) rotate(90deg)', opacity: 1, offset: 0.5 },
      { transform: 'translate(' + dx + 'px,' + dy + 'px) scale(.15) rotate(200deg)', opacity: 0.4 }
    ], { duration: 850, easing: 'cubic-bezier(.5,0,.3,1)' }).onfinish = function () {
      el.remove(); target.classList.remove('is-bump'); void target.offsetWidth; target.classList.add('is-bump');
    };
  };

  $$('[data-add-to-cart]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = $('button[type="submit"]', form);
      var fd = new FormData(form);
      if (btn) btn.disabled = true;
      api(fd).then(function (c) {
        if (btn) btn.disabled = false;
        if (!c.ok) { alert(c.error || 'Erreur, réessayez.'); return; }
        var visual = form.closest('.ptile') ? $('.pcard', form.closest('.ptile')) : $('.product__float');
        fly(visual || btn);
        render(c);
        if (btn && btn.classList.contains('add-btn')) {
          var span = $('span', btn), old = span.textContent;
          btn.classList.add('is-added'); span.textContent = 'Ajouté';
          setTimeout(function () { btn.classList.remove('is-added'); span.textContent = old; }, 1600);
        }
        if (form.hasAttribute('data-buy')) setTimeout(openDrawer, 700);
      }).catch(function () { if (btn) btn.disabled = false; form.submit(); });
    });
  });

  /* ---------- Newsletter ---------- */
  $$('[data-newsletter]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = $('[data-newsletter-msg]', form);
      fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (d) { msg.textContent = d.message; msg.classList.toggle('is-err', !d.ok); if (d.ok) form.reset(); })
        .catch(function () { msg.textContent = 'Erreur réseau, réessayez.'; msg.classList.add('is-err'); });
    });
  });

  /* ---------- Copier la référence ---------- */
  $$('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = $('[data-copy-text]', b.parentNode).textContent.trim();
      if (navigator.clipboard) navigator.clipboard.writeText(t).then(function () { b.style.color = 'var(--green)'; setTimeout(function () { b.style.color = ''; }, 1200); });
    });
  });

  /* ---------- Confettis dorés ---------- */
  var cel = $('[data-celebrate="1"]');
  if (cel && !reduced) {
    var cv = document.createElement('canvas'); cv.className = 'confetti'; body.appendChild(cv);
    var ctx = cv.getContext('2d'), W = cv.width = innerWidth, H = cv.height = innerHeight;
    var colors = ['#f6e2a6', '#ddb25c', '#a8792a', '#5a8cff', '#ffffff'];
    var parts = Array.from({ length: 140 }, function () {
      return { x: W / 2, y: H * 0.3, vx: (Math.random() - 0.5) * 16, vy: -Math.random() * 14 - 4, s: Math.random() * 6 + 3, r: Math.random() * 6, vr: (Math.random() - 0.5) * 0.3, c: colors[Math.random() * colors.length | 0] };
    });
    var frames = 0;
    setTimeout(function draw() {
      ctx.clearRect(0, 0, W, H);
      parts.forEach(function (p) {
        p.vy += 0.35; p.vx *= 0.99; p.x += p.vx; p.y += p.vy; p.r += p.vr;
        ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.r); ctx.fillStyle = p.c; ctx.fillRect(-p.s / 2, -p.s / 4, p.s, p.s / 2); ctx.restore();
      });
      if (++frames < 200) requestAnimationFrame(draw); else cv.remove();
    }, 900);
  }

  /* ---------- Réajustements ---------- */
  window.addEventListener('load', function () { if (hasGsap) ScrollTrigger.refresh(); });
})();
