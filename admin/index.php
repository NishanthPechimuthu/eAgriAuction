<?php
session_start();
include '../includes/auth.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (AdminLogin($username, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <form action="index.php" method="POST" class="max-w-md mx-auto p-4 mt-12 bg-white shadow-lg">
        <h2 class="text-2xl font-bold mb-4">Login</h2>
        <?php if (isset($error)) echo "<p class='text-red-500'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Username" class="block w-full p-2 border mb-4" required>
        <input type="password" name="password" placeholder="Password" class="block w-full p-2 border mb-4" required>
        <button type="submit" class="w-full bg-blue-500 text-white py-2">Login</button>
    </form>
</body>
</html>
