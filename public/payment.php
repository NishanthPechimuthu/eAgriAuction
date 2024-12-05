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
$seller_id = $auction['auctionCreatedBy']; // Ensure this is the ID

// Get seller details including UPI ID
$seller = getUserById($seller_id);

// Retrieve the seller's UPI ID
$upi_id = getUserUpiId($seller_id);
$highest_bid = getHighestBid($auction_id);

// Ensure there is a UPI ID available
if (!$upi_id) {
  echo '
        <p class="alert alert-danger alert-dismissible fade show d-flex align-items-center"
           role="alert"  data-bs-dismiss="alert"
                  aria-label="Close"
           style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
          The seller has not provided a UPI ID.
        </p>
    ';
  exit();
}

// Check if the logged-in user is the highest bidder
$user_id = $_SESSION['userId'] ?? null; // Use session instead of cookie
$is_highest_bidder = false;

// Fetch the highest bid details
$highest_bidder = getHighestBidder($auction_id); // Assuming this function returns the user ID of the highest bidder
if ($highest_bidder['bidUserId'] == $user_id) {
  $is_highest_bidder = true;
}

// If the user is the highest bidder, proceed with payment URL generation
if ($is_highest_bidder) {
  // UPI Payment URL
  $amount = $highest_bid; // Amount to be paid
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
  echo '
        <p class="alert alert-warning alert-dismissible fade show d-flex align-items-center"
           role="alert"  data-bs-dismiss="alert"
                  aria-label="Close"
           style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
          You are not the highest bidder for this auction.
        </p>
    ';
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Make Payment</title>
  <? include_once("../assets/link.html"); ?>
  <style>
    .move-animation {
      animation: MoveAni 3s infinite alternate cubic-bezier(1, 1, 1, 1);
      display: inline-block;
    }

    @keyframes MoveAni {
      0% {
        transform: translateX(-100%);
      }
      25% {
        transform: translateX(-50%);
      }
      50% {
        transform: translateX(0);
      }
      75% {
        transform: translateX(50%);
      }
      100% {
        transform: translateX(100%);
      }
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-wallet me-1"></i>
        Payment
      </div>
      <div class="card-body text-center">
        <p class="lead">
          Auction: <?= htmlspecialchars($auction['auctionTitle']) ?>
        </p>
        <p class="fs-4">
          Amount to Pay: &#8377;&nbsp;<?= htmlspecialchars($amount) ?>
        </p>
        <!-- UPI Link -->
        <a href="<?= htmlspecialchars($upi_link) ?>" class="btn btn-success mt-3" target="_blank">
          Pay with UPI
        </a>
        <!-- Display QR Code -->
        <div class="mt-4 flex justify-center">
          <img src="data:image/png;base64,<?= $qr_code_image_base64 ?>" alt="Scan to Pay with UPI has error&nbsp;&nbsp;Click above pay by upi button" class="img-fluid" style="border: 2px solid #bcbcbc; border-radius: 5px;">
        </div>
        <div class="mt-4 d-flex justify-content-center align-items-center p-2 border rounded-pill ">
          <a class="d-flex justify-content-center align-items-center move-animation" style="text-decoration: none;" href="./view-profile.php?id=<?=base64_encode($auction['auctionCreatedBy']) ?>">
            <img src="../images/profiles/<?= $img = getUserImg($auction['auctionCreatedBy']) ?? 'profile.webp'; ?>"
            alt="User Profile" class="rounded-circle" width="30" height="30" style="margin-right: 8px;">
            <p style="margin: 0; color: black;">
              <?= htmlspecialchars(getUserName($auction['auctionCreatedBy'])) ?>
            </p>
          </a>
        </div>
        <p class="mt-4">
          Or scan the QR code to pay
        </p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?
  include_once("./footer.php");
  ob_end_flush();
?>