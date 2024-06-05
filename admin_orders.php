<?php
session_start();
include 'connection.php';

$order_status = 'All';
if (isset($_GET['status'])) {
    $order_status = $_GET['status'];
}

if ($order_status == 'All') {
    $sql = "SELECT o.order_id, o.order_date, o.order_status, o.payment_method, o.total_price, o.order_reference_number, u.username, 
            GROUP_CONCAT(p.product_name SEPARATOR ', ') AS ordered_products, 
            GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS ordered_quantities
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            GROUP BY o.order_id
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT o.order_id, o.order_date, o.order_status, o.payment_method, o.total_price, o.order_reference_number, u.username, 
            GROUP_CONCAT(p.product_name SEPARATOR ', ') AS ordered_products, 
            GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS ordered_quantities
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            WHERE o.order_status = ?
            GROUP BY o.order_id
            ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_status);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<style>
    html, body {
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
    <?php include 'includes/admin_navbar.php' ?>

    <div class="container mt-5">
        <div class="text-center my-5">
            <h1>Order Management</h1>
            <hr />
        </div>
        <ul class="nav nav-tabs mt-4">
            <li class="nav-item">
                <a class="nav-link <?= $order_status == 'All' ? 'active' : '' ?>" href="?status=All">All Orders</a>
            </li>
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
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-success alert-dismissible fade show alert-top" role="alert">' . $_SESSION['message'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
                unset($_SESSION['message']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show alert-top" role="alert">' . $_SESSION['error'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['error']);
            }

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
                                <div class='col-md-8'>
                                    <h5 class='card-subtitle mb-4 text-muted'>#<?php echo $row["order_reference_number"]; ?></h6>
                                    <h6 class='card-subtitle'>Date Ordered: <?php echo $order_date; ?></h6>
                                    <h6 class='card-subtitle'>Ordered by: <?php echo $row["username"]; ?></h6>
                                </div>
                                <div class='col-md-4 text-end'>
                                    <p class='card-text'>Total: ₱<?php echo number_format($row["total_price"], 2); ?></p>
                                    <p class='card-text'>Status: <?php echo $row["order_status"]; ?></p>
                                    <p class='card-text'>Payment Method: <?php echo $row["payment_method"]; ?></p>
                                    <div class='justify-content-end'>
                                        <a href='admin_view_order.php?order_id=<?php echo $row["order_id"]; ?>' class='btn btn-info btn-sm text-white me-2'><i class='fa fa-eye' aria-hidden='true'></i> View</a>
                                        <a href='admin_edit_order.php?order_id=<?php echo $row["order_id"]; ?>' class='btn btn-warning btn-sm text-white me-2'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> Edit</a>
                                        <?php if ($row["order_status"] == 'To Pay' || $row["order_status"] == 'To Ship') { ?>
                                            <a href='admin_cancel_order.php?order_id=<?php echo $row["order_id"]; ?>' class='btn btn-danger btn-sm text-white me-2'><i class='fa fa-times' aria-hidden='true'></i> Cancel</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                    ob_end_flush(); // Output buffered card
                }
                echo "</div>"; // Close the last order-month div
            } else {
                echo "<p>No orders found</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
