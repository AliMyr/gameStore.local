<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Подключение к базе данных
$host = '127.127.126.50';  // Используем этот IP-адрес вместо localhost
$db = 'gamestore';         // Название базы данных
$user = 'root';            // Имя пользователя
$pass = '';                // Пароль по умолчанию пустой (если не менял)

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['submit'])) {
    // Получаем данные из формы
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Обработка изображения
    $image = $_FILES['image']['name'];
    $target = "../images/" . basename($image);

    // Сохранение игры в базу данных
    $sql = "INSERT INTO games (title, description, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $description, $price, $image]);

    // Перемещаем загруженное изображение в папку
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "Game added successfully!";
    } else {
        echo "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Game</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Add New Game</h1>
        <nav>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="add_game.php">Add Game</a></li>
                <li><a href="edit_game.php">Edit Game</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <form method="POST" action="add_game.php" enctype="multipart/form-data">
            <label for="title">Game Title:</label>
            <input type="text" id="title" name="title" required>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <br>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            <br>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" required>
            <br>
            <button type="submit" name="submit">Add Game</button>
        </form>
    </main>
</body>
</html>
