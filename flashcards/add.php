<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login(); // тільки для залогінених

if (!isset($_GET['set_id'])) {
    echo "Brak zestawu docelowego.";
    exit();
}

$set_id = (int)$_GET['set_id'];
$user_id = $_SESSION['user_id'];

// Перевіряємо, чи набір належить користувачеві
$stmt = $pdo->prepare("SELECT * FROM sets WHERE id = ? AND user_id = ?");
$stmt->execute([$set_id, $user_id]);
$set = $stmt->fetch();

if (!$set) {
    echo "Nie masz dostępu do tego zestawu.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $front = trim($_POST['front_text']);
    $back = trim($_POST['back_text']);

    if ($front === '' || $back === '') {
        $error = "Wszystkie pola są wymagane.";
    } else {
        // Pozycjonowanie — można później dodać sortowanie
        $stmt = $pdo->prepare("SELECT MAX(position) FROM flashcards WHERE set_id = ?");
        $stmt->execute([$set_id]);
        $max = $stmt->fetchColumn();
        $position = $max ? $max + 1 : 1;

        $insert = $pdo->prepare("INSERT INTO flashcards (set_id, front_text, back_text, position) VALUES (?, ?, ?, ?)");
        $insert->execute([$set_id, $front, $back, $position]);

        header("Location: list.php?set_id=" . $set_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj fiszkę</title>
</head>
<body>
    <h2>Dodaj fiszkę do zestawu: <?= htmlspecialchars($set['title']) ?></h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Przód fiszki:</label><br>
        <input type="text" name="front_text" required><br><br>

        <label>Tył fiszki:</label><br>
        <input type="text" name="back_text" required><br><br>

        <button type="submit">Dodaj fiszkę</button>
    </form>

    <p><a href="list.php?set_id=<?= $set_id ?>">↩ Powrót do zestawu</a></p>
</body>
</html>
