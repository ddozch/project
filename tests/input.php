<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

if (!isset($_GET['set_id'])) {
    echo "Brak ID zestawu.";
    exit();
}

$set_id = (int)$_GET['set_id'];
$user_id = $_SESSION['user_id'];

// sprawdź dostęp do zestawu
$stmt = $pdo->prepare("SELECT * FROM sets WHERE id = ? AND (user_id = ? OR is_public = 1)");
$stmt->execute([$set_id, $user_id]);
$set = $stmt->fetch();

if (!$set) {
    echo "Brak dostępu do zestawu.";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM flashcards WHERE set_id = ? ORDER BY position ASC");
$stmt->execute([$set_id]);
$cards = $stmt->fetchAll();

if (count($cards) < 1) {
    echo "Zestaw nie zawiera fiszek.";
    exit();
}

$score = null;
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answers'] ?? [];

    foreach ($cards as $index => $card) {
        $user_answer = strtolower(trim($answers[$card['id']] ?? ''));
        $correct = strtolower(trim($card['back_text']));
        $is_correct = $user_answer === $correct;
        $results[] = [
            'front' => $card['front_text'],
            'correct' => $card['back_text'],
            'user' => $user_answer,
            'ok' => $is_correct
        ];
    }

    $score = count(array_filter($results, fn($r) => $r['ok']));

    // Zapisz wynik testu
    $stmt = $pdo->prepare("INSERT INTO tests (user_id, set_id, test_type) VALUES (?, ?, 'input')");
    $stmt->execute([$user_id, $set_id]);
    $test_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO results (test_id, score, total) VALUES (?, ?, ?)");
    $stmt->execute([$test_id, $score, count($cards)]);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Test: wpisywanie tłumaczeń</title>
</head>
<body>
    <h2>📝 Test tłumaczeń – <?= htmlspecialchars($set['title']) ?></h2>

    <?php if ($score === null): ?>
        <form method="post">
            <?php foreach ($cards as $card): ?>
                <label><?= htmlspecialchars($card['front_text']) ?>:</label><br>
                <input type="text" name="answers[<?= $card['id'] ?>]" required><br><br>
            <?php endforeach ?>
            <button type="submit">Sprawdź odpowiedzi</button>
        </form>
    <?php else: ?>
        <h3>Twój wynik: <?= $score ?> / <?= count($cards) ?></h3>
        <table border="1" cellpadding="6">
            <tr>
                <th>Słowo</th>
                <th>Twoja odpowiedź</th>
                <th>Poprawna</th>
                <th>✔</th>
            </tr>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['front']) ?></td>
                    <td><?= htmlspecialchars($r['user']) ?></td>
                    <td><?= htmlspecialchars($r['correct']) ?></td>
                    <td><?= $r['ok'] ? '✅' : '❌' ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <br>
        <a href="results.php">📊 Zobacz historię testów</a><br>
        <a href="../dashboard.php">↩ Powrót do panelu</a>
    <?php endif ?>
</body>
</html>
