<?php
require_once __DIR__ . '/tools/secu.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$_SESSION = [];
session_regenerate_id(true);
session_destroy();

redirect('login.php');
