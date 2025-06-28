<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

if (!isset($_GET['id']) || !isset($_GET['set_id'])) {
    echo "Brak wymaganych parametrów.";
    exit();
}

$flashcard_id = (int)$_GET['id'];
$set_id = (int)$_GET['set_id'];
$user_id = $_SESSION['user_id'];

// Sprawdzenie, czy użytkownik ma prawo do tej fiszki
$stmt = $pdo->prepare("SELECT f.id, s.user_id 
                       FROM flashcards f 
                       JOIN sets s ON f.set_id = s.id 
                       WHERE f.id = ? AND f.set_id = ?");
$stmt->execute([$flashcard_id, $set_id]);
$flashcard = $stmt->fetch();

if (!$flashcard || $flashcard['user_id'] != $user_id) {
    echo "Brak dostępu lub fiszka nie istnieje.";
    exit();
}

// Usuwanie fiszki
$stmt = $pdo->prepare("DELETE FROM flashcards WHERE id = ?");
$stmt->execute([$flashcard_id]);

// Przekierowanie z powrotem do listy
header("Location: list.php?set_id=$set_id");
exit();
