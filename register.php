<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        $error = "Wszystkie pola są wymagane.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Nieprawidłowy format adresu e-mail.";
    } elseif ($password !== $password2) {
        $error = "Hasła nie są zgodne.";
    } else {
        // sprawdzamy, czy użytkownik już istnieje
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Użytkownik z tym adresem e-mail już istnieje.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role, active) VALUES (?, ?, ?, ?, 'user', 1)");
            $stmt->execute([$first_name, $last_name, $email, $password_hash]);

            // automatyczne logowanie po rejestracji
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = 'user';

            header("Location: dashboard.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
</head>
<body>
    <h2>Zarejestruj się</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Imię:</label><br>
        <input type="text" name="first_name" required><br><br>

        <label>Nazwisko:</label><br>
        <input type="text" name="last_name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Hasło:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Powtórz hasło:</label><br>
        <input type="password" name="password2" required><br><br>

        <button type="submit">Zarejestruj się</button>
    </form>

    <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
</body>
</html>
