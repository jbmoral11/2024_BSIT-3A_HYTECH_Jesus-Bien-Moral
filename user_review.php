<?php
session_start();
@include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    if (empty($comment)) {
        $_SESSION['error_message'] = "Comment cannot be empty.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?product_id=" . $product_id);
        exit;
    }

    $product_check_stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE product_id = ?");
    $product_check_stmt->bind_param("i", $product_id);
    $product_check_stmt->execute();
    $product_check_stmt->bind_result($product_exists);
    $product_check_stmt->fetch();
    $product_check_stmt->close();

    if ($product_exists) {
        $order_check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE user_id = ? AND order_status = 'Completed'");
        $order_check_stmt->bind_param("i", $user_id);
        $order_check_stmt->execute();
        $order_check_result = $order_check_stmt->get_result();

        $order_ids = [];
        while ($order_row = $order_check_result->fetch_assoc()) {
            $order_ids[] = $order_row['order_id'];
        }

        $order_check_stmt->close();

        $product_ordered = false;
        foreach ($order_ids as $order_id) {
            $product_order_check_stmt = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ? AND product_id = ?");
            $product_order_check_stmt->bind_param("ii", $order_id, $product_id);
            $product_order_check_stmt->execute();
            $product_order_check_stmt->bind_result($product_ordered_count);
            $product_order_check_stmt->fetch();
            $product_order_check_stmt->close();

            if ($product_ordered_count > 0) {
                $product_ordered = true;
                break;
            }
        }

        if ($product_ordered) {
            $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, date_added) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Review submitted successfully.";
                } else {
                    $_SESSION['error_message'] = "Error submitting review: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "You cannot review a product you haven't ordered yet.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid product ID.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?product_id=" . $product_id);
    exit;
}

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $stmt = $conn->prepare("SELECT users.username, reviews.rating, reviews.comment, reviews.date_added FROM reviews JOIN users ON reviews.user_id = users.user_id WHERE reviews.product_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reviews = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $reviews = [];
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No product ID provided.";
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        .container {
            margin-top: 30px;
        }

        .review-item {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .rating {
            color: #ffc107;
        }

        .review-form {
            margin-top: 30px;
        }

        .review-form textarea {
            resize: none;
        }
        .alert-top {
        position: fixed;
        top: 110px;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 600px;
        z-index: 1050;
        text-align: center;
    }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <h1 class="mb-4">Product Reviews</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible alert-top">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible alert-top">
                <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="mb-4">
            <a href="user_orders.php?product_id=<?php echo htmlspecialchars($product_id); ?>" class="btn btn-secondary">
                < Return
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php if (count($reviews) > 0): ?>
                    <ul class="list-unstyled">
                        <?php foreach ($reviews as $row): ?>
                            <li class="review-item">
                                <h5><?php echo htmlspecialchars($row['username'] ?? 'Unknown user'); ?></h5>
                                <div class="rating">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fa fa-star<?php echo $i < $row['rating'] ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p><?php echo htmlspecialchars($row['comment'] ?? 'No comment'); ?></p>
                                <small class="text-muted"><?php echo htmlspecialchars($row['date_added'] ?? 'No date'); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No reviews yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card review-form">
            <div class="card-header">
                <h2>Submit a Review</h2>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating:</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment:</label>
                        <textarea name="comment" id="comment" rows="4" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"></script>
</body>
</html>
