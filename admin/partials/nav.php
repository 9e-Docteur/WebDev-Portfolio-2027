<?php
$links = [
    'messages' => ['href' => 'messages.php', 'icon' => 'bi-envelope', 'label' => 'Messages'],
    'stats' => ['href' => 'stats.php', 'icon' => 'bi-bar-chart', 'label' => 'Stats'],
    'description' => ['href' => 'description.php', 'icon' => 'bi-person', 'label' => 'Description'],
    'cv' => ['href' => 'cv.php', 'icon' => 'bi-file-earmark-pdf', 'label' => 'CV'],
];
foreach (ENTITIES as $key => $navEntity) {
    $links[$key] = ['href' => 'manage.php?type=' . $key, 'icon' => $navEntity['icon'], 'label' => $navEntity['label']];
}
?>
<nav class="navbar lg:navbar-expand bg-1 border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php"><img src="../assets/icons/favicon.svg" alt="" width="28" height="28" class="me-2">Admin</a>
        <button class="btn-icon navbar-toggler" type="button" data-bs-toggle="drawer" data-bs-target="#adminNav"
            aria-controls="adminNav" aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon" aria-hidden="true"></span>
        </button>
        <dialog class="drawer drawer-end" tabindex="-1" id="adminNav" aria-labelledby="adminNavLabel">
            <div class="drawer-header">
                <h5 class="drawer-title" id="adminNavLabel">Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="drawer" aria-label="Fermer"></button>
            </div>
            <div class="drawer-body">
                <div class="nav nav-pills navbar-nav">
                    <?php foreach ($links as $key => $link): ?>
                        <a class="nav-link<?= $currentPage === $key ? ' active' : '' ?>" href="<?= e($link['href']) ?>"
                            <?= $currentPage === $key ? 'aria-current="page"' : '' ?>>
                            <i class="bi <?= e($link['icon']) ?> me-1"></i><?= e($link['label']) ?>
                            <?php if ($key === 'messages' && $unread > 0): ?>
                                <span class="badge theme-primary ms-1"><?= $unread ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                    <a class="nav-link" href="../" target="_blank" title="Voir le site"><i class="bi bi-box-arrow-up-right"></i><span class="nav-label ms-1">Voir le site</span></a>
                    <form method="post" action="logout.php" class="d-flex">
                        <?= csrf_field() ?>
                        <button type="submit" class="nav-link" title="Déconnexion"><i class="bi bi-box-arrow-right"></i><span class="nav-label ms-1">Déconnexion</span></button>
                    </form>
                </div>
            </div>
        </dialog>
    </div>
</nav>
