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
    $stmt = $conn->prepare("SELECT firstname, lastname FROM employee WHERE EID = ?");
    $stmt->bind_param("i", $user_id); // Assuming EID is an integer
    $stmt->execute();
    $result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $firstname = htmlspecialchars($row['firstname']);
    $lastname = htmlspecialchars($row['lastname']);
} else {
    echo "No user found or unable to fetch user details.";
    exit();
}
$stmt->close();
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
                
                <li><a href="book/index.php">ADD/UPDATE ITEM</a></li>
                
                
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
