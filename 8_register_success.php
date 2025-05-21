<?php
session_start();
$login = $_SESSION['generated_login'] ?? '';
$password = $_SESSION['generated_password'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Успешная регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>вы успешно зарегистрированы!</h1>
    <p>ваш логин: <b><?= htmlspecialchars($login) ?></b></p>
    <p>ваш пароль: <b><?= htmlspecialchars($password) ?></b></p>
    <p>сохраните эти данные!</p>
</body>
</html>
