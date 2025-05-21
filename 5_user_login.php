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
    <title>Вход пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>вход в качестве пользователя</h1>
    <form method="post" action="index.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>логин <input type="text" name="login" required></label><br>
        <label>пароль <input type="password" name="password" required></label><br>
        <button type="submit">ВОЙТИ</button>
    </form>
</body>
</html>
