<?php
session_start();

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';

$games = [];
$genres = [];
$prices = [];

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос для получения уникальных жанров
    $genre_sql = "SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL";
    $stmt = $conn->query($genre_sql);
    $genres = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Пример ценовых диапазонов
    $prices = [
        '0-50' => 'Under $50',
        '51-100' => '$51 to $100',
        '101-500' => '$101 to $500',
        '501+' => 'Above $500',
    ];

    // Основной запрос
    $sql = "SELECT * FROM games WHERE 1=1";

    // Фильтрация по жанру
    if (isset($_GET['genre']) && $_GET['genre'] !== '') {
        $genre = $_GET['genre'];
        $sql .= " AND genre = :genre";
    }

    // Фильтрация по цене
    if (isset($_GET['price_range']) && $_GET['price_range'] !== '') {
        $price_range = $_GET['price_range'];
        if ($price_range == '0-50') {
            $sql .= " AND price <= 50";
        } elseif ($price_range == '51-100') {
            $sql .= " AND price > 50 AND price <= 100";
        } elseif ($price_range == '101-500') {
            $sql .= " AND price > 100 AND price <= 500";
        } elseif ($price_range == '501+') {
            $sql .= " AND price > 500";
        }
    }

    $stmt = $conn->prepare($sql);
    if (isset($genre)) {
        $stmt->bindParam(':genre', $genre);
    }
    $stmt->execute();
    $games = $stmt->fetchAll();
    
} catch (PDOException $e) {
    echo "Ошибка соединения: " . $e->getMessage();
}

// Обработка добавления в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $game_id = $_POST['game_id'];
    $title = $_POST['title'];
    $price = $_POST['price'];

    // Инициализация корзины, если она еще не создана
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Если игра уже в корзине, увеличиваем количество
    if (isset($_SESSION['cart'][$game_id])) {
        $_SESSION['cart'][$game_id]['quantity']++;
    } else {
        // Если игра еще не в корзине, добавляем её
        $_SESSION['cart'][$game_id] = [
            'title' => $title,
            'price' => $price,
            'quantity' => 1
        ];
    }

    // Уведомление об успешном добавлении
    $success_message = "Game added to cart!";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Catalog</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/public/header.php'; ?>

    <main>
        <!-- Отображение сообщения об успешном добавлении -->
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Фильтры -->
        <section class="filter-section">
            <form method="GET" action="catalog.php">
                <select name="price_range">
                    <option value="">Select price range</option>
                    <?php foreach ($prices as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" <?php if (isset($_GET['price_range']) && $_GET['price_range'] == $key) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="genre">
                    <option value="">Select genre</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo htmlspecialchars($genre); ?>" <?php if (isset($_GET['genre']) && $_GET['genre'] == $genre) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($genre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Search</button>
            </form>
        </section>

        <!-- Список игр -->
        <div class="games-list">
            <?php if (!empty($games) && count($games) > 0): ?>
                <?php foreach ($games as $game): ?>
                    <?php include '../includes/public/game_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No games found.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/public/footer.php'; ?>
</body>
</html>
