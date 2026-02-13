<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$settings = getSiteSettings();
trackVisit('/');

$packages = [
    [
        'name' => 'Start',
        'tablet' => 'Tablet 8”',
        'target' => 'Dla mniejszych parafii',
        'badge' => 'Ekonomiczny start',
        'features' => [
            'Mini komputer + tablet 8” + router + licencja',
            'Baza pieśni (śpiewnik siedlecki)',
            'Dodawanie nowych pieśni i tworzenie zestawów',
            'Tryb prezentacji na pełnym ekranie',
            'Personalizacja czcionki i tła',
        ],
    ],
    [
        'name' => 'Parafia',
        'tablet' => 'Tablet 11”',
        'target' => 'Najczęściej wybierany',
        'badge' => 'Najlepszy wybór',
        'featured' => true,
        'features' => [
            'Mini komputer + tablet 11” + router + licencja',
            'Wszystko z pakietu Start',
            'Multimedia: zdjęcia i nagrania',
            'Kalendarz liturgiczny (aktualizacja online)',
            'Ukrywanie zwrotek i różaniec',
            'Podgląd kamery na ołtarz',
        ],
    ],
    [
        'name' => 'Pro+',
        'tablet' => 'Tablet 11” premium',
        'target' => 'Dla rozbudowanych instalacji',
        'badge' => 'Pełna kontrola',
        'features' => [
            'Mini komputer + tablet 11” + router + licencja',
            'Wszystko z pakietu Parafia',
            'Obsługa wielu kamer',
            'Sterowanie zasilaniem ekranów i smart urządzeń',
            'Wsparcie instalacyjne i szybka konfiguracja',
        ],
    ],
];

$services = [
    'Wybór, montaż i konfiguracja ekranów / telewizorów w kościele',
    'Montaż i konfiguracja kamery na ołtarz',
    'Montaż i konfiguracja większej ilości kamer',
];

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
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message,
            ]);
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
    <title><?= h($settings['site_name']) ?> | System tekstów i multimediów dla kościoła</title>
    <meta name="description" content="<?= h($settings['site_name']) ?> to nowoczesna aplikacja do wyświetlania tekstów, multimediów i sterowania urządzeniami podczas liturgii.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<header class="hero" id="start">
    <nav class="nav container glass">
        <div class="logo"><?= h($settings['site_name']) ?></div>
        <div class="nav-links">
            <a href="#funkcje">Funkcje</a>
            <a href="#pakiety">Pakiety</a>
            <a href="#kontakt" class="btn btn-sm">Kontakt</a>
        </div>
    </nav>

    <div class="container hero-layout">
        <div class="hero-content">
            <p class="eyebrow">Technologia, która wspiera liturgię</p>
            <h1><?= h($settings['tagline']) ?></h1>
            <p class="lead"><?= h($settings['hero_subtitle']) ?></p>
            <div class="hero-cta">
                <a href="#pakiety" class="btn">Wybierz pakiet</a>
                <a href="#funkcje" class="btn btn-outline">Zobacz możliwości</a>
            </div>
            <div class="hero-points">
                <span>✓ Sterowanie z tableta</span>
                <span>✓ Teksty + multimedia</span>
                <span>✓ Rozszerzenia kamer i smart</span>
            </div>
        </div>
        <aside class="hero-panel glass">
            <h3>Jak to działa?</h3>
            <ol>
                <li>Aplikacja działa na mini komputerze przy nagłośnieniu.</li>
                <li>Obsługa odbywa się wygodnie z tableta 8” lub 11”.</li>
                <li>Router zapewnia stabilne połączenie komputer ↔ tablet.</li>
            </ol>
            <p class="muted">Każdy pakiet zawiera licencję na aplikację.</p>
        </aside>
    </div>
</header>

<main>
    <section class="section container" id="funkcje">
        <div class="section-heading">
            <p class="eyebrow dark">Możliwości aplikacji</p>
            <h2>Wszystko, czego potrzebujesz do prowadzenia wyświetlania w kościele</h2>
        </div>
        <div class="feature-grid">
            <article class="card feature"><h3>Baza pieśni i zestawy</h3><p>Śpiewnik siedlecki, własne pieśni i gotowe playlisty na każdy dzień.</p></article>
            <article class="card feature"><h3>Tryb prezentacji</h3><p>Pełny ekran i płynne przechodzenie pomiędzy zwrotkami oraz pieśniami.</p></article>
            <article class="card feature"><h3>Personalizacja obrazu</h3><p>Regulacja wielkości i koloru czcionki oraz tła dla najlepszej czytelności.</p></article>
            <article class="card feature"><h3>Multimedia</h3><p>Dodawaj zdjęcia i nagrania, by wzbogacić oprawę nabożeństw.</p></article>
            <article class="card feature"><h3>Kalendarz liturgiczny</h3><p>Psalmy na każdy dzień, corocznie aktualizowane przez Internet.</p></article>
            <article class="card feature"><h3>Kamery i urządzenia smart</h3><p>Podgląd ołtarza, wiele kamer oraz zdalne sterowanie zasilaniem ekranów.</p></article>
        </div>
    </section>

    <section class="section section-dark" id="pakiety">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Pakiety wdrożeniowe</p>
                <h2>Wybierz 1 z 3 pakietów dopasowanych do skali parafii</h2>
            </div>
            <div class="pricing-grid">
                <?php foreach ($packages as $package): ?>
                    <article class="card pricing <?= !empty($package['featured']) ? 'featured' : '' ?>">
                        <p class="pricing-badge"><?= h($package['badge']) ?></p>
                        <h3><?= h($package['name']) ?></h3>
                        <p class="muted"><?= h($package['target']) ?></p>
                        <p class="device"><?= h($package['tablet']) ?></p>
                        <ul>
                            <?php foreach ($package['features'] as $feature): ?>
                                <li><?= h($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="#kontakt" class="btn">Zapytaj o wycenę</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section container">
        <div class="section-heading">
            <p class="eyebrow dark">Usługi dodatkowe</p>
            <h2>Kompleksowe wdrożenie w Twoim kościele</h2>
        </div>
        <div class="service-cards">
            <?php foreach ($services as $service): ?>
                <article class="card service"><p><?= h($service) ?></p></article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section container" id="kontakt">
        <div class="contact-wrap glass">
            <div>
                <p class="eyebrow dark">Kontakt</p>
                <h2>Chcesz poznać dokładną wycenę?</h2>
                <p>Wypełnij formularz — odpowiemy i pomożemy dobrać idealny zestaw.</p>
                <p class="contact-data">Telefon: <?= h($settings['cta_phone']) ?><br>E-mail: <?= h($settings['cta_email']) ?></p>
            </div>
            <div>
                <?php if ($success): ?>
                    <p class="message success">Dziękujemy! Wiadomość została zapisana.</p>
                <?php elseif ($error): ?>
                    <p class="message error"><?= h($error) ?></p>
                <?php endif; ?>

                <form method="post" class="contact-form">
                    <label>Imię i nazwisko<input type="text" name="name" required></label>
                    <label>E-mail<input type="email" name="email" required></label>
                    <label>Wiadomość<textarea name="message" rows="5" required></textarea></label>
                    <button type="submit" class="btn">Wyślij zapytanie</button>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <p>© <?= date('Y') ?> <?= h($settings['site_name']) ?> · Nazwę marki można zmienić w panelu administracyjnym.</p>
        <a href="admin/login.php">Panel admina</a>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
