<?php
session_start(); // Стартуем сессию для хранения корзины

// Подключаемся к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль пустой, если не менял

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Запрос на получение всех игр
    $stmt = $conn->query("SELECT * FROM games");
    $games = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Добавление в корзину
if (isset($_POST['add_to_cart'])) {
    $game_id = $_POST['game_id'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = []; // Создаём корзину, если её нет
    }

    // Проверяем, есть ли уже игра в корзине
    if (isset($_SESSION['cart'][$game_id])) {
        $_SESSION['cart'][$game_id]['quantity']++;
    } else {
        // Добавляем игру в корзину
        $_SESSION['cart'][$game_id] = [
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'quantity' => 1,
        ];
    }
    
    echo "<p>Игра добавлена в корзину!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
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
                            <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                            <p><?php echo htmlspecialchars($game['description']); ?></p>
                            <p>Price: $<?php echo htmlspecialchars($game['price']); ?></p>
                            <img src="../images/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" width="100">
                            
                            <form method="POST" action="index.php">
                                <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                                <input type="hidden" name="title" value="<?php echo htmlspecialchars($game['title']); ?>">
                                <input type="hidden" name="price" value="<?php echo htmlspecialchars($game['price']); ?>">
                                <button type="submit" name="add_to_cart">Add to Cart</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No games available at the moment.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Store</p>
    </footer>
</body>
</html>
