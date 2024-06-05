<?php
@include '../connection.php';
?>

<div class="main-navbar shadow-sm sticky-top">
    <div class="top-navbar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2 my-auto d-none d-sm-none d-md-block d-lg-block">
                    <h5 class="brand-name">Cyberware</h5>
                </div>
                <div class="col-md-5 my-auto">
                    <form role="search" method="GET" action="search_results.php">
                        <div class="input-group">
                            <input type="search" name="query" placeholder="Search your product" class="form-control" />
                            <button class="btn bg-white" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-5 my-auto">
                    <ul class="nav justify-content-end">
                        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) : ?>
                            <?php
                            // Fetch cart items for the logged-in user using prepared statement
                            $user_id = $_SESSION['user_id'];
                            $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row_count = $result->num_rows;
                            $stmt->close();
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cart.php">
                                    <i class="fa fa-shopping-cart"></i> Cart (<?php echo $row_count; ?>)
                                </a>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">
                                    <i class="fa fa-sign-in"></i> Login
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user"></i>
                                <?php
                                if (isset($_SESSION['user_name'])) {
                                    echo htmlspecialchars($_SESSION['user_name']);
                                } else {
                                    echo 'Guest';
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php if (isset($_SESSION['user_name'])) : ?>
                                    <li><a href="user_profile.php" class="dropdown-item"><i class="fa fa-user"></i> Profile</a></li>
                                    <li><a href="user_orders.php" class="dropdown-item"><i class="fa fa-list"></i> My Orders</a></li>
                                    <li><a href="cart.php" class="dropdown-item"><i class="fa fa-shopping-cart"></i> My Cart</a></li>
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
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Processor">Processor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Motherboard">Motherboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Graphics Card">Graphics Card</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Memory">Memory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Hard Drive">Hard Drive</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Power Supply">Power Supply</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Pc Case">Pc Case</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Laptop">Laptop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Monitor">Monitor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Keyboard">Keyboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Mouse">Mouse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product_page.php?category=Headset">Headset</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybA1r7I5/6vrwWtdG1zVVJY4lS6A2e6M/5fQ6C4t97r2bMyLD" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuW5bF7NVW5gBPExGdkaC9XyyD95kpPjQxfW1RN8zDme5i6b6MGy8tvo8+alTf4Q" crossorigin="anonymous"></script>