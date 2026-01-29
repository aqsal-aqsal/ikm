<?php
require 'public/includes/config.php';
try {
    echo "Tables in database:\n";
    $stmt = $pdo->query("SHOW TABLES");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
    
    echo "\nResponden columns:\n";
    $stmt = $pdo->query("DESCRIBE responden");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\nSurvey Jawaban columns:\n";
    $stmt = $pdo->query("DESCRIBE survey_jawaban");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo $e->getMessage();
}
?>