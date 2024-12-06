<?php
ob_start();
session_start();
include("header.php");
include("navbar.php");
isAuthenticated();

// Get auction ID from query parameter
$auction_id = $_GET['id'] ?? null;
if (!$auction_id) {
    echo "Invalid auction ID.";
    exit();
}

// Fetch auction details
$auction = getAuctionById($auction_id);
if (!$auction) {
    echo "Auction not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $start_price = $_POST['start_price'] ?? 0;
    $end_date = $_POST['end_date'] ?? '';

    // Validate and sanitize inputs
    if ($title && $start_price > 0 && $end_date) {
        if (updateAuction($auction_id, $title, $start_price, $end_date)) {
            echo "Auction updated successfully!";
             header("Location: manage-auction.php");
             exit();
        } else {
            echo "Failed to update auction.";
        }
    } else {
        echo "Please provide valid details.";
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Auction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-4">Edit Auction</h2>
        <form action="edit-auction.php?id=<?= htmlspecialchars($auction_id) ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Auction Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Starting Price</label>
                <input type="number" name="start_price" min="0" value="<?= htmlspecialchars($auction['start_price']) ?>" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="datetime-local" name="start_date" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($auction['start_date']))) ?>" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">End Date</label>
                <input type="datetime-local" name="end_date" value="<?= htmlspecialchars(date('Y-m-d\TH:i', strtotime($auction['end_date']))) ?>" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Preview Imagr</label>
                   <img class="card-img-top" src="../images/products/<?=$auction['product_img']?>" alt="Product Image">
            </div>
            <button type="submit" class="btn btn-primary">Update Auction</button>
        </form>
    </div>
</body>
</html>

