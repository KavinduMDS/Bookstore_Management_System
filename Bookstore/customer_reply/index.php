<?php
session_start();
include '../connect.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$profilePage = "../profile.php";

// Get the logged-in customer's ID
$user_id = intval($_SESSION['user_id']); // Ensure $user_id is an integer

// Fetch feedback messages sent by this customer only, sorted in descending order of feedback ID
$sql = "SELECT fid, subject, description, reply FROM feedback WHERE sender_id = $user_id ORDER BY fid DESC";
$result = $conn->query($sql);

// Ensure the query executed successfully
if (!$result) {
    die("Error fetching feedback: " . $conn->error);
}

// You do not need to close a statement when not using prepared statements
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: 20px auto; }
        .message-box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .no-reply { color: red; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Bookstore</a>
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
            <a class="nav-item nav-link" href="javascript:history.back()">Go Back</a>
        </div>
    </nav>
    <div class="container">
        <h2>My Messages</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message-box">
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    <p><strong>Reply:</strong> 
                        <?php if ($row['reply']): ?>
                            <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
                        <?php else: ?>
                            <span class="no-reply">No reply yet.</span>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No messages sent yet.</p>
        <?php endif; ?>

        
    </div>
</body>
</html>
