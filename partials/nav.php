<?php
$activeNav = $activeNav ?? 'accueil';
$navLinks = [
    'accueil' => ['icon' => 'bi-house', 'label' => 'Accueil'],
    'a-propos' => ['icon' => 'bi-person', 'label' => 'À propos'],
    'parcours' => ['icon' => 'bi-signpost-split', 'label' => 'Parcours'],
    'projets' => ['icon' => 'bi-box', 'label' => 'Projets'],
    'competences' => ['icon' => 'bi-bounding-box', 'label' => 'Compétences'],
    'contact' => ['icon' => 'bi-telephone', 'label' => 'Contact'],
];
$onHome = $activeNav === 'accueil';
?>
<header class="site-header" data-site-header>
    <nav aria-label="Navigation principale">
        <a class="title" href="<?= $onHome ? '#accueil' : e(base_url()) ?>">
            <span class="title-name">Loris Populaire</span>
            <span class="title-job">Web Developer</span>
        </a>
        <ul class="dark-bg">
            <?php foreach ($navLinks as $anchor => $link): ?>
                <li<?= $anchor === $activeNav ? ' class="active"' : '' ?>>
                    <a href="<?= $onHome ? '#' . $anchor : e(base_url()) . '#' . $anchor ?>" aria-label="<?= e($link['label']) ?>"<?= $anchor === $activeNav ? ' aria-current="true"' : '' ?>><i class="bi <?= $link['icon'] ?>" aria-hidden="true"></i><?= e($link['label']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>
