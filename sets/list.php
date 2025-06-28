<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();
$user_id = $_SESSION['user_id'];

// Отримуємо zestawy цього користувача
$stmt = $pdo->prepare("SELECT * FROM sets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$sets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje zestawy</title>
</head>
<body>
    <h2>Twoje zestawy fiszek</h2>

    <a href="add.php">➕ Dodaj nowy zestaw</a>
    <br><br>

    <?php if (count($sets) === 0): ?>
        <p>Nie masz jeszcze żadnych zestawów.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($sets as $set): ?>
                <li>
                    <strong><?= htmlspecialchars($set['title']) ?></strong>
                    <br>
                    <?= nl2br(htmlspecialchars($set['description'])) ?>
                    <br>
                    <a href="../flashcards/list.php?set_id=<?= $set['id'] ?>">📋 Zobacz fiszki</a> |
                    <a href="edit.php?id=<?= $set['id'] ?>">✏ Edytuj</a> |
                    <a href="delete.php?id=<?= $set['id'] ?>" onclick="return confirm('Czy na pewno usunąć ten zestaw?')">🗑 Usuń</a>
                    <br><br>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <br>
    <a href="../dashboard.php">↩ Powrót do panelu</a>
</body>
</html>
