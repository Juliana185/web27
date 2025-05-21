<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: 5_user_login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>редактирование данных</h1>
    <form method="post" action="submit.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>фио <input name="name" value="<?= htmlspecialchars($user['name']) ?>"></label><br>
        <label>телефон <input name="phone" value="<?= htmlspecialchars($user['phone']) ?>"></label><br>
        <label>почта <input name="email" value="<?= htmlspecialchars($user['email']) ?>"></label><br>
        <label>дата рождения <input type="date" name="birthdate" value="<?= htmlspecialchars($user['birthdate']) ?>"></label><br>
        <button type="submit" name="action" value="save">СОХРАНИТЬ</button>
    </form>
</body>
</html>
