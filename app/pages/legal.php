<?php
/** Pages légales — modèles à faire relire par un juriste ; complétez les [crochets]. */
$which = $params['path'];
$pages = [
    'mentions-legales' => ['Mentions légales', [
        ['Éditeur du site', 'Le site ' . SITE_NAME . ' est édité par [Raison sociale], [forme juridique] au capital de [montant], immatriculée au registre national des entreprises sous le numéro [identifiant unique], dont le siège est situé à [adresse complète]. Contact : ' . SITE_EMAIL . ' — ' . SITE_PHONE . '.'],
        ['Directeur de la publication', '[Prénom Nom], en qualité de [fonction].'],
        ['Hébergement', 'Le site est hébergé par [nom de l’hébergeur], [adresse de l’hébergeur], [téléphone].'],
        ['Propriété intellectuelle', 'L’ensemble des contenus du site (textes, logos, emblème Sargora, visuels, code) est la propriété exclusive de ' . SITE_NAME . ' sauf mention contraire. Toute reproduction sans autorisation écrite est interdite. Les marques citées dans la boutique (ChatGPT, Canva, Adobe, Netflix…) appartiennent à leurs propriétaires respectifs ; ' . SITE_NAME . ' n’est affiliée à aucun de ces éditeurs.'],
    ]],
    'cgv' => ['Conditions générales de vente', [
        ['Objet', 'Les présentes conditions régissent la vente de produits et services digitaux (activation d’abonnements, cartes cadeaux, produits téléchargeables et prestations) proposés sur la boutique ' . SITE_NAME . '.'],
        ['Commande', 'La commande est enregistrée après validation du formulaire et acceptation des présentes CGV. Une référence unique est attribuée à chaque commande et permet de la suivre depuis la page « Suivi de commande ».'],
        ['Prix et paiement', 'Les prix sont indiqués en ' . CURRENCY . ' toutes taxes comprises. Le paiement s’effectue par D17, Flouci, virement bancaire ou en espèces à l’agence. La commande est traitée à réception du paiement.'],
        ['Livraison', 'Les produits digitaux sont livrés par activation sur le compte du client ou par transmission des accès par e-mail, WhatsApp et sur la page de suivi, généralement sous 1 à 6 heures ouvrées (9h – 22h) après confirmation du paiement.'],
        ['Abonnements tiers', 'Les abonnements sont soumis aux conditions d’utilisation de leurs éditeurs respectifs, que le client s’engage à respecter. Les fonctionnalités et tarifs peuvent évoluer à l’initiative de l’éditeur.'],
        ['Droit de rétractation', 'Conformément à la réglementation applicable, le client reconnaît que l’exécution de la prestation digitale commence dès la livraison avec son accord exprès et renonce à son droit de rétractation pour les contenus déjà fournis.'],
        ['Garantie', 'Si l’activation échoue du fait de ' . SITE_NAME . ', celle-ci est corrigée ou remboursée sous 24 heures. Toute réclamation doit être adressée à ' . SITE_EMAIL . ' ou via WhatsApp en précisant la référence de commande.'],
        ['Litiges', 'Les présentes CGV sont soumises au droit [tunisien]. En cas de litige, une solution amiable sera recherchée avant toute action auprès des tribunaux compétents de [ville].'],
    ]],
    'confidentialite' => ['Politique de confidentialité', [
        ['Données collectées', 'Nous collectons uniquement les données nécessaires : nom, e-mail, téléphone et précisions saisies lors d’une commande ou d’une demande de devis, ainsi que l’adresse IP à des fins de sécurité.'],
        ['Utilisation', 'Ces données servent exclusivement à traiter vos commandes, répondre à vos demandes et, si vous y avez consenti, vous envoyer nos offres. Elles ne sont jamais revendues.'],
        ['Conservation', 'Les données de commande sont conservées pendant la durée légale de conservation comptable ; les demandes de contact pendant 3 ans maximum.'],
        ['Cookies', 'Le site utilise uniquement un cookie de session technique, indispensable au panier et à la sécurité des formulaires. Aucun cookie publicitaire n’est déposé.'],
        ['Vos droits', 'Vous pouvez demander l’accès, la rectification ou la suppression de vos données en écrivant à ' . SITE_EMAIL . '.'],
    ]],
];
if (!isset($pages[$which])) {
    return 404;
}
[$title, $sections] = $pages[$which];
page_meta(['title' => $title, 'no_cta' => true]);
?>
<section class="page-hero page-hero--compact">
  <div class="container narrow">
    <p class="eyebrow">Informations légales</p>
    <h1 class="h1"><?= e($title) ?></h1>
    <p class="muted">Dernière mise à jour : <?= e(fmt_date('2026-09-28')) ?></p>
  </div>
</section>
<section class="section section--tight">
  <div class="container narrow prose">
    <?php foreach ($sections as $i => [$h, $t]): ?>
    <h2><?= ($i + 1) . '. ' . e($h) ?></h2>
    <p><?= e($t) ?></p>
    <?php endforeach; ?>
  </div>
</section>
