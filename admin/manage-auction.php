<?php
session_start();
include("header.php");
include("navbar.php");
isAuthenticated();

function isAuthenticated() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

$auctions = getAllAuctions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        deleteAuction($_POST['auction_id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Auctions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="display-5 fw-bold mb-4">Manage Auctions</h2>
        <a href="add-auction.php" class="text-primary mb-3 d-inline-block">Add New Auction</a>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Starting Price</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (is_array($auctions) && count($auctions) > 0): ?>
                <?php foreach ($auctions as $auction): ?>
                    <tr>
                        <td><?= htmlspecialchars($auction['title']) ?></td>
                        <td>&#8377;&nbsp;<?= htmlspecialchars($auction['start_price']) ?></td>
                        <td><?= htmlspecialchars($auction['end_date']) ?></td>
                        <td>
                            <a href="edit-auction.php?id=<?= htmlspecialchars($auction['id']) ?>" class="text-primary">Edit</a> |
                            <form action="manage-auction.php" method="POST" class="d-inline">
                                <input type="hidden" name="auction_id" value="<?= htmlspecialchars($auction['id']) ?>">
                                <button type="submit" name="delete" class="btn btn-link text-danger p-0" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No auctions found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
ob_end_flush(); // Flush output buffer
?>
