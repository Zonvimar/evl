<?php
require_once 'config.php';
checkAuth();

$success = '';
$error = '';

if ($_POST) {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $year_published = trim($_POST['year_published'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $page_count = trim($_POST['page_count'] ?? '');
    $publisher = trim($_POST['publisher'] ?? '');
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Название книги обязательно";
    }
    
    if (empty($author)) {
        $errors[] = "Автор обязателен";
    }
    
    if (empty($price) || !is_numeric($price) || floatval($price) < 0) {
        $errors[] = "Цена должна быть положительным числом";
    }
    
    if (empty($year_published) || !is_numeric($year_published) || intval($year_published) < 1000 || intval($year_published) > date('Y')) {
        $errors[] = "Год издания должен быть от 1000 до " . date('Y');
    }
    
    if (empty($genre)) {
        $errors[] = "Жанр обязателен";
    }
    
    if (empty($page_count) || !is_numeric($page_count) || intval($page_count) < 1) {
        $errors[] = "Количество страниц должно быть положительным числом";
    }
    
    if (empty($publisher)) {
        $errors[] = "Издательство обязательно";
    }
    
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO books (title, author, price, year_published, genre, page_count, publisher) 
                    VALUES (:title, :author, :price, :year_published, :genre, :page_count, :publisher)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':price' => floatval($price),
                ':year_published' => intval($year_published),
                ':genre' => $genre,
                ':page_count' => intval($page_count),
                ':publisher' => $publisher
            ]);
            
            $success = "Книга успешно добавлена! ID: " . $pdo->lastInsertId();
            
            $title = $author = $price = $year_published = $genre = $page_count = $publisher = '';
            
        } catch(PDOException $e) {
            $error = "Ошибка при добавлении книги: " . $e->getMessage();
        }
    } else {
        $error = "Исправьте ошибки:\n" . implode("\n", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить книгу</title>
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

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .required {
            color: #dc3545;
        }

        input[type="text"], 
        input[type="number"], 
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input:focus, select:focus {
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
            white-space: pre-line;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .navigation {
                flex-direction: column;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Добавить новую книгу</h1>
        </div>

        <div class="navigation">
            <a href="index.php" class="btn">Главная</a>
            <a href="view.php" class="btn">Просмотр книг</a>
            <a href="delete.php" class="btn">Удалить книгу</a>
            <a href="logout.php" class="btn">Выйти</a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= escape($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= escape($error) ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Название книги <span class="required">*</span></label>
                        <input type="text" id="title" name="title" value="<?= escape($title ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="author">Автор <span class="required">*</span></label>
                        <input type="text" id="author" name="author" value="<?= escape($author ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Цена (₽) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" value="<?= escape($price ?? '') ?>" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="year_published">Год издания <span class="required">*</span></label>
                        <input type="number" id="year_published" name="year_published" value="<?= escape($year_published ?? '') ?>" min="1000" max="<?= date('Y') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="genre">Жанр <span class="required">*</span></label>
                        <select id="genre" name="genre" required>
                            <option value="">Выберите жанр</option>
                            <option value="Роман" <?= (isset($genre) && $genre === 'Роман') ? 'selected' : '' ?>>Роман</option>
                            <option value="Фантастика" <?= (isset($genre) && $genre === 'Фантастика') ? 'selected' : '' ?>>Фантастика</option>
                            <option value="Детектив" <?= (isset($genre) && $genre === 'Детектив') ? 'selected' : '' ?>>Детектив</option>
                            <option value="Фэнтези" <?= (isset($genre) && $genre === 'Фэнтези') ? 'selected' : '' ?>>Фэнтези</option>
                            <option value="Биография" <?= (isset($genre) && $genre === 'Биография') ? 'selected' : '' ?>>Биография</option>
                            <option value="История" <?= (isset($genre) && $genre === 'История') ? 'selected' : '' ?>>История</option>
                            <option value="Поэзия" <?= (isset($genre) && $genre === 'Поэзия') ? 'selected' : '' ?>>Поэзия</option>
                            <option value="Философия" <?= (isset($genre) && $genre === 'Философия') ? 'selected' : '' ?>>Философия</option>
                            <option value="Антиутопия" <?= (isset($genre) && $genre === 'Антиутопия') ? 'selected' : '' ?>>Антиутопия</option>
                            <option value="Сатира" <?= (isset($genre) && $genre === 'Сатира') ? 'selected' : '' ?>>Сатира</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="page_count">Количество страниц <span class="required">*</span></label>
                        <input type="number" id="page_count" name="page_count" value="<?= escape($page_count ?? '') ?>" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="publisher">Издательство <span class="required">*</span></label>
                    <input type="text" id="publisher" name="publisher" value="<?= escape($publisher ?? '') ?>" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-success">Добавить книгу</button>
                    <button type="reset" class="btn">Очистить форму</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>