<!-- game_card.php -->
<div class="game-card">
    <h3><?php echo htmlspecialchars($game['title']); ?></h3>
    <img src="../images/<?php echo !empty($game['image']) ? htmlspecialchars($game['image']) : 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($game['title']); ?>">
    <p>Price: $<?php echo htmlspecialchars($game['price']); ?></p>
    <p>Genre: <?php echo htmlspecialchars($game['genre'] ?? 'Unknown Genre'); ?></p>
    <form method="POST" action="catalog.php">
        <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game['id']); ?>">
        <input type="hidden" name="title" value="<?php echo htmlspecialchars($game['title']); ?>">
        <input type="hidden" name="price" value="<?php echo htmlspecialchars($game['price']); ?>">
        <button type="submit" name="add_to_cart">Add to Cart</button>
    </form>
</div>
