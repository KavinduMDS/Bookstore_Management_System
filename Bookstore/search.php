<?php

include('connect.php'); // Ensure this file includes your database connection details

$profilePage = "profile.php";?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Search</title>
    <link rel="stylesheet" href="stylesearch.css"> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Bookstore</a>
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
            <a class="nav-item nav-link" href="javascript:history.back()">Go Back</a>
        </div>
    </nav>
    <div class="order-container">
        <h1>Book Search</h1>
        <form method="GET" action="">
            <input type="text" class="search-bar" name="search" placeholder="Search by book name">
            <button type="submit" class="search-button">Search</button>
        </form>

        <table class="order-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                session_start(); // Start the session
                include 'connect.php';

                // Check connection
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Handle search query
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
                    $sql = "SELECT * FROM book WHERE name LIKE '%$search_term%'";
                } else {
                    $sql = "SELECT * FROM book";
                }
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['book_id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['author'] . "</td>";
                        echo "<td>" . $row['price'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>";
                        if ($row['quantity'] == 0) {
                            echo "<p class='out-of-stock'>Out of Stock</p>";
                        } else {
                            ?>
                            <form action="Cart/index.php" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                <button type="submit" class="buy-now">Buy Now</button>
                            </form>
                            <?php
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No results found.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
