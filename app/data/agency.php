<?php
/**
 * Contenus éditoriaux de l'agence.
 * ⚠ Les réalisations, témoignages et chiffres ci-dessous sont des EXEMPLES de démonstration :
 *   remplacez-les par vos vrais projets, avis clients et statistiques avant la mise en ligne.
 */
declare(strict_types=1);

function services(): array
{
    return [
        'site-web' => [
            'num' => '01', 'icon' => 'monitor', 'title' => 'Sites web', 'long' => 'Création de sites web',
            'short' => 'Sites vitrines, e-commerce et plateformes sur mesure : rapides, élégants et pensés pour convertir.',
            'intro' => 'Votre site est votre meilleur commercial, 24 h/24. Nous concevons des expériences web immersives, ultra-rapides et optimisées pour le référencement, qui transforment les visiteurs en clients.',
            'deliverables' => [
                ['Site vitrine premium', 'Une présence élégante qui inspire confiance dès la première seconde.'],
                ['E-commerce', 'Boutiques en ligne performantes, paiement local et gestion simplifiée.'],
                ['Landing pages', 'Des pages de campagne pensées pour un seul objectif : convertir.'],
                ['Plateformes sur mesure', 'Espaces clients, réservations, catalogues : nous développons vos outils.'],
                ['SEO & performance', 'Structure, vitesse et contenus optimisés pour Google.'],
                ['Maintenance & hébergement', 'Mises à jour, sauvegardes et sécurité, sans souci.'],
            ],
            'packages' => [
                ['Essentiel', 'dès 1 490 DT', ['Site vitrine jusqu’à 5 pages', 'Design sur mesure responsive', 'SEO de base', 'Formulaire de contact']],
                ['Business', 'dès 3 490 DT', ['Jusqu’à 12 pages ou e-commerce', 'Animations & expérience premium', 'SEO avancé + blog', 'Formation à l’administration'], true],
                ['Sur mesure', 'sur devis', ['Plateforme ou application web', 'Intégrations & API', 'Accompagnement continu', 'Hébergement géré']],
            ],
            'faq' => [
                ['Combien de temps pour créer mon site ?', 'Comptez 2 à 4 semaines pour un site vitrine et 4 à 8 semaines pour un e-commerce, selon le contenu disponible.'],
                ['Pourrai-je modifier mon site moi-même ?', 'Oui. Nous livrons un espace d’administration simple et une formation pour gérer vos contenus en autonomie.'],
                ['Le site sera-t-il adapté aux mobiles ?', 'Toujours. Chaque page est conçue d’abord pour le mobile, puis sublimée sur grand écran.'],
            ],
        ],
        'branding' => [
            'num' => '02', 'icon' => 'palette', 'title' => 'Branding', 'long' => 'Branding & identité visuelle',
            'short' => 'Stratégie, logo et identité visuelle : une marque cohérente, mémorable et désirable.',
            'intro' => 'Une marque forte ne se résume pas à un logo. Nous construisons votre plateforme de marque, votre identité visuelle et vos codes pour que chaque point de contact raconte la même histoire.',
            'deliverables' => [
                ['Stratégie de marque', 'Positionnement, promesse, valeurs et ton de voix.'],
                ['Naming', 'Un nom unique, disponible et facile à retenir.'],
                ['Logo & identité', 'Un emblème intemporel et un système visuel complet.'],
                ['Charte graphique', 'Le guide qui garantit la cohérence de votre marque.'],
                ['Packaging & print', 'Des supports qui donnent envie d’être touchés.'],
                ['Direction artistique', 'Photo, vidéo et contenus au service de votre image.'],
            ],
            'packages' => [
                ['Identité', 'dès 990 DT', ['Logo (3 pistes créatives)', 'Palette & typographies', 'Déclinaisons réseaux sociaux', 'Fichiers sources']],
                ['Marque complète', 'dès 2 490 DT', ['Atelier stratégie de marque', 'Logo & identité complète', 'Charte graphique détaillée', 'Papeterie & templates'], true],
                ['Rebranding', 'sur devis', ['Audit de marque existante', 'Repositionnement', 'Refonte de l’identité', 'Plan de déploiement']],
            ],
            'faq' => [
                ['Combien de propositions de logo ?', 'Nous présentons trois pistes créatives distinctes, puis affinons celle que vous choisissez.'],
                ['Serai-je propriétaire de mon logo ?', 'Oui, la cession des droits et tous les fichiers sources vous sont remis à la livraison.'],
                ['Pouvez-vous moderniser un logo existant ?', 'Absolument : nous pouvons faire évoluer votre identité tout en préservant sa reconnaissance.'],
            ],
        ],
        'sponsoring' => [
            'num' => '03', 'icon' => 'trophy', 'title' => 'Sponsoring', 'long' => 'Sponsoring & événementiel',
            'short' => 'Nous connectons votre marque aux événements, clubs et créateurs qui comptent pour votre audience.',
            'intro' => 'Le sponsoring bien pensé crée une émotion que la publicité ne peut pas acheter. Nous identifions les bons partenaires, négocions les accords et activons votre présence pour un impact mesurable.',
            'deliverables' => [
                ['Stratégie de sponsoring', 'Les partenariats alignés avec vos objectifs et votre audience.'],
                ['Recherche & négociation', 'Nous trouvons et négocions les meilleures opportunités.'],
                ['Activation terrain', 'Stands, PLV, animations et expériences de marque.'],
                ['Événements & lancements', 'Conception et production d’événements mémorables.'],
                ['Partenariats créateurs', 'Collaborations avec des influenceurs qui vous ressemblent.'],
                ['Mesure du ROI', 'Visibilité, leads et retombées : nous mesurons tout.'],
            ],
            'packages' => [
                ['Opportunité', 'dès 790 DT', ['Sélection de 5 opportunités', 'Analyse d’audience', 'Recommandation chiffrée', 'Mise en relation']],
                ['Partenariat clé en main', 'dès 2 900 DT', ['Négociation du contrat', 'Plan d’activation', 'Création des supports', 'Rapport de retombées'], true],
                ['Événement signature', 'sur devis', ['Concept créatif', 'Production complète', 'Couverture photo & vidéo', 'Campagne digitale associée']],
            ],
            'faq' => [
                ['Quel budget prévoir pour un sponsoring ?', 'Tout dépend de l’événement : nous proposons des opportunités adaptées dès les budgets de TPE.'],
                ['Travaillez-vous avec les clubs sportifs ?', 'Oui : clubs, festivals, compétitions, conférences et créateurs de contenu.'],
                ['Comment mesurez-vous les résultats ?', 'Portée, mentions, trafic, leads et ventes : un rapport complet vous est remis après chaque activation.'],
            ],
        ],
        'marketing-digital' => [
            'num' => '04', 'icon' => 'megaphone', 'title' => 'Marketing digital', 'long' => 'Marketing digital & réseaux sociaux',
            'short' => 'Contenus, publicité et community management pour faire grandir votre audience et vos ventes.',
            'intro' => 'Nous créons des contenus qui arrêtent le scroll et des campagnes qui rapportent. Stratégie, production, diffusion et optimisation : un seul partenaire pour toute votre croissance digitale.',
            'deliverables' => [
                ['Stratégie social media', 'Ligne éditoriale, calendrier et objectifs clairs.'],
                ['Création de contenu', 'Photos, vidéos, Reels et visuels qui captent l’attention.'],
                ['Publicité en ligne', 'Campagnes Meta, Google et TikTok pilotées par la donnée.'],
                ['Community management', 'Animation, modération et relation avec votre communauté.'],
                ['E-mailing & automation', 'Des séquences qui transforment vos contacts en clients.'],
                ['Reporting', 'Des tableaux de bord clairs chaque mois.'],
            ],
            'packages' => [
                ['Présence', 'dès 590 DT/mois', ['12 publications par mois', '2 réseaux sociaux', 'Visuels sur mesure', 'Rapport mensuel']],
                ['Croissance', 'dès 1 290 DT/mois', ['20 publications + 4 Reels', 'Gestion des publicités', 'Community management', 'Réunion mensuelle'], true],
                ['Performance', 'sur devis', ['Stratégie 360°', 'Production vidéo', 'Campagnes multi-plateformes', 'Automatisation marketing']],
            ],
            'faq' => [
                ['Le budget publicitaire est-il inclus ?', 'Non, le budget média est payé directement aux plateformes ; nos honoraires couvrent la stratégie et la gestion.'],
                ['Quand verrai-je des résultats ?', 'Les premiers indicateurs arrivent dès le premier mois ; la croissance durable se construit sur 3 à 6 mois.'],
                ['Créez-vous les vidéos ?', 'Oui, notre équipe réalise tournages, montages et Reels adaptés à chaque plateforme.'],
            ],
        ],
    ];
}

