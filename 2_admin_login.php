<?php
session_start();
require_once 'db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token invalid");
    }

    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: 3_admin.php');
        exit();
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход администратора</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>вход администратора</h1>

    <div class="admin-hint">
        логин: <strong>admin</strong><br>
        пароль: <strong>12345678</strong>
    </div>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>логин <input type="text" name="login" required></label><br>
        <label>пароль <input type="password" name="password" required></label><br>
        <button type="submit">ВОЙТИ</button>
    </form>
</body>
</html>
