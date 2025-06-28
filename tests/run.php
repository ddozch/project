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

// Sprawdź, czy zestaw należy do użytkownika lub jest publiczny
$stmt = $pdo->prepare("SELECT * FROM sets WHERE id = ? AND (user_id = ? OR is_public = 1)");
$stmt->execute([$set_id, $user_id]);
$set = $stmt->fetch();

if (!$set) {
    echo "Zestaw niedostępny.";
    exit();
}

// Pobierz fiszki z zestawu
$stmt = $pdo->prepare("SELECT * FROM flashcards WHERE set_id = ? ORDER BY position ASC");
$stmt->execute([$set_id]);
$cards = $stmt->fetchAll();

if (count($cards) < 3) {
    echo "Zestaw musi zawierać co najmniej 3 fiszki do testu sekwencyjnego.";
    exit();
}

$show_sequence = !isset($_POST['answer']); // czy to faza "pokaż sekwencję"
$score = null;

// Obsługa odpowiedzi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $user_input = explode(',', trim($_POST['answer']));
    $correct_sequence = array_column($cards, 'front_text');
    $score = 0;

    foreach ($user_input as $index => $word) {
        if (isset($correct_sequence[$index]) && strtolower(trim($word)) === strtolower($correct_sequence[$index])) {
            $score++;
        }
    }

    // Możesz też tu zapisać wynik do bazy (np. tabela `results`)
    // Zapisz wynik testu do bazy
        $stmt = $pdo->prepare("INSERT INTO tests (user_id, set_id, test_type) VALUES (?, ?, 'sequence')");
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
    <title>Test sekwencji – <?= htmlspecialchars($set['title']) ?></title>
</head>
<body>
    <h2>🧠 Test zapamiętywania sekwencji – <?= htmlspecialchars($set['title']) ?></h2>

    <?php if ($show_sequence): ?>
        <p>Zapamiętaj kolejność tych słów (masz kilka sekund):</p>
        <ul>
            <?php foreach ($cards as $card): ?>
                <li><strong><?= htmlspecialchars($card['front_text']) ?></strong></li>
            <?php endforeach ?>
        </ul>

        <form method="post">
            <p>Wpisz zapamiętaną sekwencję, oddzielając słowa przecinkiem:</p>
            <input type="hidden" name="shown" value="1">
            <textarea name="answer" rows="3" cols="60" placeholder="np. kot, pies, dom" required></textarea><br><br>
            <button type="submit">Sprawdź odpowiedź</button>
        </form>

    <?php elseif ($score !== null): ?>
        <h3>✅ Twój wynik: <?= $score ?> / <?= count($cards) ?></h3>
        <a href="../dashboard.php">↩ Powrót do panelu</a>
    <?php endif ?>
</body>
</html>
