<?php
session_start();
include('../connect.php'); // Include your database connection
$profilePage = "../profile.php";

$upload_message = ''; // To display messages related to file upload

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to place an order.');
}

// Proceed to checkout and save order to database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $userid = $_SESSION['user_id'];
    $grand_total = 0;

    // Calculate the grand total
    
    // Handle file upload for payment receipt
    $receipt_path = null;
    if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['payment_receipt']['tmp_name'];
        $imageName = $_FILES['payment_receipt']['name'];
        $uploadFileDir = 'uploadscart/'; // Ensure this directory exists and is writable
        $receipt_path = $uploadFileDir . basename($imageName);
    
        // Move the uploaded file to the target directory
        if (move_uploaded_file($imageTmpPath, $receipt_path)) {
            // File uploaded successfully
        } else {
            // Error uploading file
            $upload_message = 'Error uploading file. Please try again.';
        }
    }
    
    // Proceed with placing the order and inserting into the database
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
        $userid = $_SESSION['user_id'];
        $grand_total = 0;
    
        // Calculate the grand total
        foreach ($_SESSION['cart'] as $book) {
            $grand_total += $book['price'] * $book['quantity'];
        }
    
        // Insert the order into the orders table (directly inserting values)
        if ($receipt_path) {
            $sql_order = "INSERT INTO orders (userid, grand_total, receipt) VALUES ($userid, $grand_total, '$receipt_path')";
        } else {
            $sql_order = "INSERT INTO orders (userid, grand_total, receipt) VALUES ($userid, $grand_total, NULL)";
        }
        
        if ($conn->query($sql_order) === TRUE) {
            $order_id = $conn->insert_id; // Get the newly created order ID
            
            // Insert each cart item into the order_items table
            // Insert each cart item into the order_items table
foreach ($_SESSION['cart'] as $book_id => $book) {
    $book_name = mysqli_real_escape_string($conn, $book['name']);
    $book_price = $book['price'];
    $book_quantity = $book['quantity'];

    $sql_order_item = "INSERT INTO order_items (order_id, bookid, name, price, quantity) 
                       VALUES ($order_id, $book_id, '$book_name', $book_price, $book_quantity)";
    if (!$conn->query($sql_order_item)) {
        die('Error inserting order item: ' . $conn->error);
    }

    $sql_update_quantity = "UPDATE book SET quantity = quantity - $book_quantity WHERE book_id = $book_id";
    if (!$conn->query($sql_update_quantity)) {
        die('Error updating book quantity: ' . $conn->error);
    }
}

            // Clear the cart after placing the order
            $_SESSION['cart'] = [];
            $upload_message = 'Order placed successfully!';
        } else {
            $upload_message = 'Failed to place the order. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS file for styling -->
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


<div class="cart-container">
    <h1>Your Cart</h1>
    
    <?php if (!empty($_SESSION['cart'])): ?>
      
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <?php if (!empty($upload_message)): ?>
        <p><?php echo htmlspecialchars($upload_message); ?></p>
    <?php endif; ?>
</div>

</body>
</html>
