<?php
require_once __DIR__ . '/tools/secu.php';

$pageTitle = 'Messages';
$currentPage = 'messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $bdd->prepare('DELETE FROM messages WHERE id = :id')->execute(['id' => $id]);
        flash('success', 'Message supprimé.');
        redirect('messages.php');
    }

    if ($action === 'unread') {
        $bdd->prepare('UPDATE messages SET is_read = 0 WHERE id = :id')->execute(['id' => $id]);
        flash('success', 'Message marqué comme non lu.');
        redirect('messages.php');
    }

    redirect('messages.php');
}

$message = null;
if (isset($_GET['id'])) {
    $stmt = $bdd->prepare('SELECT * FROM messages WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['id']]);
    $message = $stmt->fetch();

    if (!$message) {
        flash('error', 'Message introuvable.');
        redirect('messages.php');
    }

    if (!$message['is_read']) {
        $bdd->prepare('UPDATE messages SET is_read = 1 WHERE id = :id')->execute(['id' => $message['id']]);
    }
}

$messages = $message ? [] : $bdd->query('SELECT id, firstname, lastname, email, body, is_read, created_at FROM messages ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/partials/header.php';
?>
<?php if ($message): ?>
    <a href="messages.php" class="btn btn-sm btn-text theme-secondary mb-4"><i class="bi bi-arrow-left me-1"></i>Retour aux messages</a>

    <div class="card">
        <div class="card-body border-bottom d-flex flex-row flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="fs-xl mb-0"><?= e($message['firstname'] . ' ' . $message['lastname']) ?></h1>
                <small class="fg-3">Reçu le <?= e(date('d/m/Y à H:i', strtotime($message['created_at']))) ?></small>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-solid theme-primary" href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: ton message sur mon portfolio') ?>">
                    <i class="bi bi-reply me-1"></i>Répondre
                </a>
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                    <button type="submit" name="action" value="unread" class="btn btn-sm btn-outline theme-secondary">Marquer non lu</button>
                </form>
                <form method="post" onsubmit="return confirm('Supprimer ce message ?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                    <button type="submit" name="action" value="delete" class="btn btn-sm btn-subtle theme-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <dl class="row mb-4">
                <dt class="col-4 md:col-2 fg-3">E-mail</dt>
                <dd class="col-8 md:col-10"><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></dd>
                <dt class="col-4 md:col-2 fg-3">Téléphone</dt>
                <dd class="col-8 md:col-10">
                    <?php if ($message['phone']): ?>
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $message['phone'])) ?>"><?= e($message['phone']) ?></a>
                    <?php else: ?>
                        <span class="fg-3">Non renseigné</span>
                    <?php endif; ?>
                </dd>
            </dl>
            <p class="message-body mb-0"><?= e($message['body']) ?></p>
        </div>
    </div>
<?php else: ?>
    <h1 class="fs-3xl mb-1">Messages</h1>
    <p class="fg-3 mb-6">Les messages envoyés depuis le formulaire de contact.</p>

    <?php if (!$messages): ?>
        <p class="fg-3">Aucun message pour l'instant.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th></th>
                        <th>De</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $item): ?>
                        <tr>
                            <td><i class="bi <?= $item['is_read'] ? 'bi-envelope-open fg-3' : 'bi-envelope-fill fg-primary' ?>"></i></td>
                            <td class="<?= $item['is_read'] ? '' : 'fw-bold' ?>">
                                <a href="messages.php?id=<?= (int) $item['id'] ?>" class="text-decoration-none"><?= e($item['firstname'] . ' ' . $item['lastname']) ?></a>
                                <br><small class="fg-3"><?= e($item['email']) ?></small>
                            </td>
                            <td class="text-truncate" style="max-width: 420px"><?= e($item['body']) ?></td>
                            <td class="text-nowrap fg-3"><?= e(date('d/m/Y H:i', strtotime($item['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php'; ?>
