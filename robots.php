<?php
require_once __DIR__ . '/tools/helpers.php';

header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
echo "Disallow: /admin/\n\n";
echo 'Sitemap: ' . base_url('sitemap.xml') . "\n";
