<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: 2_admin_login.php");
    exit();
}
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>панель администратора</h1>
    <a href="1_index.php">выйти</a>
    <table>
        <tr>
            <th>ID</th><th>ФИО</th><th>дата рождения</th><th>почта</th><th>телефон</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['birthdate']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
