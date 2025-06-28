<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    if ($title === '') {
        $error = "Tytuł zestawu jest wymagany.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO sets (user_id, title, description, is_public) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $is_public]);

        header("Location: list.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Nowy zestaw</title>
</head>
<body>
    <h2>Dodaj nowy zestaw fiszek</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Tytuł zestawu:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Opis (opcjonalnie):</label><br>
        <textarea name="description" rows="4" cols="40"></textarea><br><br>

        <label><input type="checkbox" name="is_public" checked> Zestaw publiczny</label><br><br>

        <button type="submit">Utwórz zestaw</button>
    </form>

    <p><a href="list.php">↩ Powrót do listy zestawów</a></p>
</body>
</html>
