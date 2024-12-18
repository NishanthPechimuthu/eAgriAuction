<?php
ob_start(); // Start output buffering
session_start();

include("header.php");
include('../tcpdf/tcpdf.php'); // Include the TCPDF library

$auction_id = $_GET['auction_id'];
$user_id = $_SESSION['userId']; // Assuming user is logged in
$highest_bid = getHighestBid($auction_id); // Get the highest bid
$sUserId = getHighestBidderId($auction_id);
$auction = getAuctionById($auction_id);
$sUser=getUserById($sUserId);
$rUser=getUserById($auction["auctionCreatedBy"]);

$trans = getInvoiceDetails($user_id,$auction_id,$highest_bid);
// Prepare the HTML content for the invoice
$html = '
    <style>
    table, tr, td {
        padding: 15px;
    }
    </style>
    <table style="background-color: #222222; color: #fff">
    <tbody>
    <tr>
    <td><h1>INVOICE<strong> #' . htmlspecialchars(explode('.', $trans["transTrackingId"])[1]) . '</strong></h1></td>
    <td align="right">
          <img src="./logos/logo.png" height="60px"/><br>
        1/283, Somvarapatti, Udumalpet, Tirppur , Tamil Nadu - 642205
        <br/>
        <strong>+91-8015864344</strong> | <strong>22ct19nishanth@gmail.com</strong>
    </td>
    </tr>
    </tbody>
    </table>
';
$html .= '
<table>
    <tbody>
        <tr style="padding: 0px;">
            <td>Invoice to<br/>
            <strong>&nbsp;&nbsp;&nbsp;&nbsp;'.htmlspecialchars($sUser["userFirstName"]." ".$sUser["userLastName"]).',</strong><br/>
            The payment has been successfully sent to user @'.$rUser["userName"].' for auction ID '.$auction["auctionId"].'
            </td>
            <td align="right">
            <strong>Total Due: &#8377;'.$highest_bid.'</strong><br/>
            GST NO: 27AAAPA1234A1Z5<br/>
            Invoice Date: '.date('d-m-Y').'
            </td>
        </tr>
        <tr>
          <td><strong>Transaction:</strong></td>
        </tr>
        <tr style="padding:5px;">
            <td>
               <br>
               <b>From: </b><br>
               <b>Name: </b>'.$sUser["userName"].'
                <br>&nbsp;&nbsp;<b>Card No: </b>'.$trans["transCardNo"].'<br>&nbsp;&nbsp;<strong>Transaction Id: </strong>'.htmlspecialchars(explode('_',explode('.', $trans["transTrackingId"])[0])[1]).'
            </td>
            <td>
                <br>
               <b>To: </b><br>
               <b>Name: </b>'.$rUser["userName"].'
                <br>&nbsp;&nbsp;<b>Account No: </b>'.$trans["transAccountNo"].'<br>&nbsp;&nbsp;<strong>Invoice Id: </strong>'.htmlspecialchars(explode('.', $trans["transTrackingId"])[1]) .'
            </td>
        </tr>
    </tbody>
</table>
';
$html .= '
<table>
    <thead>
        <tr style="font-weight:bold;">
            <th>Item name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border-bottom: 1px solid #222">'.$auction['auctionTitle'].'</td>
            <td style="border-bottom: 1px solid #222">'.htmlspecialchars(getCategoryById($auction["auctionCategoryId"])).'</td>
            <td style="border-bottom: 1px solid #222">&#8377;'.$highest_bid.'</td>
            <td style="border-bottom: 1px solid #222">1</td>
            <td style="border-bottom: 1px solid #222">&#8377;'.$highest_bid.'</td>
        </tr>
    </tbody>
</table>
';
 $html .= '
      <p style="text-align: right;"><strong>Grand total: &#8377;'.$highest_bid.'</strong></p>
      <p style="text-align: center;">
        <h2>Thank you for your business.</h2>
      </p>
     <hr>
     <span>&nbsp;&nbsp;This is a digital invoice and does not require any physical signature.</span>
     <hr>
 ';
// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(-1, 0, -1);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Set the character encoding to UTF-8
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Invoice');
$pdf->SetSubject('Auction Invoice');
$pdf->SetKeywords('TCPDF, PDF, auction, invoice');

// Use a font that supports the rupee symbol
$pdf->SetFont('freemono', '', 12);
$pdf->setFontSubsetting(true);
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);

// Output PDF directly for download
$pdf_name = 'invoice_auction_'.$auction_id.'.pdf';
$pdf->Output($pdf_name, 'D'); // Download the PDF
?>