<?php
session_start();
@include 'connection.php';

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = null;
}

if (!isset($_GET['product_id'])) {
  echo "No product specified!";
  exit();
}

$product_id = $_GET['product_id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $product = $result->fetch_assoc();
} else {
  echo "Product not found!";
  exit();
}

if (isset($_POST['add_to_cart']) || isset($_POST['buy_now_btn'])) {
  if (!$user_id) {
    header("Location: login.php");
    exit();
  }

  $product_id = mysqli_real_escape_string($conn, $_POST["product_id"]);
  $product_quantity = 1;

  $stmt = $conn->prepare("SELECT * FROM `cart` WHERE product_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $product_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE `cart` SET quantity = quantity + ? WHERE product_id = ? AND user_id = ?");
    $stmt->bind_param("iii", $product_quantity, $product_id, $user_id);
    $stmt->execute();
    $message[] = 'Product quantity updated in cart';
  } else {
    $stmt = $conn->prepare("INSERT INTO `cart` (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $product_quantity);
    $stmt->execute();
    $message[] = 'Product added to cart successfully!';
  }
  $stmt->close();

  if (isset($_POST['buy_now_btn'])) {
    // Redirect to checkout.php
    header("Location: checkout.php");
    exit();
  }
}

// Fetch product reviews
$reviews_sql = "SELECT r.rating, r.comment, u.username FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.product_id = ?";
$stmt = $conn->prepare($reviews_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = [];
if ($reviews_result->num_rows > 0) {
  while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="main.css">
  <title>Product Details</title>
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 16px;
      color: #343a40;
    }
    .product-details {
      margin-top: 30px;
    }
    .main_image img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }
    .option img {
      width: 100px;
      height: auto;
      cursor: pointer;
      margin-right: 10px;
      border-radius: 5px;
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
    .card-footer {
      background: white;
    }
    .more-details {
      margin-top: 20px;
    }
    .reviews h5 {
      margin-top: 30px;
    }
    .review-card {
      margin-bottom: 15px;
    }
    .product-info {
      display: flex;
      justify-content: space-between;
    }
    .product-info .left,
    .product-info .right {
      width: 48%;
    }
    .price {
      font-size: 1.5rem;
      color: #28a745;
    }
    .original-price {
      text-decoration: line-through;
      color: #6c757d;
    }
    .discount {
      color: #dc3545;
    }
    .product-actions {
      margin-top: 20px;
    }
    .product-actions .btn {
      width: 100%;
    }
  </style>
</head>
<body class="bg-light">
  <?php include('includes/navbar.php') ?>

  <!-- Displaying messages if any -->
  <?php
  if (isset($message)) {
    foreach ($message as $msg) {
      echo '
            <div class="alert alert-primary alert-dismissible fade show alert-top" role="alert">
                <span>' . htmlspecialchars($msg) . '</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
  }
  ?>

  <div class="container my-5">
    <div class="row">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="main_image p-3">
            <img src="<?= htmlspecialchars($product['product_img']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>" class="img-fluid">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($product['product_name']); ?></h3>
            <div class="product-info">
              <div class="left">
                <div class="price"><strong>₱<?= number_format($product['price'], 2); ?></strong></div>
                <p class="card-text mt-3"><?= htmlspecialchars($product['description']); ?></p>
                <p class="card-text"><strong>Stock:</strong> <?= htmlspecialchars($product['stock_quantity']); ?></p>
              </div>
              <div class="right text-end">
                <form method="POST" action="" class="product-actions">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                  <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg">Add to Cart</button>
                </form>
                <form method="POST" action="" class="product-actions mt-2">
                  <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                  <button type="submit" name="buy_now_btn" class="btn btn-success btn-lg">Buy Now</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="more-details card shadow-sm mt-4">
          <div class="card-body">
            <h5>More Details</h5>
            <ul class="list-unstyled">
              <li><strong>Category:</strong> <?= htmlspecialchars($product['category_name']); ?></li>
              <li><strong>Model:</strong> <?= htmlspecialchars($product['model']); ?></li>
              <li><strong>Brand:</strong> <?= htmlspecialchars($product['brand']); ?></li>
              <li><strong>Specification:</strong> <?= htmlspecialchars($product['specification']); ?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="reviews mt-5">
      <h5><strong> Reviews</strong></h5>
      <?php
      if (!empty($reviews)) {
        foreach ($reviews as $review) {
          echo '
          <div class="card review-card shadow-sm">
            <div class="card-body">
              <h6 class="card-title">' . htmlspecialchars($review['username']) . '</h6>
              <p class="card-text">Rating: ' . htmlspecialchars($review['rating']) . '/5</p>
              <p class="card-text">' . htmlspecialchars($review['comment']) . '</p>
            </div>
          </div>';
        }
      } else {
        echo '<p>No reviews yet.</p>';
      }
      ?>
    </div>
  </div>
  <!-- Include Bootstrap JavaScript and dependencies (Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"></script>
</body>
</html>
