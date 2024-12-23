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

// Check if search term is provided
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the SQL query based on the search term
$sql = "SELECT orders.*, 
               CONCAT(customer.firstname, ' ', customer.lastname) AS customer_name, 
               customer.address, 
               customer.contact, 
               customer.email 
        FROM orders 
        LEFT JOIN customer ON orders.userid = customer.id"; // Join orders and customers
if (!empty($searchTerm)) {
    $sql .= " WHERE orders.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " ORDER BY orders.order_date DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
    <h1>All Orders</h1>

    <!-- Search form for order ID -->
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search by Order ID" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Grand Total</th>
                    <th>Delivery Info</th>
                    <th>Status</th>
                    <th>Payment Receipt</th>
                    <th>Address</th> 
                    <th>Contact</th> 
                    <th>Email</th> 
                    <th>Action</th>
                    <th>Book Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo $order['userid']; ?></td>
                    <td><?php echo $order['customer_name']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td>Rs <?php echo $order['grand_total']; ?></td>
                    <td>
                    <?php
    
                        $order_id = (int)$order['order_id']; // Casting to integer to prevent SQL injection

                        // Fetch delivery details
                        $delivery_sql = "SELECT * FROM delivery WHERE order_id = $order_id";
                        $result_delivery = $conn->query($delivery_sql);

                        if ($result_delivery) {
                            $delivery_info = $result_delivery->fetch_assoc();

                            if ($delivery_info) {
                                if ($delivery_info['status'] == 'delivered') {
                                    echo "Courier Number: " . htmlspecialchars($delivery_info['courier_number'], ENT_QUOTES, 'UTF-8');
                                } else {
                                    echo "Reason for Rejection: " . htmlspecialchars($delivery_info['reason'], ENT_QUOTES, 'UTF-8');
                                }
                            } else {
                                echo "No delivery info available";
                            }
                        } else {
                            echo "Failed to fetch delivery details";
                        }
                    ?>
                    </td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <?php if ($order['receipt'] != NULL): ?>
                            <a href="../Cart/<?php echo $order['receipt']; ?>" download class="download-btn">Download Receipt</a>
                        <?php else: ?>
                            <span>No receipt uploaded</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $order['address']; ?></td> <!-- New column data -->
                    <td><?php echo $order['contact']; ?></td> <!-- New column data -->
                    <td><?php echo $order['email']; ?></td> <!-- New column data -->
                    <td>
                        <!-- Link to proceed to order processing page -->
                        <a href="process_order.php?order_id=<?php echo $order['order_id']; ?>">Go to Proceed</a>
                    </td>
                    <td>
                    <?php
   
                    $order_id = (int)$order['order_id']; // Casting to integer to prevent SQL injection

                    // Fetch order details (books and quantities)
                    $order_details_sql = "SELECT od.*, b.name AS book_name 
                                        FROM order_items od
                                        JOIN book b ON od.bookid = b.book_id
                                        WHERE od.order_id = $order_id";
                    $result_details = $conn->query($order_details_sql);

                    // Check if there are order details
                    if ($result_details && $result_details->num_rows > 0) {
                        $order_items = [];
                        while ($item = $result_details->fetch_assoc()) {
                            $order_items[] = htmlspecialchars($item['book_name'], ENT_QUOTES, 'UTF-8') . 
                                            " (x" . (int)$item['quantity'] . ")";
                        }
                        echo implode(", ", $order_items); // Display details as comma-separated list
                    } else {
                        echo "No order details found"; // If no rows are returned
                    }
                ?>
    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders available.</p>
    <?php endif; ?>
</div>

</body>
</html>
