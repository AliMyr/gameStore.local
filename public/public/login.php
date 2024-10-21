<?php
session_start();

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль пустой, если не менял

$error = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Некорректный формат email.";
        } else {
            // Проверяем, существует ли пользователь с таким email
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Если пользователь найден и пароль верный
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Загружаем корзину пользователя из базы данных
                $cart_sql = "SELECT * FROM carts WHERE user_id = :user_id";
                $cart_stmt = $conn->prepare($cart_sql);
                $cart_stmt->execute(['user_id' => $user['id']]);
                $user_cart = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

                // Инициализируем сессию корзины
                $_SESSION['cart'] = [];

                foreach ($user_cart as $cart_item) {
                    $_SESSION['cart'][$cart_item['game_id']] = [
                        'title' => $cart_item['title'],
                        'price' => $cart_item['price'],
                        'quantity' => $cart_item['quantity']
                    ];
                }

                header("Location: index.php");
                exit();
            } else {
                $error = "Неверный email или пароль.";
            }
        }
    }
} catch (PDOException $e) {
    $error = "Ошибка соединения: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="../css/public_style.css">
</head>
<body>
    <form method="POST" action="login.php">
        <h1>Вход</h1>
        
        <!-- Отображение ошибки, если она есть -->
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Поле для email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        
        <!-- Поле для пароля -->
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
        
        <!-- Кнопка для входа -->
        <button type="submit">Войти</button>

        <!-- Ссылка на регистрацию -->
        <div class="register-link">
            <p>Нет аккаунта?</p>
            <a href="register.php" class="register-btn">Зарегистрироваться</a>
        </div>
    </form>
</body>
</html>
