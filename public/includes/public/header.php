<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Game Store'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to Game Store!</h1>
        <nav>
            <ul>
                <li><a href="/public/index.php">Home</a></li>
                <li><a href="/public/catalog.php">Catalog</a></li>
                <li><a href="/public/cart.php">Cart</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="/public/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="/public/login.php">Login</a></li>
                    <li><a href="/public/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
