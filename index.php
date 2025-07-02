<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_POST) {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($login === "admin" && $password === "123") {
        $_SESSION['logged_in'] = true;
        $success = 'Авторизация успешна!';
    } else {
        $error = 'Неверный логин или пароль!';
    }
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Библиотека - Система управления</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .auth-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .navigation {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .nav-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .nav-card h3 {
            margin-bottom: 15px;
        }

        .nav-card p {
            color: #666;
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1aeb5;
        }

        .user-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media (max-width: 768px) {
            .navigation {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Система управления библиотекой</h1>
            <p>Веб-приложение для работы с базой данных книг</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= escape($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= escape($success) ?></div>
        <?php endif; ?>

        <?php if (!$isLoggedIn): ?>
            <div class="auth-form">
                <h2>Авторизация</h2>
                <p>Введите логин и пароль для доступа к системе</p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="login">Логин:</label>
                        <input type="text" id="login" name="login" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn">Войти</button>
                </form>
            </div>
        <?php else: ?>
            <div class="user-info">
                <div>
                    <h3>Добро пожаловать!</h3>
                    <p>Вы авторизованы в системе</p>
                </div>
                <a href="logout.php" class="btn btn-danger">Выйти</a>
            </div>

            <div class="navigation">
                <div class="nav-card">
                    <h3>Просмотр книг</h3>
                    <p>Просмотр всех книг в библиотеке</p>
                    <a href="view.php" class="btn">Открыть</a>
                </div>

                <div class="nav-card">
                    <h3>Добавить книгу</h3>
                    <p>Добавление новой книги в базу данных</p>
                    <a href="add.php" class="btn">Открыть</a>
                </div>

                <div class="nav-card">
                    <h3>Удалить книгу</h3>
                    <p>Удаление книги из базы данных</p>
                    <a href="delete.php" class="btn">Открыть</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>