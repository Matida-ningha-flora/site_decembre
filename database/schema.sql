CREATE DATABASE IF NOT EXISTS croisade_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE croisade_db;

CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(30) DEFAULT NULL,
    ville VARCHAR(100) DEFAULT NULL,
    eglise VARCHAR(200) DEFAULT NULL,
    participation VARCHAR(80) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_date (created_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Exemple de données de test
-- INSERT INTO inscriptions (nom, prenom, telephone, ville, eglise, participation, message)
-- VALUES ('Dupont', 'Jean', '+237600000000', 'Bafoussam', 'Église de la Lumière', 'musicien', 'Je souhaite participer.');
