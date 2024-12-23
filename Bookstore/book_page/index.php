<?php
include('../connect.php'); // Ensure this file includes your database connection details

// Fetch books from the database
$sql = "SELECT * FROM book";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore - Browse Books</title>
    <link rel="stylesheet" href="style/style.css"> <!-- Link to the CSS file for styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header class="header">
    <h1><a href="../index.php">Bookstore</a></h1>
    <nav>
        <a href="../search.php" class="search-link">
            <i class="fas fa-search"></i> Search
        </a>
        <a href="../Cart/index.php" class="cart-link">
            <i class="fas fa-shopping-cart"></i> Cart
        </a>
    </nav>
</header>
<div class="book-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="book-card">
            <img src="../Book/book_image/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="book-image">
            <h3 class="book-title"><?php echo htmlspecialchars($row['name']); ?></h3>
            <p class="book-price">Price: Rs<?php echo htmlspecialchars($row['price']); ?></p>
            <p class="book-description"><?php echo htmlspecialchars($row['description']); ?></p>
            <?php if ($row['quantity'] <=0): ?>
            <p class="out-of-stock">Out of Stock</p>
             <?php else: ?>
        <form action="../Cart/index.php" method="POST">
            <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
            <button type="submit" class="buy-now">Buy Now</button>
        </form>
    <?php endif; ?>
</div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No books found.</p>
    <?php endif; ?>
</div>

</body>
</html>