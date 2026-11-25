<?php
require_once __DIR__ . '/tools/secu.php';
require_once __DIR__ . '/tools/entities.php';
require_once dirname(__DIR__) . '/tools/upload.php';

$type = $_GET['type'] ?? '';
if (!isset(ENTITIES[$type])) {
    flash('error', 'Section inconnue.');
    redirect('index.php');
}

$entity = ENTITIES[$type];
$fields = $entity['fields'];
$listUrl = 'manage.php?type=' . $type;
$pageTitle = $entity['label'];
$currentPage = $type;

$fieldsByType = [];
foreach ($fields as $field) {
    $fieldsByType[$field['type']][] = $field;
}
$hasSkills = isset($fieldsByType['skills']);
$hasGallery = isset($fieldsByType['gallery']);
$imageField = $fieldsByType['image'][0]['name'] ?? null;
$titleLabel = 'Titre';
foreach ($fields as $field) {
    if (($field['name'] ?? null) === $entity['title']) {
        $titleLabel = $field['label'];
    }
}

function find_row(PDO $bdd, string $table, int $id): ?array
{
    $stmt = $bdd->prepare("SELECT * FROM `$table` WHERE id = :id");
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

function unique_slug(PDO $bdd, string $table, string $slug, int $ignoreId): string
{
    $candidate = $slug;
    $suffix = 2;
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM `$table` WHERE slug = :slug AND id <> :id");

    while (true) {
        $stmt->execute(['slug' => $candidate, 'id' => $ignoreId]);
        if ((int) $stmt->fetchColumn() === 0) {
            return $candidate;
        }
        $candidate = $slug . '-' . $suffix++;
    }
}

function gallery_images(PDO $bdd, int $projectId): array
{
    $stmt = $bdd->prepare('SELECT id, image FROM project_images WHERE project_id = :id ORDER BY position, id');
    $stmt->execute(['id' => $projectId]);

    return $stmt->fetchAll();
}

function linked_skill_ids(PDO $bdd, int $projectId): array
{
    $stmt = $bdd->prepare('SELECT skill_id FROM project_skills WHERE project_id = :id');
    $stmt->execute(['id' => $projectId]);

    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    $row = $id ? find_row($bdd, $type, $id) : null;

    if ($id && !$row) {
        flash('error', 'Élément introuvable.');
        redirect($listUrl);
    }

    if ($action === 'delete' && $row) {
        $files = $hasGallery ? array_column(gallery_images($bdd, $id), 'image') : [];
        $bdd->prepare("DELETE FROM `$type` WHERE id = :id")->execute(['id' => $id]);
        if ($imageField) {
            $files[] = $row[$imageField];
        }
        foreach ($files as $file) {
            delete_uploaded_image($file);
        }
        flash('success', 'Élément supprimé.');
        redirect($listUrl);
    }

    if (($action === 'up' || $action === 'down') && $row) {
        $ids = $bdd->query("SELECT id FROM `$type` ORDER BY position, id")->fetchAll(PDO::FETCH_COLUMN);
        $index = array_search($id, array_map('intval', $ids), true);
        $target = $action === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && isset($ids[$target])) {
            [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
            $bdd->beginTransaction();
            $update = $bdd->prepare("UPDATE `$type` SET position = :position WHERE id = :id");
            foreach ($ids as $position => $rowId) {
                $update->execute(['position' => $position + 1, 'id' => $rowId]);
            }
            $bdd->commit();
        }
        redirect($listUrl);
    }

    if ($action === 'save') {
        $values = [];
        $errors = [];
        $newImages = [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? null;

            if (in_array($field['type'], ['heading', 'skills', 'gallery'], true)) {
                continue;
            }

            if ($field['type'] === 'checkbox') {
                $values[$name] = isset($_POST[$name]) ? 1 : 0;
                continue;
            }

            if ($field['type'] === 'image') {
                try {
                    $uploaded = upload_image($name);
                } catch (RuntimeException $exception) {
                    $errors[] = $field['label'] . ' : ' . $exception->getMessage();
                    continue;
                }

                if ($uploaded) {
                    $values[$name] = $uploaded;
                    $newImages[] = $uploaded;
                } elseif (!$field['required'] && isset($_POST['remove_' . $name])) {
                    $values[$name] = null;
                } elseif ($field['required'] && !($row[$name] ?? null)) {
                    $errors[] = $field['label'] . ' est obligatoire.';
                }
                continue;
            }

            $value = trim((string) ($_POST[$name] ?? ''));

            if ($field['type'] === 'slug') {
                $source = trim((string) ($_POST[$field['source']] ?? ''));
                $value = slugify($value !== '' ? $value : $source);
            }

            if ($field['required'] && $value === '') {
                $errors[] = $field['label'] . ' est obligatoire.';
            } elseif (isset($field['max']) && mb_strlen($value) > $field['max']) {
                $errors[] = $field['label'] . ' : ' . $field['max'] . ' caractères maximum.';
            } elseif (isset($field['pattern']) && $value !== '' && !preg_match($field['pattern'], $value)) {
                $errors[] = $field['label'] . ' : format invalide.';
            } elseif ($field['type'] === 'url' && $value !== '' && (!filter_var($value, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $value))) {
                $errors[] = $field['label'] . ' : adresse invalide (elle doit commencer par https://).';
            } elseif ($field['type'] === 'month' && $value !== '' && !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
                $errors[] = $field['label'] . ' : format attendu AAAA-MM.';
            } elseif ($field['type'] === 'select' && !array_key_exists($value, $field['options'])) {
                $errors[] = $field['label'] . ' : valeur invalide.';
            }

            $values[$name] = ($value === '' && !$field['required']) ? null : $value;
        }

        if (isset($values['slug'])) {
            $values['slug'] = unique_slug($bdd, $type, $values['slug'], $id);
        }

        $skillIds = $hasSkills ? array_values(array_unique(array_map('intval', (array) ($_POST['skills'] ?? [])))) : [];

        $galleryUploads = [];
        if ($hasGallery) {
            try {
                $galleryUploads = upload_images('gallery');
            } catch (RuntimeException $exception) {
                $errors[] = "Captures d'écran : " . $exception->getMessage();
            }
            $newImages = array_merge($newImages, $galleryUploads);
        }

        if ($errors) {
            foreach ($newImages as $image) {
                delete_uploaded_image($image);
            }
            foreach ($errors as $error) {
                flash('error', $error);
            }
            $_SESSION['form_old'] = ['key' => $type . ':' . $id, 'values' => $values + ['skills' => $skillIds]];
            redirect($listUrl . ($row ? '&edit=' . $id : '&new=1'));
        }

        $columns = array_keys($values);
        $bdd->beginTransaction();

        if ($row) {
            $set = implode(', ', array_map(fn($column) => "`$column` = :$column", $columns));
            $bdd->prepare("UPDATE `$type` SET $set WHERE id = :id")->execute($values + ['id' => $id]);
        } else {
            $values['position'] = (int) $bdd->query("SELECT COALESCE(MAX(position), 0) + 1 FROM `$type`")->fetchColumn();
            $columns[] = 'position';
            $list = implode(', ', array_map(fn($column) => "`$column`", $columns));
            $placeholders = implode(', ', array_map(fn($column) => ":$column", $columns));
            $bdd->prepare("INSERT INTO `$type` ($list) VALUES ($placeholders)")->execute($values);
            $id = (int) $bdd->lastInsertId();
        }

        if ($hasSkills) {
            $bdd->prepare('DELETE FROM project_skills WHERE project_id = :id')->execute(['id' => $id]);
            $link = $bdd->prepare('INSERT INTO project_skills (project_id, skill_id) SELECT :project, id FROM skills WHERE id = :skill');
            foreach ($skillIds as $skillId) {
                $link->execute(['project' => $id, 'skill' => $skillId]);
            }
        }

        $removedFiles = [];
        if ($hasGallery) {
            $removeIds = array_map('intval', (array) ($_POST['remove_gallery'] ?? []));
            foreach (gallery_images($bdd, $id) as $image) {
                if (in_array((int) $image['id'], $removeIds, true)) {
                    $bdd->prepare('DELETE FROM project_images WHERE id = :id')->execute(['id' => $image['id']]);
                    $removedFiles[] = $image['image'];
                }
            }

            $position = (int) $bdd->query('SELECT COALESCE(MAX(position), 0) FROM project_images WHERE project_id = ' . $id)->fetchColumn();
            $insert = $bdd->prepare('INSERT INTO project_images (project_id, image, position) VALUES (:project, :image, :position)');
            foreach ($galleryUploads as $image) {
                $insert->execute(['project' => $id, 'image' => $image, 'position' => ++$position]);
            }
        }

        $bdd->commit();

        if ($row && $imageField && array_key_exists($imageField, $values) && $row[$imageField] !== $values[$imageField]) {
            $removedFiles[] = $row[$imageField];
        }
        foreach ($removedFiles as $file) {
            delete_uploaded_image($file);
        }

        flash('success', $row ? 'Modifications enregistrées.' : 'Élément ajouté.');
        redirect($listUrl);
    }

    redirect($listUrl);
}

$editing = null;
$showForm = isset($_GET['new']);
if (isset($_GET['edit'])) {
    $editing = find_row($bdd, $type, (int) $_GET['edit']);
    if (!$editing) {
        flash('error', 'Élément introuvable.');
        redirect($listUrl);
    }
    $showForm = true;
}

$formKey = $type . ':' . (int) ($editing['id'] ?? 0);
$old = ($_SESSION['form_old']['key'] ?? null) === $formKey ? $_SESSION['form_old']['values'] : [];
unset($_SESSION['form_old']);
$current = array_merge($editing ?? [], array_filter($old, fn($value) => $value !== null));

$allSkills = $hasSkills && $showForm ? $bdd->query('SELECT id, name, image, light_bg FROM skills ORDER BY position, id')->fetchAll() : [];
$checkedSkills = $old['skills'] ?? ($editing && $hasSkills ? linked_skill_ids($bdd, (int) $editing['id']) : []);
$gallery = $editing && $hasGallery ? gallery_images($bdd, (int) $editing['id']) : [];

$listOrder = $entity['order'] ?? 'position, id';
$sortable = !isset($entity['order']);
$rows = $bdd->query("SELECT * FROM `$type` ORDER BY $listOrder")->fetchAll();

include __DIR__ . '/partials/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-6">
    <h1 class="fs-3xl mb-0"><i class="bi <?= e($entity['icon']) ?> me-2 fg-primary"></i><?= e($entity['label']) ?></h1>
    <?php if (!$showForm): ?>
        <a href="<?= e($listUrl) ?>&new=1" class="btn btn-solid theme-primary"><i class="bi bi-plus-lg me-1"></i>Ajouter <?= e($entity['singular']) ?></a>
    <?php endif; ?>
</div>

<?php if ($showForm): ?>
    <div class="card mb-6">
        <div class="card-header fw-semibold"><?= $editing ? 'Modifier « ' . e($editing[$entity['title']]) . ' »' : 'Ajouter ' . e($entity['singular']) ?></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="vstack gap-4">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
                <?php endif; ?>

                <?php foreach ($fields as $field): ?>
                    <?php if ($field['type'] === 'heading'): ?>
                        <h2 class="fs-lg fw-semibold fg-primary border-bottom pb-2 mt-4 mb-0"><?= e($field['label']) ?></h2>
                        <?php continue; ?>
                    <?php endif; ?>
                    <?php $name = $field['name']; $value = $current[$name] ?? ''; ?>
                    <div>
                        <?php if ($field['type'] === 'checkbox'): ?>
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" class="check" id="<?= e($name) ?>" name="<?= e($name) ?>" value="1" <?= $value ? 'checked' : '' ?>>
                                <label for="<?= e($name) ?>"><?= e($field['label']) ?></label>
                            </div>
                        <?php elseif ($field['type'] === 'image'): ?>
                            <label for="<?= e($name) ?>" class="form-label"><?= e($field['label']) ?><?= $field['required'] ? '' : ' <span class="fg-3">(facultatif)</span>' ?></label>
                            <?php if ($editing && $editing[$name]): ?>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="<?= e(admin_image($editing[$name])) ?>" alt="" class="admin-preview">
                                    <?php if (!$field['required']): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" class="check" id="remove_<?= e($name) ?>" name="remove_<?= e($name) ?>" value="1">
                                            <label for="remove_<?= e($name) ?>">Retirer l'image</label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="<?= e($name) ?>" name="<?= e($name) ?>"
                                accept="image/png,image/jpeg,image/webp,image/gif" <?= $field['required'] && !($editing[$name] ?? null) ? 'required' : '' ?>>
                            <p class="form-text">PNG, JPG, WEBP ou GIF, 3 Mo maximum.<?= $editing && ($editing[$name] ?? null) ? ' Laisse vide pour garder l\'image actuelle.' : '' ?></p>
                        <?php elseif ($field['type'] === 'gallery'): ?>
                            <label for="gallery" class="form-label"><?= e($field['label']) ?> <span class="fg-3">(facultatif)</span></label>
                            <?php if ($gallery): ?>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <?php foreach ($gallery as $image): ?>
                                        <div class="vstack gap-1 align-items-start">
                                            <img src="<?= e(admin_image($image['image'])) ?>" alt="" class="admin-preview">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="checkbox" class="check" id="remove_gallery_<?= (int) $image['id'] ?>" name="remove_gallery[]" value="<?= (int) $image['id'] ?>">
                                                <label for="remove_gallery_<?= (int) $image['id'] ?>">Retirer</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="gallery" name="gallery[]" multiple accept="image/png,image/jpeg,image/webp,image/gif">
                            <p class="form-text">Tu peux en sélectionner plusieurs d'un coup. Elles s'ajoutent à celles déjà en ligne.</p>
                        <?php elseif ($field['type'] === 'skills'): ?>
                            <p class="form-label"><?= e($field['label']) ?></p>
                            <?php if (!$allSkills): ?>
                                <p class="fg-3">Ajoute d'abord des compétences.</p>
                            <?php else: ?>
                                <div class="d-flex flex-wrap gap-4">
                                    <?php foreach ($allSkills as $skill): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" class="check" id="skill_<?= (int) $skill['id'] ?>" name="skills[]" value="<?= (int) $skill['id'] ?>"
                                                <?= in_array((int) $skill['id'], $checkedSkills, true) ? 'checked' : '' ?>>
                                            <label for="skill_<?= (int) $skill['id'] ?>" class="d-flex align-items-center gap-2">
                                                <img src="<?= e(admin_image($skill['image'])) ?>" alt="" class="admin-thumb admin-thumb-sm<?= $skill['light_bg'] ? ' admin-thumb-light' : '' ?>">
                                                <?= e($skill['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($field['type'] === 'textarea'): ?>
                            <label for="<?= e($name) ?>" class="form-label"><?= e($field['label']) ?><?= $field['required'] ? '' : ' <span class="fg-3">(facultatif)</span>' ?></label>
                            <textarea class="form-control" id="<?= e($name) ?>" name="<?= e($name) ?>" rows="5"
                                <?= isset($field['max']) ? 'maxlength="' . (int) $field['max'] . '"' : '' ?> <?= $field['required'] ? 'required' : '' ?>><?= e((string) $value) ?></textarea>
                        <?php elseif ($field['type'] === 'select'): ?>
                            <label for="<?= e($name) ?>" class="form-label"><?= e($field['label']) ?></label>
                            <select class="form-control" id="<?= e($name) ?>" name="<?= e($name) ?>">
                                <?php foreach ($field['options'] as $optionValue => $optionLabel): ?>
                                    <option value="<?= e((string) $optionValue) ?>" <?= (string) $value === (string) $optionValue ? 'selected' : '' ?>><?= e($optionLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <label for="<?= e($name) ?>" class="form-label"><?= e($field['label']) ?><?= $field['required'] ? '' : ' <span class="fg-3">(facultatif)</span>' ?></label>
                            <input type="<?= ['url' => 'url', 'month' => 'month'][$field['type']] ?? 'text' ?>" class="form-control" id="<?= e($name) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"
                                <?= $field['type'] === 'url' ? 'placeholder="https://"' : '' ?>
                                <?= isset($field['max']) ? 'maxlength="' . (int) $field['max'] . '"' : '' ?> <?= $field['required'] ? 'required' : '' ?>>
                        <?php endif; ?>
                        <?php if (isset($field['help'])): ?>
                            <p class="form-text"><?= e($field['help']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-solid theme-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
                    <a href="<?= e($listUrl) ?>" class="btn btn-outline theme-secondary">Annuler</a>
                    <?php if ($editing && isset($editing['slug'])): ?>
                        <a href="../projet/<?= e(rawurlencode($editing['slug'])) ?>" target="_blank" class="btn btn-text theme-secondary ms-auto"><i class="bi bi-box-arrow-up-right me-1"></i>Voir la page</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$rows): ?>
    <p class="fg-3">Rien pour l'instant.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <?php if ($sortable): ?>
                        <th>Ordre</th>
                    <?php endif; ?>
                    <?php if ($imageField): ?>
                        <th>Image</th>
                    <?php endif; ?>
                    <th><?= e($titleLabel) ?></th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $index => $item): ?>
                    <tr>
                        <?php if ($sortable): ?>
                        <td class="text-nowrap">
                            <form method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button type="submit" name="action" value="up" class="btn-icon" aria-label="Monter" <?= $index === 0 ? 'disabled' : '' ?>><i class="bi bi-arrow-up"></i></button>
                                <button type="submit" name="action" value="down" class="btn-icon" aria-label="Descendre" <?= $index === count($rows) - 1 ? 'disabled' : '' ?>><i class="bi bi-arrow-down"></i></button>
                            </form>
                        </td>
                        <?php endif; ?>
                        <?php if ($imageField): ?>
                            <td>
                                <?php if ($item[$imageField]): ?>
                                    <img src="<?= e(admin_image($item[$imageField])) ?>" alt="" class="admin-thumb<?= !empty($item['light_bg']) ? ' admin-thumb-light' : '' ?>">
                                <?php else: ?>
                                    <span class="badge theme-warning"><i class="bi bi-cone-striped me-1"></i>WIP</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <p class="mb-0 fw-semibold"><?= bold_text((string) $item[$entity['title']]) ?></p>
                            <?php if (isset($item['slug'])): ?>
                                <small class="fg-3 d-block">/projet/<?= e($item['slug']) ?></small>
                            <?php elseif (isset($item['description'])): ?>
                                <small class="fg-3 d-block text-truncate" style="max-width: 520px"><?= e($item['description']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= e($listUrl) ?>&edit=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline theme-secondary"><i class="bi bi-pencil"></i><span class="visually-hidden">Modifier</span></a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Supprimer cet élément ?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-sm btn-subtle theme-danger"><i class="bi bi-trash"></i><span class="visually-hidden">Supprimer</span></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/partials/footer.php'; ?>
