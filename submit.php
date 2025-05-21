<?php
session_start();
require_once 'db.php';
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Неверный CSRF токен');
    }
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT user_id, password_hash FROM users_auth WHERE login = ?");
        $stmt->execute([$login]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['user_id'];
            header('Location: index.php');
            exit();
        }

        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: index.php');
        exit();

    case 'register':
        $errors = [];
        $formData = $_POST;

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $errors['name'] = 'ФИО обязательно';
        } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name) || strlen($name) > 150) {
            $errors['name'] = 'Только английские буквы и пробелы (макс. 150 символов)';
        }

        $phone = trim($_POST['phone'] ?? '');
        if (!preg_match('/^\+7\d{10}$/', $phone)) {
            $errors['phone'] = 'Формат: +7XXXXXXXXXX';
        }

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        }

        $birthdate = $_POST['birthdate'] ?? '';
        $maxDate = date('Y-m-d', strtotime('-10 years'));
        if (empty($birthdate) || $birthdate > $maxDate) {
            $errors['birthdate'] = 'Минимальный возраст: 10 лет';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            setcookie('form_data', json_encode($formData), time() + 3600, '/');
            header('Location: index.php?register=1');
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, birthdate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $birthdate]);
        $userId = $pdo->lastInsertId();

        $login = 'user_' . uniqid();
        $rawPassword = bin2hex(random_bytes(8));
        $passwordHash = password_hash($rawPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users_auth (user_id, login, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $login, $passwordHash]);

        $_SESSION['temp_login'] = $login;
        $_SESSION['temp_password'] = $rawPassword;
        $_SESSION['show_credentials'] = true;
        header('Location: index.php');
        exit();

    case 'update':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }

        $errors = [];

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $errors['name'] = 'ФИО обязательно';
        }

        $phone = trim($_POST['phone'] ?? '');
        if (!preg_match('/^\+7\d{10}$/', $phone)) {
            $errors['phone'] = 'Формат: +7XXXXXXXXXX';
        }

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        }

        $birthdate = $_POST['birthdate'] ?? '';
        $maxDate = date('Y-m-d', strtotime('-10 years'));
        if (empty($birthdate) || $birthdate > $maxDate) {
            $errors['birthdate'] = 'Минимальный возраст: 10 лет';
        }

        if (!empty($errors)) {
            $_SESSION['update_errors'] = $errors;
            header('Location: index.php');
            exit();
        }

        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, email = ?, birthdate = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $birthdate, $_SESSION['user_id']]);

        $_SESSION['update_success'] = 'Данные обновлены';
        header('Location: index.php');
        exit();

    case 'logout':
        session_destroy();
        header('Location: index.php');
        exit();

    case 'clear_form':
        setcookie('form_data', '', time() - 3600, '/');
        header('Location: index.php?register=1');
        exit();
}
?>
