/* Sargora — administration */
(function () {
  'use strict';
  document.addEventListener('click', function (e) {
    var tog = e.target.closest('[data-side-toggle]');
    if (tog) { document.getElementById('side').classList.toggle('is-open'); return; }
    var row = e.target.closest('tr[data-href]');
    if (row && !e.target.closest('a, button, form')) { location.href = row.getAttribute('data-href'); return; }
    var btn = e.target.closest('[data-confirm-btn]');
    if (btn && !confirm(btn.getAttribute('data-confirm-btn'))) { e.preventDefault(); return; }
    var copy = e.target.closest('[data-copy-btn]');
    if (copy) {
      var input = copy.parentNode.querySelector('[data-copy-input]');
      input.select();
      if (navigator.clipboard) navigator.clipboard.writeText(input.value);
      copy.style.color = '#3ddc97';
      setTimeout(function () { copy.style.color = ''; }, 1200);
    }
  });
  document.addEventListener('submit', function (e) {
    var f = e.target.closest('[data-confirm]');
    if (f && !confirm(f.getAttribute('data-confirm'))) e.preventDefault();
  });
  // Aperçu en direct de la carte produit
  var theme = document.querySelector('[data-theme-select]');
  var glyph = document.querySelector('[data-glyph-input]');
  var card = document.querySelector('.preview .pcard');
  if (card) {
    var nameIn = document.querySelector('input[name="name"]');
    if (theme) theme.addEventListener('change', function () { card.className = card.className.replace(/th-[a-z0-9-]+/, 'th-' + theme.value); });
    if (glyph) glyph.addEventListener('input', function () { card.querySelector('.pcard__glyph').textContent = glyph.value; });
    if (nameIn) nameIn.addEventListener('input', function () { card.querySelector('.pcard__name').textContent = nameIn.value; });
  }
})();
