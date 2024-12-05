<?php
session_start(); // Start the session at the very top
ob_start(); // Start output buffering

include("header.php");
include("navbar.php");
isAuthenticated();

$users = getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        deleteUser($_POST['user_id']);
        header("Location: manage-user.php");
        exit(); // Ensure exit after header redirect
    }
}

ob_end_flush(); // End buffering and flush output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center fw-bold mb-4">Manage Users</h2>
        <div class="table-responsive"> <!-- Added scrollable container -->
            <table class="table table-bordered shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th class="p-3">Username</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="p-3"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($user['role']) ?></td>
                            <td class="p-3">
                                <form action="manage-user.php" method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-link text-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
