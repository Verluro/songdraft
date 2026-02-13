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
        // Keep defaults if DB is not initialized yet.
    }

    return $settings;
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
        // Ignore stats errors to keep website available.
    }
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
