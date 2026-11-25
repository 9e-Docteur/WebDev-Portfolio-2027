<?php
require_once 'tools/helpers.php';

$activeNav = 'legal';
$pagePath = 'confidentialite';
$pageHeading = 'Confidentialité';
$pageUpdated = '25 novembre 2026';
$meta = [
    'title' => 'Politique de confidentialité | Loris Populaire',
    'description' => 'Comment le portfolio de Loris Populaire traite tes données : formulaire de contact, statistiques sans cookie, tes droits.',
    'url' => base_url('confidentialite'),
    'type' => 'website',
];

include 'partials/page-open.php';
?>
<section class="block">
    <h2><span class="prompt">&gt;</span> cat en_bref.txt</h2>
    <ul>
        <li>Pas de publicité, pas de traceur, pas de cookie de suivi.</li>
        <li>Tes données ne sont <b>jamais vendues ni partagées</b>.</li>
        <li>Les statistiques de visite sont anonymes et calculées sur mon propre serveur.</li>
    </ul>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat responsable.txt</h2>
    <p>Responsable du traitement : <b>Loris Populaire</b>, Enghien (Belgique).<br>Contact : <a href="mailto:lorisplp.pro@gmail.com">lorisplp.pro@gmail.com</a></p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat formulaire_de_contact.txt</h2>
    <p>Quand tu m'écris via le formulaire, j'enregistre : ton <b>nom</b>, ton <b>prénom</b>, ton <b>adresse e-mail</b>, ton <b>téléphone</b> (facultatif), ton <b>message</b>, la date d'envoi et l'adresse IP utilisée (pour lutter contre le spam).</p>
    <p>Ces données servent uniquement à te répondre. Base légale : ma réponse à ta demande et mon intérêt légitime à protéger le site contre les abus (article 6.1.b et 6.1.f du RGPD).</p>
    <p>Elles sont conservées <b>12 mois maximum</b>, puis supprimées automatiquement. Une copie de ton message peut m'être envoyée par e-mail sur ma messagerie (Gmail, Google Ireland Ltd.).</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat statistiques.txt</h2>
    <p>Pour savoir quelles pages sont consultées, je mesure l'audience <b>sans cookie et sans service tiers</b>. Pour chaque page vue, j'enregistre : la page, le site d'où tu viens, le type d'appareil, le navigateur et la date.</p>
    <p>Pour compter les visiteurs uniques, un identifiant anonyme est calculé à partir de ton adresse IP et de ton navigateur, mélangés à une clé secrète qui change chaque jour. Ton adresse IP n'est <b>jamais enregistrée</b> et l'identifiant ne permet pas de te reconnaître d'un jour à l'autre.</p>
    <p>Si ton navigateur envoie le signal « Do Not Track » ou « Global Privacy Control », ta visite n'est pas comptée. Ces statistiques sont conservées 13 mois.</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat cookies.txt</h2>
    <p>Le site n'utilise qu'<b>un seul cookie technique</b> (<code>PHPSESSID</code>), indispensable pour sécuriser le formulaire de contact. Il est supprimé à la fermeture de ton navigateur et ne nécessite pas de consentement.</p>
    <p>Les polices et les icônes sont hébergées sur ce site : ta visite ne déclenche aucune requête vers un service tiers.</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat hebergement.txt</h2>
    <p>Le site et ses données sont hébergés en France, dans l'Union européenne, par o2switch SAS (Clermont-Ferrand). Les échanges sont chiffrés (HTTPS).</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat tes_droits.txt</h2>
    <p>Tu peux à tout moment demander à <b>consulter, corriger ou supprimer</b> tes données, t'opposer à leur traitement, en limiter l'usage ou les récupérer. Écris-moi à <a href="mailto:lorisplp.pro@gmail.com">lorisplp.pro@gmail.com</a>, je réponds dans un délai d'un mois.</p>
    <p>Tu peux aussi introduire une réclamation auprès de l'<b>Autorité de protection des données</b>, Rue de la Presse 35, 1000 Bruxelles (<a href="https://www.autoriteprotectiondonnees.be" target="_blank" rel="noopener">autoriteprotectiondonnees.be</a>).</p>
</section>
<?php include 'partials/page-close.php'; ?>
