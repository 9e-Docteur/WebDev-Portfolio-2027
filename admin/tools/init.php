<?php
require_once dirname(__DIR__, 2) . '/tools/session.php';
require_once dirname(__DIR__, 2) . '/tools/db.php';
require_once dirname(__DIR__, 2) . '/tools/helpers.php';

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function unread_messages(PDO $bdd): int
{
    return (int) $bdd->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
}

function admin_image(?string $path): string
{
    return $path ? '../' . $path : '';
}
