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
    <meta name="description" content="Nowoczesny system do wyświetlania tekstów, zarządzania pieśniami, multimediami i sterowania urządzeniami w kościele.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="noise"></div>
<header class="hero-modern">
    <div class="container topbar glass">
        <strong class="brand"><?= h($settings['site_name']) ?></strong>
        <div class="menu">
            <a href="#funkcje">Funkcje</a>
            <a href="#pakiety">Pakiety</a>
            <a href="#zrzuty">Zrzuty</a>
            <a href="#kontakt" class="pill">Kontakt</a>
        </div>
    </div>

    <div class="container hero-grid">
        <div class="hero-copy">
            <p class="eyebrow">Technologia dla liturgii</p>
            <h1><?= h($settings['tagline']) ?></h1>
            <p class="lead"><?= h($settings['hero_subtitle']) ?></p>
            <div class="hero-actions">
                <a href="#pakiety" class="btn btn-primary">Zobacz ceny pakietów</a>
                <a href="#zrzuty" class="btn btn-ghost">Zobacz zrzuty aplikacji</a>
            </div>
            <div class="chips">
                <span>Mini / Start / Parafia / Pro+</span>
                <span>Sterowanie z tableta</span>
                <span>Podgląd kamer i multimedia</span>
            </div>
        </div>

        <aside class="hero-widget glass">
            <h3>Co dostajesz?</h3>
            <ul>
                <li>Gotowy system do pracy podczas liturgii</li>
                <li>Spójny zestaw aplikacja + konfiguracja</li>
                <li>Skalowanie od pakietu Mini do Pro+</li>
            </ul>
            <p>W każdej chwili możesz rozbudować instalację o kolejne ekrany, kamery i urządzenia smart.</p>
        </aside>
    </div>
</header>

<main class="container main-modern">
    <section id="funkcje" class="section">
        <div class="section-head">
            <p class="eyebrow dark">Możliwości</p>
            <h2><?= h($settings['features_title']) ?></h2>
        </div>
        <div class="grid feature-grid">
            <?php foreach ($features as $feature): ?>
                <article class="card card-gradient">
                    <h3><?= h($feature['title']) ?></h3>
                    <p><?= h($feature['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="pakiety" class="section">
        <div class="section-head">
            <p class="eyebrow dark">Oferta</p>
            <h2><?= h($settings['pricing_title']) ?></h2>
        </div>
        <div class="grid pricing-grid">
            <?php foreach ($packages as $package): ?>
                <article class="card pricing-card <?= !empty($package['featured']) ? 'featured' : '' ?>">
                    <p class="tag"><?= h($package['badge']) ?></p>
                    <h3><?= h($package['name']) ?></h3>
                    <p class="muted"><?= h($package['target']) ?></p>
                    <p class="device"><?= h($package['tablet']) ?></p>
                    <p class="price"><?= h($package['price']) ?></p>
                    <ul>
                        <?php foreach (explode('|', (string) $package['features']) as $f): ?>
                            <?php if (trim($f) !== ''): ?><li><?= h(trim($f)) ?></li><?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="zrzuty" class="section">
        <div class="section-head">
            <p class="eyebrow dark">UI aplikacji</p>
            <h2><?= h($settings['screenshots_title']) ?></h2>
        </div>
        <div class="grid shot-grid">
            <?php foreach ($screenshots as $shot): ?>
                <figure class="card shot-card">
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
        <div class="section-head">
            <p class="eyebrow dark">Wsparcie</p>
            <h2><?= h($settings['services_title']) ?></h2>
        </div>
        <ul class="service-list card">
            <?php foreach ($services as $service): ?>
                <li><?= h($service) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section id="kontakt" class="section">
        <div class="contact-modern card">
            <div>
                <p class="eyebrow dark">Kontakt</p>
                <h2><?= h($settings['contact_title']) ?></h2>
                <p>Telefon: <?= h($settings['cta_phone']) ?><br>E-mail: <?= h($settings['cta_email']) ?></p>
            </div>
            <div>
                <?php if ($success): ?><p class="status success">Dziękujemy! Wiadomość została zapisana.</p><?php endif; ?>
                <?php if ($error): ?><p class="status error"><?= h($error) ?></p><?php endif; ?>
                <form method="post" class="form">
                    <input name="name" placeholder="Imię i nazwisko" required>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <textarea name="message" rows="5" placeholder="Wiadomość" required></textarea>
                    <button class="btn btn-primary" type="submit">Wyślij zapytanie</button>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="footer-modern">
    <div class="container footer-row">
        <p>© <?= date('Y') ?> <?= h($settings['site_name']) ?></p>
        <a href="admin/login.php">Panel admina</a>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
