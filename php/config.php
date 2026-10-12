<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    // Try SQLite first
    $sqlitePath = __DIR__ . '/../data.db';
    $sqliteDsn  = 'sqlite:' . $sqlitePath;
    try {
        $pdo = new PDO($sqliteDsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Fallback to MySQL
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
        return new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    }
}

/* ─── Création automatique de la table ─────────────────── */
function ensureTable(): void {
    $pdo = getDB();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        // SQLite table creation (no ENGINE clause)
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS inscriptions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT NOT NULL,
                prenom TEXT NOT NULL,
                email TEXT DEFAULT NULL,
                telephone TEXT DEFAULT NULL,
                ville TEXT DEFAULT NULL,
                eglise TEXT DEFAULT NULL,
                participation TEXT DEFAULT NULL,
                message TEXT DEFAULT NULL,
                groupe_whatsapp TEXT DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );"
        );

        // Add columns if table already existed without them
        try {
            $pdo->exec("ALTER TABLE inscriptions ADD COLUMN groupe_whatsapp TEXT DEFAULT NULL;");
        } catch (Throwable $e) {}
    } else {
        // MySQL table creation
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `inscriptions` (
                `id`              INT           AUTO_INCREMENT PRIMARY KEY,
                `nom`             VARCHAR(100)  NOT NULL,
                `prenom`          VARCHAR(100)  NOT NULL,
                `email`           VARCHAR(191)  DEFAULT NULL,
                `telephone`       VARCHAR(30)   DEFAULT NULL,
                `ville`           VARCHAR(100)  DEFAULT NULL,
                `eglise`          VARCHAR(200)  DEFAULT NULL,
                `participation`   VARCHAR(80)   DEFAULT NULL,
                `message`         TEXT          DEFAULT NULL,
                `groupe_whatsapp` TEXT          DEFAULT NULL,
                `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (`email`),
                INDEX idx_date  (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        try {
            $pdo->exec("ALTER TABLE `inscriptions` ADD COLUMN `groupe_whatsapp` TEXT DEFAULT NULL;");
        } catch (Throwable $e) {}
    }
}

// SQLite‑specific helper
function ensureSQLiteTable(PDO $pdo): void {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS inscriptions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT NOT NULL,
            prenom TEXT NOT NULL,
            email TEXT DEFAULT NULL,
            telephone TEXT DEFAULT NULL,
            ville TEXT DEFAULT NULL,
            eglise TEXT DEFAULT NULL,
            participation TEXT DEFAULT NULL,
            message TEXT DEFAULT NULL,
            groupe_whatsapp TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    );
}
?>
