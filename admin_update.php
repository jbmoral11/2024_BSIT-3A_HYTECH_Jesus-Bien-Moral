<?php
session_start(); // Start the session
@include 'connection.php';

$id = $_GET['edit'];

if (isset($_POST['update_product'])) {
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
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    if (empty($product_name) || empty($product_categ) || empty($product_desc) || empty($product_model) || empty($product_brand) || empty($product_price) || empty($product_stock) || empty($product_specification)) {
        $message[] = 'Please fill out all fields';
    } else {
        if (!empty($imagePath)) {
            $update = "UPDATE products SET product_name = '$product_name', product_img = '$imagePath', description = '$product_desc', category_name = '$product_categ', brand = '$product_brand', model = '$product_model', price = '$product_price', stock_quantity = '$product_stock', specification = '$product_specification' WHERE product_id = $id";
        } else {
            $update = "UPDATE products SET product_name = '$product_name', description = '$product_desc', category_name = '$product_categ', brand = '$product_brand', model = '$product_model', price = '$product_price', stock_quantity = '$product_stock', specification = '$product_specification' WHERE product_id = $id";
        }
        $upload = mysqli_query($conn, $update);
        if ($upload) {
            $_SESSION['message'] = 'Product updated successfully'; // Set the session message
            header("Location: admin_manage_product.php");
            exit;
        } else {
            $message[] = 'Could not update the product';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>

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
        /* Adjust this value based on your navbar height */
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 600px;
        z-index: 1050;
        text-align: center;
        
    }
    .form-label{
        padding-left: 5px;
    }
</style>

<body>
    <?php include('includes/admin_navbar.php') ?>

    <?php
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '<div class="alert alert-primary alert-dismissible fade show alert-top" role="alert">' . $msg . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }
    ?>
    <div class="container">
        <div class="admin-product-form-container centered">
            <?php
            $select = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $id");
            if ($row = mysqli_fetch_assoc($select)) {
                $current_category = $row['category_name'];
            ?>
                <form action="<?php echo $_SERVER['PHP_SELF'] . "?edit=$id"; ?>" method="post" enctype="multipart/form-data">
                    <div class="text-center my-5">
                        <h1>Update Product</h1>
                        <hr />
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control form-control-lg bg-light fs-6" name="product_name" id="product_name" value="<?php echo $row['product_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="product_categ" class="form-label">Category</label>
                            <select class="form-control form-control-lg bg-light fs-6" name="product_categ" id="product_categ" required>
                                <?php
                                $categories = [
                                    "Processor", "Motherboard", "Graphics Card", "Memory", "Hard Drive", 
                                    "Power Supply", "Pc Case", "Laptop", "Monitor", "Keyboard", "Mouse", "Headset"
                                ];
                                foreach ($categories as $category) {
                                    echo "<option value=\"$category\"" . ($current_category == $category ? " selected" : "") . ">$category</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="product_desc" class="form-label">Description</label>
                            <textarea class="form-control form-control-lg bg-light fs-6" name="product_desc" id="product_desc" rows="4" required><?php echo $row['description']; ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="product_model" class="form-label">Model</label>
                            <input type="text" class="form-control form-control-lg bg-light fs-6" name="product_model" id="product_model" value="<?php echo $row['model']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="product_brand" class="form-label">Brand</label>
                            <input type="text" class="form-control form-control-lg bg-light fs-6" name="product_brand" id="product_brand" value="<?php echo $row['brand']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="product_price" class="form-label">Price</label>
                            <input type="number" class="form-control form-control-lg bg-light fs-6" name="product_price" id="product_price" value="<?php echo $row['price']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="product_stock" class="form-label">Stock</label>
                            <input type="number" class="form-control form-control-lg bg-light fs-6" name="product_stock" id="product_stock" value="<?php echo $row['stock_quantity']; ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label for="product_specification" class="form-label">Specification</label>
                            <textarea class="form-control form-control-lg bg-light fs-6" name="product_specification" id="product_specification" rows="4" required><?php echo $row['specification']; ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input class="form-control form-control-lg bg-light fs-6" type="file" name="product_image" id="product_image">
                        </div>
                        <div class="col-md-12 text-start">
                            <?php if ($row['product_img']) { ?>
                                <label for="current_image" class="form-label">Current Image</label>
                                <div class="d-flex justify-content-start">
                                    <img src="<?php echo $row['product_img']; ?>" alt="Current Product Image" style="max-height:200px;" class="img-fluid rounded">
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-12 mb-3 text-start">
                            <button type="submit" class="btn btn-primary btn-lg" name="update_product">Update Product</button>
                            <a href="admin_manage_product.php" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </div>
                </form>
            <?php
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
