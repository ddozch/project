<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login(); // доступ тільки для залогінених

if (!isset($_GET['set_id'])) {
    echo "Brak ID zestawu.";
    exit();
}

$set_id = (int)$_GET['set_id'];
$user_id = $_SESSION['user_id'];

// Перевірка, чи набір належить користувачеві
$stmt = $pdo->prepare("SELECT * FROM sets WHERE id = ? AND user_id = ?");
$stmt->execute([$set_id, $user_id]);
$set = $stmt->fetch();

if (!$set) {
    echo "Zestaw nie istnieje lub nie masz do niego dostępu.";
    exit();
}

// Завантаження fiszek
$stmt = $pdo->prepare("SELECT * FROM flashcards WHERE set_id = ? ORDER BY position ASC");
$stmt->execute([$set_id]);
$cards = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Fiszki - <?= htmlspecialchars($set['title']) ?></title>
</head>
<body>
    <h2>Fiszki w zestawie: <?= htmlspecialchars($set['title']) ?></h2>

    <a href="add.php?set_id=<?= $set_id ?>">➕ Dodaj nową fiszkę</a>
    <br><br>

    <?php if (count($cards) === 0): ?>
        <p>Brak fiszek w tym zestawie.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Przód</th>
                    <th>Tył</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cards as $index => $card): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($card['front_text']) ?></td>
                        <td><?= htmlspecialchars($card['back_text']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $card['id'] ?>">✏ Edytuj</a> |
                            <a href="delete.php?id=<?= $card['id'] ?>&set_id=<?= $set_id ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę fiszkę?');">🗑 Usuń</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

    <br>
    <a href="../dashboard.php">↩ Powrót do panelu użytkownika</a>
</body>
</html>
