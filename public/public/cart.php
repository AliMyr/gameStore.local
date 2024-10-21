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
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Удаление игры из корзины и базы данных
if (isset($_POST['remove'])) {
    $game_id = $_POST['game_id'];

    // Удаляем товар из корзины в сессии
    unset($_SESSION['cart'][$game_id]);

    // Если пользователь авторизован, удаляем товар из базы данных
    if (isset($_SESSION['user_id'])) {
        $delete_sql = "DELETE FROM carts WHERE user_id = :user_id AND game_id = :game_id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute(['user_id' => $_SESSION['user_id'], 'game_id' => $game_id]);
    }
}

// Очистка всей корзины
if (isset($_POST['clear_cart'])) {
    // Очищаем корзину в сессии
    unset($_SESSION['cart']);

    // Если пользователь авторизован, очищаем корзину в базе данных
    if (isset($_SESSION['user_id'])) {
        $clear_sql = "DELETE FROM carts WHERE user_id = :user_id";
        $clear_stmt = $conn->prepare($clear_sql);
        $clear_stmt->execute(['user_id' => $_SESSION['user_id']]);
    }
}

// Оформление заказа
if (isset($_POST['place_order'])) {
    // Проверка валидности данных
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $address = htmlspecialchars(trim($_POST['address']));
    $total_price = 0;

    if (!$email) {
        echo "<p>Invalid email address.</p>";
    } else {
        // Считаем общую стоимость заказа
        foreach ($_SESSION['cart'] as $game) {
            $total_price += $game['price'] * $game['quantity'];
        }

        // Сохраняем заказ в базе данных
        $sql = "INSERT INTO orders (user_id, name, email, address, total_price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $name, $email, $address, $total_price]);

        // Получаем ID последнего заказа
        $order_id = $conn->lastInsertId();

        // Сохраняем товары из корзины в таблицу order_items
        foreach ($_SESSION['cart'] as $game_id => $game) {
            $sql = "INSERT INTO order_items (order_id, game_title, price, quantity) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$order_id, $game['title'], $game['price'], $game['quantity']]);
        }

        // Очищаем корзину после оформления заказа
        unset($_SESSION['cart']);

        // Если пользователь авторизован, очищаем корзину в базе данных
        if (isset($_SESSION['user_id'])) {
            $clear_sql = "DELETE FROM carts WHERE user_id = :user_id";
            $clear_stmt = $conn->prepare($clear_sql);
            $clear_stmt->execute(['user_id' => $_SESSION['user_id']]);
        }

        echo "<p>Thank you for your order! Your order ID is: $order_id</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php
    include '../includes/public/header.php';
    ?>

    <main>
        <section>
            <h2>Items in your Cart</h2>

            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <ul>
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['cart'] as $game_id => $game): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                            <p>Price: $<?php echo htmlspecialchars($game['price']); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($game['quantity']); ?></p>
                            <?php $total += $game['price'] * $game['quantity']; ?>
                            
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
                                <button type="submit" name="remove">Remove from Cart</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h3>Total Price: $<?php echo number_format($total, 2); ?></h3>

                <form method="POST" action="cart.php">
                    <button type="submit" name="clear_cart">Clear Cart</button>
                </form>

                <h2>Place Your Order</h2>
                <form method="POST" action="cart.php">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    <br>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <br>
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required></textarea>
                    <br>
                    <button type="submit" name="place_order">Place Order</button>
                </form>
            <?php else: ?>
                <p>Your cart is empty.</p>
                <a href="catalog.php">Go to Catalog</a>
            <?php endif; ?>
        </section>
    </main>

    <?php
    include '../includes/public/footer.php';
    ?>
</body>
</html>
