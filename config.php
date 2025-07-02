<?php
session_start();

$host = "localhost";
$username = "test_lib_sm_usr";
$password = "1Aa11111";
$dbname = "test_lib_sm";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$admin_login = 'admin';
$admin_password = 'password123';

function checkAuth() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: index.php');
        exit();
    }
}

function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>