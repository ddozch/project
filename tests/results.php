<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();
$user_id = $_SESSION['user_id'];

// Pobieramy testy użytkownika
$stmt = $pdo->prepare("
    SELECT r.id, r.score, r.total, r.completed_at, s.title AS set_title, t.test_type
    FROM results r
    JOIN tests t ON r.test_id = t.id
    JOIN sets s ON t.set_id = s.id
    WHERE t.user_id = ?
    ORDER BY r.completed_at DESC
");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Historia testów</title>
</head>
<body>
    <h2>📊 Twoje wyniki testów</h2>

    <?php if (count($results) === 0): ?>
        <p>Nie masz jeszcze żadnych wyników.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>Zestaw</th>
                    <th>Typ testu</th>
                    <th>Wynik</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $res): ?>
                    <tr>
                        <td><?= htmlspecialchars($res['set_title']) ?></td>
                        <td><?= $res['test_type'] ?></td>
                        <td><?= $res['score'] ?> / <?= $res['total'] ?></td>
                        <td><?= $res['completed_at'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

    <br>
    <a href="../dashboard.php">↩ Powrót do panelu</a>
</body>
</html>
