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
    
    // Базовый SQL-запрос для получения всех игр
    $sql = "SELECT * FROM games WHERE 1";

    // Фильтрация по цене
    if (isset($_GET['price_range'])) {
        $price_range = $_GET['price_range'];

        if ($price_range == 'under_100') {
            $sql .= " AND price < 100";
        } elseif ($price_range == '100_500') {
            $sql .= " AND price BETWEEN 100 AND 500";
        } elseif ($price_range == 'over_500') {
            $sql .= " AND price > 500";
        }
    }

    // Фильтрация по жанрам
    if (isset($_GET['genre']) && $_GET['genre'] != '') {
        $genre = $_GET['genre'];
        $sql .= " AND genre = '$genre'";
    }

    // Поиск по ключевым словам (название и описание игры)
    if (isset($_GET['search']) && $_GET['search'] != '') {
        $search = $_GET['search'];
        $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
    }

    // Выполняем запрос с фильтрацией
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
    <title>Game Catalog</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Game Catalog</h1>
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
            <h2>Filter by Price</h2>
            <form method="GET" action="catalog.php">
                <select name="price_range" onchange="this.form.submit()">
                    <option value="">Select price range</option>
                    <option value="under_100" <?php if (isset($_GET['price_range']) && $_GET['price_range'] == 'under_100') echo 'selected'; ?>>Under $100</option>
                    <option value="100_500" <?php if (isset($_GET['price_range']) && $_GET['price_range'] == '100_500') echo 'selected'; ?>>$100 - $500</option>
                    <option value="over_500" <?php if (isset($_GET['price_range']) && $_GET['price_range'] == 'over_500') echo 'selected'; ?>>Over $500</option>
                </select>
            </form>

            <h2>Filter by Genre</h2>
            <form method="GET" action="catalog.php">
                <select name="genre" onchange="this.form.submit()">
                    <option value="">Select genre</option>
                    <option value="Action" <?php if (isset($_GET['genre']) && $_GET['genre'] == 'Action') echo 'selected'; ?>>Action</option>
                    <option value="Adventure" <?php if (isset($_GET['genre']) && $_GET['genre'] == 'Adventure') echo 'selected'; ?>>Adventure</option>
                    <option value="RPG" <?php if (isset($_GET['genre']) && $_GET['genre'] == 'RPG') echo 'selected'; ?>>RPG</option>
                    <!-- Добавь другие жанры по мере необходимости -->
                </select>
            </form>

            <h2>Search Games</h2>
            <form method="GET" action="catalog.php">
                <input type="text" name="search" placeholder="Search for games" value="<?php if (isset($_GET['search'])) echo htmlspecialchars($_GET['search']); ?>">
                <button type="submit">Search</button>
            </form>

            <h2>Games</h2>
            
            <?php if (count($games) > 0): ?>
                <ul>
                    <?php foreach ($games as $game): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                            <p>Price: $<?php echo htmlspecialchars($game['price']); ?></p>
                            <p><?php echo htmlspecialchars($game['description']); ?></p>
                            <img src="../images/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" width="100">
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No games found matching your criteria.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Store</p>
    </footer>
</body>
</html>
