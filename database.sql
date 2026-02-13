CREATE DATABASE IF NOT EXISTS songdraft_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE songdraft_site;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(64) PRIMARY KEY,
    setting_value TEXT NOT NULL
);

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

CREATE TABLE IF NOT EXISTS features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS screenshots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    badge VARCHAR(120) NOT NULL,
    target VARCHAR(180) NOT NULL,
    tablet VARCHAR(255) NOT NULL,
    price VARCHAR(80) NOT NULL,
    features TEXT NOT NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0
);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name','SongDraft'),
('tagline','Inteligentne wyświetlanie tekstów i multimediów w kościele'),
('hero_subtitle','Aplikacja uruchamiana na mini komputerze lub samodzielnie, sterowana wygodnie z tableta.'),
('cta_phone','+48 000 000 000'),
('cta_email','kontakt@songdraft.pl'),
('features_title','Co potrafi SongDraft?'),
('pricing_title','Pakiety i ceny'),
('screenshots_title','Zrzuty aplikacji'),
('services_title','Usługi dodatkowe'),
('contact_title','Skontaktuj się z nami')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
