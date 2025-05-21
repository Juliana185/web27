<?php
$host = 'sql105.infinityfree.com';
$dbname = 'if0_38713824_user';
$username = 'if0_38713824';
$password = 'fluffy1789';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных.");
}
?>
