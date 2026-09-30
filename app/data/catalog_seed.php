<?php
/**
 * Catalogue de départ de la boutique digitale.
 * Les prix sont indicatifs (en DT) : ajustez-les depuis l'administration.
 */
declare(strict_types=1);

function catalog_seed_data(): array
{
    $categories = [
        ['ia', 'Intelligence artificielle', 'Les assistants IA les plus puissants, activés sur votre compte.'],
        ['design', 'Design & création', 'Outils pro pour créer visuels, vidéos et contenus.'],
        ['streaming', 'Streaming & divertissement', 'Films, séries et musique en qualité premium.'],
        ['productivite', 'Productivité & sécurité', 'Travaillez mieux, plus vite et en toute sécurité.'],
        ['packs', 'Packs Sargora', 'Nos produits digitaux conçus par l’agence.'],
    ];

    $std = 'Activation sous 1 à 6 h (9h – 22h)';
    $products = [
        // ---------- IA ----------
        ['chatgpt-plus', 'ia', 'ChatGPT Plus', 'L’assistant IA d’OpenAI, sans limites du quotidien.',
            "Profitez de ChatGPT Plus directement sur votre propre compte : nous réglons l’abonnement officiel pour vous et vous payez en dinars.\n\nIdéal pour rédiger, analyser des documents, coder, générer des images et gagner des heures chaque semaine.",
            "Abonnement officiel activé sur votre compte\nAccès prioritaire aux derniers modèles\nGénération d’images et analyse de fichiers\nGPTs personnalisés et mode vocal avancé\nPaiement en dinars, sans carte internationale",
            [['1 mois', 69], ['3 mois', 195], ['12 mois', 749]], 'Best-seller', 'emerald', 'GPT', $std, 1],
        ['chatgpt-pro', 'ia', 'ChatGPT Pro', 'La puissance maximale d’OpenAI pour les pros exigeants.',
            "ChatGPT Pro s’adresse aux professionnels qui utilisent l’IA intensivement : usage étendu des modèles les plus avancés, raisonnement « Pro » et outils de recherche approfondie (selon l’offre en vigueur chez OpenAI).\n\nNous activons l’abonnement officiel sur votre compte et restons disponibles pour vous accompagner.",
            "Usage étendu des modèles les plus avancés\nMode de raisonnement Pro\nRecherche approfondie étendue\nOutils vidéo et agents (selon disponibilité)\nAccompagnement prioritaire Sargora",
            [['1 mois', 690]], 'Premium', 'obsidian', 'PRO', $std, 1],
        ['claude-pro', 'ia', 'Claude Pro', 'L’IA d’Anthropic, reine de l’écriture et de l’analyse.',
            "Claude Pro offre davantage d’usage que la version gratuite, l’accès aux derniers modèles Claude et aux Projets pour organiser vos documents.\n\nParfait pour la rédaction, la synthèse de longs documents et le code.",
            "Abonnement officiel activé sur votre compte\nPlus d’usage que la version gratuite\nProjets et analyse de longs documents\nExcellente qualité rédactionnelle en français",
            [['1 mois', 69], ['3 mois', 195]], null, 'clay', 'Cl', $std, 1],
        ['google-ai-pro', 'ia', 'Google AI Pro (Gemini)', 'Gemini au cœur de Gmail, Docs et plus de stockage.',
            "Google AI Pro débloque les modèles Gemini avancés, leur intégration dans Gmail, Docs et Sheets, ainsi qu’un espace de stockage Google étendu (selon l’offre en vigueur).",
            "Modèles Gemini avancés\nIntégration Gmail, Docs, Sheets\nStockage Google étendu\nActivation sur votre compte Google",
            [['1 mois', 65], ['12 mois', 690]], null, 'aurora', 'G', $std, 0],
        ['midjourney', 'ia', 'Midjourney', 'Des images IA d’une qualité cinématographique.',
            "Midjourney est la référence pour créer des visuels artistiques et publicitaires d’une qualité bluffante. Choisissez le plan adapté à votre volume de création.",
            "Plans Basic, Standard ou Pro\nGénérations rapides (GPU)\nDroits d’usage commercial selon le plan\nActivation sur votre compte",
            [['Basic · 1 mois', 39], ['Standard · 1 mois', 105], ['Pro · 1 mois', 199]], null, 'mono', 'MJ', $std, 0],
        ['perplexity-pro', 'ia', 'Perplexity Pro', 'Le moteur de réponses IA qui cite ses sources.',
            "Perplexity Pro combine plusieurs modèles d’IA et la recherche web en temps réel pour des réponses sourcées : idéal pour la veille, les études et la préparation de contenus.",
            "Recherches Pro illimitées au quotidien\nChoix du modèle d’IA\nAnalyse de fichiers\nSources citées pour chaque réponse",
            [['1 mois', 69], ['12 mois', 590]], null, 'teal', 'Px', $std, 0],

        // ---------- Design ----------
        ['canva-pro', 'design', 'Canva Pro', 'Tout Canva débloqué : modèles, kit de marque, IA.',
            "Accédez à des millions de modèles et éléments premium, au kit de marque et aux outils IA de Canva. Le compagnon idéal de vos réseaux sociaux.",
            "Modèles et éléments premium\nKit de marque (logos, couleurs, polices)\nSuppression d’arrière-plan et outils IA\nRedimensionnement magique",
            [['1 mois', 25], ['12 mois', 119]], 'Populaire', 'violet', 'Cv', $std, 1],
        ['adobe-creative-cloud', 'design', 'Adobe Creative Cloud', 'Photoshop, Illustrator, Premiere Pro… toutes les apps.',
            "L’offre Toutes les applications d’Adobe : Photoshop, Illustrator, InDesign, Premiere Pro, After Effects et plus, avec les fonctionnalités d’IA générative.",
            "Plus de 20 applications créatives\nFonctionnalités d’IA générative\nStockage cloud inclus\nActivation sur votre Adobe ID",
            [['1 mois', 189], ['12 mois', 1790]], null, 'crimson', 'Cc', $std, 1],
        ['capcut-pro', 'design', 'CapCut Pro', 'Le montage vidéo pro pour Reels et TikTok.',
            "Effets, transitions et outils IA premium pour des vidéos qui captent l’attention dès la première seconde.",
            "Effets et filtres premium\nSous-titres automatiques\nSuppression de fond vidéo\nExport sans filigrane",
            [['1 mois', 35], ['12 mois', 299]], 'Nouveau', 'ink', 'Cp', $std, 0],
        ['envato-elements', 'design', 'Envato Elements', 'Des millions d’assets créatifs en illimité.',
            "Modèles vidéo, photos, musiques, polices, mockups… téléchargements illimités avec licence commerciale.",
            "Téléchargements illimités\nLicence commerciale\nModèles vidéo, audio, graphiques\nNouvelles ressources chaque jour",
            [['1 mois', 95], ['12 mois', 790]], null, 'lime', 'Ee', $std, 0],
        ['freepik-premium', 'design', 'Freepik Premium', 'Vecteurs, photos et outils IA premium.',
            "Accès aux ressources premium de Freepik et à ses outils de génération IA pour vos créations et campagnes.",
            "Ressources premium illimitées\nSans attribution obligatoire\nOutils IA de génération d’images\nLicence commerciale",
            [['1 mois', 49], ['12 mois', 399]], null, 'azure', 'Fp', $std, 0],

        // ---------- Streaming ----------
        ['netflix-premium', 'streaming', 'Netflix Premium', 'Films et séries en 4K, sur votre propre compte.',
            "Rechargez votre compte Netflix personnel et payez en dinars. Formule Premium : Ultra HD et plusieurs écrans simultanés.",
            "Activation sur votre compte personnel\nUltra HD 4K et HDR\nPlusieurs écrans simultanés\nSans engagement",
            [['1 mois', 45], ['3 mois', 129]], null, 'ruby', 'N', $std, 0],
        ['spotify-premium', 'streaming', 'Spotify Premium', 'Votre musique sans pub, même hors connexion.',
            "Écoute sans publicité, téléchargements hors connexion et qualité audio supérieure, sur votre propre compte Spotify.",
            "Sans publicité\nÉcoute hors connexion\nSauts illimités\nQualité audio élevée",
            [['1 mois', 19], ['12 mois', 199]], null, 'green', 'Sp', $std, 1],
        ['youtube-premium', 'streaming', 'YouTube Premium', 'YouTube sans pub, en arrière-plan et YouTube Music.',
            "Regardez sans publicité, en arrière-plan et hors connexion, avec YouTube Music Premium inclus.",
            "Vidéos sans publicité\nLecture en arrière-plan\nTéléchargements hors connexion\nYouTube Music inclus",
            [['1 mois', 25], ['12 mois', 249]], null, 'scarlet', 'YT', $std, 0],
        ['shahid-vip', 'streaming', 'Shahid VIP', 'Le meilleur des séries et films arabes.',
            "Séries exclusives, films et programmes arabes en avant-première, en haute qualité et sans publicité.",
            "Contenus exclusifs et avant-premières\nSans publicité\nHaute qualité\nActivation rapide",
            [['1 mois', 22], ['12 mois', 199]], null, 'jade', 'Sh', $std, 0],

        // ---------- Productivité ----------
        ['microsoft-365', 'productivite', 'Microsoft 365 Personnel', 'Word, Excel, PowerPoint et 1 To OneDrive.',
            "La suite Office complète avec 1 To de stockage OneDrive et les fonctionnalités Copilot selon l’offre en vigueur.",
            "Word, Excel, PowerPoint, Outlook\n1 To de stockage OneDrive\nPC, Mac, tablette et mobile\nMises à jour incluses",
            [['12 mois', 229]], null, 'sunset', '365', $std, 0],
        ['grammarly-pro', 'productivite', 'Grammarly Pro', 'Des textes anglais impeccables et percutants.',
            "Corrections avancées, reformulation, ton et clarté : écrivez en anglais comme un natif dans tous vos outils.",
            "Suggestions avancées de style\nReformulation de phrases\nDétection du ton\nIA générative intégrée",
            [['1 mois', 49], ['12 mois', 390]], null, 'mint', 'Gr', $std, 0],
        ['notion-plus', 'productivite', 'Notion Plus', 'L’espace de travail tout-en-un de vos projets.',
            "Pages et blocs illimités, historique étendu, collaboration fluide : organisez vos projets, docs et wikis au même endroit.",
            "Blocs illimités pour l’équipe\nHistorique de versions étendu\nImport de fichiers illimité\nCollaboration en temps réel",
            [['1 mois', 45], ['12 mois', 420]], null, 'paper', 'N', $std, 0],
        ['nordvpn', 'productivite', 'NordVPN', 'Naviguez en toute sécurité, partout.',
            "Chiffrement de niveau militaire, milliers de serveurs et protection contre les menaces sur 10 appareils.",
            "Jusqu’à 10 appareils\nPolitique stricte de non-journalisation\nProtection contre les menaces\nServeurs dans plus de 100 pays",
            [['12 mois', 169], ['24 mois', 289]], null, 'ice', 'VPN', $std, 0],
        ['linkedin-premium', 'productivite', 'LinkedIn Premium Career', 'Boostez votre visibilité et votre carrière.',
            "Messages InMail, statistiques sur qui a consulté votre profil, formations LinkedIn Learning et outils de candidature.",
            "Messages InMail\nQui a consulté votre profil\nLinkedIn Learning inclus\nCandidat mis en avant",
            [['1 mois', 99], ['3 mois', 279]], null, 'cobalt', 'in', $std, 0],

        // ---------- Packs Sargora ----------
        ['kit-reseaux-sociaux', 'packs', 'Kit Réseaux Sociaux', '60 templates Canva premium, prêts à publier.',
            "Un kit conçu par nos designers : posts, stories et couvertures modifiables dans Canva, avec un guide de publication. Idéal pour lancer ou relooker vos réseaux en une journée.",
            "60 templates (posts, stories, couvertures)\nModifiables dans Canva (version gratuite)\nGuide de publication en PDF\nMises à jour offertes",
            [['Téléchargement', 49]], 'Exclusif', 'gold', 'SG', 'Lien de téléchargement sous 1 h', 1],
        ['logo-express', 'packs', 'Logo Express', '3 propositions de logo en 72 h par nos designers.',
            "Un brief de 15 minutes, trois pistes créatives en 72 h, deux séries de retouches et tous les fichiers sources. La façon la plus rapide d’obtenir un logo professionnel.",
            "3 propositions de logo en 72 h\n2 séries de retouches\nFichiers AI, SVG, PNG, PDF\nMini-guide d’utilisation",
            [['Pack Essentiel', 249], ['Pack + charte graphique', 490]], null, 'gold', 'LX', 'Premières propositions sous 72 h', 1],
        ['audit-seo', 'packs', 'Audit SEO & Performance', 'Le diagnostic complet de votre site, en PDF.',
            "Nous analysons la vitesse, le référencement, l’expérience mobile et la conversion de votre site, puis vous livrons un plan d’action priorisé.",
            "Audit technique, SEO et mobile\nAnalyse de 3 concurrents\nPlan d’action priorisé\nAppel de restitution de 30 min",
            [['Site vitrine', 149], ['E-commerce', 290]], null, 'gold', 'SEO', 'Rapport livré sous 5 jours ouvrés', 0],
        ['formation-ia-entrepreneurs', 'packs', 'Formation IA pour entrepreneurs', '3 h de vidéo pour automatiser votre business.',
            "Une formation concrète pour utiliser ChatGPT, Claude, Canva et les outils d’automatisation dans votre marketing, votre service client et votre gestion.",
            "3 h de vidéos en français\n50 prompts prêts à l’emploi\nCas pratiques marketing\nAccès à vie",
            [['Accès à vie', 79]], null, 'gold', 'IA', 'Accès envoyé sous 1 h', 0],
    ];

    return [$categories, $products];
}

function seed_catalog(PDO $pdo): void
{
    [$categories, $products] = catalog_seed_data();
    $now = date('Y-m-d H:i:s');
    $catIds = [];
    $st = $pdo->prepare('INSERT INTO categories (slug, name, description, sort) VALUES (?, ?, ?, ?)');
    foreach ($categories as $i => [$slug, $name, $desc]) {
        $st->execute([$slug, $name, $desc, $i]);
        $catIds[$slug] = (int) $pdo->lastInsertId();
    }
    $st = $pdo->prepare('INSERT INTO products (slug, category_id, name, tagline, description, features, options, badge, theme, glyph, delivery, featured, in_stock, active, sort, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, ?, ?, ?)');
    foreach ($products as $i => [$slug, $cat, $name, $tagline, $desc, $features, $opts, $badge, $theme, $glyph, $delivery, $featured]) {
        $options = array_map(fn($o) => ['label' => $o[0], 'price' => (int) round($o[1] * 10 ** (int) CURRENCY_DECIMALS)], $opts);
        $st->execute([$slug, $catIds[$cat], $name, $tagline, $desc, $features, json_encode($options, JSON_UNESCAPED_UNICODE),
            $badge, $theme, $glyph, $delivery, $featured, $i, $now, $now]);
    }
}
