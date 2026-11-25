<?php
$siteName = 'Loris Populaire - Portfolio';
$siteUrl = base_url();
$personId = $siteUrl . '#loris-populaire';
$defaultImage = base_url(asset('assets/img/og-image.jpg'));

$meta = array_merge([
    'title' => 'Loris Populaire - Portfolio | Développeur web fullstack',
    'description' => "Portfolio de Loris Populaire, étudiant en BES Web Developer à l'EPSE (Enghien). Projets PHP, Symfony et JavaScript, compétences, parcours et contact.",
    'url' => $siteUrl,
    'image' => $defaultImage,
    'image_alt' => 'Loris Populaire, Web Developer',
    'type' => 'profile',
    'schema' => [],
], $meta ?? []);

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => array_merge([
        [
            '@type' => 'WebSite',
            '@id' => $siteUrl . '#website',
            'url' => $siteUrl,
            'name' => $siteName,
            'description' => "Portfolio de Loris Populaire, développeur web.",
            'inLanguage' => 'fr',
            'publisher' => ['@id' => $personId],
        ],
        [
            '@type' => 'Person',
            '@id' => $personId,
            'name' => 'Loris Populaire',
            'jobTitle' => 'Développeur web',
            'description' => "Étudiant en 2e année du BES Web Developer à l'EPSE, passionné de back-end et d'administration système.",
            'url' => $siteUrl,
            'image' => $defaultImage,
            'email' => 'mailto:lorisplp.pro@gmail.com',
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Enghien', 'addressCountry' => 'BE'],
            'affiliation' => [
                '@type' => 'EducationalOrganization',
                'name' => "EPSE - Enseignement de Promotion Sociale d'Enghien",
                'url' => 'https://webdev.epse.be',
                'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Enghien', 'addressCountry' => 'BE'],
            ],
            'knowsAbout' => array_values(array_unique(array_merge(
                array_column($skills ?? [], 'name'),
                array_column($software ?? [], 'name')
            ))),
            'knowsLanguage' => [
                ['@type' => 'Language', 'name' => 'Français', 'alternateName' => 'fr'],
                ['@type' => 'Language', 'name' => 'Néerlandais', 'alternateName' => 'nl'],
                ['@type' => 'Language', 'name' => 'Anglais', 'alternateName' => 'en'],
            ],
            'sameAs' => array_column(social_links(), 'url'),
        ],
    ], $meta['schema']),
];
?>
<title><?= e($meta['title']) ?></title>
<base href="<?= e($siteUrl) ?>">
<meta name="description" content="<?= e($meta['description']) ?>">
<meta name="author" content="Loris Populaire">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
<meta name="theme-color" content="#202020">
<link rel="canonical" href="<?= e($meta['url']) ?>">

<meta property="og:type" content="<?= e($meta['type']) ?>">
<meta property="og:locale" content="fr_BE">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($meta['title']) ?>">
<meta property="og:description" content="<?= e($meta['description']) ?>">
<meta property="og:url" content="<?= e($meta['url']) ?>">
<meta property="og:image" content="<?= e($meta['image']) ?>">
<?php if ($meta['image'] === $defaultImage): ?>
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<?php endif; ?>
<meta property="og:image:alt" content="<?= e($meta['image_alt']) ?>">
<?php if ($meta['type'] === 'profile'): ?>
<meta property="profile:first_name" content="Loris">
<meta property="profile:last_name" content="Populaire">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($meta['title']) ?>">
<meta name="twitter:description" content="<?= e($meta['description']) ?>">
<meta name="twitter:image" content="<?= e($meta['image']) ?>">
<meta name="twitter:image:alt" content="<?= e($meta['image_alt']) ?>">

<link rel="icon" href="<?= e(asset('assets/icons/favicon.svg')) ?>" type="image/svg+xml">
<link rel="icon" href="<?= e(asset('assets/icons/favicon-32.png')) ?>" type="image/png" sizes="32x32">
<link rel="icon" href="<?= e(asset('assets/icons/favicon-16.png')) ?>" type="image/png" sizes="16x16">
<link rel="shortcut icon" href="<?= e(asset('favicon.ico')) ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= e(asset('assets/icons/apple-touch-icon.png')) ?>">
<link rel="manifest" href="site.webmanifest">

<link rel="preload" href="assets/fonts/Silkscreen-Regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/EduQLDHand-Regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/MPLUSRounded1c-Regular.woff2" as="font" type="font/woff2" crossorigin>

<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>

<link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>">
