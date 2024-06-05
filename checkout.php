<?php
session_start();
@include "connection.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$shipping_fee = 100;

// Fetch user's address from database
$address_query = $conn->prepare("SELECT * FROM `customer_address` WHERE user_id = ?");
$address_query->bind_param("i", $user_id);
$address_query->execute();
$address_result = $address_query->get_result();
if ($address_result && $address_result->num_rows > 0) {
    $address_data = $address_result->fetch_assoc();
    $saved_contact = $address_data['contact_no'];
    $saved_flat = $address_data['house_no'];
    $saved_street = $address_data['street_name'];
    $saved_barangay = $address_data['barangay'];
    $saved_city = $address_data['city'];
    $saved_state = $address_data['country'];
    $saved_zip_code = $address_data['zip_code'];
}

if (isset($_POST['order_btn'])) {
    $contact_no = $_POST['contact_no'];
    $payment_method = $_POST['payment_method'];
    $flat = $_POST['flat'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $order_status = 'To Pay';
    $order_reference_number = uniqid();
    $expected_delivery = date('Y-m-d H:i:s', strtotime('+7 days'));

    // Additional fields for GCash payment
    $gcash_fullname = $_POST['gcash_fullname'] ?? null;
    $gcash_phone = $_POST['gcash_phone'] ?? null;
    $gcash_ref = $_POST['gcash_ref'] ?? null;

    // Validate GCash fields if payment method is GCash
    if ($payment_method == 'gcash' && (empty($gcash_fullname) || empty($gcash_phone) || empty($gcash_ref))) {
        echo "<p>Please fill in all GCash payment details.</p>";
    } else {
        // Get the cart items for the user
        $cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $cart_query->bind_param("i", $user_id);
        $cart_query->execute();
        $cart_result = $cart_query->get_result();

        $product_total = 0;
        $price_total = 0;
        $product_name = [];

        if ($cart_result->num_rows > 0) {
            while ($product_item = $cart_result->fetch_assoc()) {
                $product_id = $product_item['product_id'];
                $product_quantity = $product_item['quantity'];

                // Get product details
                $product_query = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
                $product_query->bind_param("i", $product_id);
                $product_query->execute();
                $product_result = $product_query->get_result();
                $product_data = $product_result->fetch_assoc();
                $product_name[] = $product_data['product_name'] . ' (' . $product_quantity . ')';
                $product_price = (float)$product_data['price'] * (int)$product_quantity;
                $price_total += $product_price;

                // Deduct stock quantity
                $new_stock_quantity = $product_data['stock_quantity'] - $product_quantity;
                if ($new_stock_quantity < 0) {
                    echo "<p>Sorry, not enough stock for " . htmlspecialchars($product_data['product_name']) . ".</p>";
                    exit();
                }
                $update_stock_query = $conn->prepare("UPDATE `products` SET stock_quantity = ? WHERE product_id = ?");
                $update_stock_query->bind_param("ii", $new_stock_quantity, $product_id);
                $update_stock_query->execute();
            }
        }

        $total_product = implode(', ', $product_name);
        $total_price = $price_total + $shipping_fee;

        // Insert order details
        $order_query = $conn->prepare("INSERT INTO `orders` (user_id, order_date, order_status, shipping_fee, total_price, order_reference_number, expected_delivery, payment_method) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)");
        $order_query->bind_param("issdsss", $user_id, $order_status, $shipping_fee, $total_price, $order_reference_number, $expected_delivery, $payment_method);

        if ($order_query->execute()) {
            $order_id = $order_query->insert_id;

            // Insert order items
            $cart_result->data_seek(0);
            while ($product_item = $cart_result->fetch_assoc()) {
                $product_id = $product_item['product_id'];
                $product_quantity = $product_item['quantity'];
                $order_items_query = $conn->prepare("INSERT INTO `order_items` (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $order_items_query->bind_param("iii", $order_id, $product_id, $product_quantity);
                $order_items_query->execute();
            }

            // Insert or update customer address
            if ($address_result->num_rows > 0) {
                // Update existing address
                $update_address_query = $conn->prepare("UPDATE `customer_address` SET contact_no=?, house_no=?, street_name=?, barangay=?, city=?, country=?, zip_code=? WHERE user_id=?");
                $update_address_query->bind_param("sssssssi", $contact_no, $flat, $street, $barangay, $city, $state, $zip_code, $user_id);
                $update_address_query->execute();
            } else {
                // Insert new address
                $insert_address_query = $conn->prepare("INSERT INTO `customer_address` (user_id, contact_no, house_no, street_name, barangay, city, country, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_address_query->bind_param("issssssi", $user_id, $contact_no, $flat, $street, $barangay, $city, $state, $zip_code);
                $insert_address_query->execute();
            }

            // Clear the cart after the order is placed
            $clear_cart_query = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $clear_cart_query->bind_param("i", $user_id);
            $clear_cart_query->execute();

            // Insert GCash payment details if payment method is GCash
            if ($payment_method == 'gcash') {
                $gcash_payment_query = $conn->prepare("INSERT INTO `gcash_payments` (order_id, fullname, phone_number, reference_number) VALUES (?, ?, ?, ?)");
                $gcash_payment_query->bind_param("isss", $order_id, $gcash_fullname, $gcash_phone, $gcash_ref);
                $gcash_payment_query->execute();
            }

            // Set the payment confirmation message
            $payment_message = $payment_method == 'gcash' ? "(*Wait until we confirm your order*)" : "(*Pay when product arrives*)";

            echo "
                <div class='order-message-container'>
                    <div class='message-container'>
                        <h3>Thank you for shopping</h3>
                        <div class='order-detail'>
                            <p>Total Purchase<span>$total_product</span></p>
                            <span class='total'>Total: ₱" . number_format($total_price, 2) . "</span>
                        </div>
                        <div class='customer-detail'>
                            <p>Contact: <span>$contact_no</span></p>
                            <p>Address: <span>$flat, $street, $barangay, $city, $state - $zip_code</span></p>
                            <p>Courier: <span>J&T</span></p>
                            <p>Payment Mode: <span>$payment_method</span></p>
                            <p>$payment_message</p>
                        </div>
                        <a href='index.php' class='btn btn-primary'>Continue Shopping</a>
                    </div>
                </div>
            ";
        } else {
            echo "<p>Failed to place the order. Please try again.</p>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>
<style>
        html,
    body {
        position: relative;
        height: 100%;
    }

    body {
        background: #eee;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        font-size: 14px;
        color: #000;
        margin: 0;
        padding: 0;
    }

    a:link {
        text-decoration: none;
        color: #007aff;
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

    .container {
        margin-top: 50px;
    }

    .section-header {
        text-align: center;
        margin-bottom: 30px;
        text-transform: uppercase;
    }

    .inputBox {
        margin-bottom: 15px;
    }

    .inputBox span {
        font-weight: bold;
    }

    .inputBox input,
    .inputBox select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    .order-message-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .message-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .message-container h3 {
        margin-bottom: 20px;
    }

    .message-container .order-detail,
    .message-container .customer-detail {
        margin-bottom: 20px;
    }

    .message-container .order-detail span,
    .message-container .customer-detail p span {
        display: block;
        margin-top: 5px;
        font-weight: bold;
    }

    .message-container .total {
        font-size: 18px;
        color: #28a745;
    }

    .message-container .btn {
        text-transform: uppercase;
    }
    .gcash-fields {
        display: none;
    }

    .gcash-fields {
        display: none;
    }
</style>
<body>
    <?php include "includes/navbar.php"; ?>

    <div class="container">
        <section>
            <h1 class="section-header">Complete your Order</h1>

            <div class="row">
                <div class="col-md-6">
                    <div class="display-order">
                        <table class="table">
                            <h3>Order Summary</h3>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $select_cart = $conn->prepare("SELECT c.*, p.product_name, p.price FROM `cart` c JOIN `products` p ON c.product_id = p.product_id WHERE c.user_id = ?");
                                $select_cart->bind_param("i", $user_id);
                                $select_cart->execute();
                                $cart_result = $select_cart->get_result();

                                $total = 0;
                                $grand_total = 0;
                                if ($cart_result->num_rows > 0) {
                                    while ($fetch_cart = $cart_result->fetch_assoc()) {
                                        $price = is_numeric($fetch_cart['price']) ? (float)$fetch_cart['price'] : 0;
                                        $quantity = is_numeric($fetch_cart['quantity']) ? (int)$fetch_cart['quantity'] : 0;
                                        $total_price = $price * $quantity;
                                        $total += $total_price;
                                ?>
                                        <tr>
                                            <td><?= htmlspecialchars($fetch_cart['product_name']); ?></td>
                                            <td><?= htmlspecialchars($fetch_cart['quantity']); ?></td>
                                            <td>₱<?= number_format($total_price, 2); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    $grand_total = $total;
                                    ?>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Subtotal:</td>
                                        <td>₱<?= number_format($grand_total, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Shipping Fee:</td>
                                        <td>₱<?= number_format($shipping_fee, 2); ?></td>
                                    </tr>
                                    <tr class="table-active">
                                        <td colspan="2" class="text-end fw-bold">Total Amount:</td>
                                        <td>₱<?= number_format($grand_total + $shipping_fee, 2); ?></td>
                                    </tr>
                                <?php
                                } else {
                                    echo "<tr><td colspan='3'>Your cart is empty!</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <form action="" method="post">
                        <div class="row">
                            <h3 class="mb-4">Delivery Address</h3>
                            <div class="mb-3">
                                <label for="delivery_contact" class="form-label">Contact No.</label>
                                <input type="number" id="delivery_contact" name="contact_no" min="1" class="form-control" value="<?php echo isset($saved_contact) ? $saved_contact : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_flat" class="form-label">Flat no.</label>
                                <input type="text" id="delivery_flat" name="flat" class="form-control" value="<?php echo isset($saved_flat) ? $saved_flat : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_street" class="form-label">Street</label>
                                <input type="text" id="delivery_street" name="street" class="form-control" value="<?php echo isset($saved_street) ? $saved_street : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_barangay" class="form-label">Barangay</label>
                                <input type="text" id="delivery_barangay" name="barangay" class="form-control" value="<?php echo isset($saved_barangay) ? $saved_barangay : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_city" class="form-label">City/Municipality</label>
                                <input type="text" id="delivery_city" name="city" class="form-control" value="<?php echo isset($saved_city) ? $saved_city : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_state" class="form-label">Province</label>
                                <input type="text" id="delivery_state" name="state" class="form-control" value="<?php echo isset($saved_state) ? $saved_state : ''; ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="delivery_zip_code" class="form-label">Zip code</label>
                                <input type="number" id="delivery_zip_code" name="zip_code" min="1" class="form-control" value="<?php echo isset($saved_zip_code) ? $saved_zip_code : ''; ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="shipper" class="form-label"><h4><strong> Courier Will Be J&T Express</strong></h4></label>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="form-select" required>
                                    <option value="cod">Cash on Delivery</option>
                                    <option value="gcash">GCash</option>
                                </select>
                            </div>
                            <!-- GCash fields -->
                            <div class="gcash-fields">
                                <div class="mb-3">
                                    <label for="gcash_fullname" class="form-label">Full Name</label>
                                    <input type="text" id="gcash_fullname" name="gcash_fullname" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="gcash_phone" class="form-label">GCash Phone Number</label>
                                    <input type="text" id="gcash_phone" name="gcash_phone" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="gcash_ref" class="form-label">GCash Reference Number</label>
                                    <input type="text" id="gcash_ref" name="gcash_ref" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Cancel and Order Now Buttons -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="cart.php" class="btn btn-secondary btn-lg w-100">Cancel</a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="submit" value="Order now" name="order_btn" class="btn btn-primary btn-lg w-100">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"></script>
    <script>
        // Show/Hide GCash fields based on payment method
        document.getElementById('payment_method').addEventListener('change', function () {
            var gcashFields = document.querySelector('.gcash-fields');
            if (this.value === 'gcash') {
                gcashFields.style.display = 'block';
            } else {
                gcashFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
