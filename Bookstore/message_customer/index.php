<?php
session_start();
include '../connect.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['user_role'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Define profile page based on user role
$profilePage = "profile.php"; // Default for customer
if ($_SESSION['user_role'] === "admin") {
    $profilePage = "../profile_admin.php";
} elseif ($_SESSION['user_role'] === "order") {
    $profilePage = "../profile_order.php";
} elseif ($_SESSION['user_role'] === "inventory") {
    $profilePage = "../profile_inventory.php";
}



// Fetch all feedback messages from customers, ordered by fid in descending order
$sql = "SELECT feedback.fid, feedback.subject, feedback.description, feedback.reply, customer.firstname, customer.lastname
        FROM feedback
        JOIN customer ON feedback.sender_id = customer.id
        ORDER BY feedback.fid DESC"; // Ordering by fid in descending order
$result = $conn->query($sql);

// Check for form submission to save the reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fid']) && isset($_POST['reply'])) {
    $fid = $_POST['fid'];
    $reply = $_POST['reply'];

    // Update the reply in the feedback table
    $update_sql = "UPDATE feedback SET reply = '$reply' WHERE fid = $fid"; // Direct insertion without prepared statement
    if ($conn->query($update_sql) === TRUE) {
        // Redirect to the same page with a success parameter
        header("Location: ".$_SERVER['PHP_SELF']."?reply=success");
        exit();
    } else {
        echo "<p>Error sending reply.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: 20px auto; }
        .message-box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .reply-form { display: none; margin-top: 15px; }
        button { background-color: #200393; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #200380; }
        .no-reply { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        // Function to toggle reply form visibility
        function toggleReplyForm(fid) {
            var form = document.getElementById("reply-form-" + fid);
            form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
        }
    </script>
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
        <h2>Customer Messages</h2>
        
        <!-- Success message display -->
        <?php if (isset($_GET['reply']) && $_GET['reply'] == 'success'): ?>
            <p class="success">Reply sent successfully!</p>
        <?php endif; ?>
        
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="message-box">
                <p><strong>From:</strong> <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></p>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                
                <p><strong>Reply:</strong> 
                    <?php if ($row['reply']): ?>
                        <?php echo nl2br(htmlspecialchars($row['reply'])); ?>
                    <?php else: ?>
                        <span class="no-reply">No reply yet.</span>
                    <?php endif; ?>
                </p>
                
                <!-- Reply button -->
                <button onclick="toggleReplyForm(<?php echo $row['fid']; ?>)">Reply</button>
                
                <!-- Reply form -->
                <div class="reply-form" id="reply-form-<?php echo $row['fid']; ?>">
                    <form method="POST" action="">
                        <input type="hidden" name="fid" value="<?php echo $row['fid']; ?>">
                        <textarea name="reply" rows="3" placeholder="Type your reply here..." required></textarea><br>
                        <button type="submit">Send Reply</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>

        <?php $conn->close(); ?>
    </div>
</body>
</html>
