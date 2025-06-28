<?php
// includes/db.php

$host = 'localhost';
$db   = 'flashcard_app';
$user = 'root';
$pass = ''; // у XAMPP — без пароля
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
    exit();
}
