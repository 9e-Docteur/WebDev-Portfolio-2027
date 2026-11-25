<?php
require_once 'tools/session.php';
require_once 'tools/helpers.php';

$file = __DIR__ . '/' . CV_PATH;
if (!is_file($file)) {
    http_response_code(404);
    exit('CV indisponible pour le moment.');
}

require_once 'tools/db.php';
require_once 'tools/stats.php';
track_page_view($bdd, '/cv.pdf');

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: inline; filename="CV-Loris-Populaire.pdf"');
header('Cache-Control: no-cache');
header('X-Robots-Tag: noindex');
readfile($file);
