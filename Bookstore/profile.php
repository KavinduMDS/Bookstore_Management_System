<?php
session_start();
include 'connect.php'; // Include your database connection file

// Initialize variables
$firstname = "Guest"; // Default value if user is not logged in
$lastname = ""; // Default value if user is not logged in

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Get user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Fetch user name from database
    $sql = "SELECT firstname, lastname FROM customer WHERE id = '$user_id'";
    $result = $conn->query($sql);

    // Check if query was successful and fetch the data
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $firstname = htmlspecialchars($row['firstname']); // Sanitize output
        $lastname = htmlspecialchars($row['lastname']); // Sanitize output
    } else {
        echo "No user found or unable to fetch user details.";
        exit();
    }
} else {
    // Redirect to index page if not logged in
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="update_profile/index.php">MY PROFILE</a></li>
                <li><a href="messages/index.php">MESSAGES</a></li>
                <li><a href="customer_reply/index.php">Message from Employee</a></li>
                <li><a href="orderview/customer_order.php">Order History</a></li>
                <li><a href="book_page/index.php">Buy Now</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <div class="welcome">
                    <span>Welcome, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></span>
                </div>
                <form method="POST" action="logout.php">
                    <button type="submit" class="logout">LOG OUT</button>
                </form>
            </header>
            
            <footer>
            <p>Telephone Number:0779128882</p>
            <p>Address:22,Highlevel Road, Maharagama</p>
            </footer>
        </div>
    </div>
</body>
</html>
