<?php
session_start();
include('../connect.php'); // Ensure this file includes your database connection details

$profilePage = "../profile.php";

// Check if the user is logged in and fetch their data
if (isset($_SESSION['user_id'])) {
    // Fetch user details from the database
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM customer WHERE id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        // Handle the case where user is not found in the database
        echo "User not found!";
        exit;
    }
} else {
    // Handle the case where user is not logged in
    echo "Please log in first!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address'], $_POST['contact'], $_POST['email'])) {
    // Get the updated user data from the form
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    // Validate the input
    if (!empty($address) && !empty($contact) && !empty($email)) {
        // Update the user information in the database
        $updateSql = "UPDATE customer SET address = '$address', contact = '$contact', email = '$email' WHERE id = $user_id";
        if ($conn->query($updateSql)) {
            // Update was successful, set success message
            $success_message = "Your information has been updated successfully.";

            // Refresh the user data
            $user['address'] = $address;
            $user['contact'] = $contact;
            $user['email'] = $email;
        } else {
            // Handle update failure
            $error_message = "Failed to update information. Please try again.";
        }
    } else {
        $error_message = "Please fill in all the fields.";
    }
}

// Initialize cart in session if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// Update item quantity in cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity']) && isset($_POST['book_id']) && isset($_POST['quantity'])) {
    $book_id = $_POST['book_id'];
    $new_quantity = (int)$_POST['quantity'];

    // Fetch the current stock and book name for the book
    $stockCheckSql = "SELECT name, quantity FROM book WHERE book_id = $book_id";
    $stockResult = $conn->query($stockCheckSql);

    if ($stockResult && $stockResult->num_rows > 0) {
        $stockData = $stockResult->fetch_assoc();
        $available_stock = (int)$stockData['quantity'];
        $book_name = htmlspecialchars($stockData['name']); // Use the book name for user-friendly messages

        if ($new_quantity <= $available_stock) {
            // Update quantity in session if it's within stock
            $_SESSION['cart'][$book_id]['quantity'] = $new_quantity;
        } else {
            // If requested quantity exceeds stock, set to max available and show error
            $_SESSION['cart'][$book_id]['quantity'] = $available_stock;
            $update_err = "Stock available for '$book_name' is only $available_stock. Quantity updated to match the stock.";
        }
    } else {
        $update_err = "Failed to fetch stock information for the selected book.";
    }
}


// Add book to cart when coming from the book page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_quantity']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $quantity = 1; // Default quantity to 1

    // Check if book is already in the cart
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]['quantity'] += 1; // Increment quantity if it exists
    } else {
        // Fetch book details from the database
        $sql = "SELECT * FROM book WHERE book_id = $book_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $_SESSION['cart'][$book_id] = [
                'name' => $book['name'],
                'price' => $book['price'],
                'image_path' => $book['image_path'],
                'quantity' => $quantity,
            ];
        }
    }
}

// Remove item from cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    unset($_SESSION['cart'][$book_id]);
}

// Handle payment receipt upload and clear user info on checkout
if (isset($_POST['checkout'])) {
    // Check if the payment receipt file is uploaded
    if (empty($_FILES['payment_receipt']['name'])) {
        $error_message = "Please upload a payment receipt.";
    } else {
        // File is present, proceed with the checkout process
        // Update book quantities in the database
        foreach ($_SESSION['cart'] as $book_id => $book) {
            $new_quantity = $book['quantity'];

            // Update the book's quantity in the database
            $updateSql = "UPDATE book SET quantity = quantity - $new_quantity WHERE book_id = $book_id";
            if (!$conn->query($updateSql)) {
                // Handle the error if the update fails
                $error_message = "Failed to update the quantity of books. Please try again.";
                break;
            }
        }

        // Clear customer information (address, contact, email) after checkout
        $updateSql = "UPDATE customer SET address=NULL, contact=NULL, email=NULL WHERE id=$user_id";
        $conn->query($updateSql);

        // Clear the cart session
        unset($_SESSION['cart']);

        // Assuming payment receipt logic is handled here
        $upload_message = "Your payment receipt has been uploaded and your information has been cleared.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Cart</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the CSS file for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../index.php">Bookstore</a>
        <div class="navbar-nav ml-auto">
            <a class="nav-item nav-link" href="<?php echo $profilePage; ?>">Profile</a>
            <a class="nav-item nav-link" href="javascript:history.back()">Go Back</a>
            <a class="nav-item nav-link" href="../book_page/index.php">Buy More</a>
        </div>
    </nav>



<div class="form-container">
    <h2>Edit Your Information</h2>
    <form action="" method="POST">
        <label>Address:</label>
        <textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
        <label>Contact:</label>
        <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" >
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" >
        <button type="submit">Update Information</button>
    </form>
    
    <?php if (isset($success_message)): ?>
            <div style="color: green; font-weight: bold; margin-top: 10px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div style="color: red; font-weight: bold; margin-top: 10px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
</div>

<div class="cart-container">
    <h1>Your Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                foreach ($_SESSION['cart'] as $book_id => $book): 
                    $total_price = $book['price'] * $book['quantity'];
                    $grand_total += $total_price;
                ?>
                    <tr>
                        <td><img src="../Book/book_image/<?php echo htmlspecialchars($book['image_path']); ?>" alt="<?php echo htmlspecialchars($book['name']); ?>" class="cart-book-image"></td>
                        <td><?php echo htmlspecialchars($book['name']); ?></td>
                        <td>Rs<?php echo htmlspecialchars($book['price']); ?></td>
                        <td>
                            <form action="index.php" method="POST" class="update-quantity-form">
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($book['quantity']); ?>" min="1">
                                <input type="hidden" name="update_quantity" value="1">
                                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                                <button type="submit" class="update-btn">Update</button>
                                <?php if (isset($update_err)): ?>
                                    <div style="color: red; font-weight: bold; margin-top: 10px;">
                                     <?php echo $update_err; ?>
                                    </div>
                                    <?php endif; ?>
                            </form>
                        </td>
                        <td>Rs<?php echo $total_price; ?></td>
                        <td><a href="index.php?action=remove&book_id=<?php echo $book_id; ?>" class="remove-btn">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="grand-total-label">Grand Total</td>
                    <td>Rs<?php echo $grand_total; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4" class="upload-receipt-label">Upload Payment Receipt Account No:824122 BOC: Balangoda</td>
                    <td colspan="2">
                        <form action="cart.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="payment_receipt" id="payment_receipt" required />
                            <button type="submit" name="checkout">Proceed to Checkout</button>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

</body>
</html>
