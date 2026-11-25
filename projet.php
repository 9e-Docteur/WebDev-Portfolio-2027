<?php
require_once 'tools/session.php';
require_once 'tools/db.php';
require_once 'tools/helpers.php';
require_once 'tools/content.php';
require_once 'tools/stats.php';

$slug = (string) ($_GET['slug'] ?? '');
$project = $slug !== '' ? project_by_slug($bdd, $slug) : null;

if (!$project) {
    http_response_code(404);
}

$skills = ordered_rows($bdd, 'skills');
$software = ordered_rows($bdd, 'software');
$activeNav = 'projets';

if ($project) {
    track_page_view($bdd, '/projet/' . $project['slug']);
    $technologies = project_skills($bdd, (int) $project['id']);
    $gallery = project_images($bdd, (int) $project['id']);
    $features = lines($project['features']);
    $others = array_values(array_filter(
        ordered_rows($bdd, 'projects'),
        fn($item) => (int) $item['id'] !== (int) $project['id']
    ));

    $infos = array_filter([
        'Type' => $project['kind'],
        'Contexte' => $project['context'],
        'Année' => $project['year'],
        'Durée' => $project['duration'],
        'Rôle' => $project['role'],
        'Statut' => $project['status'],
    ], fn($value) => $value !== null && $value !== '');

    $url = project_url($project);
    $images = array_map('base_url', array_filter(array_merge([$project['image']], $gallery)));
    $description = excerpt($project['tagline'] ?: $project['description']);

    $meta = [
        'title' => $project['title'] . ($project['kind'] ? ' - ' . $project['kind'] : '') . ' | Loris Populaire',
        'description' => $description,
        'url' => $url,
        'image' => $images[0] ?? base_url('assets/img/og-image.jpg'),
        'image_alt' => 'Aperçu du projet ' . $project['title'],
        'type' => 'article',
        'schema' => [
            [
                '@type' => 'CreativeWork',
                '@id' => $url . '#project',
                'name' => $project['title'],
                'headline' => $project['title'] . ($project['tagline'] ? ' : ' . $project['tagline'] : ''),
                'description' => excerpt($project['content'] ?: $project['description'], 300),
                'url' => $url,
                'image' => array_values($images),
                'genre' => $project['kind'],
                'dateCreated' => preg_match('/^\d{4}$/', (string) $project['year']) ? $project['year'] : null,
                'dateModified' => date('c', strtotime($project['updated_at'])),
                'keywords' => implode(', ', array_column($technologies, 'name')),
                'author' => ['@id' => base_url() . '#loris-populaire'],
                'creator' => ['@id' => base_url() . '#loris-populaire'],
                'inLanguage' => 'fr',
                'sameAs' => array_values(array_filter([$project['url_site'], $project['url_code']])),
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => base_url()],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Projets', 'item' => base_url() . '#projets'],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $project['title'], 'item' => $url],
                ],
            ],
        ],
    ];
} else {
    $meta = [
        'title' => 'Projet introuvable | Loris Populaire',
        'description' => "Ce projet n'existe pas ou a été retiré.",
        'url' => base_url(),
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (!$project): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <?php include 'partials/head.php'; ?>
</head>

<body>
    <?php include 'partials/nav.php'; ?>

    <main class="project">
        <?php if (!$project): ?>
            <div class="project-head">
                <h1>404</h1>
                <p class="tagline">Ce projet n'existe pas, ou plus.</p>
            </div>
            <p class="project-back"><a class="btn btn-primary" href="<?= e(base_url()) ?>#projets"><i class="bi bi-arrow-left"></i>Voir tous les projets</a></p>
        <?php else: ?>
            <nav class="project-path" aria-label="Fil d'Ariane">
                <a href="<?= e(base_url()) ?>#projets"><i class="bi bi-arrow-left" aria-hidden="true"></i>Retour</a>
                <span class="path">~/projets/<b><?= e($project['slug']) ?></b></span>
            </nav>

            <header class="project-head">
                <h1><?= e($project['title']) ?></h1>
                <?php if ($project['tagline']): ?>
                    <p class="tagline"><?= e($project['tagline']) ?></p>
                <?php endif; ?>
            </header>

            <div class="project-cover">
                <?php if ($project['image']): ?>
                    <img src="<?= e($project['image']) ?>" alt="Aperçu du projet <?= e($project['title']) ?>" fetchpriority="high">
                <?php else: ?>
                    <div class="card-wip">
                        <i class="bi bi-cone-striped" aria-hidden="true"></i>
                        <span>Work in progress</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php $hasSide = $infos || $technologies || $project['url_site'] || $project['url_code']; ?>
            <div class="project-grid<?= $hasSide ? '' : ' project-grid-full' ?>">
                <div>
                    <section class="block" aria-labelledby="readme">
                        <h2 id="readme"><span class="prompt">&gt;</span> cat README.md</h2>
                        <?= rich_text($project['content'] ?: $project['description']) ?>
                    </section>

                    <?php if ($features): ?>
                        <section class="block" aria-labelledby="fonctionnalites">
                            <h2 id="fonctionnalites"><span class="prompt">&gt;</span> ls fonctionnalites/</h2>
                            <ul>
                                <?php foreach ($features as $feature): ?>
                                    <li><?= bold_text($feature) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>

                    <?php if ($project['learnings']): ?>
                        <section class="block" aria-labelledby="appris">
                            <h2 id="appris"><span class="prompt">&gt;</span> cat ce_que_j_ai_appris.txt</h2>
                            <?= rich_text($project['learnings']) ?>
                        </section>
                    <?php endif; ?>

                    <?php if ($gallery): ?>
                        <section class="block" aria-labelledby="captures">
                            <h2 id="captures"><span class="prompt">&gt;</span> ls captures/</h2>
                            <div class="gallery">
                                <?php foreach ($gallery as $index => $image): ?>
                                    <a class="shot" href="<?= e($image) ?>" target="_blank">
                                        <img src="<?= e($image) ?>" alt="Capture <?= $index + 1 ?> du projet <?= e($project['title']) ?>" loading="lazy" decoding="async">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <?php if ($hasSide): ?>
                <aside class="project-side">
                    <?php if ($infos): ?>
                        <div class="terminal">
                            <div class="terminal-bar">
                                <span class="terminal-dot terminal-dot-red"></span>
                                <span class="terminal-dot terminal-dot-yellow"></span>
                                <span class="terminal-dot terminal-dot-green"></span>
                                <span class="terminal-title">infos.txt</span>
                            </div>
                            <dl class="terminal-body">
                                <?php foreach ($infos as $label => $value): ?>
                                    <div class="info-row">
                                        <dt><?= e($label) ?></dt>
                                        <dd><?= e($value) ?></dd>
                                    </div>
                                <?php endforeach; ?>
                            </dl>
                        </div>
                    <?php endif; ?>

                    <?php if ($technologies): ?>
                        <h2 class="side-title">Technologies</h2>
                        <div class="project-tech">
                            <?php foreach ($technologies as $item): ?>
                                <div class="compt-item">
                                    <img src="<?= e($item['image']) ?>" alt="Logo <?= e($item['name']) ?>" width="34" height="34"<?= $item['light_bg'] ? ' class="compt-icon-light"' : '' ?>>
                                    <div class="compt-back">
                                        <p><?= e($item['name']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($project['url_site'] || $project['url_code']): ?>
                        <h2 class="side-title">Liens</h2>
                        <div class="project-links">
                            <?php if ($project['url_site']): ?>
                                <a class="btn btn-primary" href="<?= e($project['url_site']) ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>Voir en ligne</a>
                            <?php endif; ?>
                            <?php if ($project['url_code']): ?>
                                <a class="btn btn-ghost" href="<?= e($project['url_code']) ?>" target="_blank" rel="noopener"><i class="bi bi-github" aria-hidden="true"></i>Voir le code</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </aside>
                <?php endif; ?>
            </div>

            <?php if ($others): ?>
                <section class="project-others" aria-labelledby="autres-projets">
                    <div class="slide-header">
                        <h2 id="autres-projets">Autres projets</h2>
                        <p>Envie d'en voir plus ?</p>
                    </div>
                    <div class="card-holder">
                        <?php foreach ($others as $other): ?>
                            <a class="card card-link" href="<?= e(project_url($other)) ?>">
                                <div class="card-content">
                                    <?php if ($other['image']): ?>
                                        <img src="<?= e($other['image']) ?>" alt="Aperçu du projet <?= e($other['title']) ?>" loading="lazy" decoding="async">
                                    <?php else: ?>
                                        <div class="card-wip">
                                            <i class="bi bi-cone-striped" aria-hidden="true"></i>
                                            <span>Work in progress</span>
                                        </div>
                                    <?php endif; ?>
                                    <h3><?= e($other['title']) ?></h3>
                                    <p><?= e($other['description']) ?></p>
                                    <span class="card-more">Voir le projet <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php include 'partials/footer.php'; ?>
    <script src="<?= e(asset('assets/js/bg.js')) ?>" defer></script>
    <script src="<?= e(asset('assets/js/header.js')) ?>" defer></script>
</body>

</html>
