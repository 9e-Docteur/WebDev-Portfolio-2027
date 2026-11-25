<?php
require_once __DIR__ . '/tools/secu.php';

$pageTitle = 'Description';
$currentPage = 'description';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whoami = trim((string) ($_POST['whoami'] ?? ''));

    if ($whoami === '' || mb_strlen($whoami) > 3000) {
        flash('error', 'La description doit contenir entre 1 et 3000 caractères.');
    } else {
        $stmt = $bdd->prepare(
            'INSERT INTO settings (name, value) VALUES (\'whoami\', :value)
             ON DUPLICATE KEY UPDATE value = VALUES(value)'
        );
        $stmt->execute(['value' => $whoami]);
        flash('success', 'Description enregistrée.');
    }

    redirect('description.php');
}

require_once dirname(__DIR__) . '/tools/content.php';
$whoami = setting($bdd, 'whoami');

include __DIR__ . '/partials/header.php';
?>
<h1 class="fs-3xl mb-1">Description</h1>
<p class="fg-3 mb-6">Le texte affiché sous la commande <code>whoami</code> dans la section « A propos de moi ».</p>

<div class="row g-6">
    <div class="col-12 lg:col-7">
        <form method="post" class="vstack gap-4">
            <?= csrf_field() ?>
            <div>
                <label for="whoami" class="form-label">Texte</label>
                <textarea class="form-control font-monospace" id="whoami" name="whoami" rows="8" maxlength="3000"
                    required><?= e($whoami) ?></textarea>
                <p class="form-text">Une ligne = un paragraphe. Entoure un mot de <code>**deux étoiles**</code> pour le surligner.</p>
            </div>
            <button type="submit" class="btn btn-solid theme-primary align-self-start"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </form>
    </div>
    <div class="col-12 lg:col-5">
        <p class="form-label">Aperçu actuel</p>
        <div class="card">
            <div class="card-body vstack gap-2"><?= rich_text($whoami) ?></div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
