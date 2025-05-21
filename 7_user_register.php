<?php
session_start();
require_once 'db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>регистрация пользователя</h1>
    <form method="post" action="submit.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>фио <input name="name" required></label><br>
        <label>телефон <input name="phone" required></label><br>
        <label>почта <input name="email" required></label><br>
        <label>дата рождения <input type="date" name="birthdate" required></label><br>
        <button type="submit" name="action" value="register">регистрация</button>
    </form>
</body>
</html>
