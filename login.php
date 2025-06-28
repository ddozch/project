<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Nieprawidłowy e-mail lub hasło.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
</head>
<body>
    <h2>Zaloguj się</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Hasło:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Zaloguj się</button>
    </form>

    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</body>
</html>
