<?php
require_once __DIR__ . '/tools/secu.php';

$pageTitle = 'Tableau de bord';
$currentPage = 'index';

require_once __DIR__ . '/tools/entities.php';

$counts = [];
foreach (array_keys(ENTITIES) as $table) {
    $counts[$table] = (int) $bdd->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
}

$latest = $bdd->query('SELECT id, firstname, lastname, body, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 5')->fetchAll();

include __DIR__ . '/partials/header.php';
?>
<h1 class="fs-3xl mb-1">Bonjour <?= e($_SESSION['admin_name'] ?? '') ?> 👋</h1>
<p class="fg-3 mb-6">Gère le contenu de ton portfolio depuis ici.</p>

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    <a href="messages.php" class="card text-decoration-none">
        <div class="card-body">
            <p class="fg-3 mb-1"><i class="bi bi-envelope me-1"></i>Messages non lus</p>
            <p class="fs-3xl fw-bold fg-primary mb-0"><?= $unread ?></p>
        </div>
    </a>
    <a href="description.php" class="card text-decoration-none">
        <div class="card-body">
            <p class="fg-3 mb-1"><i class="bi bi-person me-1"></i>Description</p>
            <p class="fs-lg fw-semibold mb-0">Modifier le whoami</p>
        </div>
    </a>
    <?php foreach (ENTITIES as $key => $entity): ?>
        <a href="manage.php?type=<?= e($key) ?>" class="card text-decoration-none">
            <div class="card-body">
                <p class="fg-3 mb-1"><i class="bi <?= e($entity['icon']) ?> me-1"></i><?= e($entity['label']) ?></p>
                <p class="fs-3xl fw-bold mb-0"><?= $counts[$key] ?></p>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fs-xl mb-0">Derniers messages</h2>
    <a href="messages.php" class="btn btn-sm btn-outline theme-secondary">Tout voir</a>
</div>

<?php if (!$latest): ?>
    <p class="fg-3">Aucun message pour l'instant.</p>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($latest as $message): ?>
            <a href="messages.php?id=<?= (int) $message['id'] ?>" class="list-group-item list-group-item-action d-flex gap-3 align-items-start">
                <i class="bi <?= $message['is_read'] ? 'bi-envelope-open fg-3' : 'bi-envelope-fill fg-primary' ?> mt-1"></i>
                <div class="flex-grow-1 text-truncate">
                    <p class="mb-0 <?= $message['is_read'] ? '' : 'fw-bold' ?>"><?= e($message['firstname'] . ' ' . $message['lastname']) ?></p>
                    <p class="mb-0 fg-3 text-truncate"><?= e($message['body']) ?></p>
                </div>
                <small class="fg-3 text-nowrap"><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></small>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php'; ?>
