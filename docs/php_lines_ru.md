# Пояснения к PHP-коду

В этом документе описываются все строки PHP-кода из файлов проекта. Для каждой функции приведена ссылка на официальную документацию PHP.

## config.php
### Файл `config.php`
| Строка | Код | Описание |
|-------|-----|----------|
|1|`<?php`|Начало скрипта PHP.|
|2|`session_start();`|Старт PHP сессии. [Док.](https://www.php.net/manual/ru/function.session-start.php)|
|4-7|`$host`, `$username`, `$password`, `$dbname`|Параметры подключения к базе данных.|
|9-14|Блок `try/catch` с `new PDO`|Создание подключения к MySQL через [PDO](https://www.php.net/manual/ru/class.pdo.php). В случае ошибки выводится сообщение.|
|16-17|`$admin_login`, `$admin_password`|Учётные данные администратора.|
|19-24|`function checkAuth()`|Проверка авторизации пользователя. Если нет - редирект с помощью [header](https://www.php.net/manual/ru/function.header.php).|
|26-28|`function escape($data)`|Экранирование вывода через [htmlspecialchars](https://www.php.net/manual/ru/function.htmlspecialchars.php).|
|29|`?>`|Закрывающий тег PHP.|

## index.php
### Файл `index.php`
| Строка | Код | Описание |
|-------|-----|----------|
|1|`<?php`|Начало PHP-блока.|
|2|`require_once 'config.php';`|Подключение файла конфигурации. [Док.](https://www.php.net/manual/ru/function.require-once.php)|
|4-5|`$error`, `$success`|Переменные для сообщений об ошибке и успешной авторизации.|
|7|`if ($_POST) {`|Проверка, была ли отправлена форма методом POST.|
|8|`$login = trim($_POST['login'] ?? '');`|Получение логина из POST и удаление пробелов. [trim](https://www.php.net/manual/ru/function.trim.php)|
|9|`$password = trim($_POST['password'] ?? '');`|Аналогично для пароля.|
|11-16|Проверка логина и пароля. При совпадении устанавливается сессионная переменная `logged_in`.|
|19|`$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;`|Определение статуса авторизации. [isset](https://www.php.net/manual/ru/function.isset.php)|
|20|`?>`|Закрытие PHP-блока.|

В дальнейшей части файла используются конструкции `<?php ... ?>` для вывода сообщений и проверки авторизации в HTML.
## view.php
| Строка | Код | Описание |
|-------|-----|----------|
|1|`<?php`|Начало PHP-кода.|
|2|`require_once 'config.php';`|Подключение настроек и функций проекта.|
|3|`checkAuth();`|Проверка авторизации пользователя.|
|5|`$limit = 10;`|Количество записей на страницу.|
|6|`$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;`|Получение номера страницы из GET-параметра. Используются функции [isset](https://www.php.net/manual/ru/function.isset.php) и [intval](https://www.php.net/manual/ru/function.intval.php).|
|7|`$offset = ($page - 1) * $limit;`|Расчёт смещения для SQL.|
|9|`$allowed_sort = [...]`|Список допустимых столбцов для сортировки.|
|10|`$sort_by = ...`|Определение выбранного столбца сортировки с проверкой через [in_array](https://www.php.net/manual/ru/function.in-array.php).|
|11|`$sort_order = ...`|Порядок сортировки (asc/desc).|
|13-14|`$sql_order = strtoupper($sort_order);`|Перевод значения сортировки в верхний регистр. [strtoupper](https://www.php.net/manual/ru/function.strtoupper.php)|
|16-33|Блок `try/catch` для получения данных из БД с помощью PDO. Выполняются запросы `COUNT` и `SELECT`.|
|35-38|Функция `sortLink()` возвращает URL с параметрами сортировки.|
|39|`?>`|Закрытие PHP-блока.|

В HTML-разметке далее используются вставки `<?= ... ?>` для вывода значений и циклы `foreach` для отображения таблицы книг.
## add.php
| Строка | Код | Описание |
|-------|-----|----------|
|1|`<?php`|Начало скрипта.|
|2|`require_once 'config.php';`|Подключение настроек.|
|3|`checkAuth();`|Проверка авторизации.| 
|5-6|`$success`, `$error`|Сообщения о результате добавления.|
|8|`if ($_POST) {`|Обработка формы отправки.|
|9-15|Получение значений полей формы с использованием `trim()`.| 
|17|`$errors = [];`|Массив для хранения ошибок валидации.| 
|19-45|Проверки заполнения полей формы (функции `empty`, `is_numeric`, `floatval`, `intval`, `date`).| 
|47-66|При отсутствии ошибок выполняется SQL `INSERT` через PDO. Используются методы [`prepare`](https://www.php.net/manual/ru/pdo.prepare.php) и [`execute`](https://www.php.net/manual/ru/pdostatement.execute.php). Полученный ID возвращается через [`lastInsertId`](https://www.php.net/manual/ru/pdo.lastinsertid.php).|
|67-69|Перехват исключений `PDOException` и запись сообщения об ошибке.| 
|70-72|В случае ошибок формируется строка с их перечислением с помощью [`implode`](https://www.php.net/manual/ru/function.implode.php).|
|73|`}` Завершение условия.| 
|74|`?>` Завершение PHP блока.| 

Далее в HTML присутствуют блоки `<?php if (...) ?>` для вывода сообщений и значения полей формы через `<?= escape(...) ?>`.
## delete.php
| Строка | Код | Описание |
|-------|-----|----------|
|1|`<?php`|Начало файла.|
|2|`require_once 'config.php';`|Подключение конфигурации.|
|3|`checkAuth();`|Проверка прав доступа.|
|5-7|`$success`, `$error`, `$book_title`|Переменные для результата операции.|
|9|`if ($_POST) {`|Проверка отправки формы.|
|10|`$book_id = trim($_POST['book_id'] ?? '');`|Получение ID книги.|
|12-28|Проверка ID, выбор книги, удаление через PDO (`prepare`, `execute`).|
|30-31|Обработка исключения `PDOException`.|
|34|Закрывающая фигурная скобка.| 
|35|`?>` Завершение PHP-кода.|

В разметке далее используются конструкции `<?php if (...) ?>` для вывода сообщений.
## logout.php
| Строка | Код | Описание |
|-------|-----|----------|
|1|`<?php`|Открытие PHP-блока.|
|2|`require_once 'config.php';`|Подключение настроек (для доступа к сессии).|
|4|`session_destroy();`|Завершение сессии пользователя. [Док.](https://www.php.net/manual/ru/function.session-destroy.php)|
|6|`header('Location: exam/index.php');`|Перенаправление на страницу входа.| 
|7|`exit();`|Прерывание дальнейшего выполнения скрипта.| 
|8|`?>`|Закрытие PHP.|
