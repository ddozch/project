<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login(); // переконайся, що користувач залогінений

// Отримуємо ID картки
if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$flashcard_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Завантажуємо картку з БД
$stmt = $pdo->prepare("SELECT f.id, f.front_text, f.back_text, f.set_id, s.user_id 
                       FROM flashcards f 
                       JOIN sets s ON f.set_id = s.id 
                       WHERE f.id = ?");
$stmt->execute([$flashcard_id]);
$flashcard = $stmt->fetch();

if (!$flashcard || $flashcard['user_id'] != $user_id) {
    echo "Brak dostępu lub fiszka nie istnieje.";
    exit();
}

// Обробка форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $front = trim($_POST['front_text']);
    $back = trim($_POST['back_text']);

    if ($front === '' || $back === '') {
        $error = "Wszystkie pola są wymagane.";
    } else {
        $update = $pdo->prepare("UPDATE flashcards SET front_text = ?, back_text = ? WHERE id = ?");
        $update->execute([$front, $back, $flashcard_id]);
        header("Location: list.php?set_id=" . $flashcard['set_id']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj fiszkę</title>
</head>
<body>
    <h2>Edytuj fiszkę</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Przód fiszki:</label><br>
        <input type="text" name="front_text" value="<?= htmlspecialchars($flashcard['front_text']) ?>" required><br><br>

        <label>Tył fiszki:</label><br>
        <input type="text" name="back_text" value="<?= htmlspecialchars($flashcard['back_text']) ?>" required><br><br>

        <button type="submit">Zapisz zmiany</button>
    </form>

    <p><a href="list.php?set_id=<?= $flashcard['set_id'] ?>">↩ Powrót do zestawu</a></p>
</body>
</html>