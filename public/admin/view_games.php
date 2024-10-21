<?php
session_start();

// Проверяем, авторизован ли админ
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Подключение к базе данных
$host = '127.127.126.50';
$db = 'gamestore';
$user = 'root';
$pass = '';  // Пароль по умолчанию

$games = [];

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запрос для получения всех игр
    $sql = "SELECT * FROM games ORDER BY created_at DESC";
    $stmt = $conn->query($sql);
    $games = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}

?>

<?php include '../includes/admin/header.php'; ?>

<h2>List of Added Games</h2>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>Title</th>
            <th>Price</th>
            <th>Genre</th>
            <th>Description</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($games) > 0): ?>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?php echo htmlspecialchars($game['title']); ?></td>
                    <td>$<?php echo htmlspecialchars($game['price']); ?></td>
                    <td><?php echo isset($game['genre']) ? htmlspecialchars($game['genre']) : 'No genre'; ?></td>
                    <td><?php echo isset($game['description']) ? htmlspecialchars($game['description']) : 'No description'; ?></td>
                    <td><?php echo htmlspecialchars($game['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No games have been added yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/admin/footer.php'; ?>
