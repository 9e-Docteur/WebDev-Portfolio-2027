<?php
require_once __DIR__ . '/tools/init.php';

if (is_admin()) {
    redirect('index.php');
}

$error = null;
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_valid()) {
        $error = 'La session a expiré, merci de réessayer.';
    } else {
        $stmt = $bdd->prepare('SELECT id, password FROM admins WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_name'] = $username;
            redirect('index.php');
        }

        usleep(random_int(300000, 600000));
        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion - Admin Portfolio</title>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>

<body class="d-flex align-items-center min-vh-100">
    <main class="container login-box">
        <div class="card">
            <div class="card-body p-6">
                <h1 class="fs-2xl mb-5 d-flex align-items-center gap-3"><img src="../assets/icons/favicon.svg" alt="" width="40" height="40">Admin Portfolio</h1>

                <?php if ($error): ?>
                    <div class="alert theme-danger mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p><?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <form method="post" class="vstack gap-4">
                    <?= csrf_field() ?>
                    <div>
                        <label for="username" class="form-label">Identifiant</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= e($username) ?>"
                            autocomplete="username" required autofocus>
                    </div>
                    <div>
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password"
                            autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-solid theme-primary">Se connecter</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
