/* ==========================================================================
   SARGORA — mode statique (GitHub Pages, sans PHP)
   Panier conservé dans le navigateur ; commandes et devis envoyés sur WhatsApp.
   ========================================================================== */
(function () {
  'use strict';

  var body = document.body;
  var BASE = body.getAttribute('data-base') || '';
  var WA = body.getAttribute('data-wa') || '';
  var KEY = 'sg-cart';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var dataP = fetch(BASE + '/assets/data/shop.json').then(function (r) { return r.json(); });

  var load = function () { try { return JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { return {}; } };
  var save = function (c) { try { localStorage.setItem(KEY, JSON.stringify(c)); } catch (e) {} };
  var waLink = function (text) { return 'https://wa.me/' + WA + '?text=' + encodeURIComponent(text); };

  var money = function (data, m) {
    var f = Math.pow(10, data.decimals), whole = m % f === 0;
    var s = (m / f).toLocaleString('fr-FR', { minimumFractionDigits: whole ? 0 : data.decimals, maximumFractionDigits: whole ? 0 : data.decimals });
    return s + ' ' + data.currency;
  };

  var payload = function (data) {
    var cart = load(), items = [], count = 0, total = 0, clean = {};
    Object.keys(cart).forEach(function (key) {
      var parts = key.split(':'), p = data.products[parts[0]], opt = p && p.options[+parts[1]];
      var qty = Math.max(0, Math.min(10, parseInt(cart[key], 10) || 0));
      if (!p || !opt || !p.in_stock || !qty) return;
      clean[key] = qty;
      var line = opt.price * qty;
      count += qty; total += line;
      items.push({ key: key, name: p.name, option: opt.label, qty: qty, slug: p.slug, theme: p.theme, glyph: p.glyph,
        unit: money(data, opt.price), line: money(data, line), lineRaw: line });
    });
    save(clean);
    return { ok: true, count: count, total: money(data, total), totalRaw: total, items: items };
  };

  var api = function (fd) {
    return dataP.then(function (data) {
      var ok = true;
      if (fd) {
        var cart = load(), action = fd.get('action');
        if (action === 'add') {
          var id = String(fd.get('id')), opt = parseInt(fd.get('opt'), 10) || 0, p = data.products[id];
          ok = !!(p && p.in_stock && p.options[opt]);
          if (ok) { var k = id + ':' + opt; cart[k] = Math.min(10, (cart[k] || 0) + Math.max(1, parseInt(fd.get('qty') || '1', 10) || 1)); }
        } else if (action === 'set' || action === 'remove') {
          var q = action === 'remove' ? 0 : parseInt(fd.get('qty'), 10) || 0;
          if (q <= 0) delete cart[fd.get('key')]; else cart[fd.get('key')] = Math.min(10, q);
        }
        save(cart);
      }
      var out = payload(data);
      if (!ok) { out.ok = false; out.error = 'Ce produit n’est pas disponible.'; }
      updateBadges(out.count);
      return out;
    });
  };

  var updateBadges = function (n) {
    $$('[data-cart-count]').forEach(function (el) { el.textContent = n; el.classList.toggle('is-on', n > 0); });
  };

  window.SGStore = { api: api };

  var svg = function (p) { return '<svg class="ico" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + p + '</svg>'; };
  var I = {
    trash: svg('<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>'),
    right: svg('<path d="M5 12h14M13 6l6 6-6 6"/>'),
    left: svg('<path d="M19 12H5M11 18l-6-6 6-6"/>'),
    bag: svg('<path d="M6 7h12l1 13H5L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/>'),
    lock: svg('<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>'),
    wa: svg('<path d="M4 20l1.3-3.9A8.5 8.5 0 1 1 8.2 19L4 20Z"/><path d="M9 8.6c0 3 2.4 5.9 5.6 6.4l1.2-1.5-2-1-1 .9c-1-.5-1.9-1.4-2.4-2.4l.9-1-1-2L9 8.6Z"/>'),
  };
  var check = '<span class="order-hero__icon is-celebrate"><svg viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="m15 27 7.5 7.5L38 19"/></svg></span>';
  var emptyBlock = '<div class="empty empty--big">' + I.bag + '<p><strong>Votre panier est vide.</strong> Découvrez nos abonnements digitaux payables en dinars.</p><a class="btn btn--gold" href="' + BASE + '/boutique"><span>Explorer la boutique</span>' + I.right + '</a></div>';

  /* ---------- Page panier ---------- */
  var cartBox = $('[data-static-cart]');
  var renderCart = function (c) {
    if (!c.items.length) { cartBox.innerHTML = emptyBlock; return; }
    cartBox.innerHTML = '<div class="cart"><div class="cart__list">' + c.items.map(function (it) {
      var href = BASE + '/produit/' + esc(it.slug);
      return '<div class="cart__row" data-key="' + esc(it.key) + '">' +
        '<a class="pthumb th-' + esc(it.theme) + '" href="' + href + '" aria-hidden="true" tabindex="-1"><span>' + esc(it.glyph) + '</span></a>' +
        '<div class="cart__info"><a class="cart__name" href="' + href + '">' + esc(it.name) + '</a><span class="cart__opt">' + esc(it.option) + ' · ' + esc(it.unit) + '</span></div>' +
        '<label class="qty qty--sm"><span class="sr-only">Quantité</span><input type="number" min="0" max="10" value="' + it.qty + '" data-static-qty></label>' +
        '<strong class="cart__line">' + esc(it.line) + '</strong>' +
        '<button class="icon-btn" type="button" data-static-remove aria-label="Retirer ' + esc(it.name) + '">' + I.trash + '</button></div>';
    }).join('') + '<div class="cart__actions"><a class="link-arrow" href="' + BASE + '/boutique">' + I.left + ' Continuer mes achats</a></div></div>' +
      '<aside class="summary glass"><h2>Récapitulatif</h2><div class="summary__row"><span>Articles</span><span>' + c.count + '</span></div>' +
      '<div class="summary__row"><span>Livraison digitale</span><span>Offerte</span></div>' +
      '<div class="summary__total"><span>Total</span><strong>' + esc(c.total) + '</strong></div>' +
      '<a class="btn btn--gold btn--block btn--lg" href="' + BASE + '/commande"><span>Passer la commande</span>' + I.right + '</a>' +
      '<p class="summary__note">' + I.lock + ' Paiement en dinars : D17, Flouci, virement ou espèces.</p></aside></div>';
  };
  if (cartBox) {
    api().then(renderCart);
    cartBox.addEventListener('change', function (e) {
      if (!e.target.matches('[data-static-qty]')) return;
      var fd = new FormData(); fd.append('action', 'set'); fd.append('key', e.target.closest('[data-key]').getAttribute('data-key')); fd.append('qty', e.target.value);
      api(fd).then(renderCart);
    });
    cartBox.addEventListener('click', function (e) {
      var b = e.target.closest('[data-static-remove]'); if (!b) return;
      var fd = new FormData(); fd.append('action', 'remove'); fd.append('key', b.closest('[data-key]').getAttribute('data-key'));
      api(fd).then(renderCart);
    });
  } else {
    api();
  }

  /* ---------- Validation de formulaire ---------- */
  var setErr = function (input, msg) {
    var wrap = input.closest('.field, .terms') || input.parentNode;
    var old = $('.field__err', wrap); if (old) old.remove();
    wrap.classList.toggle('has-err', !!msg);
    if (msg) { var s = document.createElement('span'); s.className = 'field__err'; s.textContent = msg; wrap.appendChild(s); }
    return !msg;
  };
  var validEmail = function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v); };
  var validPhone = function (v) { return /^\+?[0-9 ().-]{8,20}$/.test(v); };
  var refCode = function () {
    var L = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    return 'SG-' + String(new Date().getFullYear()).slice(2) + L[Math.random() * 24 | 0] + L[Math.random() * 24 | 0] + (1000 + Math.floor(Math.random() * 9000));
  };

  /* ---------- Commande ---------- */
  var co = $('[data-static-checkout]');
  if (co) {
    var list = $('[data-static-items]', co), totalEl = $('[data-static-total]', co);
    api().then(function (c) {
      if (!c.items.length) { co.parentNode.innerHTML = emptyBlock; return; }
      list.innerHTML = c.items.map(function (it) {
        return '<li><span class="pthumb pthumb--xs th-' + esc(it.theme) + '"><span>' + esc(it.glyph) + '</span></span><span class="summary__name">' + esc(it.name) + '<small>' + esc(it.option) + ' × ' + it.qty + '</small></span><strong>' + esc(it.line) + '</strong></li>';
      }).join('');
      totalEl.textContent = c.total;
    });
    co.addEventListener('submit', function (e) {
      e.preventDefault();
      var f = co.elements, ok = true;
      ok = setErr(f.name, f.name.value.trim().length < 3 ? 'Indiquez votre nom complet.' : '') && ok;
      ok = setErr(f.email, validEmail(f.email.value.trim()) ? '' : 'Adresse e-mail invalide.') && ok;
      ok = setErr(f.phone, validPhone(f.phone.value.trim()) ? '' : 'Numéro de téléphone invalide.') && ok;
      ok = setErr(f.terms, f.terms.checked ? '' : 'Vous devez accepter les conditions générales de vente.') && ok;
      if (!ok) { var first = $('.has-err input, .has-err textarea', co); if (first) first.focus(); return; }
      var pay = (co.querySelector('input[name=payment]:checked') || {}).value || 'd17';
      Promise.all([dataP, api()]).then(function (r) {
        var data = r[0], c = r[1], pm = data.payments[pay] || { label: pay, steps: [] }, ref = refCode();
        var lines = ['Bonjour Sargora, voici ma commande ' + ref + ' :'];
        c.items.forEach(function (it) { lines.push('• ' + it.name + ' (' + it.option + ') × ' + it.qty + ' = ' + it.line); });
        lines.push('Total : ' + c.total, 'Paiement : ' + pm.label, '', 'Nom : ' + f.name.value.trim(), 'E-mail : ' + f.email.value.trim(), 'Téléphone : ' + f.phone.value.trim());
        if (f.note.value.trim()) lines.push('Précisions : ' + f.note.value.trim());
        var link = waLink(lines.join('\n'));
        window.open(link, '_blank', 'noopener');
        save({}); updateBadges(0);
        var box = co.parentNode;
        box.innerHTML = '<div class="order-hero__inner" style="text-align:center;margin-bottom:40px">' + check +
          '<h2 class="h1">Plus qu’une <em>étape !</em></h2>' +
          '<p class="lead" style="margin-inline:auto">Envoyez votre commande sur WhatsApp pour la valider, puis réglez-la avec les instructions ci-dessous. Nous activons vos accès dès réception du paiement.</p>' +
          '<div class="order-ref"><span>Référence</span><strong>' + esc(ref) + '</strong></div></div>' +
          '<div class="order-grid"><div class="order-main"><div class="panel glass"><h2 class="panel__title">1. Valider sur WhatsApp</h2>' +
          '<a class="btn btn--gold btn--lg" href="' + esc(link) + '" target="_blank" rel="noopener">' + I.wa + '<span>Envoyer ma commande</span></a></div>' +
          '<div class="panel glass"><h2 class="panel__title">2. Payer par ' + esc(pm.label) + '</h2><p class="pay-amount">Montant à régler : <strong>' + esc(c.total) + '</strong></p><ol class="pay-steps">' +
          pm.steps.map(function (s) { return '<li>' + esc(s) + '</li>'; }).join('') + '</ol></div></div>' +
          '<aside class="summary glass"><h2>Détail</h2><ul class="summary__items">' + c.items.map(function (it) {
            return '<li><span class="summary__name">' + esc(it.name) + '<small>' + esc(it.option) + ' × ' + it.qty + '</small></span><strong>' + esc(it.line) + '</strong></li>';
          }).join('') + '</ul><div class="summary__total"><span>Total</span><strong>' + esc(c.total) + '</strong></div></aside></div>';
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
  }

  /* ---------- Devis ---------- */
  var dv = $('[data-static-contact]');
  if (dv) {
    var wanted = new URLSearchParams(location.search).get('service');
    if (wanted) { var box = dv.querySelector('input[name="services[]"][value="' + wanted.replace(/[^a-z0-9-]/g, '') + '"]'); if (box) box.checked = true; }
    dv.addEventListener('submit', function (e) {
      e.preventDefault();
      var f = dv.elements, ok = true;
      ok = setErr(f.name, f.name.value.trim().length < 2 ? 'Indiquez votre nom.' : '') && ok;
      ok = setErr(f.email, validEmail(f.email.value.trim()) ? '' : 'Adresse e-mail invalide.') && ok;
      ok = setErr(f.phone, !f.phone.value.trim() || validPhone(f.phone.value.trim()) ? '' : 'Numéro invalide.') && ok;
      ok = setErr(f.message, f.message.value.trim().length < 10 ? 'Décrivez votre projet en quelques mots (10 caractères minimum).' : '') && ok;
      if (!ok) return;
      var pick = function (sel) { return $$(sel, dv).filter(function (i) { return i.checked; }).map(function (i) { return i.nextElementSibling.textContent.trim(); }); };
      var lines = ['Bonjour Sargora, voici ma demande de devis :'];
      var svc = pick('input[name="services[]"]'), bud = pick('input[name=budget]'), tl = pick('input[name=timeline]');
      if (svc.length) lines.push('Services : ' + svc.join(', '));
      if (bud.length) lines.push('Budget : ' + bud[0]);
      if (tl.length) lines.push('Délai : ' + tl[0]);
      lines.push('', 'Nom : ' + f.name.value.trim());
      if (f.company.value.trim()) lines.push('Entreprise : ' + f.company.value.trim());
      lines.push('E-mail : ' + f.email.value.trim());
      if (f.phone.value.trim()) lines.push('Téléphone : ' + f.phone.value.trim());
      lines.push('', f.message.value.trim());
      var link = waLink(lines.join('\n'));
      window.open(link, '_blank', 'noopener');
      dv.outerHTML = '<div class="panel glass sent">' + check + '<h2 class="h2">Demande <em>prête.</em></h2>' +
        '<p class="lead">Merci ' + esc(f.name.value.trim()) + ' ! Votre demande s’est ouverte dans WhatsApp : appuyez sur « Envoyer » pour nous la transmettre. Nous répondons sous 24 h ouvrées.</p>' +
        '<a class="btn btn--gold" href="' + esc(link) + '" target="_blank" rel="noopener">' + I.wa + '<span>Ouvrir WhatsApp</span></a></div>';
    });
  }
})();
