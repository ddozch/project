<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    echo "Brak ID zestawu.";
    exit();
}

$set_id = (int)$_GET['id'];

// Pobieramy zestaw
$stmt = $pdo->prepare("SELECT * FROM sets WHERE id = ? AND user_id = ?");
$stmt->execute([$set_id, $user_id]);
$set = $stmt->fetch();

if (!$set) {
    echo "Zestaw nie istnieje lub nie masz do niego dostępu.";
    exit();
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    if ($title === '') {
        $error = "Tytuł jest wymagany.";
    } else {
        $stmt = $pdo->prepare("UPDATE sets SET title = ?, description = ?, is_public = ? WHERE id = ?");
        $stmt->execute([$title, $description, $is_public, $set_id]);
        header("Location: list.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj zestaw</title>
</head>
<body>
    <h2>Edytuj zestaw fiszek</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Tytuł zestawu:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($set['title']) ?>" required><br><br>

        <label>Opis:</label><br>
        <textarea name="description" rows="4" cols="40"><?= htmlspecialchars($set['description']) ?></textarea><br><br>

        <label><input type="checkbox" name="is_public" <?= $set['is_public'] ? 'checked' : '' ?>> Zestaw publiczny</label><br><br>

        <button type="submit">Zapisz zmiany</button>
    </form>

    <p><a href="list.php">↩ Powrót do listy zestawów</a></p>
</body>
</html>
