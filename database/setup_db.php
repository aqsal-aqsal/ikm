<?php
require_once 'config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/database.sql');
    if (!$sql) {
        die("Error: File database.sql tidak ditemukan.");
    }
    
    $pdo->exec($sql);
    echo "Database berhasil di-setup! Tabel dan dummy data telah dibuat.";
} catch (PDOException $e) {
    echo "Error setup database: " . $e->getMessage();
}
