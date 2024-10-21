<?php
session_start();

// Проверка авторизации администратора
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль пустой, если не менял

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Обработка формы для добавления игры
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $genre = $_POST['genre'];

    // Проверка и обработка загрузки изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_destination = '../images/' . $image_name;
        move_uploaded_file($image_tmp_name, $image_destination);
    } else {
        $image_name = 'default.jpg'; // Если изображение не загружено, используем изображение по умолчанию
    }

    // Сохранение данных игры в базу данных
    $sql = "INSERT INTO games (title, description, price, image, genre) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $description, $price, $image_name, $genre]);

    echo "<p>Game added successfully!</p>";
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
<?php include '../includes/admin/header.php'; ?>

    <main>
        <section>
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

                <label for="genre">Genre:</label>
                <select name="genre" id="genre" required>
                    <option value="Action">Action</option>
                    <option value="Adventure">Adventure</option>
                    <option value="RPG">RPG</option>
                    <option value="Strategy">Strategy</option>
                    <option value="Shooter">Shooter</option>
                    <!-- Добавь другие жанры по мере необходимости -->
                </select>
                <br>
                
                <label for="image">Image:</label>
                <input type="file" id="image" name="image">
                <br>

                <button type="submit">Add Game</button>
            </form>
        </section>
    </main>

    <?php include '../includes/admin/footer.php'; ?>
</body>
</html>
