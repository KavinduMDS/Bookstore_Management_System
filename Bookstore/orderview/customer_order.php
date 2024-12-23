<?php
session_start();
include('../connect.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to view your orders.');
}
$profilePage = "../profile.php";

$user_id = $_SESSION['user_id'];

// Fetch orders placed by the logged-in customer
$sql = "SELECT o.*, oi.name AS book_name, oi.price AS book_price, oi.quantity AS book_qty
        FROM orders o
        INNER JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.userid = ?
        ORDER BY o.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
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
    <h1>Your Orders</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th>Delivery Info</th>
                    <th>Ordered Books</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                
                $orders = [];
                while ($order = $result->fetch_assoc()) {
                    $order_id = $order['order_id'];
                
                    if (!isset($orders[$order_id])) {
                        $orders[$order_id] = $order;
                        $orders[$order_id]['items'] = [];
                    }
                
                    $orders[$order_id]['items'][] = [
                        'name' => $order['book_name'],
                        'price' => $order['book_price'],
                        'quantity' => $order['book_qty']
                    ];
                }
                
                foreach ($orders as $order) {
                 ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td>Rs <?php echo $order['grand_total']; ?></td>
                    <td class="<?php echo ($order['status'] == 'Delivered') ? 'status-delivered' : (($order['status'] == 'Rejected') ? 'status-rejected' : ''); ?>">
                     <?php echo $order['status']; ?>
                        </td>
                    <td>
                        
                    <?php
                    // Sanitize order_id
                    $order_id = (int)$order['order_id']; // Casting to integer to prevent SQL injection

                    // Fetch delivery details
                    $delivery_sql = "SELECT * FROM delivery WHERE order_id = $order_id";
                    $result_delivery = $conn->query($delivery_sql);

                    if ($result_delivery) {
                        $delivery_info = $result_delivery->fetch_assoc();

                        if ($delivery_info) {
                            if ($delivery_info['status'] == 'delivered') {
                                echo "Courier Number: " . htmlspecialchars($delivery_info['courier_number'], ENT_QUOTES, 'UTF-8');
                            } elseif ($delivery_info['status'] == 'rejected') {
                                echo "Reason for Rejection: " . htmlspecialchars($delivery_info['reason'], ENT_QUOTES, 'UTF-8');
                            }
                        }
                    }
                    ?>
                    </td>
                    <td>
                    <?php
                    foreach ($order['items'] as $item) {
                        echo $item['name'] . ' (Rs ' . $item['price'] . ' x ' . $item['quantity'] . ')<br>';
                    }
                ?>
  </td>
                </tr>
                <?php
            }
            ?>
                
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
