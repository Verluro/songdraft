<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

requireLogin();

$settings = getSiteSettings();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed = ['site_name', 'tagline', 'cta_phone', 'cta_email', 'hero_subtitle'];
    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');

    foreach ($allowed as $key) {
        $value = trim($_POST[$key] ?? '');
        $stmt->execute([':key' => $key, ':value' => $value]);
    }

    $settings = getSiteSettings();
    $message = 'Zapisano ustawienia.';
}

$visitsToday = 0;
$totalVisits = 0;
$inquiries = [];

try {
    $visitsToday = (int) db()->query('SELECT COUNT(*) FROM visit_stats WHERE DATE(visited_at) = CURDATE()')->fetchColumn();
    $totalVisits = (int) db()->query('SELECT COUNT(*) FROM visit_stats')->fetchColumn();
    $inquiries = db()->query('SELECT name, email, message, created_at FROM inquiries ORDER BY created_at DESC LIMIT 10')->fetchAll();
} catch (Throwable $e) {
    // Silent fallback for non-initialized DB.
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admina</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<main class="section container">
    <h1>Zaplecze SongDraft</h1>
    <p><a class="btn btn-outline" href="logout.php">Wyloguj</a> <a class="btn btn-outline" href="../index.php">Podgląd strony</a></p>

    <section class="grid-3">
        <article class="card"><h3>Wizyty dzisiaj</h3><p><?= $visitsToday ?></p></article>
        <article class="card"><h3>Wizyty łącznie</h3><p><?= $totalVisits ?></p></article>
        <article class="card"><h3>Nowe zapytania</h3><p><?= count($inquiries) ?></p></article>
    </section>

    <section class="section">
        <h2>Ustawienia strony</h2>
        <?php if ($message): ?><p class="message success"><?= h($message) ?></p><?php endif; ?>
        <form method="post" class="contact-form">
            <label>Nazwa strony<input type="text" name="site_name" value="<?= h($settings['site_name']) ?>"></label>
            <label>Hasło przewodnie<input type="text" name="tagline" value="<?= h($settings['tagline']) ?>"></label>
            <label>Opis hero<input type="text" name="hero_subtitle" value="<?= h($settings['hero_subtitle']) ?>"></label>
            <label>Telefon<input type="text" name="cta_phone" value="<?= h($settings['cta_phone']) ?>"></label>
            <label>E-mail<input type="email" name="cta_email" value="<?= h($settings['cta_email']) ?>"></label>
            <button type="submit" class="btn">Zapisz</button>
        </form>
    </section>

    <section class="section">
        <h2>Ostatnie zapytania</h2>
        <?php if (!$inquiries): ?>
            <p class="muted">Brak zapisanych zapytań.</p>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($inquiries as $inquiry): ?>
                    <article class="card">
                        <h3><?= h($inquiry['name']) ?></h3>
                        <p><a href="mailto:<?= h($inquiry['email']) ?>"><?= h($inquiry['email']) ?></a></p>
                        <p><?= nl2br(h($inquiry['message'])) ?></p>
                        <small><?= h($inquiry['created_at']) ?></small>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
