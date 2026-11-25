<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/head.php'; ?>
</head>

<body>
    <?php include __DIR__ . '/nav.php'; ?>
    <main class="project legal">
        <nav class="project-path" aria-label="Fil d'Ariane">
            <a href="<?= e(base_url()) ?>"><i class="bi bi-arrow-left" aria-hidden="true"></i>Accueil</a>
            <span class="path">~/<b><?= e($pagePath) ?></b></span>
        </nav>
        <header class="project-head">
            <h1><?= e($pageHeading) ?></h1>
            <p class="tagline">Mis à jour le <?= e($pageUpdated) ?></p>
        </header>
