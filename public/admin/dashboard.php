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
<?php include '../includes/admin/header.php'; ?>
    <main>
        <section>
            <h2>Welcome, Admin!</h2>
            <!-- Здесь будет отображаться информация о магазине -->
        </section>
    </main>
    <?php include '../includes/admin/footer.php'; ?>
</body>
</html>
