<?php
require_once __DIR__ . '/../tools/entities.php';
$currentPage = $currentPage ?? '';
$unread = unread_messages($bdd);
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle ?? 'Admin') ?> - Admin Portfolio</title>
    <?php include __DIR__ . '/head.php'; ?>
</head>

<body>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="container py-6">
        <?php foreach (take_flashes() as $flash): ?>
            <div class="alert theme-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> mb-4" role="alert">
                <i class="bi <?= $flash['type'] === 'error' ? 'bi-exclamation-triangle' : 'bi-check-circle' ?>"></i>
                <p><?= e($flash['message']) ?></p>
            </div>
        <?php endforeach; ?>
