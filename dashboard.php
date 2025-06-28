<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

require_login();
$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name']; // zakładamy, że to było ustawione przy logowaniu

// Pobieramy zestawy użytkownika
$stmt = $pdo->prepare("SELECT * FROM sets WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$sets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel użytkownika</title>
</head>
<body>
    <h2>Witaj, <?= htmlspecialchars($first_name) ?>!</h2>

    <p>Tu możesz zarządzać swoimi zestawami fiszek, tworzyć testy i śledzić postępy.</p>

    <h3>📦 Twoje zestawy (ostatnie 5)</h3>

    <a href="sets/add.php">➕ Stwórz nowy zestaw</a><br>
    <a href="sets/list.php">📋 Zobacz wszystkie zestawy</a><br><br>

    <?php if (count($sets) === 0): ?>
        <p>Nie masz jeszcze żadnych zestawów.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($sets as $set): ?>
                <li>
                    <strong><?= htmlspecialchars($set['title']) ?></strong> <br>
                    <?= nl2br(htmlspecialchars($set['description'])) ?><br>
                    <a href="flashcards/list.php?set_id=<?= $set['id'] ?>">➡ Zobacz fiszki</a>
                </li>
                <br>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <br><br>
    <a href="logout.php">🚪 Wyloguj się</a>
</body>
</html>
