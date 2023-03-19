
-- Tabelle für hochgeladene Dateien erstellen
CREATE TABLE IF NOT EXISTS files (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL,
    upload_date DATETIME NOT NULL
);

-- Tabelle für Admin-Benutzer erstellen
CREATE TABLE IF NOT EXISTS admin_users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Admin-Benutzer hinzufügen
INSERT INTO admin_users (username, password) VALUES ('admin', '$2y$10$GOFN1gL2Ts10nBcsURZP0OvOhWXwb4hgocroz0nCKGy2pBWLZZAl6');