function project_categories(): array
{
    return ['web' => 'Sites web', 'branding' => 'Branding', 'sponsoring' => 'Sponsoring', 'marketing' => 'Marketing digital'];
}

function projects(): array
{
    return [
        'olivea' => [
            'title' => 'Olivéa', 'client' => 'Olivéa · Huile d’olive premium', 'cat' => 'branding', 'year' => '2026', 'style' => 'brand',
            'palette' => ['#16261d', '#c9a24b', '#f3ead7'], 'mark' => 'leaf',
            'summary' => 'Une identité noble pour porter l’huile d’olive tunisienne sur les tables étoilées d’Europe.',
            'services' => ['Stratégie de marque', 'Logo & identité', 'Packaging'],
            'challenge' => 'Olivéa produisait une huile d’exception mais son image ne reflétait ni la qualité du produit ni son héritage familial.',
            'solution' => 'Nous avons construit un récit autour de la terre et du geste, un emblème inspiré de la feuille d’olivier et un packaging or et vert profond.',
            'results' => [['×3', 'distributeurs en Europe'], ['+48 %', 'de panier moyen'], ['2', 'prix de design packaging']],
        ],
        'blue-reef' => [
            'title' => 'Blue Reef Resort', 'client' => 'Blue Reef · Hôtel & spa', 'cat' => 'web', 'year' => '2026', 'style' => 'web',
            'palette' => ['#0b2740', '#2ec4b6', '#f4e3c1'], 'mark' => 'wave',
            'summary' => 'Un site de réservation immersif qui fait vivre la mer avant même d’arriver.',
            'services' => ['Design UI/UX', 'Développement', 'Réservation en ligne'],
            'challenge' => 'L’hôtel dépendait à 90 % des plateformes de réservation et de leurs commissions.',
            'solution' => 'Un site immersif en vidéo plein écran, un moteur de réservation direct et un parcours mobile en trois étapes.',
            'results' => [['+62 %', 'de réservations directes'], ['1,4 s', 'de temps de chargement'], ['-35 %', 'de commissions']],
        ],
        'medina-run' => [
            'title' => 'Médina Run', 'client' => 'Course urbaine · 12 000 coureurs', 'cat' => 'sponsoring', 'year' => '2026', 'style' => 'event',
            'palette' => ['#0c0c10', '#ff5a36', '#d4a84b'], 'mark' => 'bolt',
            'summary' => 'Un programme de sponsoring qui a transformé une course populaire en événement de marque.',
            'services' => ['Recherche de sponsors', 'Activation terrain', 'Campagne digitale'],
            'challenge' => 'La course avait une forte audience mais aucun partenaire majeur pour financer sa croissance.',
            'solution' => 'Nous avons packagé des offres de visibilité, signé quatre sponsors et conçu des activations terrain et digitales.',
            'results' => [['4', 'sponsors principaux signés'], ['2,1 M', 'd’impressions'], ['+30 %', 'd’inscrits']],
        ],
        'nour-bijoux' => [
            'title' => 'Nour Bijoux', 'client' => 'Joaillerie artisanale', 'cat' => 'web', 'year' => '2025', 'style' => 'web',
            'palette' => ['#1a1423', '#e7c9a9', '#f7efe6'], 'mark' => 'gem',
            'summary' => 'Une boutique en ligne raffinée qui vend des bijoux faits main dans le monde entier.',
            'services' => ['E-commerce', 'Photographie produit', 'SEO'],
            'challenge' => 'Les ventes se faisaient uniquement en messages privés, sans catalogue ni paiement en ligne.',
            'solution' => 'Un e-commerce élégant, des fiches produits photographiées en studio et un tunnel de commande optimisé.',
            'results' => [['+180 %', 'de chiffre d’affaires'], ['38 %', 'des ventes à l’étranger'], ['4,9/5', 'de satisfaction']],
        ],
        'atlas-fit' => [
            'title' => 'Atlas Fit', 'client' => 'Réseau de salles de sport', 'cat' => 'marketing', 'year' => '2025', 'style' => 'social',
            'palette' => ['#0d0d0d', '#c6ff3d', '#ffffff'], 'mark' => 'bolt',
            'summary' => 'Une campagne social media qui a rempli trois nouvelles salles avant leur ouverture.',
            'services' => ['Stratégie social media', 'Reels & vidéo', 'Publicité Meta'],
            'challenge' => 'Ouvrir trois salles en six mois avec des abonnements vendus dès le premier jour.',
            'solution' => 'Une série de Reels « avant l’ouverture », une offre fondateurs et des campagnes géolocalisées.',
            'results' => [['1 850', 'pré-inscriptions'], ['4,2 M', 'de vues'], ['×5', 'retour sur investissement']],
        ],
        'kairo-motors' => [
            'title' => 'Kairo Motors', 'client' => 'Concession automobile', 'cat' => 'branding', 'year' => '2025', 'style' => 'brand',
            'palette' => ['#0c1320', '#3d7bff', '#c9d3e3'], 'mark' => 'k',
            'summary' => 'Un rebranding premium pour une concession qui voulait jouer dans la cour des grands.',
            'services' => ['Rebranding', 'Signalétique', 'Site web'],
            'challenge' => 'Une image vieillissante qui ne correspondait plus aux véhicules haut de gamme vendus.',
            'solution' => 'Un monogramme dynamique, une palette acier et bleu électrique et une signalétique showroom repensée.',
            'results' => [['+41 %', 'de demandes d’essai'], ['3', 'showrooms relookés'], ['+22 %', 'de notoriété']],
        ],
        'nomad-coffee' => [
            'title' => 'Nomad Coffee', 'client' => 'Coffee shop de spécialité', 'cat' => 'branding', 'year' => '2025', 'style' => 'brand',
            'palette' => ['#2b1a12', '#e8b86d', '#f6ecdc'], 'mark' => 'sun',
            'summary' => 'Une marque chaleureuse et voyageuse pour un café de spécialité qui ouvre sa troisième adresse.',
            'services' => ['Naming', 'Identité visuelle', 'Packaging'],
            'challenge' => 'Se démarquer dans un marché du café saturé avec un budget maîtrisé.',
            'solution' => 'Un univers inspiré des routes du café, des illustrations maison et des gobelets devenus objets de collection.',
            'results' => [['3', 'adresses ouvertes'], ['+65 %', 'd’abonnés Instagram'], ['12 k', 'gobelets partagés']],
        ],
        'festival-lumieres' => [
            'title' => 'Festival des Lumières', 'client' => 'Festival culturel', 'cat' => 'sponsoring', 'year' => '2024', 'style' => 'event',
            'palette' => ['#120a2a', '#ffcf5a', '#7c5cff'], 'mark' => 'star',
            'summary' => 'Trois nuits de lumière, huit partenaires et une expérience de marque inoubliable.',
            'services' => ['Sponsoring', 'Scénographie', 'Contenus vidéo'],
            'challenge' => 'Financer une première édition ambitieuse sans historique d’audience.',
            'solution' => 'Un dossier de sponsoring cinématique, des espaces de marque immersifs et une couverture vidéo complète.',
            'results' => [['8', 'partenaires engagés'], ['45 000', 'visiteurs'], ['96 %', 'de partenaires renouvelés']],
        ],
    ];
}

