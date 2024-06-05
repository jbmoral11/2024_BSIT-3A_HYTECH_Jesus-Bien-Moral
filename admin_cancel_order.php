<?php
session_start();
include 'connection.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Update the order status to 'Canceled'
    $sql = "UPDATE orders SET order_status = 'Canceled' WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order has been canceled successfully.";
    } else {
        $_SESSION['error'] = "Failed to cancel the order. Please try again.";
    }
    
    $stmt->close();
    $conn->close();
}

header("Location: admin_orders.php");
exit();
?>
