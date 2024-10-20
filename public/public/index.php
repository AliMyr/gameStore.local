<?php
session_start();

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = ''; 

$games = [];  // Массив для последних игр

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос для получения последних 3 игр
    $sql = "SELECT * FROM games ORDER BY created_at DESC LIMIT 3";
    $stmt = $conn->query($sql);
    $games = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Ошибка соединения: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Store</title>
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
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="featured-games">
            <h2>Latest Games</h2>
            <div class="games-list">
                <?php if (isset($games) && count($games) > 0): ?>
                    <?php foreach ($games as $game): ?>
                        <div class="game-card">
                            <h3><?php echo htmlspecialchars($game['title'] ?? 'No title'); ?></h3>
                            <img src="../images/<?php echo htmlspecialchars($game['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" width="150">
                            <p>Price: $<?php echo htmlspecialchars($game['price']); ?></p>
                            <p>Genre: <?php echo htmlspecialchars($game['genre']); ?></p>
                            <a href="product.php?id=<?php echo htmlspecialchars($game['id']); ?>" class="details-link">View Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No games available.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Store</p>
    </footer>
</body>
</html>
