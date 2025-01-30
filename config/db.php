<?php
// config/db.php
$host = "localhost";
$dbname = "youdemy";
$username = "root";
$password = "1234";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
}