<?php
session_start();
include("header.php");
include("navbar.php");
// Retrieve counts for overview
$activeAuctions = count(getActiveAuctions());
$totalUsers = count(getAllUsers());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="max-w-4xl mx-auto py-8">
        <h2 class="text-3xl font-bold mb-6">Admin Dashboard</h2>
        <div class="grid grid-cols-2 gap-6">
            <div class="p-6 bg-green-500 text-white shadow-md rounded">
                <h3 class="text-xl">Active Auctions</h3>
                <p class="text-2xl font-bold"><?= $activeAuctions ?></p>
                <a href="manage-auction.php" class="text-white underline">Manage Auctions</a>
            </div>
            <div class="p-6 bg-blue-500 text-white shadow-md rounded">
                <h3 class="text-xl">Total Users</h3>
                <p class="text-2xl font-bold"><?= $totalUsers ?></p>
                <a href="manage-user.php" class="text-white underline">Manage Users</a>
            </div>
        </div>
    </div>
</body>
</html>
