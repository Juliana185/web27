<?php
session_start();
require_once 'db.php';
error_reporting(0);
ini_set('display_errors', 0);

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$formData = json_decode($_COOKIE['form_data'] ?? '{}', true);
$userData = [];

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT name, phone, email, birthdate FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Выбор статуса</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (!isset($_GET['role']) && !isset($_SESSION['user_id'])): ?>
    <h1>Выберите статус</h1>
    <form action="2_admin_login.php" method="get" style="display:inline-block; margin: 20px;">
        <button type="submit">я админ</button>
    </form>
    <form action="index.php" method="get" style="display:inline-block; margin: 20px;">
        <input type="hidden" name="role" value="user">
        <button type="submit">я пользователь</button>
    </form>
<?php endif; ?>

<?php if (isset($_SESSION['show_credentials'])): ?>
    <div class="credentials-box">
        <h2>Данные для входа</h2>
        <p>Логин: <?= htmlspecialchars($_SESSION['temp_login'] ?? '') ?></p>
        <p>Пароль: <?= htmlspecialchars($_SESSION['temp_password'] ?? '') ?></p>
        <p class="warning">* Сохраните пароль!</p>
        <a href="index.php" class="login-button">На главную</a>
    </div>
    <?php
    unset($_SESSION['show_credentials'], $_SESSION['temp_login'], $_SESSION['temp_password']);
    exit();
    ?>
<?php endif; ?>

<?php if (isset($_GET['role']) && !isset($_SESSION['user_id']) && !isset($_GET['register'])): ?>
    <form method="POST" action="submit.php?action=login">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <h2>Вход</h2>
        <input type="text" name="login" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
        <a href="index.php?role=user&register=1">Регистрация</a>
    </form>
<?php endif; ?>

<?php if (isset($_SESSION['login_error'])): ?>
    <div class="error-message">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
        <?php unset($_SESSION['login_error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['register']) && isset($_GET['role']) && !isset($_SESSION['user_id'])): ?>
    <form method="POST" action="submit.php?action=register">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <h2>Регистрация</h2>

        <label>ФИО: <input type="text" name="name" value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required></label><br>
        <label>Телефон: <input type="tel" name="phone" pattern="\+7\d{10}" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required></label><br>
        <label>Дата рождения: <input type="date" name="birthdate" max="<?= date('Y-m-d', strtotime('-10 years')) ?>" value="<?= htmlspecialchars($formData['birthdate'] ?? '') ?>" required></label><br>

        <button type="submit">Зарегистрироваться</button>
        <a href="submit.php?action=clear_form">Очистить</a>
    </form>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php if (isset($_SESSION['update_success'])): ?>
        <div class="success"><?= htmlspecialchars($_SESSION['update_success']) ?></div>
        <?php unset($_SESSION['update_success']); ?>
    <?php endif; ?>

    <form method="POST" action="submit.php?action=update">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <h2>Редактирование</h2>

        <label>ФИО: <input type="text" name="name" value="<?= htmlspecialchars($userData['name'] ?? '') ?>" required></label><br>
        <label>Телефон: <input type="tel" name="phone" pattern="\+7\d{10}" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" required></label><br>
        <label>Дата рождения: <input type="date" name="birthdate" max="<?= date('Y-m-d', strtotime('-10 years')) ?>" value="<?= htmlspecialchars($userData['birthdate'] ?? '') ?>" required></label><br>

        <button type="submit">Сохранить</button>
        <a href="submit.php?action=logout">Выйти</a>
    </form>
<?php endif; ?>

<?php unset($_SESSION['errors'], $_SESSION['update_errors'], $_SESSION['form_data']); ?>
</body>
</html>
