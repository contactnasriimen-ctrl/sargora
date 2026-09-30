/* Sargora — exécuté avant l'affichage : classes d'état sur <html> */
(function (d) {
  var h = d.documentElement;
  h.classList.remove('no-js');
  h.classList.add('js');
  try {
    if (sessionStorage.getItem('sg-intro')) h.classList.add('intro-seen');
    if (sessionStorage.getItem('sg-nav')) { h.classList.add('is-arriving'); sessionStorage.removeItem('sg-nav'); }
  } catch (e) { h.classList.add('intro-seen'); }
  if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) h.classList.add('reduced');
})(document);
