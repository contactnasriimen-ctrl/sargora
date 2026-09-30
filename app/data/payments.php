<?php
/**
 * Moyens de paiement proposés au checkout.
 * Remplacez les valeurs entre crochets par vos vraies coordonnées avant la mise en ligne.
 */
return [
    'd17' => [
        'label' => 'D17 · La Poste',
        'desc'  => 'Transfert instantané depuis l’application D17.',
        'icon'  => 'phone',
        'steps' => [
            'Ouvrez l’application D17 et choisissez « Envoyer de l’argent ».',
            'Envoyez le montant exact au numéro D17 : [00 000 000].',
            'Indiquez la référence de commande en motif, puis envoyez-nous la capture sur WhatsApp.',
        ],
    ],
    'flouci' => [
        'label' => 'Flouci',
        'desc'  => 'Paiement mobile rapide via le portefeuille Flouci.',
        'icon'  => 'zap',
        'steps' => [
            'Ouvrez Flouci et transférez le montant au numéro [00 000 000].',
            'Ajoutez la référence de commande en commentaire.',
            'Envoyez la capture de confirmation sur WhatsApp.',
        ],
    ],
    'virement' => [
        'label' => 'Virement bancaire',
        'desc'  => 'Virement ou versement sur notre compte bancaire.',
        'icon'  => 'briefcase',
        'steps' => [
            'Titulaire : [Sargora SARL] — Banque : [Nom de la banque].',
            'RIB : [00 000 0000000000000 00].',
            'Motif : la référence de commande. Envoyez le reçu sur WhatsApp ou par e-mail.',
        ],
    ],
    'especes' => [
        'label' => 'Espèces en agence',
        'desc'  => 'Réglez directement à l’agence, sur rendez-vous.',
        'icon'  => 'pin',
        'steps' => [
            'Contactez-nous sur WhatsApp pour fixer un rendez-vous.',
            'Présentez la référence de commande à l’agence : [adresse].',
            'Votre commande est livrée dès réception du paiement.',
        ],
    ],
];
