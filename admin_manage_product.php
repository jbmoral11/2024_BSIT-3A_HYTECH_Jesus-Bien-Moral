<?php
session_start(); // Start the session
@include 'connection.php';

$message = []; // Initialize message array

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_categ = $_POST['product_categ'];
    $product_desc = $_POST['product_desc'];
    $product_model = $_POST['product_model'];
    $product_brand = $_POST['product_brand'];
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $product_specification = $_POST['product_specification'];
    $imagePath = '';

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $targetDir = __DIR__ . "/Image/";
        $targetFile = $targetDir . basename($_FILES["product_image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check !== false) {
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
                $imagePath = "Image/" . basename($_FILES["product_image"]["name"]);
            } else {
                $message[] = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message[] = "File is not an image.";
        }
    }

    if (empty($product_name) || empty($product_categ) || empty($product_desc) || empty($product_model) || empty($product_brand) || empty($product_price) || empty($imagePath) || empty($product_stock) || empty($product_specification)) {
        $message[] = 'Please fill out all fields';
    } else {
        $insert = $conn->prepare("INSERT INTO products (product_name, product_img, description, category_name, brand, model, price, stock_quantity, specification, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $insert->bind_param("ssssssdss", $product_name, $imagePath, $product_desc, $product_categ, $product_brand, $product_model, $product_price, $product_stock, $product_specification);

        if ($insert->execute()) {
            $message[] = 'New product added successfully';
        } else {
            $message[] = 'Could not add the product';
        }
        $insert->close();
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $selectStatus = $conn->prepare("SELECT status FROM products WHERE product_id = ?");
    $selectStatus->bind_param("i", $id);
    $selectStatus->execute();
    $result = $selectStatus->get_result();
    $product = $result->fetch_assoc();
    $newStatus = ($product['status'] == 'active') ? 'inactive' : 'active';
    $selectStatus->close();

    $updateStatus = $conn->prepare("UPDATE products SET status = ? WHERE product_id = ?");
    $updateStatus->bind_param("si", $newStatus, $id);
    if ($updateStatus->execute()) {
        $_SESSION['message'] = 'Product status updated successfully';
    } else {
        $_SESSION['message'] = 'Could not update the product status';
    }
    $updateStatus->close();
    header('location:admin_manage_product.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>

<style>
    html,
    body {
        position: relative;
        height: 100%;
    }

    body {
        background: #fff;
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

    .table img {
        max-height: 100px;
        width: auto;
    }

    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
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

    .admin-product-form-container {
        margin-bottom: 50px;
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

    .btn-bg-green-300 {
        background-color: #78bc78;
    }

    .btn-bg-green-300:hover {
        background-color: #78bc78;
        color: white !important;
    }

    .form-label {
        padding-left: 5px;
    }
</style>

<body>
    <?php include('includes/admin_navbar.php') ?>

    <div class="container">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-primary alert-dismissible fade show alert-top" role="alert">' . $_SESSION['message'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            // Clear the message after displaying it
            unset($_SESSION['message']);
        }

        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="alert alert-success alert-dismissible fade show alert-top" role="alert">' . $msg . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
        }
        ?>

        <div class="admin-product-form-container">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <div class="text-center my-5">
                    <h1>Add a New Product</h1>
                    <hr />
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control form-control-lg bg-light fs-6" name="product_name" id="product_name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="product_categ" class="form-label">Category</label>
                        <select class="form-select form-select-lg bg-light fs-6" name="product_categ" id="product_categ" required>
                            <option value="" selected disabled>Select a category</option>
                            <option value="Processor">Processor</option>
                            <option value="Motherboard">Motherboard</option>
                            <option value="Graphics Card">Graphics Card</option>
                            <option value="Memory">Memory</option>
                            <option value="Hard Drive">Hard Drive</option>
                            <option value="Power Supply">Power Supply</option>
                            <option value="Pc Case">Pc Case</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Keyboard">Keyboard</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Headset">Headset</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="product_desc" class="form-label">Description</label>
                        <textarea class="form-control form-control-lg bg-light fs-6" name="product_desc" id="product_desc" rows="4" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="product_model" class="form-label">Model</label>
                        <input type="text" class="form-control form-control-lg bg-light fs-6" name="product_model" id="product_model" required>
                    </div>
                       
                    <div class="col-md-6">
                        <label for="product_brand" class="form-label">Brand</label>
                        <input type="text" class="form-control form-control-lg bg-light fs-6" name="product_brand" id="product_brand" required>
                    </div>
                    <div class="col-md-6">
                        <label for="product_price" class="form-label">Price</label>
                        <input type="number" class="form-control form-control-lg bg-light fs-6" name="product_price" min="1" id="product_price" required>
                    </div>
                    <div class="col-md-6">
                        <label for="product_stock" class="form-label">Stock</label>
                        <input type="number" class="form-control form-control-lg bg-light fs-6" name="product_stock" min="0" id="product_stock" required>
                    </div>
                    <div class="col-md-12">
                        <label for="product_specification" class="form-label">Specification</label>
                        <textarea class="form-control form-control-lg bg-light fs-6" name="product_specification" id="product_specification" rows="4" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="product_image" class="form-label">Product Image</label>
                        <input class="form-control form-control-lg bg-light fs-6" type="file" name="product_image" id="product_image" required>
                    </div>
                    <div class="col-12 text-start">
                        <button type="submit" class="btn btn-primary btn-lg" name="add_product">Add Product</button>
                    </div>
                </div>
            </form>
        </div>

        <?php
        $select = mysqli_query($conn, "SELECT * FROM products");
        ?>

        <div class="text-center my-5">
            <h1>Update Product</h1>
            <hr />
        </div>

        <div class="product-display">
            <div class="table-responsive">
                <table class="table table-hover table-light table-bordered text-center">
                    <thead>
                        <tr>
                            <th scope="col">Image</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Model</th>
                            <th scope="col">Brand</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Specification</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($select)) {
                        ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($row['product_img']); ?>" class="img-fluid" alt="Product Image"></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['model']); ?></td>
                                <td><?php echo htmlspecialchars($row['brand']); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                <td><?php echo htmlspecialchars($row['stock_quantity']); ?></td>
                                <td><?php echo htmlspecialchars($row['specification']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <div class="d-grid gap-2">
                                        <a href="admin_update.php?edit=<?php echo $row['product_id']; ?>" class="btn btn-bg-green-300 btn-sm text-white">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit
                                        </a>
                                        <a href="admin_manage_product.php?toggle=<?php echo $row['product_id']; ?>" class="btn btn-secondary btn-sm text-white toggle-status" data-status="<?php echo $row['status']; ?>">
                                            <i class="fa fa-toggle-on" aria-hidden="true"></i> 
                                            <?php echo ($row['status'] == 'active') ? 'Active' : 'Inactive'; ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
