<?php
session_start();

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
