<?php
require_once __DIR__ . '/init.php';

if (!is_admin()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_valid()) {
    http_response_code(400);
    exit('Requête invalide, recharge la page et réessaie.');
}

require_once dirname(__DIR__, 2) . '/tools/stats.php';
if (random_int(1, 20) === 1) {
    purge_old_data($bdd);
}
