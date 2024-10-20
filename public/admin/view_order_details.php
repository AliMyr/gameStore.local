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

    // Получаем ID заказа из запроса
    $order_id = $_GET['order_id'];

    // Запрос на получение информации о заказе
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    // Запрос на получение всех товаров в заказе
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();

    // Обновление статуса заказа
    if (isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $order['status'] = $new_status; // Обновляем локальную переменную для отображения

        // Отправляем уведомление клиенту на e-mail
        $to = $order['email'];  // Email клиента
        $subject = 'Order Status Update - Game Store';
        $message = "Your order #$order_id status has been updated to: $new_status.";
        $headers = 'From: no-reply@gamestore.com' . "\r\n" .
                   'Reply-To: no-reply@gamestore.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        echo "<p>Status updated successfully and notification sent to customer!</p>";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Order Details</h1>
        <nav>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="add_game.php">Add Game</a></li>
                <li><a href="view_orders.php">View Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Order #<?php echo htmlspecialchars($order['id']); ?></h2>
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Total Price:</strong> $<?php echo htmlspecialchars($order['total_price']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>

            <h3>Items in Order</h3>
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($item['game_title']); ?></strong> - $<?php echo htmlspecialchars($item['price']); ?> x <?php echo htmlspecialchars($item['quantity']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Отображение и изменение статуса -->
            <h3>Order Status: <?php echo htmlspecialchars($order['status']); ?></h3>
            <form method="POST" action="view_order_details.php?order_id=<?php echo $order['id']; ?>">
                <label for="status">Change Status:</label>
                <select name="status" id="status">
                    <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Processing" <?php if ($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                    <option value="Completed" <?php if ($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Canceled" <?php if ($order['status'] == 'Canceled') echo 'selected'; ?>>Canceled</option> <!-- Новый статус -->
                </select>
                <button type="submit" name="update_status">Update Status</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Game Store</p>
    </footer>
</body>
</html>
