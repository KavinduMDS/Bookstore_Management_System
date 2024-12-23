<?php
session_start();
include('../connect.php'); // Include your database connection

if (!isset($_SESSION['user_role'])) {
    // Redirect to login page if not logged in
    header("Location: ../memberlogin.php");
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

// Check if an order ID is provided
if (!isset($_GET['order_id'])) {
    die('Order ID not specified.');
}

$order_id = (int)$_GET['order_id']; // Cast to integer for safety

// Fetch order details
$sql = "SELECT * FROM orders WHERE order_id = $order_id";
$order_result = $conn->query($sql);
$order = $order_result->fetch_assoc();

// Handle the order status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_order'])) {
    $status = $conn->real_escape_string($_POST['status']);
    $reason = ($status === 'Rejected') ? $conn->real_escape_string($_POST['reason']) : null;
    $courier_number = ($status === 'Delivered') ? $conn->real_escape_string($_POST['courier_number']) : null;

    // Check if a delivery entry already exists for this order
    $check_sql = "SELECT * FROM delivery WHERE order_id = $order_id";
    $result_check = $conn->query($check_sql);

    if ($result_check->num_rows > 0) {
        // Update existing delivery record
        $update_delivery_sql = "UPDATE delivery 
                                SET status = '$status', 
                                    reason = " . ($reason ? "'$reason'" : "NULL") . ", 
                                    courier_number = " . ($courier_number ? "'$courier_number'" : "NULL") . " 
                                WHERE order_id = $order_id";
        $conn->query($update_delivery_sql);
    } else {
        // Insert new delivery record
        $insert_delivery_sql = "INSERT INTO delivery (order_id, status, reason, courier_number) 
                                VALUES ($order_id, '$status', " . ($reason ? "'$reason'" : "NULL") . ", " . ($courier_number ? "'$courier_number'" : "NULL") . ")";
        $conn->query($insert_delivery_sql);
    }

    // Update the order status in the orders table
    $update_order_sql = "UPDATE orders SET status = '$status' WHERE order_id = $order_id";
    $conn->query($update_order_sql);

    echo "<p>Order status updated successfully.</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Order #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="style.css">
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


<div class="order-container">
    <h1>Process Order #<?php echo $order_id; ?></h1>
    <p><strong>Customer ID:</strong> <?php echo $order['userid']; ?></p>
    <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
    <p><strong>Grand Total:</strong> Rs <?php echo $order['grand_total']; ?></p>

    <form action="process_order.php?order_id=<?php echo $order_id; ?>" method="POST">
        <label for="status">Update Status:</label>
        <select id="status" name="status" onchange="toggleFields()" required>
            <option value="">Select Status</option>
            <option value="Delivered">Mark as Delivered</option>
            <option value="Rejected">Reject Order</option>
            <option value="Deliver Home">Deliver Home</option>
        </select><br><br>

        <!-- Field for courier number, shown if "Delivered" is selected -->
        <div id="courierField" style="display: none;">
            <label for="courier_number">Courier Number:</label>
            <input type="text" name="courier_number" placeholder="Enter courier number">
        </div>

        <!-- Field for rejection reason, shown if "Rejected" is selected -->
        <div id="reasonField" style="display: none;">
            <label for="reason">Rejection Reason:</label>
            <textarea name="reason" placeholder="Enter reason for rejection"></textarea>
        </div><br>

        <button type="submit" name="update_order">Update Order</button>
    </form>
</div>

<script>
function toggleFields() {
    var status = document.getElementById('status').value;
    document.getElementById('courierField').style.display = status === 'Delivered' ? 'block' : 'none';
    document.getElementById('reasonField').style.display = status === 'Rejected' ? 'block' : 'none';

    // Set required attribute for reason only when Rejected is selected
    document.getElementById('reasonField').querySelector('textarea').required = (status === 'Rejected');
}
</script>

</body>
</html>
