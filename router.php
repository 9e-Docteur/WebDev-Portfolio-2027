<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/(\.|vendor/|tools/|database/|bin/|resources/|assets/cv/|admin/(tools|partials)/|composer\.)#', $path)) {
    http_response_code(404);
    exit('Not found');
}

if (preg_match('#^/projet/([a-z0-9-]+)/?$#', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    $_SERVER['SCRIPT_NAME'] = '/projet.php';
    require __DIR__ . '/projet.php';
    return true;
}

if (preg_match('#^/(mentions-legales|confidentialite)/?$#', $path, $matches)) {
    $_SERVER['SCRIPT_NAME'] = '/' . $matches[1] . '.php';
    require __DIR__ . '/' . $matches[1] . '.php';
    return true;
}

if ($path === '/cv.pdf') {
    $_SERVER['SCRIPT_NAME'] = '/cv.php';
    require __DIR__ . '/cv.php';
    return true;
}

if ($path === '/sitemap.xml') {
    $_SERVER['SCRIPT_NAME'] = '/sitemap.php';
    require __DIR__ . '/sitemap.php';
    return true;
}

if ($path === '/robots.txt') {
    $_SERVER['SCRIPT_NAME'] = '/robots.php';
    require __DIR__ . '/robots.php';
    return true;
}

return false;
