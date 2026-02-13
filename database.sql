CREATE DATABASE IF NOT EXISTS songdraft_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE songdraft_site;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(64) PRIMARY KEY,
    setting_value TEXT NOT NULL
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'SongDraft'),
('tagline', 'Nowoczesne wyświetlanie tekstów i sterowanie multimediami w kościele'),
('cta_phone', '+48 000 000 000'),
('cta_email', 'kontakt@songdraft.pl'),
('hero_subtitle', 'Aplikacja uruchamiana na mini komputerze, wygodnie sterowana z tableta.')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

CREATE TABLE IF NOT EXISTS visit_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(255) NOT NULL,
    ip_address VARCHAR(64) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    visited_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL
);
