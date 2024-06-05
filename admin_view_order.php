<?php
session_start();
include 'connection.php';

// Check if the order_id is set in the URL
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Fetch order details
    $sql = "SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    // Fetch order items
    $sql_items = "SELECT oi.*, p.product_name 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.product_id 
                  WHERE oi.order_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();

    // Fetch GCash payment info if the payment method is GCash
    if ($order['payment_method'] === 'gcash') {
        $sql_gcash = "SELECT * FROM gcash_payments WHERE order_id = ?";
        $stmt_gcash = $conn->prepare($sql_gcash);
        $stmt_gcash->bind_param("i", $order_id);
        $stmt_gcash->execute();
        $result_gcash = $stmt_gcash->get_result();
        $gcash_info = $result_gcash->fetch_assoc();
        $stmt_gcash->close();
    }

    $stmt->close();
    $stmt_items->close();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: adminpage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <?php include 'includes/admin_navbar.php'; ?>
    <div class="container mt-5">
        <a href="admin_orders.php" class="btn btn-primary mb-3"><i class="fa fa-angle-left" aria-hidden="true"></i> Back to Orders</a>
        <h2>Order Details</h2>
        <table class="table table-bordered">
            <tr>
                <th>Order ID</th>
                <td><?php echo $order['order_id']; ?></td>
            </tr>
            <tr>
                <th>Order Date</th>
                <td><?php echo $order['order_date']; ?></td>
            </tr>
            <tr>
                <th>Order Status</th>
                <td><?php echo $order['order_status']; ?></td>
            </tr>
            <tr>
                <th>Shipping Fee</th>
                <td>₱<?php echo $order['shipping_fee']; ?></td>
            </tr>
            <tr>
                <th>Total Price</th>
                <td>₱<?php echo $order['total_price']; ?></td>
            </tr>
            <tr>
                <th>Order Reference Number</th>
                <td><?php echo $order['order_reference_number']; ?></td>
            </tr>
            <tr>
                <th>Tracking Number</th>
                <td><?php echo $order['tracking_number']; ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo $order['username']; ?></td>
            </tr>
        </table>
        
        <?php if ($order['payment_method'] === 'gcash') : ?>
            <h4>GCash Payment Details</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Full Name</th>
                    <td><?php echo $gcash_info['fullname']; ?></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td><?php echo $gcash_info['phone_number']; ?></td>
                </tr>
                <tr>
                    <th>Reference Number</th>
                    <td><?php echo $gcash_info['reference_number']; ?></td>
                </tr>
                <tr>
                    <th>Payment Date</th>
                    <td><?php echo $gcash_info['created_at']; ?></td>
                </tr>
            </table>
        <?php endif; ?>
        
        <h4>Order Items</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($item = $result_items->fetch_assoc()) {
                    echo "<tr>
                        <td>{$item['product_id']}</td>
                        <td>{$item['product_name']}</td>
                        <td>{$item['quantity']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
$conn->close();
?>
