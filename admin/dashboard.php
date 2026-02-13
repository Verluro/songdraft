<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth.php';

requireLogin();

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = db();

        if (($_POST['action'] ?? '') === 'save_settings') {
            $allowed = array_keys(SITE_DEFAULTS);
            $stmt = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (:k,:v) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
            foreach ($allowed as $key) {
                $stmt->execute([':k' => $key, ':v' => trim($_POST[$key] ?? '')]);
            }
            $message = 'Zapisano ustawienia strony.';
        }

        if (($_POST['action'] ?? '') === 'save_features') {
            $pdo->exec('TRUNCATE TABLE features');
            $stmt = $pdo->prepare('INSERT INTO features (title, description, sort_order) VALUES (:t,:d,:s)');
            foreach (($_POST['feature_title'] ?? []) as $i => $title) {
                $title = trim((string) $title);
                $desc = trim((string) ($_POST['feature_desc'][$i] ?? ''));
                if ($title !== '' && $desc !== '') {
                    $stmt->execute([':t' => $title, ':d' => $desc, ':s' => $i + 1]);
                }
            }
            $message = 'Zapisano funkcje.';
        }

        if (($_POST['action'] ?? '') === 'save_services') {
            $pdo->exec('TRUNCATE TABLE services');
            $stmt = $pdo->prepare('INSERT INTO services (service_name, sort_order) VALUES (:n,:s)');
            foreach (($_POST['service_name'] ?? []) as $i => $name) {
                $name = trim((string) $name);
                if ($name !== '') {
                    $stmt->execute([':n' => $name, ':s' => $i + 1]);
                }
            }
            $message = 'Zapisano usługi.';
        }

        if (($_POST['action'] ?? '') === 'save_packages') {
            $pdo->exec('TRUNCATE TABLE packages');
            $stmt = $pdo->prepare('INSERT INTO packages (name,badge,target,tablet,price,features,featured,sort_order) VALUES (:name,:badge,:target,:tablet,:price,:features,:featured,:sort)');
            foreach (($_POST['pkg_name'] ?? []) as $i => $name) {
                $name = trim((string) $name);
                if ($name === '') {
                    continue;
                }
                $stmt->execute([
                    ':name' => $name,
                    ':badge' => trim((string) ($_POST['pkg_badge'][$i] ?? '')),
                    ':target' => trim((string) ($_POST['pkg_target'][$i] ?? '')),
                    ':tablet' => trim((string) ($_POST['pkg_tablet'][$i] ?? '')),
                    ':price' => trim((string) ($_POST['pkg_price'][$i] ?? '')),
                    ':features' => trim((string) ($_POST['pkg_features'][$i] ?? '')),
                    ':featured' => isset($_POST['pkg_featured'][$i]) ? 1 : 0,
                    ':sort' => $i + 1,
                ]);
            }
            $message = 'Zapisano pakiety i ceny.';
        }

        if (($_POST['action'] ?? '') === 'save_screenshots') {
            $pdo->exec('TRUNCATE TABLE screenshots');
            $stmt = $pdo->prepare('INSERT INTO screenshots (title,description,image_url,sort_order) VALUES (:t,:d,:u,:s)');
            foreach (($_POST['shot_title'] ?? []) as $i => $title) {
                $title = trim((string) $title);
                $desc = trim((string) ($_POST['shot_desc'][$i] ?? ''));
                $url = trim((string) ($_POST['shot_url'][$i] ?? ''));
                if ($title !== '' && $url !== '') {
                    $stmt->execute([':t' => $title, ':d' => $desc, ':u' => $url, ':s' => $i + 1]);
                }
            }
            $message = 'Zapisano zrzuty aplikacji.';
        }
    } catch (Throwable $e) {
        $message = 'Błąd zapisu: ' . $e->getMessage();
    }
}

$settings = getSiteSettings();
$features = getFeatures();
$services = getServices();
$packages = getPackages();
$screenshots = getScreenshots();

