<?php
session_start();

// Проверка на авторизацию
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Game Store</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Admin Panel - Game Store</h1>
        <nav>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="add_game.php">Add Game</a></li>
                <li><a href="view_orders.php">View Orders</a></li>
                <li><a href="edit_game.php">Edit Game</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Welcome, Admin!</h2>
            <!-- Здесь будет отображаться информация о магазине -->
        </section>
    </main>
</body>
</html>
