<?php
require_once 'config.php';
checkAuth();

$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$allowed_sort = ['id', 'title', 'author', 'price', 'year_published', 'genre'];
$sort_by = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

// Для SQL запроса нужен верхний регистр
$sql_order = strtoupper($sort_order);

try {
    $count_sql = "SELECT COUNT(*) FROM books";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute();
    $total_records = $count_stmt->fetchColumn();

    $total_pages = ceil($total_records / $limit);
    
    $sql = "SELECT * FROM books ORDER BY $sort_by $sql_order LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Ошибка при получении данных: " . $e->getMessage();
}

function sortLink($column, $current_sort, $current_order) {
    $new_order = ($current_sort === $column && $current_order === 'asc') ? 'desc' : 'asc';
    return "view.php?sort=$column&order=$new_order";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр книг</title>
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
            max-width: 1200px;
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

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        th a {
            color: #333;
            text-decoration: none;
        }

        th a:hover {
            color: #007bff;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .pagination a, .pagination span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            background: white;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
        }

        .pagination .current {
            background: #007bff;
            color: white;
        }

        .stats {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .navigation {
                flex-direction: column;
            }
            
            th, td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Просмотр книг</h1>
        </div>

        <div class="navigation">
            <a href="index.php" class="btn">Главная</a>
            <a href="add.php" class="btn">Добавить книгу</a>
            <a href="delete.php" class="btn">Удалить книгу</a>
            <a href="logout.php" class="btn">Выйти</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert"><?= escape($error) ?></div>
        <?php endif; ?>

        <div class="stats">
            Всего книг: <?= $total_records ?> | 
            Страница <?= $page ?> из <?= $total_pages ?> | 
            Показано записей: <?= count($books) ?>
        </div>

        <?php if (empty($books)): ?>
            <div class="table-container">
                <div style="padding: 40px; text-align: center; color: #666;">
                    <h3>Нет данных для отображения</h3>
                    <p>В базе данных пока нет книг. <a href="add.php">Добавить первую книгу</a></p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-container">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th><a href="<?= sortLink('id', $sort_by, $sort_order) ?>">ID<?= $sort_by === 'id' ? ($sort_order === 'asc' ? ' ↑' : ' ↓') : '' ?></a></th>
                                <th><a href="<?= sortLink('title', $sort_by, $sort_order) ?>">Название<?= $sort_by === 'title' ? ($sort_order === 'asc' ? ' ↑' : ' ↓') : '' ?></a></th>
                                <th><a href="<?= sortLink('author', $sort_by, $sort_order) ?>">Автор<?= $sort_by === 'author' ? ($sort_order === 'asc' ? ' ↑' : ' ↓') : '' ?></a></th>
                                <th><a href="<?= sortLink('price', $sort_by, $sort_order) ?>">Цена<?= $sort_by === 'price' ? ($sort_order === 'asc' ? ' ↑' : ' ↓') : '' ?></a></th>
                                <th><a href="<?= sortLink('year_published', $sort_by, $sort_order) ?>">Год<?= $sort_by === 'year_published' ? ($sort_order === 'asc' ? ' ↑' : ' ↓') : '' ?></a></th>
                                <th><a href="<?= sortLink('genre', $sort_by, $sort_order) ?>">Жанр<?= $sort_by === 'genre' ? ($sort_order === 'asc' ? ' ↑' : ' ↓') : '' ?></a></th>
                                <th>Страницы</th>
                                <th>Издательство</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= escape($book['id']) ?></td>
                                    <td><strong><?= escape($book['title']) ?></strong></td>
                                    <td><?= escape($book['author']) ?></td>
                                    <td><?= number_format($book['price'], 2) ?> ₽</td>
                                    <td><?= escape($book['year_published']) ?></td>
                                    <td><?= escape($book['genre']) ?></td>
                                    <td><?= escape($book['page_count']) ?></td>
                                    <td><?= escape($book['publisher']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1&sort=<?= $sort_by ?>&order=<?= $sort_order ?>">Первая</a>
                        <a href="?page=<?= $page - 1 ?>&sort=<?= $sort_by ?>&order=<?= $sort_order ?>">Предыдущая</a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                        if ($i == $page):
                    ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&sort=<?= $sort_by ?>&order=<?= $sort_order ?>"><?= $i ?></a>
                    <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&sort=<?= $sort_by ?>&order=<?= $sort_order ?>">Следующая</a>
                        <a href="?page=<?= $total_pages ?>&sort=<?= $sort_by ?>&order=<?= $sort_order ?>">Последняя</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>