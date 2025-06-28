<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

if (!is_admin()) {
    echo "Brak dostępu. Tylko administrator.";
    exit();
}

// Pobierz użytkowników z bazy
$stmt = $pdo->prepare("SELECT id, first_name, last_name, email, role, active FROM users ORDER BY role DESC, last_name ASC");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Użytkownicy - Panel admina</title>
</head>
<body>
    <h2>👑 Panel administratora – Użytkownicy</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię i nazwisko</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['first_name']) ?> <?= htmlspecialchars($u['last_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['active'] ? '✅ aktywne' : '⛔ nieaktywne' ?></td>
                    <td>
                        <a href="user_edit.php?id=<?= $u['id'] ?>">✏ Edytuj</a>
                        <?php if ($u['role'] !== 'admin'): ?>
                            <?php if ($u['active']): ?>
                                | <a href="user_toggle.php?id=<?= $u['id'] ?>&action=deactivate" onclick="return confirm('Dezaktywować użytkownika?')">🛑 Dezaktywuj</a>
                            <?php else: ?>
                                | <a href="user_toggle.php?id=<?= $u['id'] ?>&action=activate">✅ Aktywuj</a>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <br>
    <a href="../dashboard.php">↩ Powrót do panelu</a>
</body>
</html>
