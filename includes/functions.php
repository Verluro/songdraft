<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function getSiteSettings(): array
{
    $settings = SITE_DEFAULTS;

    try {
        $stmt = db()->query('SELECT setting_key, setting_value FROM site_settings');
        foreach ($stmt as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Throwable $e) {
    }

    return $settings;
}

function getFeatures(): array
{
    $defaults = [
        ['title' => 'Baza pieśni i zestawy', 'description' => 'Śpiewnik siedlecki, własne pieśni i playlisty na każdy dzień.'],
        ['title' => 'Tryb prezentacji', 'description' => 'Pełny ekran i wygodne przechodzenie między zwrotkami i pieśniami.'],
        ['title' => 'Multimedia i kamery', 'description' => 'Zdjęcia, nagrania oraz podgląd z kamery na ołtarz i innych kamer.'],
        ['title' => 'Sterowanie smart', 'description' => 'Włączanie/wyłączanie ekranów i innych urządzeń smart.'],
    ];

    try {
        $rows = db()->query('SELECT title, description FROM features ORDER BY sort_order ASC, id ASC')->fetchAll();
        return $rows ?: $defaults;
    } catch (Throwable $e) {
        return $defaults;
    }
}

function getServices(): array
{
    $defaults = [
        'Wybór, montaż i konfiguracja ekranów / telewizorów w kościele',
        'Montaż i konfiguracja kamery na ołtarz',
        'Montaż i konfiguracja większej ilości kamer',
    ];

    try {
        $rows = db()->query('SELECT service_name FROM services ORDER BY sort_order ASC, id ASC')->fetchAll();
        if (!$rows) {
            return $defaults;
        }

        return array_map(static fn(array $row) => $row['service_name'], $rows);
    } catch (Throwable $e) {
        return $defaults;
    }
}

function getPackages(): array
{
    $defaults = [
        [
            'name' => 'Mini',
            'badge' => 'Sama aplikacja',
            'target' => 'Dla parafii z własnym sprzętem',
            'tablet' => 'Brak urządzeń w pakiecie',
            'price' => '1499 zł',
            'features' => 'Licencja na aplikację|Baza pieśni i zestawy|Tryb prezentacji|Personalizacja czcionki i tła',
            'featured' => 0,
        ],
        [
            'name' => 'Start',
            'badge' => 'Podstawowy zestaw',
            'target' => 'Dla mniejszych parafii',
            'tablet' => 'Tablet 8” + mini komputer + router',
            'price' => '3499 zł',
            'features' => 'Sprzęt + licencja|Baza pieśni|Dodawanie własnych pieśni|Tworzenie zestawów',
            'featured' => 0,
        ],
        [
            'name' => 'Parafia',
            'badge' => 'Najczęściej wybierany',
            'target' => 'Dla standardowych wdrożeń',
            'tablet' => 'Tablet 11” + mini komputer + router',
            'price' => '4999 zł',
            'features' => 'Wszystko ze Start|Multimedia|Kalendarz liturgiczny|Różaniec i ukrywanie zwrotek|Podgląd kamery',
            'featured' => 1,
        ],
        [
            'name' => 'Pro+',
            'badge' => 'Pełne możliwości',
            'target' => 'Dla rozbudowanych instalacji',
            'tablet' => 'Tablet 11” premium + mini komputer + router',
            'price' => '6999 zł',
            'features' => 'Wszystko z Parafia|Obsługa wielu kamer|Sterowanie smart urządzeniami|Priorytetowe wsparcie',
            'featured' => 0,
        ],
    ];

    try {
        $rows = db()->query('SELECT * FROM packages ORDER BY sort_order ASC, id ASC')->fetchAll();
        return $rows ?: $defaults;
    } catch (Throwable $e) {
        return $defaults;
    }
}

function getScreenshots(): array
{
    $defaults = [
        ['title' => 'Widok listy pieśni', 'description' => 'Miejsce na zrzut listy pieśni i wyszukiwarki.', 'image_url' => 'https://placehold.co/1200x700/0f172a/a5b4fc?text=Screenshot+1'],
        ['title' => 'Tryb prezentacji', 'description' => 'Miejsce na pełnoekranowy widok wyświetlania tekstu.', 'image_url' => 'https://placehold.co/1200x700/1e1b4b/c4b5fd?text=Screenshot+2'],
        ['title' => 'Panel sterowania', 'description' => 'Miejsce na zrzut sterowania kamerami i urządzeniami.', 'image_url' => 'https://placehold.co/1200x700/312e81/e9d5ff?text=Screenshot+3'],
    ];

    try {
        $rows = db()->query('SELECT title, description, image_url FROM screenshots ORDER BY sort_order ASC, id ASC')->fetchAll();
        return $rows ?: $defaults;
    } catch (Throwable $e) {
        return $defaults;
    }
}

function trackVisit(string $path): void
{
    try {
        $stmt = db()->prepare('INSERT INTO visit_stats (path, ip_address, user_agent, visited_at) VALUES (:path, :ip, :ua, NOW())');
        $stmt->execute([
            ':path' => $path,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255),
        ]);
    } catch (Throwable $e) {
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
