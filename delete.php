<?php
require_once 'config.php';
checkAuth();

$success = '';
$error = '';
$book_title = '';

if ($_POST) {
    $book_id = trim($_POST['book_id'] ?? '');
    
    if (empty($book_id) || !is_numeric($book_id)) {
        $error = "ID должен быть числом!";
    } else {
        try {
            $check_sql = "SELECT title FROM books WHERE id = :id";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([':id' => intval($book_id)]);
            $book = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$book) {
                $error = "Книга с ID $book_id не существует!";
            } else {
                $delete_sql = "DELETE FROM books WHERE id = :id";
                $delete_stmt = $pdo->prepare($delete_sql);
                $delete_stmt->execute([':id' => intval($book_id)]);
                
                $success = "Книга \"" . $book['title'] . "\" удалена!";
            }
        } catch(PDOException $e) {
            $error = "Ошибка при удалении: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить книгу</title>
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
            max-width: 600px;
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

        .navigation {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #007bff;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .navigation {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Удаление книги</h1>
        </div>

        <div class="navigation">
            <a href="index.php" class="btn">Главная</a>
            <a href="view.php" class="btn">Просмотр книг</a>
            <a href="add.php" class="btn">Добавить книгу</a>
            <a href="logout.php" class="btn">Выйти</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= escape($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= escape($error) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h3>Удаление по ID</h3>
            <p>Введите ID книги для удаления</p>

            <form method="POST" action="" onsubmit="return confirm('Удалить книгу?')">
                <div class="form-group">
                    <label for="book_id">ID книги:</label>
                    <input type="number" id="book_id" name="book_id" min="1" required placeholder="Введите ID">
                </div>

                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>
</body>
</html>