<?php
require_once __DIR__ . '/tools/helpers.php';
require_once __DIR__ . '/tools/db.php';
require_once __DIR__ . '/tools/content.php';

$homeModified = max(
    filemtime(__DIR__ . '/index.php'),
    filemtime(__DIR__ . '/partials/about.php'),
    filemtime(__DIR__ . '/assets/css/style.css')
);

$projects = ordered_rows($bdd, 'projects');

$urls = [[
    'loc' => base_url(),
    'lastmod' => date('Y-m-d', $homeModified),
    'images' => array_merge(
        [base_url('assets/img/og-image.jpg')],
        array_map(fn($project) => base_url($project['image']), array_filter($projects, fn($project) => $project['image']))
    ),
]];

foreach ($projects as $project) {
    $urls[] = [
        'loc' => project_url($project),
        'lastmod' => date('Y-m-d', strtotime($project['updated_at'])),
        'images' => array_map('base_url', array_filter(array_merge([$project['image']], project_images($bdd, (int) $project['id'])))),
    ];
}

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($urls as $url): ?>
    <url>
        <loc><?= e($url['loc']) ?></loc>
        <lastmod><?= e($url['lastmod']) ?></lastmod>
<?php foreach ($url['images'] as $image): ?>
        <image:image>
            <image:loc><?= e($image) ?></image:loc>
        </image:image>
<?php endforeach; ?>
    </url>
<?php endforeach; ?>
</urlset>
