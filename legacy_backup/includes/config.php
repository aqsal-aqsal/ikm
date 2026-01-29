<?php
session_start();

$host = 'localhost';
$db   = 'ikm';
$user = 'root';
$pass = ''; // Default Laragon password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to check login
function requireAuth() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

// Helper function to check role
function requireRole($allowed_roles) {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
    if (!in_array($_SESSION['user']['role'], $allowed_roles)) {
        header('Location: dashboard.php'); // Or 403 page
        exit;
    }
}
?>