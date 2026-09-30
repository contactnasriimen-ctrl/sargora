// node _build/shots.mjs <path> <prefix> <width> <height> <y1,y2,...>
import { createRequire } from 'module';
const require = createRequire('D:/Projects/_e2e-tools/package.json');
const puppeteer = require('puppeteer-core');
const [, , path = '/', prefix = 's', w = '1440', h = '900', ys = '0'] = process.argv;
const mobile = +w < 600;
const browser = await puppeteer.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: 'new', args: ['--use-angle=swiftshader', '--enable-unsafe-swiftshader', '--hide-scrollbars'] });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', (e) => errors.push('pageerror: ' + e.message));
page.on('console', (m) => { if (m.type() === 'error') errors.push(m.text()); });
await page.setViewport({ width: +w, height: +h, isMobile: mobile, hasTouch: mobile });
if (mobile) await page.setUserAgent('Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0 Mobile Safari/537.36');
await page.evaluateOnNewDocument(() => { try { sessionStorage.setItem('sg-intro', '1'); } catch (e) {} });
await page.goto('http://localhost:8090' + path, { waitUntil: 'networkidle2', timeout: 60000 });
await new Promise((r) => setTimeout(r, 2500));
let cur = 0;
for (const [i, y] of ys.split(',').map(Number).entries()) {
  const step = y > cur ? 250 : -250;
  for (let yy = cur; step > 0 ? yy < y : yy > y; yy += step) { await page.evaluate((v) => window.scrollTo(0, v), yy); await new Promise((r) => setTimeout(r, 40)); }
  await page.evaluate((v) => window.scrollTo(0, v), y); cur = y;
  await new Promise((r) => setTimeout(r, 1600));
  await page.screenshot({ path: `${prefix}-${i}.png` });
}
const info = await page.evaluate(() => ({ overflow: document.documentElement.scrollWidth - innerWidth, height: document.documentElement.scrollHeight }));
console.log(JSON.stringify({ ...info, errors }));
await browser.close();
