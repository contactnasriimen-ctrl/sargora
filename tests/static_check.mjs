// Vérifie l'export statique servi sous /sargora/ comme sur GitHub Pages.  node tests/static_check.mjs
import http from 'http';
import fs from 'fs';
import path from 'path';
import { createRequire } from 'module';
const require = createRequire('D:/Projects/_e2e-tools/package.json');
const puppeteer = require('puppeteer-core');
const DIST = path.resolve('dist'), PORT = 8098, BASE = '/sargora';
const types = { '.html': 'text/html; charset=utf-8', '.js': 'text/javascript', '.css': 'text/css', '.json': 'application/json', '.webp': 'image/webp', '.png': 'image/png', '.jpg': 'image/jpeg', '.xml': 'application/xml', '.webmanifest': 'application/manifest+json' };
const srv = http.createServer((req, res) => {
  let p = decodeURIComponent(req.url.split('?')[0]);
  if (!p.startsWith(BASE)) { res.writeHead(404); return res.end(); }
  p = p.slice(BASE.length) || '/';
  let f = path.join(DIST, p);
  if (fs.existsSync(f) && fs.statSync(f).isDirectory()) {
    if (!p.endsWith('/')) { res.writeHead(301, { Location: BASE + p + '/' }); return res.end(); }
    f = path.join(f, 'index.html');
  }
  if (!fs.existsSync(f)) { res.writeHead(404, { 'Content-Type': types['.html'] }); return res.end(fs.readFileSync(path.join(DIST, '404.html'))); }
  res.writeHead(200, { 'Content-Type': types[path.extname(f)] || 'application/octet-stream' });
  res.end(fs.readFileSync(f));
}).listen(PORT);

let ok = 0, fail = 0;
const check = (c, l) => { c ? ok++ : fail++; console.log((c ? '  ✓ ' : '  ✗ ') + l); };
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
const browser = await puppeteer.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: 'new', args: ['--use-angle=swiftshader', '--enable-unsafe-swiftshader'] });
const page = await browser.newPage();
const errors = [], missing = [];
page.on('pageerror', (e) => errors.push(e.message));
page.on('console', (m) => { if (m.type() === 'error' && !page.url().includes('nexiste-pas')) errors.push(m.text()); });
page.on('response', (r) => { if (r.status() >= 400 && r.url().includes(':' + PORT) && !r.url().includes('nexiste-pas')) missing.push(r.status() + ' ' + r.url()); });
await page.setViewport({ width: 1366, height: 860 });
await page.evaluateOnNewDocument(() => { window.open = (u) => { sessionStorage.setItem('opened', u); return null; }; });
const opened = () => page.evaluate(() => sessionStorage.getItem('opened') || '');
const U = (p) => `http://localhost:${PORT}${BASE}${p}`;

await page.goto(U('/'), { waitUntil: 'networkidle2' });
await sleep(5000);
check(await page.$eval('.hero', (h) => h.classList.contains('is-3d')), 'accueil : scène 3D active');
await page.goto(U('/boutique/'), { waitUntil: 'networkidle2' });
await page.click('.ptile [data-add-to-cart] button');
await page.waitForFunction(() => document.querySelector('[data-cart-count]').textContent.trim() === '1', { timeout: 8000 });
check(true, 'ajout au panier (stockage local)');
await page.goto(U('/produit/canva-pro/'), { waitUntil: 'networkidle2' });
await page.click('.opt:nth-of-type(2) .opt__box');
await page.click('.buy__add');
await page.waitForSelector('[data-drawer].is-open .ditem', { timeout: 8000 });
check((await page.$$('.ditem')).length === 2, 'tiroir panier : 2 produits');
await page.goto(U('/panier/'), { waitUntil: 'networkidle2' });
await page.waitForSelector('.cart__row', { timeout: 8000 });
check((await page.$$('.cart__row')).length === 2, 'page panier : 2 lignes, total ' + (await page.$eval('.summary__total strong', (e) => e.textContent)));
await page.goto(U('/commande/'), { waitUntil: 'networkidle2' });
await page.waitForFunction(() => document.querySelectorAll('[data-static-items] li').length === 2, { timeout: 8000 });
check(true, 'commande : récapitulatif affiché');
await page.click('button[type=submit].btn--gold');
check((await page.$$('.field__err')).length >= 3, 'validation des champs');
await page.type('input[name=name]', 'Client Test');
await page.type('input[name=email]', 'client@example.com');
await page.type('input[name=phone]', '+216 22 333 444');
await page.click('.terms__box');
await page.click('button[type=submit].btn--gold');
await sleep(800);
const waUrl = await opened();
check(/wa\.me|whatsapp/.test(waUrl) && decodeURIComponent(waUrl).includes('Canva Pro'), 'WhatsApp ouvert avec la commande');
await page.waitForSelector('.order-ref', { timeout: 8000 });
check(await page.$eval('[data-cart-count]', (e) => e.textContent.trim()) === '0', 'confirmation + panier vidé');
await page.goto(U('/contact/?service=branding'), { waitUntil: 'networkidle2' });
check(await page.$eval('input[value=branding]', (e) => e.checked), 'devis : service pré-coché');
await page.type('input[name=name]', 'Sonia');
await page.type('input[name=email]', 'sonia@example.com');
await page.type('textarea[name=message]', 'Nous voulons une nouvelle identité.');
await page.click('.devis button[type=submit]');
await sleep(800);
const waDevis = decodeURIComponent(await opened());
check(/wa\.me/.test(waDevis) && waDevis.includes('Branding') && waDevis.includes('Sonia'), 'devis envoyé vers WhatsApp (services + nom)');
for (const p of ['/services/', '/services/branding/', '/realisations/', '/realisations/olivea/', '/agence/', '/suivi/', '/cgv/']) {
  const r = await page.goto(U(p), { waitUntil: 'domcontentloaded' });
  check(r.status() === 200, 'page ' + p);
}
const r404 = await page.goto(U('/nexiste-pas'), { waitUntil: 'networkidle2' });
check(r404.status() === 404 && (await page.content()).includes('éteinte'), 'page 404 stylée');
check(missing.length === 0, 'aucune ressource manquante' + (missing.length ? ' : ' + missing.slice(0, 5).join(' | ') : ''));
check(errors.length === 0, 'aucune erreur JavaScript' + (errors.length ? ' : ' + errors.slice(0, 5).join(' | ') : ''));
console.log(`\nRÉSULTAT : ${ok} réussis, ${fail} échecs`);
await browser.close(); srv.close();
process.exit(fail ? 1 : 0);
