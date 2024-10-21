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

// Удаление игры из корзины
if (isset($_POST['remove'])) {
    $game_id = $_POST['game_id'];
    unset($_SESSION['cart'][$game_id]);
}

// Очистка всей корзины
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
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
        $sql = "INSERT INTO orders (name, email, address, total_price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $address, $total_price]);

        // Получаем ID последнего заказа
        $order_id = $conn->lastInsertId();

        // Сохраняем товары из корзины в таблицу order_items
        foreach ($_SESSION['cart'] as $game_id => $game) {
            $sql = "INSERT INTO order_items (order_id, game_title, price, quantity) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$order_id, $game['title'], $game['price'], $game['quantity']]);
        }

        // Отправляем подтверждение заказа на e-mail
        $to = $email;
        $subject = 'Order Confirmation - Game Store';
        $message = "Thank you for your order! Your order ID is $order_id. We will process it shortly.";
        $headers = 'From: no-reply@gamestore.com' . "\r\n" .
                   'Reply-To: no-reply@gamestore.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            echo "<p>Thank you for your order! Your order ID is: $order_id</p>";
        } else {
            echo "<p>Order placed, but confirmation email could not be sent.</p>";
        }

        // Очищаем корзину после оформления заказа
        unset($_SESSION['cart']);
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
