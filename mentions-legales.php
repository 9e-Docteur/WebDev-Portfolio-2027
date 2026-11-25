<?php
require_once 'tools/helpers.php';

$activeNav = 'legal';
$pagePath = 'mentions-legales';
$pageHeading = 'Mentions légales';
$pageUpdated = '25 novembre 2026';
$meta = [
    'title' => 'Mentions légales | Loris Populaire',
    'description' => 'Mentions légales du portfolio de Loris Populaire : éditeur, hébergeur et propriété intellectuelle.',
    'url' => base_url('mentions-legales'),
    'type' => 'website',
];

include 'partials/page-open.php';
?>
<section class="block">
    <h2><span class="prompt">&gt;</span> cat editeur.txt</h2>
    <p>Ce site est édité à titre personnel par <b>Loris Populaire</b>, particulier domicilié à Enghien (Belgique).</p>
    <p>Responsable de la publication : Loris Populaire.<br>Contact : <a href="mailto:lorisplp.pro@gmail.com">lorisplp.pro@gmail.com</a></p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat hebergeur.txt</h2>
    <p><b>o2switch SAS</b><br>Chemin des Pardiaux, 63000 Clermont-Ferrand, France<br><a href="https://www.o2switch.fr" target="_blank" rel="noopener">www.o2switch.fr</a></p>
    <p>Nom de domaine enregistré auprès de <b>OVH SAS</b>, 2 rue Kellermann, 59100 Roubaix, France.</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat propriete_intellectuelle.txt</h2>
    <p>Les textes, le code, le design, le logo et les visuels de ce site sont la propriété de Loris Populaire, sauf mention contraire. Toute reproduction sans autorisation est interdite.</p>
    <p>Les logos des langages et logiciels présentés appartiennent à leurs propriétaires respectifs et sont utilisés à titre d'illustration.</p>
    <p>Polices <b>Silkscreen</b>, <b>Edu QLD Hand</b> et <b>M PLUS Rounded 1c</b> sous licence SIL Open Font License. Icônes <b>Bootstrap Icons</b> sous licence MIT.</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat responsabilite.txt</h2>
    <p>Les informations de ce site sont fournies à titre indicatif. Les liens vers des sites externes sont proposés pour faciliter la navigation ; leur contenu ne relève pas de ma responsabilité.</p>
    <p>Ce site est soumis au droit belge.</p>
</section>

<section class="block">
    <h2><span class="prompt">&gt;</span> cat donnees_personnelles.txt</h2>
    <p>Le traitement de tes données personnelles est détaillé dans la <a href="<?= e(base_url('confidentialite')) ?>">politique de confidentialité</a>.</p>
</section>
<?php include 'partials/page-close.php'; ?>
