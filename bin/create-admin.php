<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}

require dirname(__DIR__) . '/tools/db.php';

$username = $argv[1] ?? null;
if (!$username) {
    fwrite(STDERR, "Usage : php bin/create-admin.php <username>\n");
    exit(1);
}

fwrite(STDOUT, 'Mot de passe : ');
system('stty -echo');
$password = trim((string) fgets(STDIN));
system('stty echo');
fwrite(STDOUT, "\n");

if (strlen($password) < 10) {
    fwrite(STDERR, "Le mot de passe doit contenir au moins 10 caractères.\n");
    exit(1);
}

$stmt = $bdd->prepare(
    'INSERT INTO admins (username, password) VALUES (:username, :password)
     ON DUPLICATE KEY UPDATE password = VALUES(password)'
);
$stmt->execute([
    'username' => $username,
    'password' => password_hash($password, PASSWORD_DEFAULT),
]);

fwrite(STDOUT, "Administrateur « $username » enregistré.\n");
