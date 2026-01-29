<?php
require 'public/includes/config.php';
try {
    $stmt = $pdo->query("DESCRIBE survey");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    $stmt = $pdo->query("DESCRIBE survey_detail"); // Check if this exists too
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo $e->getMessage();
}
?>