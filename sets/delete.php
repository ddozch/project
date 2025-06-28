<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

if (!isset($_GET['id'])) {
    echo "Brak ID zestawu.";
    exit();
}

$set_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Sprawdzenie, czy zestaw należy do użytkownika
$stmt = $pdo->prepare("SELECT * FROM sets WHERE id = ? AND user_id = ?");
$stmt->execute([$set_id, $user_id]);
$set = $stmt->fetch();

if (!$set) {
    echo "Nie masz uprawnień do usunięcia tego zestawu.";
    exit();
}

// Usuwanie zestawu – wszystkie powiązane fiszki zostaną usunięte przez ON DELETE CASCADE
$stmt = $pdo->prepare("DELETE FROM sets WHERE id = ?");
$stmt->execute([$set_id]);

header("Location: list.php");
exit();
