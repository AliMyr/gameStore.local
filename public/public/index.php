<?php
session_start();

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль пустой, если не менял

// Инициализируем переменную $games как пустой массив
$games = [];

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Запрос для получения всех игр
    $sql = "SELECT * FROM games";
    $stmt = $conn->query($sql);
    $games = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Game Store!</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to Game Store!</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="catalog.php">Catalog</a></li>
                <li><a href="cart.php">Cart</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Available Games</h2>
            
            <?php if (count($games) > 0): ?>
                <ul>
                    <?php foreach ($games as $game): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($game['title'] ?? 'No title'); ?></h3>
                            <p>Price: $<?php echo htmlspecialchars($game['price'] ?? 'N/A'); ?></p>
                            <p>Genre: <?php echo htmlspecialchars($game['genre'] ?? 'No genre'); ?></p> <!-- Отображаем жанр -->
                            <p><?php echo htmlspecialchars($game['description'] ?? 'No description available'); ?></p>
                            <!-- Проверка наличия изображения и вывод изображения по умолчанию -->
                            <img src="../images/<?php echo htmlspecialchars($game['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($game['title'] ?? 'No title'); ?>" width="100">
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['id']); ?>">
                                <button type="submit">Add to Cart</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No games available.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Store</p>
    </footer>
</body>
</html>
