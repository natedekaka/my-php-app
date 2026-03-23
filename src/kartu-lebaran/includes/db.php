<?php
/**
 * Database Connection using PDO
 * E-Card Lebaran 1447 H
 */

define('DB_HOST', getenv('DB_HOST') ?: 'ecard-lebaran-db');
define('DB_NAME', getenv('DB_NAME') ?: 'kartu_lebaran_db');
define('DB_USER', getenv('DB_USER') ?: 'ecard_user');
define('DB_PASS', getenv('DB_PASS') ?: 'ecard_pass_2024');

function getDBConnection(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Koneksi database gagal. Silakan coba lagi nanti.');
        }
    }
    
    return $pdo;
}

function initializeDatabase(): bool {
    $pdo = getDBConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS kartu_ucapan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(50) NOT NULL UNIQUE,
        pengirim VARCHAR(100) NOT NULL,
        penerima VARCHAR(100) NOT NULL,
        pesan TEXT NOT NULL,
        template_path VARCHAR(255) NOT NULL DEFAULT 'templates/template1.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_slug (slug),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log('Table creation failed: ' . $e->getMessage());
        return false;
    }
}
