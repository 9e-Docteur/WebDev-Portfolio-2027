<?php
require_once __DIR__ . '/mailer.php';

function handle_contact(PDO $bdd): void
{
    $input = [
        'lastname' => trim((string) ($_POST['name'] ?? '')),
        'firstname' => trim((string) ($_POST['firstname'] ?? '')),
        'email' => trim((string) ($_POST['mail'] ?? '')),
        'phone' => trim((string) ($_POST['phone'] ?? '')),
        'body' => trim((string) ($_POST['msg'] ?? '')),
    ];

    if (!empty($_POST['website'])) {
        redirect('./?sent=1#contact');
    }

    $errors = [];
    if (!csrf_valid()) {
        $errors[] = 'La session a expiré, merci de réessayer.';
    }
    if ($input['lastname'] === '' || mb_strlen($input['lastname']) > 100) {
        $errors[] = 'Le nom est obligatoire (100 caractères maximum).';
    }
    if ($input['firstname'] === '' || mb_strlen($input['firstname']) > 100) {
        $errors[] = 'Le prénom est obligatoire (100 caractères maximum).';
    }
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($input['email']) > 190) {
        $errors[] = "L'adresse e-mail n'est pas valide.";
    }
    if ($input['phone'] !== '' && !preg_match('/^[0-9 +().\/-]{6,30}$/', $input['phone'])) {
        $errors[] = "Le numéro de téléphone n'est pas valide.";
    }
    if (mb_strlen($input['body']) < 10 || mb_strlen($input['body']) > 5000) {
        $errors[] = 'Le message doit contenir entre 10 et 5000 caractères.';
    }
    if (time() - ($_SESSION['last_contact'] ?? 0) < 60) {
        $errors[] = 'Merci de patienter une minute avant de renvoyer un message.';
    }

    if ($errors) {
        $_SESSION['contact_errors'] = $errors;
        $_SESSION['contact_old'] = $input;
        redirect('./#contact');
    }

    $stmt = $bdd->prepare(
        'INSERT INTO messages (lastname, firstname, email, phone, body, ip)
         VALUES (:lastname, :firstname, :email, :phone, :body, :ip)'
    );
    $stmt->execute([
        'lastname' => $input['lastname'],
        'firstname' => $input['firstname'],
        'email' => $input['email'],
        'phone' => $input['phone'] !== '' ? $input['phone'] : null,
        'body' => $input['body'],
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    $_SESSION['last_contact'] = time();
    send_contact_notification($input);

    redirect('./?sent=1#contact');
}
