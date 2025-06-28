<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

if (!is_admin()) {
    echo "Brak dostępu.";
    exit();
}

if (!isset($_GET['id'])) {
    echo "Brak ID użytkownika.";
    exit();
}

$user_id = (int)$_GET['id'];

// Pobieramy dane użytkownika
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Użytkownik nie istnieje.";
    exit();
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($first_name === '' || $last_name === '' || $email === '') {
        $error = "Wszystkie pola oprócz hasła są wymagane.";
    } else {
        // aktualizacja danych podstawowych
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $email, $user_id]);

        // jeśli podano nowe hasło – aktualizujemy
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$password_hash, $user_id]);
        }

        header("Location: users.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj użytkownika</title>
</head>
<body>
    <h2>Edytuj dane użytkownika</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Imię:</label><br>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required><br><br>

        <label>Nazwisko:</label><br>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>Nowe hasło (opcjonalnie):</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Zapisz zmiany</button>
    </form>

    <p><a href="users.php">↩ Powrót do listy użytkowników</a></p>
</body>
</html>
