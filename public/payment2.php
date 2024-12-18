<?php
session_start();
include("header.php");
include("navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['userId'])) {
    echo "You need to be logged in to make a payment.";
    exit();
}

$user_id = $_SESSION['userId'];
$auction_id = $_GET['auction_id'] ?? null;

if (!$auction_id) {
    echo "Invalid auction ID.";
    exit();
}

// Get auction details
$auction = getAuctionById($auction_id);
$highest_bid = getHighestBid($auction_id);
$accountNo = getUserAccountNo($auction["auctionCreatedBy"]);

// Check if the user is the highest bidder
$is_highest_bidder = false;
$highest_bidder = getHighestBidder($auction_id);
if ($highest_bidder['bidUserId'] == $user_id) {
    $is_highest_bidder = true;
}

if (!$is_highest_bidder) {
    echo "You are not the highest bidder for this auction.";
    exit();
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $cardNumber = $_POST['cardNumber'] ?? null;
    $expiryMonth = $_POST['expiryMonth'] ?? null;
    $expiryYear = $_POST['expiryYear'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    if (!$username || !$cardNumber || !$expiryMonth || !$expiryYear || !$cvv) {
        echo "Please complete the payment form.";
        exit();
    }

    // Generate a unique transaction tracking ID
    $transaction_tracking_id = uniqid('txn_', true);

    try {
        // Insert transaction details into the database
        $query = "INSERT INTO trans 
                  (transTrackingId, transCardNo, transAccountNo, transUserId, transAmount, transAuctionId) 
                  VALUES 
                  (:transTrackingId, :transCardNo, :transAccountNo, :transUserId, :transAmount, :transAuctionId)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':transTrackingId' => $transaction_tracking_id,
            ':transCardNo' => $cardNumber,
            ':transAccountNo' => $accountNo,
            ':transUserId' => $user_id,
            ':transAmount' => $highest_bid,
            ':transAuctionId' => $auction_id
        ]);

        // Check if the transaction was successful
        if ($stmt->rowCount() > 0) {
            // JavaScript for SweetAlert2 success pop-up
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                  <script>
                      $(document).ready(function() {
                          Swal.fire({
                              title: 'Payment Successful!',
                              text: 'Transaction ID: $transaction_tracking_id\\nAmount Paid: ₹$highest_bid\\n',
                              icon: 'success',
                              confirmButtonText: 'OK',
                              confirmButtonColor: '#28a745',
                              allowOutsideClick: false
                          }).then((result) => {
                              if (result.isConfirmed) {
                                  window.location.href = 'bid.php?id=".$auction_id."'; // Redirect to another page
                              }
                          });
                      });
                  </script>";
        } else {
            echo "<div class='alert alert-danger'>Payment failed. Please try again later.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }

    exit(); // End the script here after payment processing
}

?>
<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>Payment</title>
        <? include("../assets/link.html"); ?>
        <style>
            ::-webkit-scrollbar {
                width: 8px;
            }
            /* Track */
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            /* Handle */
            ::-webkit-scrollbar-thumb {
                background: #888;
            }
            /* Handle on hover */
            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }
            body {
                background: #f5f5f5;
            }
            .rounded {
                border-radius: 1rem;
            }
            .nav-pills .nav-link {
                color: #555;
            }
            .nav-pills .nav-link.active {
                color: white;
            }
            input[type="radio"] {
                margin-right: 5px;
            }
            .bold {
                font-weight: bold;
            }
        </style>
    </head>
    <body className='snippet-body'>
        <div class="container py-5">
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-6">Payment for Auction #<?php echo $auction_id; ?></h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
                                    <li class="nav-item"> 
                                        <a data-toggle="pill" class="nav-link bg-success text-white fw-bolder"> 
                                            <i class="fas fa-credit-card mr-2 fw-bolder"></i> Credit Card 
                                        </a> 
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div id="credit-card" class="tab-pane fade show active pt-3">
                                    <form role="form" method="POST">
                                        <div class="form-group"> 
                                            <label for="username">
                                                <h6>Card Owner</h6>
                                            </label> 
                                            <input type="text" name="username" placeholder="Card Owner Name" required class="form-control"> 
                                        </div>
                                        <div class="form-group"> 
                                            <label for="cardNumber">
                                                <h6>Card number</h6>
                                            </label>
                                            <div class="input-group"> 
                                                <input type="text" name="cardNumber" placeholder="Valid card number" class="form-control" required>
                                                <div class="input-group-append"> 
                                                    <span class="input-group-text text-muted"> 
                                                        <i class="fab fa-cc-visa mx-1"></i> 
                                                        <i class="fab fa-cc-mastercard mx-1"></i> 
                                                        <i class="fab fa-cc-amex mx-1"></i> 
                                                    </span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group"> 
                                                    <label>
                                                        <h6>Expiration Date</h6>
                                                    </label>
                                                    <div class="input-group"> 
                                                        <input type="number" placeholder="MM" name="expiryMonth" class="form-control" required> 
                                                        <input type="number" placeholder="YY" name="expiryYear" class="form-control" required> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group mb-4"> 
                                                    <label data-toggle="tooltip" title="Three digit CV code on the back of your card">
                                                        <h6>CVV <i class="fa fa-question-circle d-inline"></i></h6>
                                                    </label> 
                                                    <input type="text" name="cvv" required class="form-control"> 
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block fw-bolder"> Confirm Payment </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type='text/javascript'>$(function() {
          $('[data-toggle="tooltip"]').tooltip()
        })</script>
    </body>
</html>