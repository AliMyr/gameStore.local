<?php
session_start();

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль пустой, если не менял

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хэшируем пароль

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);

        echo "Регистрация успешна! Теперь можно <a href='login.php'>войти</a>.";
    }
} catch (PDOException $e) {
    echo "Ошибка соединения: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Регистрация</h1>
    <form method="POST" action="register.php">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>