function testimonials(): array
{
    return [
        ['Sargora a donné à notre huile l’image qu’elle méritait. Les distributeurs nous prennent enfin au sérieux.', 'Sonia B.', 'Fondatrice, Olivéa'],
        ['Notre site est devenu notre premier canal de réservation. L’équipe est réactive, créative et précise.', 'Karim M.', 'Directeur, Blue Reef Resort'],
        ['Ils ont trouvé nos sponsors, négocié les contrats et géré les activations. Un vrai partenaire de croissance.', 'Yassine T.', 'Organisateur, Médina Run'],
        ['Je commande nos abonnements ChatGPT et Canva chez Sargora : activation rapide, paiement en dinars, zéro stress.', 'Amel R.', 'Gérante d’agence de voyage'],
    ];
}

function agency_stats(): array
{
    // [valeur, suffixe, libellé] — à adapter à vos vrais chiffres
    return [[120, '+', 'projets livrés'], [60, '+', 'marques accompagnées'], [98, '%', 'clients satisfaits'], [24, 'h', 'délai de réponse']];
}

function process_steps(): array
{
    return [
        ['Immersion', 'Nous plongeons dans votre marque, votre marché et votre audience pour comprendre ce qui vous rend unique.', 'compass'],
        ['Stratégie', 'Positionnement, messages et plan d’action : nous traçons la route et fixons des objectifs mesurables.', 'target'],
        ['Création', 'Design, développement et contenus prennent vie, avec vous à chaque étape de validation.', 'pen'],
        ['Impact', 'Lancement, pilotage et optimisation continue pour faire grandir vos résultats, mois après mois.', 'rocket'],
    ];
}

function shop_faq(): array
{
    return [
        ['Comment se passe la livraison ?', 'Après confirmation de votre paiement, nous activons l’abonnement sur votre compte ou vous envoyons vos accès par e-mail et WhatsApp, généralement en 1 à 6 h (9h – 22h).'],
        ['S’agit-il d’abonnements officiels ?', 'Oui. Nous réglons l’abonnement officiel auprès de l’éditeur pour votre compte personnel, ou vous remettons une carte cadeau officielle. Vous payez simplement en dinars.'],
        ['Quels moyens de paiement acceptez-vous ?', 'D17, Flouci, virement bancaire ou espèces en agence. Les instructions s’affichent après la validation de votre commande.'],
        ['Que se passe-t-il si l’activation échoue ?', 'Si l’activation échoue de notre fait, nous la corrigeons ou vous remboursons sous 24 h. Notre support répond 7 j/7 sur WhatsApp.'],
        ['Puis-je suivre ma commande ?', 'Oui : rendez-vous sur la page « Suivi de commande » avec votre référence et votre e-mail. Vos accès y apparaissent dès la livraison.'],
        ['Puis-je obtenir une facture ?', 'Bien sûr, une facture est disponible sur simple demande pour chaque commande.'],
    ];
}
