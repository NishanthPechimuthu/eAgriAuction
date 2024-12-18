<?php
session_start();
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $mail->addAddress("nishanthpechimuthu@gmail.com"); // Recipient email

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
<p>Dear <b>Nishanth P</b>,</p>
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
                        Name: @blk<br>
                        Card No: 599u<br>
                        Transaction ID: 578t555v
                    </td>
                    <td>
                        <b>To:</b><br>
                        Name: thh<br>
                        Account No: rtyy45<br>
                        Invoice ID: 5786ty
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
                    <td>Black Grass</td>
                    <td>Grass</td>
                    <td>₹899</td>
                    <td>1</td>
                    <td>₹899</td>
                </tr>
            </tbody>
        </table>

        <p class="total">Grand Total: ₹899</p>
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
        <? include("../assets/link.html"); ?>