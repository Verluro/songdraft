<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$settings = getSiteSettings();
$features = getFeatures();
$services = getServices();
$packages = getPackages();
$screenshots = getScreenshots();

trackVisit('/');

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = 'Wypełnij wszystkie pola formularza.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Podaj poprawny adres e-mail.';
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO inquiries (name, email, message, created_at) VALUES (:name, :email, :message, NOW())');
            $stmt->execute([':name' => $name, ':email' => $email, ':message' => $message]);
            $success = true;
        } catch (Throwable $e) {
            $error = 'Nie udało się zapisać wiadomości. Spróbuj ponownie.';
        }
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($settings['site_name']) ?> | System do wyświetlania tekstów</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header class="hero-alt">
    <div class="container nav-alt">
        <strong><?= h($settings['site_name']) ?></strong>
        <div class="menu">
            <a href="#funkcje">Funkcje</a>
            <a href="#pakiety">Pakiety</a>
            <a href="#zrzuty">Zrzuty</a>
            <a href="#kontakt" class="pill">Kontakt</a>
        </div>
    </div>

    <div class="container hero-main">
        <div>
            <p class="eyebrow">Nowoczesna aplikacja dla parafii</p>
            <h1><?= h($settings['tagline']) ?></h1>
            <p><?= h($settings['hero_subtitle']) ?></p>
            <a href="#pakiety" class="btn">Zobacz ceny pakietów</a>
        </div>
        <div class="hero-box">
            <h3>W zestawach znajdziesz:</h3>
            <ul>
                <li>Licencję aplikacji SongDraft</li>
                <li>Pakiety sprzętowe (oprócz Mini)</li>
                <li>Sterowanie aplikacją z tableta</li>
            </ul>
        </div>
    </div>
</header>

<main class="container">
    <section id="funkcje" class="section">
        <h2><?= h($settings['features_title']) ?></h2>
        <div class="cards">
            <?php foreach ($features as $feature): ?>
                <article class="card">
                    <h3><?= h($feature['title']) ?></h3>
                    <p><?= h($feature['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="pakiety" class="section">
        <h2><?= h($settings['pricing_title']) ?></h2>
        <div class="pricing-cards">
            <?php foreach ($packages as $package): ?>
                <article class="price-card <?= !empty($package['featured']) ? 'is-featured' : '' ?>">
                    <p class="tag"><?= h($package['badge']) ?></p>
                    <h3><?= h($package['name']) ?></h3>
                    <p class="muted"><?= h($package['target']) ?></p>
                    <p><strong><?= h($package['tablet']) ?></strong></p>
                    <p class="price"><?= h($package['price']) ?></p>
                    <ul>
                        <?php foreach (explode('|', (string) $package['features']) as $f): ?>
                            <?php if (trim($f) !== ''): ?>
                                <li><?= h(trim($f)) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="zrzuty" class="section">
        <h2><?= h($settings['screenshots_title']) ?></h2>
        <div class="shots">
            <?php foreach ($screenshots as $shot): ?>
                <figure class="shot-card">
                    <img src="<?= h($shot['image_url']) ?>" alt="<?= h($shot['title']) ?>">
                    <figcaption>
                        <h3><?= h($shot['title']) ?></h3>
                        <p><?= h($shot['description']) ?></p>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section">
        <h2><?= h($settings['services_title']) ?></h2>
        <ul class="service-list">
            <?php foreach ($services as $service): ?>
                <li><?= h($service) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section id="kontakt" class="section contact-box">
        <div>
            <h2><?= h($settings['contact_title']) ?></h2>
            <p>Telefon: <?= h($settings['cta_phone']) ?><br>E-mail: <?= h($settings['cta_email']) ?></p>
        </div>
        <div>
            <?php if ($success): ?><p class="success">Dziękujemy! Wiadomość została zapisana.</p><?php endif; ?>
            <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
            <form method="post" class="form">
                <input name="name" placeholder="Imię i nazwisko" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <textarea name="message" rows="5" placeholder="Wiadomość" required></textarea>
                <button class="btn" type="submit">Wyślij</button>
            </form>
        </div>
    </section>
</main>

<footer class="footer-alt">
    <div class="container">
        <p>© <?= date('Y') ?> <?= h($settings['site_name']) ?></p>
        <a href="admin/login.php">Panel admina</a>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
