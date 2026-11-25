<?php
require_once __DIR__ . '/tools/secu.php';

$pageTitle = 'CV';
$currentPage = 'cv';
$cvFile = dirname(__DIR__) . '/' . CV_PATH;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        if (is_file($cvFile)) {
            unlink($cvFile);
        }
        flash('success', 'CV retiré du site.');
        redirect('cv.php');
    }

    $upload = $_FILES['cv'] ?? null;
    if (!$upload || $upload['error'] === UPLOAD_ERR_NO_FILE) {
        flash('error', 'Choisis un fichier PDF.');
    } elseif ($upload['error'] !== UPLOAD_ERR_OK) {
        flash('error', "L'envoi du fichier a échoué.");
    } elseif ($upload['size'] > 5 * 1024 * 1024) {
        flash('error', 'Le PDF dépasse 5 Mo.');
    } elseif ((new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']) !== 'application/pdf'
        || file_get_contents($upload['tmp_name'], false, null, 0, 5) !== '%PDF-') {
        flash('error', 'Le fichier doit être un PDF.');
    } else {
        if (!is_dir(dirname($cvFile))) {
            mkdir(dirname($cvFile), 0755, true);
        }
        if (move_uploaded_file($upload['tmp_name'], $cvFile)) {
            flash('success', 'CV mis à jour. Il est en ligne.');
        } else {
            flash('error', "Impossible d'enregistrer le PDF.");
        }
    }

    redirect('cv.php');
}

$exists = is_file($cvFile);
$downloads = (int) $bdd->query("SELECT COUNT(*) FROM page_views WHERE path = '/cv.pdf' AND created_at >= (NOW() - INTERVAL 30 DAY)")->fetchColumn();

include __DIR__ . '/partials/header.php';
?>
<h1 class="fs-3xl mb-1"><i class="bi bi-file-earmark-pdf me-2 fg-primary"></i>CV</h1>
<p class="fg-3 mb-6">Le PDF proposé en téléchargement sur le site (accueil, contact et footer).</p>

<div class="row g-6">
    <div class="col-12 lg:col-6">
        <div class="card">
            <div class="card-body vstack gap-3">
                <?php if ($exists): ?>
                    <p class="mb-0"><i class="bi bi-check-circle fg-primary me-2"></i>CV en ligne depuis le <?= e(date('d/m/Y à H:i', filemtime($cvFile))) ?> (<?= number_format(filesize($cvFile) / 1024, 0, ',', ' ') ?> Ko)</p>
                    <p class="fg-3 mb-0"><?= $downloads ?> ouverture<?= $downloads > 1 ? 's' : '' ?> ces 30 derniers jours.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="../cv.pdf" target="_blank" class="btn btn-outline theme-secondary"><i class="bi bi-eye me-1"></i>Voir le PDF</a>
                        <form method="post" onsubmit="return confirm('Retirer le CV du site ?')">
                            <?= csrf_field() ?>
                            <button type="submit" name="action" value="delete" class="btn btn-subtle theme-danger"><i class="bi bi-trash me-1"></i>Retirer</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="mb-0 fg-3">Aucun CV en ligne : les boutons de téléchargement sont masqués sur le site.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12 lg:col-6">
        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" class="vstack gap-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="upload">
                    <div>
                        <label for="cv" class="form-label"><?= $exists ? 'Remplacer le CV' : 'Envoyer un CV' ?></label>
                        <input type="file" class="form-control" id="cv" name="cv" accept="application/pdf" required>
                        <p class="form-text">PDF uniquement, 5 Mo maximum.</p>
                    </div>
                    <button type="submit" class="btn btn-solid theme-primary align-self-start"><i class="bi bi-upload me-1"></i>Mettre en ligne</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
