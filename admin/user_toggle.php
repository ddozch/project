<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

if (!is_admin()) {
    echo "Brak dostępu. Tylko administrator.";
    exit();
}

if (!isset($_GET['id'], $_GET['action'])) {
    echo "Brak danych wejściowych.";
    exit();
}

$user_id = (int)$_GET['id'];
$action = $_GET['action'];

// Zabezpieczenie: nie można dezaktywować administratorów
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || $user['role'] === 'admin') {
    echo "Nie można zmienić statusu tego konta.";
    exit();
}

if ($action === 'deactivate') {
    $stmt = $pdo->prepare("UPDATE users SET active = 0 WHERE id = ?");
    $stmt->execute([$user_id]);
} elseif ($action === 'activate') {
    $stmt = $pdo->prepare("UPDATE users SET active = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
} else {
    echo "Nieznana akcja.";
    exit();
}

header("Location: users.php");
exit();
