<?php
/* ============================================================
   config.php — Database Configuration
   Croisade Que Ton Règne Vienne
   ============================================================
   IMPORTANT : Modifiez ces paramètres selon votre serveur.
   ============================================================ */

define('DB_HOST',     'localhost');
define('DB_USER',     'root');        // Votre utilisateur MySQL
define('DB_PASSWORD', '');            // Votre mot de passe MySQL
define('DB_NAME',     'croisade_db');
define('DB_CHARSET',  'utf8mb4');




/* ─── Connexion PDO ─────────────────────────────────────── */
function getDB(): PDO {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    } catch (PDOException $e) {
        // Ne jamais exposer les détails en production
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de connexion à la base de données.'
        ]);
        exit;
    }
}

/* ─── Création automatique de la table ─────────────────── */
function ensureTable(): void {
    $pdo = getDB();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `inscriptions` (
            `id`            INT           AUTO_INCREMENT PRIMARY KEY,
            `nom`           VARCHAR(100)  NOT NULL,
            `prenom`        VARCHAR(100)  NOT NULL,
            `email`         VARCHAR(191)  NOT NULL,
            `telephone`     VARCHAR(30)   DEFAULT NULL,
            `ville`         VARCHAR(100)  DEFAULT NULL,
            `eglise`        VARCHAR(200)  DEFAULT NULL,
            `participation` VARCHAR(80)   DEFAULT NULL,
            `message`       TEXT          DEFAULT NULL,
            `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (`email`),
            INDEX idx_date  (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}
