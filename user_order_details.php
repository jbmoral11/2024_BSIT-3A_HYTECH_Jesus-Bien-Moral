<?php
session_start(); // Start the session

@include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: orders.php"); // Redirect back if order_id is not provided
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

// Fetch order details including tracking number and payment method
$sql = "SELECT 
            o.order_id,
            o.order_date,
            o.order_status,
            o.shipping_fee,
            o.total_price,
            o.order_reference_number,
            o.tracking_number,
            o.payment_method,
            p.product_id,
            p.product_name,
            p.price,
            oi.quantity,
            p.product_img
        FROM 
            orders o
        JOIN 
            order_items oi ON o.order_id = oi.order_id
        JOIN 
            products p ON oi.product_id = p.product_id
        WHERE 
            o.order_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Details</title>
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="main.css">

    </head>
    <style>
     
        .container{
            padding-left:80px;
        }
    </style>
    <body>
        <?php include('includes/navbar.php') ?>

        <div class="container mt-5">
            <h2 class="mb-4">Order Details</h2>
            <div class="mb-4">
            <a href="user_orders.php" class="btn btn-secondary">
                < Return
            </a>
        </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Order Reference Number: <?php echo $order['order_reference_number']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Order Date: <?php echo date('F j, Y', strtotime($order['order_date'])); ?></h6>
                    <p class="card-text">Status: <?php echo $order['order_status']; ?></p>
                    <p class="card-text">Total Price: ₱<?php echo number_format($order['total_price'], 2); ?></p>
                    <p class="card-text">Shipping Fee: ₱<?php echo number_format($order['shipping_fee'], 2); ?></p>
                    <p class="card-text">Tracking Number: <?php echo $order['tracking_number']; ?></p>
                    <p class="card-text">Payment Method: <?php echo $order['payment_method']; ?></p>
                </div>
            </div>
        </div>
        <div class="container mt-3">
            <!-- Product details -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 d-flex align-items-center">
                                    <img src="<?php echo $order['product_img']; ?>" alt="Product Image" class="card-img-top custom-image-size" style="width: 200px; height: auto; ">
                                </div>
                                <div class="col-md-8 d-flex align-items-center justify-content-center">
                                    <div class="container">
                                        <h4 class="card-title"><?php echo $order['product_name']; ?></h4>
                                        <p class="card-text">Price: $<?php echo number_format($order['price'], 2); ?></p>
                                        <p class="card-text">Quantity: <?php echo $order['quantity']; ?></p>
                                        <form id="reviewForm" method='get' action="user_review.php">
                                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($order['product_id']); ?>">
                                            <button id="addReviewBtn" type="submit" class='btn btn-primary btn-sm'>Add Review</button>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
                            <p id="reviewMessage" class="alert alert-warning d-none alert-dismissible fade show alert-top" role="alert">Order is not yet completed.</p> 
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('addReviewBtn').addEventListener('click', function(event) {
                if ('<?php echo $order['order_status']; ?>' !== 'Completed') {
                    event.preventDefault();
                    document.getElementById('reviewMessage').classList.remove('d-none');
                }
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"></script>
    </body>

    </html>
<?php
} else {
    // Order not found, handle the error
    echo "Order not found.";
}
$conn->close();
?>