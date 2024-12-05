<?php
ob_start();
session_start(); // Start session at the top of the file

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/index.php");
    exit(); // Always follow header with exit()
}

// Handle logout request
if (isset($_POST['logout'])) {
    logout();
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-2 py-1">
    <a class="navbar-brand" href="../public/auctions.php"><img width="54px" height="54px" src="../images/logo/android-chrome-192x192.png" alt=""></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="../admin/dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Auction
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../admin/manage-user.php">Manage User</a>
                    <a class="dropdown-item" href="../admin/manage-auction.php">Manage Auction</a>
                </div>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="../images/profiles/profile.jpg" alt="Profile" class="rounded-circle" width="30" height="30">
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                    <div class="dropdown-item d-flex align-items-center">
                        <img src="../images/profiles/profile.jpg" alt="Profile" class="rounded-circle" width="40" height="40">
                        <div class="ml-2">
                            <p class="font-weight-bold"><?=$_SESSION["user_name"]?></p>
                            <p class="text-muted"><?= htmlspecialchars($_SESSION["role"]) ?></p>
                        </div>
                    </div>
                    <form action="" method="post">
                        <button class="dropdown-item" name="logout">Logout</button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Include Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
