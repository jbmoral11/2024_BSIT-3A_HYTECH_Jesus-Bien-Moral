<?php
session_start();
@include 'connection.php';

$messages = []; // Initialize an array to store messages

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header("Location: login.php");
    exit;
}

// Handle order cancellation
if (isset($_POST['cancel_order_id'])) {
    $cancel_order_id = $_POST['cancel_order_id'];

    // Update order status to 'Canceled' if not already shipped
    $update_sql = "UPDATE orders SET order_status = 'Canceled' WHERE order_id = ? AND user_id = ? AND order_status IN ('To Pay', 'To Ship')";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $cancel_order_id, $user_id);
    if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
        $messages[] = "Order Cancelled Successfully";
    } else {
        $error[] = "Failed to Cancel Order";
    }
    $update_stmt->close();
}

// Handle order received
if (isset($_POST['receive_order_id'])) {
    $receive_order_id = $_POST['receive_order_id'];

    // Update order status to 'Completed' if the status is 'To Receive'
    $update_sql = "UPDATE orders SET order_status = 'Completed' WHERE order_id = ? AND user_id = ? AND order_status = 'To Receive'";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $receive_order_id, $user_id);
    if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
        $messages[] = "Order Marked As Received";
    } else {
        $error[] = "Failed to Mark Order as Received";
    }
    $update_stmt->close();
}

// Determine the order status filter based on the selected tab
$order_status = 'To Pay';
if (isset($_GET['status'])) {
    $order_status = $_GET['status'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<style>
    html,
    body {
        height: 100%;
    }

    body {
        background: #f8f9fa;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 14px;
        color: #000;
        margin: 0;
        padding: 0;
    }

    .order-history {
        padding: 20px;
    }

    .order-item {
        margin-bottom: 20px;
    }

    .content {
        margin: auto;
        padding: 20px;
        max-width: 1000px;
    }

    .order-history {
        background: #fff;
        padding: 20px;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .order-history h2 {
        font-size: 2em;
        margin-bottom: 20px;
        text-align: center;
    }

    .order-month {
        margin-bottom: 20px;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .order-item h4 {
        margin: 0;
        font-size: 1.2em;
    }

    .order-item span {
        font-size: 0.9em;
        color: #666;
    }

    .order-details {
        text-align: right;
    }

    .nav-tabs {
        border-bottom: none;
    }

    .alert-top {
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 600px;
        z-index: 1050;
        text-align: center;
    }
</style>

<body>
    <?php include('includes/navbar.php') ?>

    <div class="container">
        <ul class="nav nav-tabs mt-4">
            <li class="nav-item">
                <a class="nav-link <?= $order_status == 'To Pay' ? 'active' : '' ?>" href="?status=To Pay">To Pay</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $order_status == 'To Ship' ? 'active' : '' ?>" href="?status=To Ship">To Ship</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $order_status == 'To Receive' ? 'active' : '' ?>" href="?status=To Receive">To Receive</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $order_status == 'Completed' ? 'active' : '' ?>" href="?status=Completed">Completed</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $order_status == 'Canceled' ? 'active' : '' ?>" href="?status=Canceled">Canceled</a>
            </li>
        </ul>

        <div class="order-history">
            <h2>Order History - <?= $order_status ?></h2>

            <?php
            // Output stored messages
            if (isset($messages)) {
                foreach ($messages as $msg) {
                    echo '
                        <div class="alert alert-success alert-dismissible fade show alert-top" role="alert">
                            <span>' . htmlspecialchars($msg) . '</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                }
            }
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '
                        <div class="alert alert-danger alert-dismissible fade show alert-top" role="alert">
                            <span>' . htmlspecialchars($error) . '</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                }
            }

            // SQL query to fetch orders for the specific user based on the selected status
            $sql = "SELECT 
                        o.order_id,
                        o.order_date,
                        o.order_status,
                        o.shipping_fee,
                        o.total_price,
                        o.order_reference_number,
                        COUNT(oi.order_item_id) AS total_items
                    FROM 
                        orders o
                    JOIN 
                        order_items oi ON o.order_id = oi.order_id
                    JOIN 
                        products p ON oi.product_id = p.product_id
                    WHERE 
                        o.user_id = ? AND o.order_status = ?
                    GROUP BY 
                        o.order_id
                    ORDER BY 
                        o.order_date DESC";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $user_id, $order_status);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if any records were found
            if ($result->num_rows > 0) {
                $current_month = '';
                ob_start(); // Start output buffering
                while ($row = $result->fetch_assoc()) {
                    $order_month = date('F Y', strtotime($row["order_date"]));
                    $order_date = date('F j, Y', strtotime($row["order_date"])); // Format the order date
                    if ($current_month != $order_month) {
                        if ($current_month != '') {
                            echo "</div>";
                        }
                        $current_month = $order_month;
                        echo "<h4 class='mt-4'>" . $current_month . "</h4>";
                        echo "<div class='order-month'>";
                    }
                    ob_start(); // Start buffering for card
            ?>
                    <div class='card order-item'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-md-6'>
                                    <h4 class='card-title'>Order #<?= htmlspecialchars($row["order_reference_number"]) ?></h4>
                                    <p class='card-text'>
                                        <strong>Order Date:</strong> <?= htmlspecialchars($order_date) ?><br>
                                        <strong>Status:</strong> <?= htmlspecialchars($row["order_status"]) ?><br>
                                    </p>
                                </div>
                                <div class='col-md-6 d-flex flex-column align-items-end'>
                                    <div class='text-end mb-3'>
                                        <strong>Total Items:</strong> <?= htmlspecialchars($row["total_items"]) ?><br>
                                        <strong>Shipping Fee:</strong> ₱<?= number_format(htmlspecialchars($row["shipping_fee"]), 2) ?><br>
                                        <strong>Total Price:</strong> ₱<?= number_format(htmlspecialchars($row["total_price"]), 2) ?><br>
                                    </div>
                                    <form method='post'>
                                        <a href='user_order_details.php?order_id=<?php echo $row["order_id"]; ?>' class='btn btn-primary btn-sm'>View Details</a>
                                        <?php if (in_array($row["order_status"], ['To Pay', 'To Ship'])) { ?>
                                            <input type='hidden' name='cancel_order_id' value='<?php echo $row["order_id"]; ?>'>
                                            <button type='submit' class='btn btn-danger btn-sm'>Cancel</button>
                                        <?php } elseif ($row["order_status"] == 'To Receive') { ?>
                                            <input type='hidden' name='receive_order_id' value='<?php echo $row["order_id"]; ?>'>
                                            <button type='submit' class='btn btn-success btn-sm'>Mark as Received</button>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                    ob_end_flush(); // Flush the card content
                }
                echo "</div>";
            } else {
                echo "<p>No orders found for this status.</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"></script>
</body>

</html>
