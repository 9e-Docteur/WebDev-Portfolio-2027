<?php

function setting(PDO $bdd, string $name, string $default = ''): string
{
    $stmt = $bdd->prepare('SELECT value FROM settings WHERE name = :name');
    $stmt->execute(['name' => $name]);
    $value = $stmt->fetchColumn();

    return $value === false ? $default : $value;
}

function ordered_rows(PDO $bdd, string $table): array
{
    $allowed = ['passions', 'objectives', 'skills', 'software', 'projects', 'timeline'];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Unknown table ' . $table);
    }

    $order = $table === 'timeline' ? 'start_month IS NULL, start_month DESC, id DESC' : 'position, id';

    return $bdd->query("SELECT * FROM `$table` ORDER BY $order")->fetchAll();
}

function project_by_slug(PDO $bdd, string $slug): ?array
{
    $stmt = $bdd->prepare('SELECT * FROM projects WHERE slug = :slug');
    $stmt->execute(['slug' => $slug]);

    return $stmt->fetch() ?: null;
}

function project_skills(PDO $bdd, int $projectId): array
{
    $stmt = $bdd->prepare(
        'SELECT s.* FROM skills s
         JOIN project_skills ps ON ps.skill_id = s.id
         WHERE ps.project_id = :id
         ORDER BY s.position, s.id'
    );
    $stmt->execute(['id' => $projectId]);

    return $stmt->fetchAll();
}

function project_images(PDO $bdd, int $projectId): array
{
    $stmt = $bdd->prepare('SELECT image FROM project_images WHERE project_id = :id ORDER BY position, id');
    $stmt->execute(['id' => $projectId]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
