<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'klinik_gudang_');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL untuk redirect (sesuaikan dengan nama folder di htdocs/www)
define('BASE_URL', '/stock_gudang');

function getDbConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Dalam mode production, jangan tampilkan pesan error asli ke layar
            die("Koneksi database gagal. Silakan coba beberapa saat lagi.");
        }
    }
    return $pdo;
}
