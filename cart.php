<?php
session_start();
@include 'connection.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit();
}

if (isset($_POST['update_update_btn'])) {
    $update_value = $_POST['update_quantity'];
    $update_id = $_POST['update_quantity_id'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE cart_id = ?");
    $stmt->bind_param('ii', $update_value, $update_id);
    if ($stmt->execute()) {
        header('Location: cart.php');
    }
    $stmt->close();
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM `cart` WHERE cart_id = ?");
    $stmt->bind_param('i', $remove_id);
    $stmt->execute();
    header('Location: cart.php');
    $stmt->close();
}

if (isset($_GET['delete_all'])) {
    $stmt = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    header('Location: cart.php');
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
    }

    .table img {
        max-height: 100px;
        width: auto;
     
    }

    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
        color: #000;
    }

    .table th,
    .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .table thead {
        background-color: #343a40;
        color: #fff;
    }

    .btn {
        font-family: inherit;
        font-size: inherit;
    }
 /* General table styling */
table {
    border-collapse: collapse;
    width: 100%;
}

/* Specific rows and cells to hide borders */
tr.no-border, td.no-border {
    border: none;
}
.btn-text-white{
    color: #EC3812;
}
.btn-text-white:hover{
    color: #FF4A23;
}
</style>

<body>
    <?php include "includes/navbar.php"; ?>

    <div class="container my-5">
        <section class="shopping-cart">
            <h1 class="heading text-center mb-4">Shopping Cart</h1>
            <div class="table-responsive">
                <table class="table table-hover table-light table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->prepare("SELECT cart.*, products.product_name, products.price, products.product_img FROM `cart` JOIN `products` ON cart.product_id = products.product_id WHERE cart.user_id = ?");
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $select_cart = $stmt->get_result();

                        $grand_total = 0;
                        if ($select_cart->num_rows > 0) {
                            while ($fetch_cart = $select_cart->fetch_assoc()) {
                                $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
                                $grand_total += $sub_total;
                        ?>
                                <tr>
                                    <td><img src="<?= $fetch_cart['product_img']; ?>" alt="image" class="img-fluid" style="max-width: 100px;"></td>
                                    <td><?= $fetch_cart['product_name']; ?></td>
                                    <td>Php<?= number_format($fetch_cart['price'], 2); ?></td>
                                    <td>
                                        <form action="" method="post" class="d-inline">
                                            <input type="hidden" name="update_quantity_id" value="<?= $fetch_cart['cart_id']; ?>">
                                            <input type="number" name="update_quantity" min="1" value="<?= $fetch_cart['quantity']; ?>" class="form-control w-50 mx-auto">
                                            <input type="submit" value="Update" name="update_update_btn" class="btn btn-secondary btn-sm mt-2">
                                        </form>
                                    </td>
                                    <td>Php <?= number_format($sub_total, 2); ?></td>
                                    <td><a href="cart.php?remove=<?= $fetch_cart['cart_id']; ?>" onclick="return confirm('Remove item from cart?')" class="btn-text-white"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center">No items in the cart</td></tr>';
                        }
                        ?>
                        <tr>
                            <td><a href="index.php" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Continue Shopping</a></td>
                            <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                            <td><strong>Php <?= number_format($grand_total, 2); ?></strong></td>
                            <td><a href="cart.php?delete_all" onclick="return confirm('Are you sure you want to delete all?');" class="btn btn-danger text-white"><i class="fa fa-trash" aria-hidden="true"></i> Delete All</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <a href="checkout.php" class="btn btn-success <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>