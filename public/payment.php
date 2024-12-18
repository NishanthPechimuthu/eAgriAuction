<?php
session_start();
include("header.php");
include("navbar.php");
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
isAuthenticated();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
$sUserId = getHighestBidderId($auction_id);
$auction = getAuctionById($auction_id);
$sUser = getUserById($sUserId);
$rUser = getUserById($auction["auctionCreatedBy"]);
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


    $mail = new PHPMailer(true);
$trans = getInvoiceDetails($sUserId, $auction_id, $highest_bid);
    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '22ct19nishanth@gmail.com'; // Your Gmail address
        $mail->Password = 'aynqaraezfiltmwh'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('22ct19nishanth@gmail.com', 'e-Agri Auction');
        $mail->addAddress($email); // Recipient email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful for your Order';
        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
<h3>Order Confirmation</h3>
<p>Dear <b>' . htmlspecialchars($sUser["userFirstName"]) . '&nbsp;' . htmlspecialchars($sUser["userLastName"]) . '</b>,</p>
<p>Your payment for the order has been successfully processed. Below are the details:</p>
<h3>Transaction Details</h3>
        <table>
            <thead>
                <tr>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <b>From:</b><br>
                        Name:'. htmlspecialchars($sUser["userName"]).'<br>
                        Card No: '.htmlspecialchars($trans["transCardNo"]).'<br>
                        Transaction ID: '. htmlspecialchars(explode('_', explode('.', $trans["transTrackingId"])[0])[1]).'
                    </td>
                    <td>
                        <b>To:</b><br>
                        Name: '.htmlspecialchars($rUser["userName"]).'<br>
                        Account No: '. htmlspecialchars($trans["transAccountNo"]) .'<br>
                        Invoice ID: '. htmlspecialchars(explode('.', $trans["transTrackingId"])[1]) .'
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'. htmlspecialchars($auction["auctionTitle"]).'</td>
                    <td>'. htmlspecialchars(getCategoryById($auction["auctionCategoryId"])).'</td>
                    <td>₹'. htmlspecialchars($highest_bid).'</td>
                    <td>1</td>
                    <td>₹'. htmlspecialchars($highest_bid).'</td>
                </tr>
            </tbody>
        </table>

        <p class="total">Grand Total: ₹'. htmlspecialchars($highest_bid).'</p>
        <p class="footer">
            <h2>Thank you for your business!</h2>
            We appreciate your trust in our services.
        </p>
        
    </div>
</body>
</html>
        ';

        // Send email
        if ($mail->send()) {
            echo "<p class='alert alert-success alert-dismissible fade show' role='alert'>
                    Email has been sent successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </p>";
        } else {
            echo "<p class='alert alert-warning alert-dismissible fade show' role='alert'>
                    Failed to send email. Error: " . $mail->ErrorInfo . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </p>";
        }
    } catch (Exception $e) {
        echo "<p class='alert alert-danger alert-dismissible fade show' role='alert'>
                Email could not be sent. Error: {$mail->ErrorInfo}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </p>";
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '22ct19nishanth@gmail.com'; // Your Gmail address
        $mail->Password = 'aynqaraezfiltmwh'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('22ct19nishanth@gmail.com', 'e-Agri Auction');
        $mail->addAddress($sUser["userEmailc"]); // Recipient email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Payment Confirmation for Auction ID: '.$auction_id;
        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h3 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
<h3>Auction Payment Process</h3>
<p>Dear <b>'.htmlspecialchars($rUser["userFirstName"]) .'&nbsp;'. htmlspecialchars($rUser["userLastName"]).'</b>,</p>
<p>You have received the payment for auction ID '. $auction_id .'. The client has successfully received the item from you. Below are the details:</p>
<h3>Transaction Details</h3>
        <table>
            <thead>
                <tr>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <b>From:</b><br>
                        Name:'. htmlspecialchars($sUser["userName"]).'<br>
                        Card No: '.htmlspecialchars($trans["transCardNo"]).'<br>
                        Transaction ID: '. htmlspecialchars(explode('_', explode('.', $trans["transTrackingId"])[0])[1]).'
                    </td>
                    <td>
                        <b>To:</b><br>
                        Name: '.htmlspecialchars($rUser["userName"]).'<br>
                        Account No: '. htmlspecialchars($trans["transAccountNo"]) .'<br>
                        Invoice ID: '. htmlspecialchars(explode('.', $trans["transTrackingId"])[1]) .'
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>'. htmlspecialchars($auction["auctionTitle"]).'</td>
                    <td>'. htmlspecialchars(getCategoryById($auction["auctionCategoryId"])).'</td>
                    <td>₹'. htmlspecialchars($highest_bid).'</td>
                    <td>1</td>
                    <td>₹'. htmlspecialchars($highest_bid).'</td>
                </tr>
            </tbody>
        </table>

        <p class="total">Grand Total: ₹'. htmlspecialchars($highest_bid).'</p>
        <p class="footer">
            <h2>Thank you for your business!</h2>
            We appreciate your trust in our services.
        </p>
    </div>
</body>
</html>
        ';

        // Send email
        if ($mail->send()) {
            echo "<p class='alert alert-success alert-dismissible fade show' role='alert'>
                    Email has been sent successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </p>";
        } else {
            echo "<p class='alert alert-warning alert-dismissible fade show' role='alert'>
                    Failed to send email. Error: " . $mail->ErrorInfo . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </p>";
        }
    } catch (Exception $e) {
        echo "<p class='alert alert-danger alert-dismissible fade show' role='alert'>
                Email could not be sent. Error: {$mail->ErrorInfo}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </p>";
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
