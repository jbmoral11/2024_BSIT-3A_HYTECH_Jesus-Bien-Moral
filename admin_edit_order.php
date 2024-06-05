<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $tracking_number = $_POST['tracking_number'];
    $gcash_confirm = isset($_POST['gcash_confirm']) ? 1 : 0;
    
    
    $sql = "UPDATE orders SET order_status=?, tracking_number=?, gcash_confirm=? WHERE order_id=?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssdi", $order_status, $tracking_number, $gcash_confirm, $order_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Order updated successfully";
        } else {
            $_SESSION['error'] = "Error updating order: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Error preparing statement: " . $conn->error;
    }

    header("Location: admin_orders.php");
    exit();
}

$order_id = $_GET['order_id'];
$sql = "SELECT * FROM orders WHERE order_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
} else {
    $_SESSION['error'] = "Error preparing statement: " . $conn->error;
    header("Location: admin_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <?php include 'includes/admin_navbar.php'; ?>
    <div class="container mt-5">
        <h2>Edit Order</h2>
        <form method="post" action="">
            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
            <div class="form-group">
                <label for="order_status">Order Status</label>
                <select name="order_status" id="order_status" class="form-control">
                    <option value="To Pay" <?php if ($order['order_status'] == 'To Pay') echo 'selected'; ?>>To Pay</option>
                    <option value="To Ship" <?php if ($order['order_status'] == 'To Ship') echo 'selected'; ?>>To Ship</option>
                    <option value="To Receive" <?php if ($order['order_status'] == 'To Receive') echo 'selected'; ?>>To Receive</option>
                    <option value="Completed" <?php if ($order['order_status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Canceled" <?php if ($order['order_status'] == 'Canceled') echo 'selected'; ?>>Canceled</option>
                </select>
            </div>
            <div class="form-group mt-3">
                <label for="tracking_number">Tracking Number</label>
                <input type="text" name="tracking_number" id="tracking_number" class="form-control" value="<?php echo $order['tracking_number']; ?>" required>
            </div>
            <?php if ($order['payment_method'] == 'gcash'): ?>
                <div class="form-group mt-3">
                    <label>GCash Payment Confirmation</label>
                    <div>
                        <button type="button" id="gcash_confirm_button" class="btn btn-outline-primary" data-confirm="<?php echo $order['gcash_confirm']; ?>">
                            <?php echo $order['gcash_confirm'] == 1 ? 'Confirmed' : 'Confirm GCash Payment'; ?>
                        </button>
                        <input type="hidden" name="gcash_confirm" id="gcash_confirm" value="<?php echo $order['gcash_confirm']; ?>">
                    </div>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary mt-3">Update Order</button>
        </form>
        <a href="admin_orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const gcashConfirmButton = document.getElementById('gcash_confirm_button');
            const gcashConfirmInput = document.getElementById('gcash_confirm');

            gcashConfirmButton.addEventListener('click', function () {
                const currentStatus = parseInt(gcashConfirmButton.getAttribute('data-confirm'));
                const newStatus = currentStatus === 1 ? 0 : 1;

                gcashConfirmButton.setAttribute('data-confirm', newStatus);
                gcashConfirmButton.textContent = newStatus === 1 ? 'Confirmed' : 'Confirm GCash Payment';
                gcashConfirmInput.value = newStatus;
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>