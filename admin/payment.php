<?php
session_start(); // Start the session
include("header.php");
include("navbar.php");

// Call the authentication function
isAuthenticated();

$auction_id = $_GET['auction_id'] ?? null;
if (!$auction_id) {
    echo "Invalid auction ID.";
    exit();
}

// Get auction details
$auction = getAuctionById($auction_id);
$seller_id = $auction['created_by']; // Ensure this is the ID

// Get seller details including UPI ID
$seller = getUserById($seller_id);

// Retrieve the seller's UPI ID
$upi_id = getUserUpiId($seller_id);
$highest_bid = getHighestBid($auction_id);

// Ensure there is a UPI ID available
if (!$upi_id) {
    echo "The seller has not provided a UPI ID.";
    exit();
}

// Check if the logged-in user is the highest bidder
$user_id = $_SESSION['user_id'] ?? null; // Use session instead of cookie
$is_highest_bidder = false;

// Fetch the highest bid details
$highest_bidder = getHighestBidder($auction_id); // Assuming this function returns the user ID of the highest bidder
if ($highest_bidder && $highest_bidder['user_id'] == $user_id) {
    $is_highest_bidder = true;
}

// If the user is the highest bidder, proceed with payment URL generation
if ($is_highest_bidder) {
    // UPI Payment URL
    $amount = $highest_bid;  // Amount to be paid
    $upi_link = "upi://pay?pa={$upi_id}&pn=" . urlencode($seller['name']) . "&am={$amount}&cu=INR";

    // Load PHP QR Code Library
    require_once '../phpqrcode/qrlib.php'; // Adjust path as necessary

    // Generate QR Code directly to output
    ob_start(); // Start output buffering
    QRcode::png($upi_link, null, QR_ECLEVEL_L, 4); // Output directly
    $qr_code_image = ob_get_contents(); // Get the output
    ob_end_clean(); // Clean the buffer
    $qr_code_image_base64 = base64_encode($qr_code_image); // Encode as base64 for inline use
} else {
    echo "You are not the highest bidder for this auction.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Make Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="text-center">
            <h2 class="display-5 mb-4">Payment for Auction</h2>
            <p class="lead">Auction: <?= htmlspecialchars($auction['title']) ?></p>
            <p class="fs-4">Amount to Pay: &#8377;&nbsp;<?= htmlspecialchars($amount) ?></p>

            <!-- UPI Link -->
            <a href="<?= htmlspecialchars($upi_link) ?>" class="btn btn-success mt-3" target="_blank">
                Pay with UPI
            </a>
            <!-- Display QR Code -->
            <div class="mt-5 flex justify-center">
                <img src="data:image/png;base64,<?= $qr_code_image_base64 ?>" alt="Scan to Pay with UPI has error&nbsp;&nbsp;Click above pay by upi button" class="img-fluid">
            </div>
            <p class="mt-3">Or scan the QR code to pay</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
