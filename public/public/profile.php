<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Получаем данные пользователя
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Получаем заказы пользователя
    $orders_stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $orders_stmt->execute([$user_id]);
    $orders = $orders_stmt->fetchAll(); // fetchAll всегда возвращает массив, даже если он пустой
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Обработка изменений профиля
if (isset($_POST['update_profile'])) {
    // Получаем новые данные
    $new_username = htmlspecialchars(trim($_POST['username']));
    $new_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверяем корректность введённых данных
    if (!$new_email) {
        echo "<p>Invalid email format.</p>";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        echo "<p>Passwords do not match.</p>";
    } else {
        try {
            // Обновляем данные пользователя
            $update_sql = "UPDATE users SET username = :username, email = :email WHERE id = :user_id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->execute([
                'username' => $new_username,
                'email' => $new_email,
                'user_id' => $user_id
            ]);

            // Если был введён новый пароль, обновляем его
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_sql = "UPDATE users SET password = :password WHERE id = :user_id";
                $update_password_stmt = $conn->prepare($update_password_sql);
                $update_password_stmt->execute([
                    'password' => $hashed_password,
                    'user_id' => $user_id
                ]);
            }

            echo "<p>Profile updated successfully!</p>";
        } catch (PDOException $e) {
            echo "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/public/header.php'; ?>

    <main>
        <section>
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

            <h2>Your Orders</h2>
            <?php if (!empty($orders) && count($orders) > 0): ?>
                <ul>
                    <?php foreach ($orders as $order): ?>
                        <li>
                            <h3>Order #<?php echo $order['id']; ?> (Total: $<?php echo $order['total_price']; ?>)</h3>
                            <p>Placed on: <?php echo $order['created_at']; ?></p>

                            <!-- Получаем товары для этого заказа -->
                            <?php
                            $order_items_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
                            $order_items_stmt->execute([$order['id']]);
                            $order_items = $order_items_stmt->fetchAll();
                            ?>
                            <ul>
                                <?php foreach ($order_items as $item): ?>
                                    <li>
                                        <?php echo htmlspecialchars($item['game_title']); ?> - $<?php echo $item['price']; ?> (Quantity: <?php echo $item['quantity']; ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no orders yet.</p>
            <?php endif; ?>

            <!-- Форма для редактирования профиля -->
            <form method="POST" action="profile.php">
                <h2>Edit Profile</h2>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="password">New Password:</label>
                <input type="password" id="password" name="password">

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password">

                <button type="submit" name="update_profile">Save Changes</button>
            </form>
        </section>
    </main>

    <?php include '../includes/public/footer.php'; ?>
</body>
</html>
