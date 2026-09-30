/* ==========================================================================
   SARGORA — scène 3D du hero (three.js)
   L'emblème reconstruit en 3D : anneau d'or ouvert, flammes or & bleu
   métalliques, perles d'or, braises flottantes, halo et bloom cinématique.
   ========================================================================== */
import * as THREE from '../vendor/three/build/three.module.min.js';
import { RoomEnvironment } from '../vendor/three/jsm/environments/RoomEnvironment.js';
import { EffectComposer } from '../vendor/three/jsm/postprocessing/EffectComposer.js';
import { RenderPass } from '../vendor/three/jsm/postprocessing/RenderPass.js';
import { UnrealBloomPass } from '../vendor/three/jsm/postprocessing/UnrealBloomPass.js';
import { OutputPass } from '../vendor/three/jsm/postprocessing/OutputPass.js';

const easeOut = (t) => 1 - Math.pow(1 - t, 3);
const easeBack = (t) => { const c1 = 1.5, c3 = c1 + 1; return 1 + c3 * Math.pow(t - 1, 3) + c1 * Math.pow(t - 1, 2); };
const clamp01 = (t) => Math.min(1, Math.max(0, t));

/* Feuille / flamme : surface nervurée, courbée et vrillée */
function bladeGeometry({ len, width, bend, twist, crease = 0.22, sway = 0.12, asym = 0.25 }) {
  const U = 12, V = 72;
  const pos = [], uv = [], idx = [];
  for (let j = 0; j <= V; j++) {
    const t = j / V;
    const w = width * Math.pow(Math.sin(Math.PI * Math.pow(t, 0.72)), 0.9);
    const cx = bend * len * t * t * 0.42;
    const cy = len * t;
    const cz = sway * Math.sin(t * Math.PI);
    const a = twist * t;
    const ca = Math.cos(a), sa = Math.sin(a);
    for (let i = 0; i <= U; i++) {
      const u = (i / U) * 2 - 1;
      const xl = (u + asym * (1 - u * u)) * w;
      const zl = crease * (1 - Math.abs(u)) * w;
      pos.push(cx + xl * ca + zl * sa, cy, cz - xl * sa + zl * ca);
      uv.push(i / U, t);
    }
  }
  for (let j = 0; j < V; j++) {
    for (let i = 0; i < U; i++) {
      const a = j * (U + 1) + i, b = a + U + 1;
      idx.push(a, b, a + 1, b, b + 1, a + 1);
    }
  }
  const g = new THREE.BufferGeometry();
  g.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
  g.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
  g.setIndex(idx);
  g.computeVertexNormals();
  return g;
}

function glowTexture(inner, mid, size = 256) {
  const c = document.createElement('canvas');
  c.width = c.height = size;
  const ctx = c.getContext('2d');
  const g = ctx.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
  g.addColorStop(0, inner);
  g.addColorStop(0.18, mid);
  g.addColorStop(1, 'rgba(0,0,0,0)');
  ctx.fillStyle = g;
  ctx.fillRect(0, 0, size, size);
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  return tex;
}

function flareTexture(color, size = 256) {
  const c = document.createElement('canvas');
  c.width = c.height = size;
  const ctx = c.getContext('2d');
  const h = size / 2;
  const lg = ctx.createLinearGradient(0, h, size, h);
  lg.addColorStop(0, 'rgba(0,0,0,0)'); lg.addColorStop(0.5, color); lg.addColorStop(1, 'rgba(0,0,0,0)');
  ctx.fillStyle = lg; ctx.fillRect(0, h - 1.5, size, 3);
  const vg = ctx.createLinearGradient(h, 0, h, size);
  vg.addColorStop(0, 'rgba(0,0,0,0)'); vg.addColorStop(0.5, color); vg.addColorStop(1, 'rgba(0,0,0,0)');
  ctx.fillStyle = vg; ctx.fillRect(h - 1.5, size * 0.2, 3, size * 0.6);
  const rg = ctx.createRadialGradient(h, h, 0, h, h, size * 0.14);
  rg.addColorStop(0, 'rgba(255,255,255,1)'); rg.addColorStop(1, 'rgba(255,255,255,0)');
  ctx.fillStyle = rg; ctx.fillRect(0, 0, size, size);
  const tex = new THREE.CanvasTexture(c);
  tex.colorSpace = THREE.SRGBColorSpace;
  return tex;
}

