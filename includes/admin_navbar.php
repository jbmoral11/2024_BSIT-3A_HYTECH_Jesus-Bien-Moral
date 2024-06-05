<?php

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_name'])) {
    // If not, redirect to the login page
    header('Location: login.php');
    exit;
}
?>

<div class="main-navbar shadow-sm sticky-top">
    <div class="top-navbar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2 my-auto d-none d-sm-none d-md-block d-lg-block">
                    <h5 class="brand-name">Admin Panel</h5>
                </div>
                <div class="col-md-5 my-auto ms-auto">
                    <ul class="nav justify-content-end">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_manage_product.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_product_reviews.php">Reviews</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_orders.php">Orders</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user"></i>
                                <?php
                                // Display the admin name if logged in
                                if (isset($_SESSION['admin_name'])) {
                                    echo htmlspecialchars($_SESSION['admin_name']);
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if (isset($_SESSION['admin_name'])) : ?>
                                    <li><a href="logout.php" class="dropdown-item"><i class="fa fa-sign-out"></i> Logout</a></li>
                                <?php else : ?>
                                    <li><a href="login.php" class="dropdown-item"><i class="fa fa-sign-in"></i> Login</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand d-block d-sm-block d-md-none d-lg-none" href="#">
                HyperTech
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybA1r7I5/6vrwWtdG1zVVJY4lS6A2e6M/5fQ6C4t97r2bMyLD" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuW5bF7NVW5gBPExGdkaC9XyyD95kpPjQxfW1RN8zDme5i6b6MGy8tvo8+alTf4Q" crossorigin="anonymous"></script>