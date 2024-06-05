<?php
session_start();
include 'connection.php';
// Pagination parameters
$items_per_page = 10;

// Inventory report pagination
$inventory_page = isset($_GET['inventory_page']) ? (int)$_GET['inventory_page'] : 1;
$inventory_offset = ($inventory_page - 1) * $items_per_page;
$total_inventory_query = "SELECT COUNT(*) AS total FROM products";
$total_inventory_result = mysqli_query($conn, $total_inventory_query);
$total_inventory_row = mysqli_fetch_assoc($total_inventory_result);
$total_inventory_pages = ceil($total_inventory_row['total'] / $items_per_page);

$inventory_query = "SELECT product_name, category_name, stock_quantity FROM products LIMIT $items_per_page OFFSET $inventory_offset";
$inventory_result = mysqli_query($conn, $inventory_query);

// User activity pagination
$user_activity_page = isset($_GET['user_activity_page']) ? (int)$_GET['user_activity_page'] : 1;
$user_activity_offset = ($user_activity_page - 1) * $items_per_page;
$total_user_activity_query = "SELECT COUNT(*) AS total FROM orders";
$total_user_activity_result = mysqli_query($conn, $total_user_activity_query);
$total_user_activity_row = mysqli_fetch_assoc($total_user_activity_result);
$total_user_activity_pages = ceil($total_user_activity_row['total'] / $items_per_page);

$user_activity_query = "
    SELECT 
        u.username, 
        o.order_reference_number, 
        o.order_status, 
        o.order_date, 
        o.total_price 
    FROM 
        users u 
    JOIN 
        orders o ON u.user_id = o.user_id
    ORDER BY 
        o.order_date DESC
    LIMIT $items_per_page OFFSET $user_activity_offset";
$user_activity_result = mysqli_query($conn, $user_activity_query);

// Queries to fetch the required data
$sales_today_query = "SELECT SUM(total_price) AS total_sales FROM orders WHERE DATE(order_date) = CURDATE() AND order_status = 'Completed'";
$sales_yesterday_query = "SELECT SUM(total_price) AS total_sales FROM orders WHERE DATE(order_date) = CURDATE() - INTERVAL 1 DAY AND order_status = 'Completed'";
$sales_today_result = mysqli_query($conn, $sales_today_query);
$sales_yesterday_result = mysqli_query($conn, $sales_yesterday_query);
$sales_today_row = mysqli_fetch_assoc($sales_today_result);
$sales_yesterday_row = mysqli_fetch_assoc($sales_yesterday_result);
$sales_today = $sales_today_row['total_sales'];
$sales_yesterday = $sales_yesterday_row['total_sales'];

$sales_this_year_query = "SELECT SUM(total_price) AS total_sales FROM orders WHERE YEAR(order_date) = YEAR(CURDATE()) AND order_status = 'Completed'";
$sales_last_year_query = "SELECT SUM(total_price) AS total_sales FROM orders WHERE YEAR(order_date) = YEAR(CURDATE()) - 1 AND order_status = 'Completed'";
$sales_this_year_result = mysqli_query($conn, $sales_this_year_query);
$sales_last_year_result = mysqli_query($conn, $sales_last_year_query);
$sales_this_year_row = mysqli_fetch_assoc($sales_this_year_result);
$sales_last_year_row = mysqli_fetch_assoc($sales_last_year_result);
$sales_this_year = $sales_this_year_row['total_sales'];
$sales_last_year = $sales_last_year_row['total_sales'];

$pending_orders_query = "SELECT COUNT(order_id) AS pending_orders FROM orders WHERE order_status = 'To Pay'";
$pending_orders_result = mysqli_query($conn, $pending_orders_query);
$pending_orders_row = mysqli_fetch_assoc($pending_orders_result);
$pending_orders = $pending_orders_row['pending_orders'];

$total_orders_query = "SELECT COUNT(order_id) AS total_orders FROM orders";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders_row = mysqli_fetch_assoc($total_orders_result);
$total_orders = $total_orders_row['total_orders'];

$top_items_query = "
    SELECT 
        p.product_name, 
        SUM(oi.quantity) AS total_sold 
    FROM 
        order_items oi 
    JOIN 
        products p ON oi.product_id = p.product_id 
    GROUP BY 
        p.product_name 
    ORDER BY 
        total_sold DESC 
    LIMIT 10";
$top_items_result = mysqli_query($conn, $top_items_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <style>
        body {
            background: #fff;
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-title {
            font-weight: bold;
        }

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include('includes/admin_navbar.php'); ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
            </div>
        </div>

        <!-- Top section with 3 columns 2 rows layout -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="card-title">Sales Today vs Yesterday</div>
                        <p class="card-text">₱<?php echo $sales_today; ?> vs ₱<?php echo $sales_yesterday; ?></p>
                    </div>
                </div>
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="card-title">Pending Orders</div>
                        <p class="card-text"><?php echo $pending_orders; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="card-title">Sales This Year vs Last Year</div>
                        <p class="card-text">₱<?php echo $sales_this_year; ?> vs ₱<?php echo $sales_last_year; ?></p>
                    </div>
                </div>
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="card-title">Total Orders</div>
                        <p class="card-text"><?php echo $total_orders; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">Top 10 Items</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Total Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($top_items_result)) { ?>
                                        <tr>
                                            <td><?php echo $row['product_name']; ?></td>
                                            <td><?php echo $row['total_sold']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom section with 2 columns 1 row layout -->
        <div class="row">
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">Inventory Reports / Stocks</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Stock Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($inventory_result)) { ?>
                                        <tr>
                                            <td><?php echo $row['product_name']; ?></td>
                                            <td><?php echo $row['category_name']; ?></td>
                                            <td><?php echo $row['stock_quantity']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Controls -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_inventory_pages; $i++) { ?>
                                    <li class="page-item <?php if ($i == $inventory_page) echo 'active'; ?>">
                                        <a class="page-link" href="?inventory_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">User Activity</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Order Reference</th>
                                        <th>Order Status</th>
                                        <th>Order Date</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($user_activity_result)) { ?>
                                        <tr>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['order_reference_number']; ?></td>
                                            <td><?php echo $row['order_status']; ?></td>
                                            <td><?php echo $row['order_date']; ?></td>
                                            <td>$<?php echo $row['total_price']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Controls -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_user_activity_pages; $i++) { ?>
                                    <li class="page-item <?php if ($i == $user_activity_page) echo 'active'; ?>">
                                        <a class="page-link" href="?user_activity_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
