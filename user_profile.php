<?php
session_start();
@include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or display an error message
    header("Location: login.php");
    exit(); // Stop further execution
}

$user_id = $_SESSION['user_id'];

// Update User Information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateUserInfo'])) {
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];

    $updateUserQuery = "UPDATE users SET username = ?, first_name = ?, last_name = ?, email = ? WHERE user_id = ?";

    if ($stmt = mysqli_prepare($conn, $updateUserQuery)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $firstName, $lastName, $email, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Information updated successfully";
        } else {
            $_SESSION['error'] = "Error updating information: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Update Customer Address
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateAddress'])) {
    $contactNo = $_POST['contactNo'];
    $houseNo = $_POST['houseNo'];
    $street_name = $_POST['street_name'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];

    // Add other address fields here

    $updateAddressQuery = "UPDATE customer_address SET contact_no = ?, house_no = ?, street_name = ?, barangay = ?, city = ?, country = ?, zip_code = ? WHERE user_id = ?";

    if ($stmt = mysqli_prepare($conn, $updateAddressQuery)) {
        mysqli_stmt_bind_param($stmt, "sssssssi", $contactNo, $houseNo, $street_name, $barangay, $city, $country, $zip_code, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Address updated successfully";
        } else {
            $_SESSION['error'] = "Error updating address: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Fetch user data from database
$query = "SELECT * FROM users WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = "Error preparing user statement: " . mysqli_error($conn);
}

// Fetch customer address data from database
$query = "SELECT * FROM customer_address WHERE user_id = ?";
if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $address_result = mysqli_stmt_get_result($stmt);
    $address = mysqli_fetch_assoc($address_result);
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = "Error preparing address statement: " . mysqli_error($conn);
}

// Change Password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Verify current password
    $verifyPasswordQuery = "SELECT password FROM users WHERE user_id = ?";
    if ($stmt = mysqli_prepare($conn, $verifyPasswordQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['password'];

        if (password_verify($currentPassword, $storedPassword)) {
            // Verify new password matches confirm password
            if ($newPassword === $confirmPassword) {
                // Update password in the database
                $updatePasswordQuery = "UPDATE users SET password = ? WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $updatePasswordQuery)) {
                    mysqli_stmt_bind_param($stmt, "si", $newPassword, $user_id);

                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['message'] = "Password changed successfully";
                    } else {
                        $_SESSION['error'] = "Error updating password: " . mysqli_error($conn);
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $_SESSION['error'] = "Error preparing statement: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error'] = "New password and confirm password do not match";
            }
        } else {
            $_SESSION['error'] = "Incorrect current password";
        }
    } else {
        $_SESSION['error'] = "Error preparing statement: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <title>User Profile</title>
</head>
<style>
    body {
        position: relative;
        height: 100%;
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

    body {
        background: #F1F3FA;
    }

    /* Profile container */
    .profile {
        margin: 20px 0;
    }

    /* Profile sidebar */
    .profile-sidebar {
        padding: 20px 0 10px 0;
        background: #fff;
    }

    .profile-userpic img {
        float: none;
        margin: 0 auto;
        width: 50%;
        height: 50%;
        -webkit-border-radius: 50% !important;
        -moz-border-radius: 50% !important;
        border-radius: 50% !important;
    }

    .profile-usertitle {
        text-align: center;
        margin-top: 20px;
    }

    .profile-usertitle-name {
        color: #5a7391;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .profile-usertitle-job {
        text-transform: uppercase;
        color: #5b9bd1;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .profile-userbuttons {
        text-align: center;
        margin-top: 10px;
    }

    .profile-userbuttons .btn {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 15px;
        margin-right: 5px;
    }

    .profile-userbuttons .btn:last-child {
        margin-right: 0px;
    }

    .profile-usermenu {
        margin-top: 30px;
    }

    .profile-usermenu ul li {
        border-bottom: 1px solid #f0f4f7;
    }

    .profile-usermenu ul li:last-child {
        border-bottom: none;
    }

    .profile-usermenu ul li a {
        color: #93a3b5;
        font-size: 14px;
        font-weight: 400;
    }

    .profile-usermenu ul li a i {
        margin-right: 8px;
        font-size: 14px;
    }

    .profile-usermenu ul li a:hover {
        background-color: #fafcfd;
        color: #5b9bd1;
    }

    .profile-usermenu ul li.active {
        border-bottom: none;
    }

    .profile-address ul li.active a {
        color: #5b9bd1;
        background-color: #f6f9fb;
        border-left: 2px solid #5b9bd1;
        margin-left: -2px;
    }

    /* Profile Content */
    .profile-content {
        padding: 20px;
        background: #fff;
        min-height: 460px;
    }

    input {
        display: none;
    }

    .mb-3 {
        padding-top: 10px;
    }

    .btn-primary {
        margin-top: 10px;
    }
</style>
<body>
    <div class="container-fluid">
        <div class="row profile">
            <div class="col-lg-3 col-md-5">
                <div class="profile-sidebar">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="https://static.change.org/profile-img/default-user-profile.svg" class="img-responsive" alt="">
                    </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            <?php echo isset($user['username']) ? $user['username'] : ''; ?>
                        </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="user_profile.php">
                                    <i class="glyphicon glyphicon-home"></i>
                                    User Profile
                                </a>
                            </li>
                            <li>
                                <a href="user_address.php">
                                    <i class="glyphicon glyphicon-user"></i>
                                    Address
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END SIDEBAR MENU -->
                </div>
            </div>
            <div class="col-lg-8 col-md-7">
                <div class="profile-content">
                    <div class="container mt-5">
                        <?php
                        // Display success or error messages if they exist in the session
                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show alert-top" role="alert">' . $_SESSION['message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            unset($_SESSION['message']); // Clear the message to prevent it from showing again
                        }
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger alert-dismissible alert-top" role="alert">' . $_SESSION['error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                            unset($_SESSION['error']); // Clear the error to prevent it from showing again
                        }
                        ?>
                        <a href="index.php" class="btn btn-secondary mb-3">Go Back</a>
                        <h1>User Profile</h1>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <!-- User Information Form -->
                                <h2>Your Information</h2>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($user['username']) ? $user['username'] : ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo isset($user['first_name']) ? $user['first_name'] : ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="updateUserInfo">Update Information</button>
                                </form>
                            </div>

                            <!-- Password Change Form -->
                            <div class="col-md-5">
                                <h2>Change Password</h2>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return confirm('Are you sure you want to change the password?')">
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="changePassword">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>
