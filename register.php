<?php

@include 'connection.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];

    $errors = [];

    if ($pass != $cpass) {
        $errors[] = 'Passwords do not match!';
    } else {
        // Check if user already exists
        $select = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $select->bind_param("ss", $username, $email);
        $select->execute();
        $result = $select->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'User already exists!';
        } else {

            // Insert into users table
            $insert_user = $conn->prepare("INSERT INTO users (username, password, roles, first_name, last_name, email, date_added) VALUES (?, ?, 'U', ?, ?, ?, NOW())");
            $insert_user->bind_param("sssss", $username, $pass, $fname, $lname, $email);
            $insert_user->execute();
            $user_id = $conn->insert_id; // Get the ID of the inserted user

            // Insert into customer table with user_id foreign key
            $insert_customer = $conn->prepare("INSERT INTO customer_address (user_id, contact_no, house_no, street_name, barangay, city, country, zip_code) VALUES (?, '', '', '', '', '', '', '')");
            $insert_customer->bind_param("i", $user_id);
            $insert_customer->execute();

            header('location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #103cbe;">
                <div class="featured-image mb-3">
                    <img src="images/1.png" class="img-fluid" style="width: 250px;">
                </div>
                <p class="text-white fs-2" style="font-family: 'Courier New', Courier, monospace; font-weight: 600;">Be Verified</p>
                <small class="text-white text-wrap text-center" style="width: 17rem;font-family: 'Courier New', Courier, monospace;">Join experienced Designers on this platform.</small>
            </div>
            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-3">
                        <h2>Hello</h2>
                        <p>Kindly fill up the following to register.</p>
                    </div>
                    <form action="" method="post">
                        <?php
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo "<div class='alert alert-danger alert-dismissible fade show alert-top' role='alert'>$error
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                            }
                        }
                        ?>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Username" name="username" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="First Name" name="firstname" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Last Name" name="lastname" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email address" name="email" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" name="password" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Confirm Password" name="cpassword" required>
                        </div>
                        <div class="input-group mb-3">
                            <button class="btn btn-lg btn-primary w-100 fs-6" name="submit">Sign Up</button>
                        </div>
                        <div class="input-group mb-3">
                            <button class="btn btn-lg btn-light w-100 fs-6"><img src="images/google.png" style="width:20px" class="me-2"><small>Sign Up with Google</small></button>
                        </div>
                        <div class="row">
                            <small>Already have account? <a href="login.php">Log in</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JavaScript and dependencies (Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"></script>

</body>

</html>