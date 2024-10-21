<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="/css/admin-style.css">
</head>
<body>
    <header>
        <h1>Admin Panel - Game Store</h1>
        <nav>
            <ul>
                <li><a href="/admin/admin.php">Dashboard</a></li>
                <li><a href="/admin/add_game.php">Add Game</a></li>
                <li><a href="/admin/view_orders.php">View Orders</a></li>
                <li><a href="/admin/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