export function initHero(canvas, opts = {}) {
  const mobile = !!opts.mobile;
  const renderer = new THREE.WebGLRenderer({ canvas, antialias: !mobile, alpha: false, powerPreference: 'high-performance' });
  const dpr = Math.min(window.devicePixelRatio || 1, mobile ? 1.5 : 1.6);
  renderer.setPixelRatio(dpr);
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 0.92;
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.setClearColor(0x060913, 1);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 100);
  camera.position.set(0, 0, 10);

  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
  scene.environmentIntensity = 0.95;

  /* ---- Fond : nébuleuse animée ---- */
  const nebula = new THREE.Mesh(
    new THREE.PlaneGeometry(60, 34),
    new THREE.ShaderMaterial({
      depthWrite: false,
      uniforms: { uTime: { value: 0 }, uShift: { value: new THREE.Vector2(0.72, 0.55) } },
      vertexShader: 'varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }',
      fragmentShader: `
        uniform float uTime; uniform vec2 uShift; varying vec2 vUv;
        float hash(vec2 p){ return fract(sin(dot(p, vec2(127.1,311.7))) * 43758.5453); }
        float noise(vec2 p){ vec2 i=floor(p), f=fract(p); vec2 u=f*f*(3.0-2.0*f);
          return mix(mix(hash(i),hash(i+vec2(1,0)),u.x), mix(hash(i+vec2(0,1)),hash(i+vec2(1,1)),u.x), u.y); }
        float fbm(vec2 p){ float v=0.0, a=0.5; for(int k=0;k<5;k++){ v+=a*noise(p); p*=2.03; a*=0.5; } return v; }
        void main(){
          vec2 uv = vUv; uv.x *= 1.76;
          float n = fbm(uv*2.4 + vec2(uTime*0.018, -uTime*0.012));
          float n2 = fbm(uv*5.0 - vec2(uTime*0.01, uTime*0.02));
          vec2 c = vec2(uShift.x*1.76, uShift.y);
          float d = distance(uv, c);
          vec3 base = vec3(0.0018, 0.0028, 0.0068);
          vec3 blue = vec3(0.010, 0.030, 0.120) * smoothstep(1.1, 0.0, d) * (0.55 + 0.9*n);
          vec3 deep = vec3(0.004, 0.010, 0.050) * smoothstep(0.9, 0.2, vUv.y) * n2;
          vec3 gold = vec3(0.090, 0.050, 0.012) * smoothstep(0.75, 0.0, distance(uv, c + vec2(0.35, 0.28))) * n * n;
          gl_FragColor = vec4(base + blue + deep + gold, 1.0);
          #include <tonemapping_fragment>
          #include <colorspace_fragment>
        }`
    })
  );
  nebula.position.z = -12;
  scene.add(nebula);

  /* ---- Lumières ---- */
  const key = new THREE.DirectionalLight(0xffe0b0, 2.0);
  key.position.set(-4, 6, 6);
  scene.add(key);
  const rim = new THREE.DirectionalLight(0x5b8cff, 2.2);
  rim.position.set(5, -2, -4);
  scene.add(rim);
  const baseLight = new THREE.PointLight(0x4d7dff, 7, 6, 2);
  scene.add(baseLight);
  const sweep = new THREE.PointLight(0xffd79a, 22, 9, 2);
  scene.add(sweep);

  /* ---- Emblème ---- */
  const emblem = new THREE.Group();
  const holder = new THREE.Group();
  holder.add(emblem);
  scene.add(holder);

  const gold = new THREE.MeshPhysicalMaterial({ color: 0xe8b95e, metalness: 1, roughness: 0.2, clearcoat: 0.6, clearcoatRoughness: 0.18, side: THREE.DoubleSide });
  const goldRing = new THREE.MeshPhysicalMaterial({ color: 0xe3b25a, metalness: 1, roughness: 0.16, clearcoat: 0.8, clearcoatRoughness: 0.1 });
  const blue = new THREE.MeshPhysicalMaterial({ color: 0x1238a8, metalness: 0.55, roughness: 0.28, clearcoat: 0.7, clearcoatRoughness: 0.08, envMapIntensity: 0.7, emissive: 0x031040, emissiveIntensity: 0.4, side: THREE.DoubleSide });

  // Anneau ouvert en haut (comme le logo) + filet intérieur
  const R = 2.3, gap = 0.2;
  const ring = new THREE.Mesh(new THREE.TorusGeometry(R, 0.1, 40, 260, Math.PI * 2 - gap * 2), goldRing);
  ring.rotation.z = Math.PI / 2 + gap;
  ring.scale.z = 0.7;
  emblem.add(ring);
  const inner = new THREE.Mesh(new THREE.TorusGeometry(R - 0.2, 0.022, 16, 260, Math.PI * 2 - gap * 2.6), goldRing);
  inner.rotation.z = Math.PI / 2 + gap * 1.3;
  emblem.add(inner);
  [Math.PI / 2 + gap, Math.PI / 2 - gap].forEach((a) => {
    const cap = new THREE.Mesh(new THREE.SphereGeometry(0.1, 24, 16), goldRing);
    cap.position.set(Math.cos(a) * R, Math.sin(a) * R, 0);
    cap.scale.z = 0.7;
    emblem.add(cap);
  });

  // Gerbe de flammes (angle en degrés depuis la verticale, positif = gauche)
  const baseY = -R + 0.28;
  const specs = [
    { a: 44, len: 2.3, w: 0.36, bend: -0.55, tw: 0.9, m: 'gold', z: 0.05 },
    { a: 30, len: 3.0, w: 0.34, bend: 0.25, tw: -0.8, m: 'blue', z: -0.1 },
    { a: 17, len: 3.5, w: 0.33, bend: 0.4, tw: 0.9, m: 'blue', z: 0.12 },
    { a: 6, len: 4.3, w: 0.32, bend: -0.25, tw: -0.7, m: 'gold', z: 0.22 },
    { a: -4, len: 3.6, w: 0.3, bend: 0.55, tw: 0.8, m: 'gold', z: 0.0 },
    { a: -14, len: 3.5, w: 0.34, bend: 0.65, tw: -0.9, m: 'blue', z: 0.18 },
    { a: -27, len: 3.2, w: 0.34, bend: 0.8, tw: 0.8, m: 'blue', z: -0.05 },
    { a: -40, len: 2.9, w: 0.34, bend: 0.95, tw: -0.7, m: 'gold', z: 0.1 },
    { a: -54, len: 2.2, w: 0.3, bend: 1.05, tw: 0.6, m: 'blue', z: -0.12 },
  ];
  const blades = specs.map((s, i) => {
    const g = bladeGeometry({ len: s.len, width: s.w * 1.55, bend: s.bend, twist: s.tw * 0.8, crease: 0.3, sway: 0.08 + (i % 3) * 0.04 });
    const mesh = new THREE.Mesh(g, s.m === 'gold' ? gold : blue);
    const pivot = new THREE.Group();
    pivot.position.set(0, baseY, s.z);
    pivot.rotation.z = THREE.MathUtils.degToRad(s.a);
    pivot.add(mesh);
    emblem.add(pivot);
    return { pivot, mesh, base: pivot.rotation.z, phase: i * 0.7, delay: 0.25 + Math.abs(i - 4) * 0.08 };
  });

  // Perles d'or
  const beads = [[0.95, 1.65, 0.1, 0.13], [1.45, 1.05, 0.05, 0.1], [-0.8, 1.35, 0.05, 0.1], [-1.25, 0.9, 0, 0.06], [1.75, 0.35, 0.1, 0.06], [-0.35, 1.85, -0.1, 0.05]]
    .map(([x, y, z, r], i) => {
      const m = new THREE.Mesh(new THREE.SphereGeometry(r, 32, 24), goldRing);
      m.position.set(x, y, z);
      emblem.add(m);
      return { m, y, phase: i * 1.3 };
    });

  // Halos : étincelle bleue à la base, éclat doré sur l'anneau
  const baseGlow = new THREE.Sprite(new THREE.SpriteMaterial({ map: glowTexture('rgba(220,235,255,1)', 'rgba(70,120,255,.55)'), blending: THREE.AdditiveBlending, depthWrite: false, transparent: true }));
  baseGlow.position.set(0, baseY, 0.4);
  baseGlow.scale.set(1, 1, 1);
  emblem.add(baseGlow);
  const baseFlare = new THREE.Sprite(new THREE.SpriteMaterial({ map: flareTexture('rgba(140,180,255,.9)'), blending: THREE.AdditiveBlending, depthWrite: false, transparent: true }));
  baseFlare.position.copy(baseGlow.position);
  baseFlare.scale.set(2.2, 1.1, 1);
  emblem.add(baseFlare);
  const ringFlare = new THREE.Sprite(new THREE.SpriteMaterial({ map: flareTexture('rgba(255,225,160,.95)'), blending: THREE.AdditiveBlending, depthWrite: false, transparent: true }));
  const fa = THREE.MathUtils.degToRad(140);
  ringFlare.position.set(Math.cos(fa) * R, Math.sin(fa) * R, 0.35);
  ringFlare.scale.set(1.6, 1.6, 1);
  emblem.add(ringFlare);
  baseLight.position.set(0, baseY + 0.3, 1.2);

  // Aura derrière l'emblème
  const aura = new THREE.Sprite(new THREE.SpriteMaterial({ map: glowTexture('rgba(60,100,230,.55)', 'rgba(30,60,170,.25)'), blending: THREE.AdditiveBlending, depthWrite: false, transparent: true, opacity: 0.3 }));
  aura.position.set(0, 0.1, -1.2);
  aura.scale.set(7, 7, 1);
  emblem.add(aura);

  /* ---- Braises (particules) ---- */
  const COUNT = mobile ? 700 : 1800;
  const pGeo = new THREE.BufferGeometry();
  const pPos = new Float32Array(COUNT * 3), pSeed = new Float32Array(COUNT * 4);
  for (let i = 0; i < COUNT; i++) {
    const r = 1.5 + Math.pow(Math.random(), 0.7) * 9;
    const th = Math.random() * Math.PI * 2;
    pPos[i * 3] = Math.cos(th) * r;
    pPos[i * 3 + 1] = (Math.random() - 0.5) * 12;
    pPos[i * 3 + 2] = Math.sin(th) * r * 0.6 - 2.5;
    pSeed[i * 4] = Math.random();              // vitesse
    pSeed[i * 4 + 1] = Math.random() * 100;    // phase
    pSeed[i * 4 + 2] = 0.4 + Math.random();    // taille
    pSeed[i * 4 + 3] = Math.random();          // couleur (or/bleu)
  }
  pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
  pGeo.setAttribute('aSeed', new THREE.BufferAttribute(pSeed, 4));
  const pMat = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
    uniforms: { uTime: { value: 0 }, uPR: { value: dpr }, uSpeed: { value: 1 }, uAlpha: { value: 0 } },
    vertexShader: `
      attribute vec4 aSeed; uniform float uTime; uniform float uPR; uniform float uSpeed;
      varying float vMix; varying float vTw;
      void main(){
        vec3 p = position;
        float sp = 0.15 + aSeed.x * 0.45;
        p.y = mod(p.y + 6.0 + uTime * sp * uSpeed, 12.0) - 6.0;
        p.x += sin(uTime * 0.4 + aSeed.y) * 0.35;
        p.z += cos(uTime * 0.3 + aSeed.y) * 0.25;
        vec4 mv = modelViewMatrix * vec4(p, 1.0);
        gl_Position = projectionMatrix * mv;
        vTw = 0.55 + 0.45 * sin(uTime * (1.5 + aSeed.x * 3.0) + aSeed.y);
        vMix = aSeed.w;
        gl_PointSize = aSeed.z * 34.0 * uPR / -mv.z;
      }`,
    fragmentShader: `
      uniform float uAlpha; varying float vMix; varying float vTw;
      void main(){
        float d = length(gl_PointCoord - 0.5);
        float a = smoothstep(0.5, 0.0, d);
        a *= a;
        vec3 goldC = vec3(1.0, 0.72, 0.32);
        vec3 blueC = vec3(0.35, 0.55, 1.0);
        vec3 c = mix(goldC, blueC, step(0.72, vMix));
        gl_FragColor = vec4(c * 1.6, a * vTw * uAlpha);
      }`
  });
  const embers = new THREE.Points(pGeo, pMat);
  scene.add(embers);

  /* ---- Post-traitement ---- */
  let composer = null;
  if (!mobile) {
    const rt = new THREE.WebGLRenderTarget(1, 1, { type: THREE.HalfFloatType, samples: 4 });
    composer = new EffectComposer(renderer, rt);
    composer.addPass(new RenderPass(scene, camera));
    composer.addPass(new UnrealBloomPass(new THREE.Vector2(256, 256), 0.38, 0.5, 0.92));
    composer.addPass(new OutputPass());
  }

  /* ---- Mise en page ---- */
  let layout = { x: 0, y: 0, s: 1 };
  const resize = () => {
    const w = canvas.clientWidth || window.innerWidth, h = canvas.clientHeight || window.innerHeight;
    renderer.setSize(w, h, false);
    if (composer) composer.setSize(w, h);
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    const vh = 2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * camera.position.z;
    const vw = vh * camera.aspect;
    if (camera.aspect > 1.05) {
      const s = Math.min(vh * 0.13, vw * 0.075);
      layout = { x: vw * 0.24, y: -vh * 0.01, s };
    } else {
      const s = Math.min(vw * 0.115, vh * 0.068);
      layout = { x: 0, y: vh * 0.25, s };
    }
    nebula.material.uniforms.uShift.value.set(camera.aspect > 1.05 ? 0.74 : 0.5, camera.aspect > 1.05 ? 0.52 : 0.72);
  };
  resize();
  window.addEventListener('resize', resize);

  /* ---- Animation ---- */
  const pointer = { x: 0, y: 0, tx: 0, ty: 0 };
  let progress = 0, progressT = 0;
  let introStart = -1;
  let running = true, visible = true;
  const clock = new THREE.Clock();
  const hero = canvas.closest('.hero');

  emblem.scale.setScalar(0.001);

  const frame = () => {
    if (!running) return;
    requestAnimationFrame(frame);
    if (!visible || document.hidden) return;
    const t = clock.getElapsedTime();

    if (introStart < 0 && hero && hero.classList.contains('is-in')) introStart = t;
    const it = introStart < 0 ? 0 : clamp01((t - introStart) / 2.6);

    pointer.x += (pointer.tx - pointer.x) * 0.05;
    pointer.y += (pointer.ty - pointer.y) * 0.05;
    progress += (progressT - progress) * 0.08;

    // Emblème : apparition, respiration, souris, défilement
    const sIntro = easeBack(clamp01(it * 1.25));
    emblem.scale.setScalar(Math.max(0.001, sIntro));
    emblem.rotation.z = (1 - easeOut(it)) * -0.9;
    holder.position.set(layout.x, layout.y + progress * 2.4 + Math.sin(t * 0.6) * 0.06, -progress * 2);
    holder.scale.setScalar(layout.s * (1 + progress * 0.25));
    holder.rotation.y = Math.sin(t * 0.25) * 0.22 + pointer.x * 0.38 + progress * 0.9;
    holder.rotation.x = Math.sin(t * 0.2) * 0.05 - pointer.y * 0.2 + progress * 0.3;

    blades.forEach((b) => {
      const g = easeBack(clamp01((it - b.delay) / 0.55));
      b.pivot.scale.set(Math.max(0.001, 0.6 + 0.4 * g), Math.max(0.001, g), 1);
      b.pivot.rotation.z = b.base + Math.sin(t * 1.1 + b.phase) * 0.035 + (1 - g) * 0.4;
      b.pivot.rotation.y = Math.sin(t * 0.7 + b.phase) * 0.12;
    });
    beads.forEach((b) => {
      b.m.position.y = b.y + Math.sin(t * 1.2 + b.phase) * 0.06;
      b.m.scale.setScalar(easeBack(clamp01((it - 0.9) / 0.5)) || 0.001);
    });
    const pulse = 0.85 + Math.sin(t * 2.3) * 0.15;
    baseGlow.scale.setScalar(0.9 * pulse * clamp01(it * 2));
    baseFlare.scale.set(1.8 * pulse * clamp01(it * 2), 0.8 * clamp01(it * 2), 1);
    ringFlare.material.opacity = clamp01(it * 1.5) * (0.55 + 0.45 * Math.sin(t * 0.9) ** 2);
    ringFlare.material.rotation = t * 0.15;
    baseLight.intensity = 7 * pulse * clamp01(it * 2);

    // Lumière qui balaie l'or
    sweep.position.set(layout.x + Math.cos(t * 0.45) * 3.4, layout.y + Math.sin(t * 0.3) * 2.2, 3.2);

    pMat.uniforms.uTime.value = t;
    pMat.uniforms.uSpeed.value = 1 + progress * 3;
    pMat.uniforms.uAlpha.value = clamp01(t / 2);
    embers.rotation.y = t * 0.02 + pointer.x * 0.1;
    nebula.material.uniforms.uTime.value = t;

    camera.position.x = pointer.x * 0.35;
    camera.position.y = -pointer.y * 0.25;
    camera.lookAt(0, 0, 0);

    if (composer) composer.render(); else renderer.render(scene, camera);
  };

  const visObs = new IntersectionObserver((entries) => { visible = entries[0].isIntersecting; }, { threshold: 0 });
  visObs.observe(canvas);

  const ready = new Promise((resolve) => {
    requestAnimationFrame(() => {
      frame();
      requestAnimationFrame(resolve);
    });
  });

  return {
    ready,
    intro() { if (introStart < 0) introStart = clock.getElapsedTime(); },
    setPointer(x, y) { pointer.tx = x; pointer.ty = y; },
    setProgress(p) { progressT = p; },
    destroy() { running = false; visObs.disconnect(); window.removeEventListener('resize', resize); renderer.dispose(); },
  };
}
