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
    $province = $_POST['province'];
    $country = $_POST['country'];
    $zip_code = $_POST['zip_code'];

    // Add other address fields here

    $updateAddressQuery = "UPDATE customer_address SET contact_no = ?, house_no = ?, street_name = ?, barangay = ?, city = ?, province = ?, country = ?, zip_code = ? WHERE user_id = ?";

    if ($stmt = mysqli_prepare($conn, $updateAddressQuery)) {
        mysqli_stmt_bind_param($stmt, "sssssssi", $contactNo, $houseNo, $street_name, $barangay, $city, $province, $country, $zip_code, $user_id);

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
<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<style> 
.mb-3{
    padding-top: 10px;
}
.btn-primary{
    margin-top: 10px;
}
</style>

<?php include('includes/user_dashboard.php') ?>
<!------ Include the above in your HEAD tag ---------->
<div class="col-lg-8 col-md-7">
            <div class="profile-content">
            <div class="container mt-5">
            <div class="row mt-5">
            <div class="col-md-9">
        
                <h2>Your Address</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="mb-3">
                        <label for="contactNo" class="form-label">Contact No</label>
                        <input type="text" class="form-control" id="contactNo" name="contactNo" value="<?php echo isset($address['contact_no']) ? $address['contact_no'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="houseNo" class="form-label">House No</label>
                        <input type="text" class="form-control" id="houseNo" name="houseNo" value="<?php echo isset($address['house_no']) ? $address['house_no'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="street_name" class="form-label">Street Name</label>
                        <input type="text" class="form-control" id="street_name" name="street_name" value="<?php echo isset($address['street_name']) ? $address['street_name'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                        <input type="text" class="form-control" id="barangay" name="barangay" value="<?php echo isset($address['barangay']) ? $address['barangay'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="province" class="form-label">Province</label>
                        <input type="text" class="form-control" id="province" name="province" value="<?php echo isset($address['province']) ? $address['province'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City/Municipality</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo isset($address['city']) ? $address['city'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Province</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo isset($address['country']) ? $address['country'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="zip_code" class="form-label">Zip Code</label>
                        <input type="text" class="form-control" id="zip_code" name="zip_code" value="<?php echo isset($address['zip_code']) ? $address['zip_code'] : ''; ?>">
                    </div>
                    <!-- Add other address fields here -->
                    <button type="submit" class="btn btn-primary" name="updateAddress">Update Address</button>
                </form>
            </div>
        </div>
    </div>
        <!-- Customer Address Form -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


<?php
mysqli_close($conn);
?>
            </div>
		</div>
	</div>
</div>



