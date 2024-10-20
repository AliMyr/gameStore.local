<?php
session_start();

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль по умолчанию

$game = null;  // Игра, которая будет отображаться

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Проверяем, передан ли ID игры
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $game_id = $_GET['id'];
        
        // Запрос на получение деталей игры по ID
        $sql = "SELECT * FROM games WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
        $stmt->execute();

        $game = $stmt->fetch();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Если игра не найдена, перенаправляем на главную
if (!$game) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['title'] ?? 'Game Details'); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($game['title'] ?? 'No title'); ?></h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="catalog.php">Catalog</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="game-details">
            <div class="game-card">
                <!-- Отображение изображения игры, если оно доступно -->
                <img src="../images/<?php echo htmlspecialchars($game['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($game['title'] ?? 'No title'); ?>" width="250">
                
                <!-- Отображение цены -->
                <p><strong>Price:</strong> $<?php echo htmlspecialchars($game['price'] ?? 'N/A'); ?></p>

                <!-- Отображение жанра игры -->
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($game['genre'] ?? 'No genre'); ?></p>

                <!-- Отображение описания игры -->
                <p><strong>Description:</strong> <?php echo htmlspecialchars($game['description'] ?? 'No description available'); ?></p>
                
                <!-- Форма для добавления игры в корзину -->
                <form method="POST" action="cart.php">
                    <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['id']); ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Store</p>
    </footer>
</body>
</html>
