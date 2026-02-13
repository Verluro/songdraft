<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Nieprawidłowy login lub hasło.';
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie admin</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<main class="section container">
    <h1>Panel administracyjny</h1>
    <?php if ($error): ?><p class="message error"><?= h($error) ?></p><?php endif; ?>
    <form method="post" class="contact-form">
        <label>Login<input type="text" name="username" required></label>
        <label>Hasło<input type="password" name="password" required></label>
        <button type="submit" class="btn">Zaloguj</button>
        <p class="muted">Domyślnie: admin / songdraft123</p>
    </form>
</main>
</body>
</html>
