<?php
ob_start();
include("header.php");
include("navbar.php");

// Call the authentication function
isAuthenticated();
$user_id = $_SESSION["user_id"];
$user = getUserById($user_id);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $upi_id = $_POST['upi_id'];
    // Update user profile
    if (updateUserProfile($user_id, $name, $upi_id)) {
        echo "<p class='text-success'>Profile updated successfully!</p>";
        // Fetch updated user data
        $user = getUserById($user_id);
    } else {
        echo "<p class='text-danger'>Failed to update profile.</p>";
    }
    // echo "Name: ".$name." UPI ID: ".$upi_id." User ID: ".$user_id;
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="max-w-md mx-auto py-4">
            <h2 class="text-center fw-bold mb-4">Update Profile</h2>
            <form action="update-profile.php" method="POST">
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="upi_id">UPI ID</label>
                    <input type="text" name="upi_id" id="upi_id" value="<?= htmlspecialchars($user['upi_id']) ?>" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
