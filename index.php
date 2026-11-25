<?php
require_once 'tools/session.php';
require_once 'tools/db.php';
require_once 'tools/helpers.php';
require_once 'tools/content.php';
require_once 'tools/stats.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'tools/contact.php';
    handle_contact($bdd);
}

track_page_view($bdd, '/');

$whoami = setting($bdd, 'whoami');
$passions = ordered_rows($bdd, 'passions');
$objectives = ordered_rows($bdd, 'objectives');
$timeline = ordered_rows($bdd, 'timeline');
$projects = ordered_rows($bdd, 'projects');
$skills = ordered_rows($bdd, 'skills');
$software = ordered_rows($bdd, 'software');

$contactErrors = $_SESSION['contact_errors'] ?? [];
$contactOld = $_SESSION['contact_old'] ?? [];
unset($_SESSION['contact_errors'], $_SESSION['contact_old']);
$contactSent = isset($_GET['sent']);

$meta = [
    'schema' => [[
        '@type' => 'ProfilePage',
        '@id' => base_url() . '#profile',
        'url' => base_url(),
        'name' => 'Loris Populaire - Portfolio',
        'isPartOf' => ['@id' => base_url() . '#website'],
        'mainEntity' => ['@id' => base_url() . '#loris-populaire'],
        'inLanguage' => 'fr',
        'hasPart' => array_map(fn($project) => ['@id' => project_url($project) . '#project'], $projects),
    ]],
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'partials/head.php'; ?>
</head>

<body>
    <?php include 'partials/nav.php'; ?>
    <main>
    <section class="slide" id="accueil" aria-label="Accueil">
        <div class="centered flex-direction-column">
            <img src="<?= e(asset('assets/img/photo.jpg')) ?>" alt="Photo de Loris Populaire, développeur web" id="avatar" width="320" height="320" fetchpriority="high">
            <div class="title txt-centered">
                <h1 class="title-name">Loris Populaire</h1>
                <p class="title-job">Web Developer</p>
            </div>
            <div class="badges">
                <img src="assets/svg/badges/html.svg" alt="Badge HTML" width="98" height="26">
                <img src="assets/svg/badges/css.svg" alt="Badge CSS" width="98" height="26">
                <img src="assets/svg/badges/js.svg" alt="Badge JS" width="98" height="26">
                <img src="assets/svg/badges/php.svg" alt="Badge PHP" width="98" height="26">
                <img src="assets/svg/badges/react.svg" alt="Badge React" width="98" height="26">
                <img src="assets/svg/badges/symfony.svg" alt="Badge Symfony" width="98" height="26">
            </div>
            <?php if ($cvUrl = cv_url()): ?>
                <a class="btn btn-primary hero-cv" href="<?= e($cvUrl) ?>" target="_blank" rel="noopener"><i class="bi bi-file-earmark-arrow-down" aria-hidden="true"></i>Télécharger mon CV</a>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'partials/about.php'; ?>

    <?php if ($timeline): ?>
        <?php include 'partials/timeline.php'; ?>
    <?php endif; ?>

    <section class="slide" id="projets" aria-labelledby="projets-title">
        <div class="slide-header">
            <h2 id="projets-title">Projets</h2>
            <p>Projets personnels réalisés sur mon temps libre, pour d'autres personnes ou dans le cadre de mes
                cours</p>
        </div>

        <div class="card-holder">
            <?php foreach ($projects as $project): ?>
                <a class="card card-link" href="<?= e(project_url($project)) ?>">
                    <div class="card-content">
                        <?php if ($project['image']): ?>
                            <img src="<?= e($project['image']) ?>" alt="Aperçu du projet <?= e($project['title']) ?>" loading="lazy" decoding="async">
                        <?php else: ?>
                            <div class="card-wip">
                                <i class="bi bi-cone-striped"></i>
                                <span>Work in progress</span>
                            </div>
                        <?php endif; ?>
                        <h3><?= e($project['title']) ?></h3>
                        <p><?= e($project['description']) ?></p>
                        <span class="card-more">Voir le projet <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="slide" id="competences" aria-labelledby="competences-title">
        <div class="slide-header">
            <h2 id="competences-title">Compétences</h2>
            <p>Les langages et technologies que je maîtrise</p>
        </div>
        <div class="compt-holder">
            <?php foreach ($skills as $item): ?>
                <div class="compt-item">
                    <img src="<?= e($item['image']) ?>" alt="Logo <?= e($item['name']) ?>" width="72" height="72" loading="lazy" decoding="async"<?= $item['light_bg'] ? ' class="compt-icon-light"' : '' ?>>
                    <div class="compt-back">
                        <p><?= e($item['name']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="slide-header" id="logiciels">
            <h2>Logiciels utilisés</h2>
            <p>Ce que j'utilise pour le développement</p>
        </div>
        <div class="compt-holder">
            <?php foreach ($software as $item): ?>
                <div class="compt-item">
                    <img src="<?= e($item['image']) ?>" alt="Logo <?= e($item['name']) ?>" width="72" height="72" loading="lazy" decoding="async"<?= $item['light_bg'] ? ' class="compt-icon-light"' : '' ?>>
                    <div class="compt-back">
                        <p><?= e($item['name']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="slide" id="contact" aria-labelledby="contact-title">
        <div class="slide-header">
            <h2 id="contact-title">Contact</h2>
            <p>Un stage, un projet, une question ? Écris-moi</p>
        </div>

        <div class="contact">
            <div class="contact-info">
                <a class="ct-item" href="mailto:lorisplp.pro@gmail.com">
                    <i class="bi bi-envelope"></i>
                    <span>
                        <small>E-mail</small>
                        lorisplp.pro@gmail.com
                    </span>
                </a>

                <a class="ct-item" href="tel:+32493027518">
                    <i class="bi bi-phone"></i>
                    <span>
                        <small>Téléphone</small>
                        +32 493 02 75 18
                    </span>
                </a>

                <div class="ct-item">
                    <i class="bi bi-geo-alt"></i>
                    <span>
                        <small>Localisation</small>
                        Enghien, Belgique
                    </span>
                </div>

                <?php if (cv_url()): ?>
                    <a class="ct-item" href="<?= e(cv_url()) ?>" target="_blank" rel="noopener">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <span>
                            <small>CV</small>
                            Télécharger en PDF
                        </span>
                    </a>
                <?php endif; ?>

                <?php $socials = social_links(); ?>
                <?php if ($socials): ?>
                    <div class="contact-socials">
                        <?php foreach ($socials as $social): ?>
                            <a href="<?= e($social['url']) ?>" target="_blank" rel="noopener" aria-label="<?= e($social['label']) ?>" title="<?= e($social['label']) ?>">
                                <i class="bi <?= e($social['icon']) ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <form class="contact-form" method="post" action="./#contact">
                <?= csrf_field() ?>
                <?php if ($contactSent): ?>
                    <p class="form-alert form-alert-success">Merci, ton message a bien été envoyé !</p>
                <?php endif; ?>
                <?php foreach ($contactErrors as $error): ?>
                    <p class="form-alert form-alert-error"><?= e($error) ?></p>
                <?php endforeach; ?>
                <div class="two-fields">
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name" value="<?= e($contactOld['lastname'] ?? '') ?>" placeholder="Dupont" class="form-control"
                            autocomplete="family-name" required>
                    </div>
                    <div class="form-group">
                        <label for="firstname">Prénom</label>
                        <input type="text" name="firstname" id="firstname" value="<?= e($contactOld['firstname'] ?? '') ?>" placeholder="Jean" class="form-control"
                            autocomplete="given-name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="mail">Adresse e-mail</label>
                    <input type="email" name="mail" id="mail" value="<?= e($contactOld['email'] ?? '') ?>" placeholder="jean.dupont@exemple.com"
                        class="form-control" autocomplete="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Téléphone <small>(facultatif)</small></label>
                    <input type="tel" name="phone" id="phone" value="<?= e($contactOld['phone'] ?? '') ?>" placeholder="+32 470 00 00 00" class="form-control"
                        autocomplete="tel">
                </div>
                <div class="form-group">
                    <label for="msg">Message</label>
                    <textarea name="msg" id="msg" placeholder="Bonjour Loris, ..." class="form-control"
                        minlength="10" maxlength="5000" required><?= e($contactOld['body'] ?? '') ?></textarea>
                </div>
                <div class="form-trap" aria-hidden="true">
                    <label for="website">Site web</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i>Envoyer</button>
                <p class="form-note">Tes données servent uniquement à te répondre et sont supprimées après 12 mois. <a href="<?= e(base_url('confidentialite')) ?>">En savoir plus</a></p>
            </form>
        </div>
    </section>
    </main>
    <?php include 'partials/footer.php'; ?>
    <script src="<?= e(asset('assets/js/bg.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/header.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/about.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/nav.js')) ?>" defer></script>
</body>

</html>