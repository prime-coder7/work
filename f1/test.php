<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=testdb', 'root', 'password');
    echo "PDO connection successful!";
} catch (PDOException $e) {
    echo "PDO error: " . $e->getMessage();
}
?>
