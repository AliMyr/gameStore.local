<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Подключение к базе данных
    $host = '127.127.126.50';
    $db = 'gamestore';
    $user = 'root';
    $pass = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Сохраняем корзину пользователя в базе данных
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $game_id => $game) {
                $check_sql = "SELECT COUNT(*) FROM carts WHERE user_id = :user_id AND game_id = :game_id";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->execute(['user_id' => $user_id, 'game_id' => $game_id]);

                if ($check_stmt->fetchColumn() > 0) {
                    // Обновляем количество
                    $update_sql = "UPDATE carts SET quantity = :quantity WHERE user_id = :user_id AND game_id = :game_id";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->execute([
                        'quantity' => $game['quantity'],
                        'user_id' => $user_id,
                        'game_id' => $game_id
                    ]);
                } else {
                    // Если товар не в базе, добавляем
                    $insert_sql = "INSERT INTO carts (user_id, game_id, title, price, quantity) VALUES (:user_id, :game_id, :title, :price, :quantity)";
                    $insert_stmt = $conn->prepare($insert_sql);
                    $insert_stmt->execute([
                        'user_id' => $user_id,
                        'game_id' => $game_id,
                        'title' => $game['title'],
                        'price' => $game['price'],
                        'quantity' => $game['quantity']
                    ]);
                }
            }
        }

    } catch (PDOException $e) {
        // Обработка ошибки соединения
        echo "Ошибка соединения: " . $e->getMessage();
        exit();
    }
}

// Удаление всех переменных сессии
$_SESSION = [];

// Если нужны куки сессии, то также можно удалить сессию на уровне браузера
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Наконец, уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на главную страницу или страницу входа
header("Location: login.php");
exit();
