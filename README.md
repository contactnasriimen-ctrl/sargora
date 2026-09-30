# Sargora — Grow brands. Create impact.

Site vitrine cinématique + boutique digitale de l'agence Sargora (PHP 8, sans framework).

## Lancer le site en local

Double-cliquez sur **`start.bat`** → le site s'ouvre sur http://localhost:8090
(administration : http://localhost:8090/admin).

La base de données SQLite (`data/sargora.sqlite`) se crée toute seule au premier lancement avec les 24 produits.

Mot de passe admin perdu ? `php tools/create_admin.php admin@sargora.com` en génère un nouveau.

## Pages

| URL | Contenu |
|---|---|
| `/` | Accueil : intro animée, hero 3D (emblème en WebGL), manifeste, services, réalisations en défilement horizontal, méthode, chiffres, boutique, témoignages |
| `/services`, `/services/{site-web, branding, sponsoring, marketing-digital}` | Expertises, formules, FAQ |
| `/realisations`, `/realisations/{projet}` | Portfolio filtrable + études de cas |
| `/agence` | Histoire, valeurs, pôles |
| `/boutique`, `/boutique/{catégorie}`, `/produit/{produit}` | Boutique digitale (filtres et recherche instantanés) |
| `/panier`, `/commande`, `/commande/{réf}` | Panier, paiement (D17, Flouci, virement, espèces), confirmation |
| `/suivi` | Le client retrouve sa commande et ses accès avec sa référence + e-mail |
| `/contact` | Demande de devis |
| `/admin` | Tableau de bord, commandes, produits, demandes, newsletter |

## Fonctionnement de la boutique

1. Le client commande → statut **En attente de paiement** ; il voit les instructions de paiement et un bouton WhatsApp pour envoyer sa preuve.
2. Dans l'admin, vous passez la commande en **Paiement confirmé**, puis en **Livrée** en remplissant « Accès & instructions ».
3. Le client voit ses accès sur sa page de commande (lien privé ou page `/suivi`). Le bouton « Prévenir le client » ouvre WhatsApp avec un message prêt.

Aucune passerelle de paiement en ligne : pour ajouter Konnect, Flouci API ou Stripe, brancher le paiement dans `app/shop.php` (`order_create`).

## À personnaliser avant la mise en ligne

- **`app/config.local.php`** (copie de `config.local.example.php`) : domaine, e-mail, téléphone, WhatsApp, réseaux sociaux, `DEMO_MODE` à `false`.
- **`app/data/payments.php`** : numéro D17, Flouci, RIB, adresse (valeurs entre crochets).
- **`app/data/agency.php`** : ⚠ les réalisations, témoignages et chiffres sont des **exemples** — remplacez-les par les vôtres. Pour une vraie photo de projet, déposez `assets/img/work/{slug}.webp`.
- **Prix** : indicatifs, modifiables dans Admin → Produits.
- **Pages légales** (`app/pages/legal.php`) : compléter les [crochets] et faire relire.

## Version en ligne sur GitHub Pages

https://contactnasriimen-ctrl.github.io/sargora/ : export **statique** du site (GitHub Pages n'exécute pas le PHP).
Le design, la 3D et le panier fonctionnent ; la commande et les demandes de devis sont envoyées sur **WhatsApp**
(numéro `SITE_WHATSAPP` de la configuration). L'administration n'existe que dans la version PHP.

Pour publier une mise à jour : double-cliquez sur **`deploy.bat`** (sauvegarde le code sur GitHub, régénère `dist/`
avec `tools/build_static.php`, puis publie la branche `gh-pages`). Vérification locale : `node tests/static_check.mjs`.

## Mise en ligne complète (hébergement Apache + PHP 8)

Envoyer tout le dossier (sauf `tests/`). `.htaccess` gère les URL propres et bloque `app/`, `data/`, `sql/`, `tools/`.
Le dossier `data/` doit être accessible en écriture. Pour MySQL : `DB_DRIVER = 'mysql'` dans `config.local.php` puis importer `sql/schema.mysql.sql`.
Sans accès terminal : définir `SETUP_KEY` dans `config.local.php` et ouvrir `/admin/installation` pour créer le premier admin.

## Technique

- `index.php` : contrôleur frontal (routes) · `router.php` : routeur du serveur local
- `app/` : configuration, base de données, boutique, pages, gabarits
- `assets/js/hero3d.js` : scène three.js (emblème reconstruit en 3D, braises, bloom) · `assets/js/main.js` : animations (GSAP, Lenis), panier
- Bibliothèques incluses localement dans `assets/vendor/` (three.js r170, GSAP 3.13, Lenis) — aucun CDN de script, CSP stricte
- Sécurité : jetons CSRF, requêtes préparées, mots de passe hachés, limitation des tentatives de connexion, prix toujours recalculés côté serveur
- Test de bout en bout : `node tests/e2e.mjs <mot-de-passe-admin>` (serveur lancé, base neuve) — 23 vérifications
