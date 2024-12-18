<?php
// Include PHPMailer classes
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email']; // Get email from form

    // Create an instance of PHPMailer
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
        $mail->setFrom('22ct19nishanth@gmail.com', 'Madan');
        $mail->addAddress($email); // Recipient email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email';
        $mail->Body = "
          <html>
          <head>
              <style>
                  body {
                      font-family: Arial, sans-serif;
                      background-color: #f4f4f4;
                      color: #333;
                      margin: 0;
                      padding: 0;
                  }
                  .container {
                      max-width: 600px;
                      margin: 50px auto;
                      background-color: #ffffff;
                      padding: 20px;
                      border-radius: 8px;
                      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                  }
                  .content {
                      font-size: 16px;
                      line-height: 1.6;
                      color: #555;
                  }
              </style>
          </head>
          <body>
              <div class='container'>
                  <div class='content'>
                      <p>Hi, I am Madan.</p>
                      <p>This is a test email to verify the setup.</p>
                  </div>
              </div>
          </body>
          </html>
        ";

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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Email</title>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <h2 class="h4 mb-4">Send Test Email</h2>
    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" id="email" name="email" required class="form-control">
      </div>
      <button type="submit" class="btn btn-primary">Send Email</button>
    </form>
  </div>
</body>
</html>