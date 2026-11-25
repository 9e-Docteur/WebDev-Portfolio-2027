<?php

const UPLOAD_DIR = 'assets/img/uploads';
const UPLOAD_MAX_SIZE = 3 * 1024 * 1024;
const UPLOAD_TYPES = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];

function upload_image(string $field): ?string
{
    return store_uploaded_image($_FILES[$field] ?? null);
}

function upload_images(string $field): array
{
    $files = $_FILES[$field] ?? null;
    if (!$files || !is_array($files['name'])) {
        return [];
    }

    $paths = [];
    try {
        foreach (array_keys($files['name']) as $index) {
            $path = store_uploaded_image([
                'name' => $files['name'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error' => $files['error'][$index],
                'size' => $files['size'][$index],
            ]);
            if ($path) {
                $paths[] = $path;
            }
        }
    } catch (RuntimeException $exception) {
        array_map('delete_uploaded_image', $paths);
        throw $exception;
    }

    return $paths;
}

function store_uploaded_image(?array $file): ?string
{
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("L'envoi de l'image a échoué.");
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException("L'image dépasse 3 Mo.");
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(UPLOAD_TYPES[$mime]) || getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Format accepté : PNG, JPG, WEBP ou GIF.');
    }

    $name = bin2hex(random_bytes(12)) . '.' . UPLOAD_TYPES[$mime];
    $target = dirname(__DIR__) . '/' . UPLOAD_DIR . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException("Impossible d'enregistrer l'image.");
    }

    return UPLOAD_DIR . '/' . $name;
}

function delete_uploaded_image(?string $path): void
{
    if (!$path || !str_starts_with($path, UPLOAD_DIR . '/') || str_contains($path, '..')) {
        return;
    }

    $file = dirname(__DIR__) . '/' . $path;
    if (is_file($file)) {
        unlink($file);
    }
}
