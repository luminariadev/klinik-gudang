<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents(__DIR__ . '/database/schema_v2.sql');
    
    // Create DB first if not exists because it's part of the script, but PDO needs to execute multiple statements.
    // PDO can execute multiple statements if emulated prepares are on.
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
    
    $pdo->exec($sql);
    echo "Schema migrated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
