<?php
session_start();
include '../connect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Get the logged-in user ID from session
$sender_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $subject = $_POST['subject'];
    $description = $_POST['message'];

    // Prepare the SQL query to insert feedback
    $sql = "INSERT INTO feedback (sender_id, subject, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters to prevent SQL injection
        $stmt->bind_param("iss", $sender_id, $subject, $description);
        
        // Execute the query
        if ($stmt->execute()) {
            echo "Feedback submitted successfully!";
            header("Location: index.php"); // Redirect back to the messages page
            exit();
        } else {
            echo "Error submitting feedback: " . $conn->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