$visitsToday = 0;
$totalVisits = 0;
$inquiries = [];
try {
    $visitsToday = (int) db()->query('SELECT COUNT(*) FROM visit_stats WHERE DATE(visited_at)=CURDATE()')->fetchColumn();
    $totalVisits = (int) db()->query('SELECT COUNT(*) FROM visit_stats')->fetchColumn();
    $inquiries = db()->query('SELECT name,email,message,created_at FROM inquiries ORDER BY created_at DESC LIMIT 10')->fetchAll();
} catch (Throwable $e) {
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel admina</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<main class="section container">
    <h1>Panel administracyjny</h1>
    <p><a class="btn" href="../index.php">Podgląd strony</a> <a class="btn" href="logout.php">Wyloguj</a></p>
    <?php if ($message): ?><p class="success"><?= h($message) ?></p><?php endif; ?>

    <section class="cards">
        <article class="card"><h3>Wizyty dzisiaj</h3><p><?= $visitsToday ?></p></article>
        <article class="card"><h3>Wizyty łącznie</h3><p><?= $totalVisits ?></p></article>
        <article class="card"><h3>Zapytania</h3><p><?= count($inquiries) ?></p></article>
    </section>

    <section class="section">
        <h2>Treści główne</h2>
        <form method="post" class="form">
            <input type="hidden" name="action" value="save_settings">
            <?php foreach (SITE_DEFAULTS as $key => $value): ?>
                <label><?= h($key) ?><input type="text" name="<?= h($key) ?>" value="<?= h((string) ($settings[$key] ?? '')) ?>"></label>
            <?php endforeach; ?>
            <button class="btn" type="submit">Zapisz</button>
        </form>
    </section>

    <section class="section">
        <h2>Funkcje</h2>
        <form method="post" class="form">
            <input type="hidden" name="action" value="save_features">
            <?php for ($i = 0; $i < 8; $i++): $f = $features[$i] ?? ['title' => '', 'description' => '']; ?>
                <input type="text" name="feature_title[]" placeholder="Tytuł funkcji" value="<?= h((string) $f['title']) ?>">
                <textarea name="feature_desc[]" placeholder="Opis funkcji"><?= h((string) $f['description']) ?></textarea>
            <?php endfor; ?>
            <button class="btn" type="submit">Zapisz funkcje</button>
        </form>
    </section>

    <section class="section">
        <h2>Pakiety i ceny</h2>
        <form method="post" class="form">
            <input type="hidden" name="action" value="save_packages">
            <?php for ($i = 0; $i < 6; $i++): $p = $packages[$i] ?? ['name'=>'','badge'=>'','target'=>'','tablet'=>'','price'=>'','features'=>'','featured'=>0]; ?>
                <div class="card">
                    <input type="text" name="pkg_name[]" placeholder="Nazwa pakietu" value="<?= h((string) $p['name']) ?>">
                    <input type="text" name="pkg_badge[]" placeholder="Badge" value="<?= h((string) $p['badge']) ?>">
                    <input type="text" name="pkg_target[]" placeholder="Dla kogo" value="<?= h((string) $p['target']) ?>">
                    <input type="text" name="pkg_tablet[]" placeholder="Urządzenia" value="<?= h((string) $p['tablet']) ?>">
                    <input type="text" name="pkg_price[]" placeholder="Cena" value="<?= h((string) $p['price']) ?>">
                    <textarea name="pkg_features[]" placeholder="Funkcje oddzielone |\"><?= h((string) $p['features']) ?></textarea>
                    <label><input type="checkbox" name="pkg_featured[<?= $i ?>]" <?= !empty($p['featured']) ? 'checked' : '' ?>> Wyróżniony</label>
                </div>
            <?php endfor; ?>
            <button class="btn" type="submit">Zapisz pakiety</button>
        </form>
    </section>

    <section class="section">
        <h2>Zrzuty aplikacji</h2>
        <form method="post" class="form">
            <input type="hidden" name="action" value="save_screenshots">
            <?php for ($i = 0; $i < 6; $i++): $s = $screenshots[$i] ?? ['title'=>'','description'=>'','image_url'=>'']; ?>
                <div class="card">
                    <input type="text" name="shot_title[]" placeholder="Tytuł" value="<?= h((string) $s['title']) ?>">
                    <input type="text" name="shot_url[]" placeholder="URL obrazka" value="<?= h((string) $s['image_url']) ?>">
                    <textarea name="shot_desc[]" placeholder="Opis"><?= h((string) $s['description']) ?></textarea>
                </div>
            <?php endfor; ?>
            <button class="btn" type="submit">Zapisz zrzuty</button>
        </form>
    </section>

    <section class="section">
        <h2>Usługi dodatkowe</h2>
        <form method="post" class="form">
            <input type="hidden" name="action" value="save_services">
            <?php for ($i = 0; $i < 8; $i++): ?>
                <input type="text" name="service_name[]" value="<?= h((string) ($services[$i] ?? '')) ?>" placeholder="Nazwa usługi">
            <?php endfor; ?>
            <button class="btn" type="submit">Zapisz usługi</button>
        </form>
    </section>
</main>
</body>
</html>
