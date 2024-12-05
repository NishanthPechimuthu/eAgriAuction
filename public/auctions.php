<?php
ob_start();
session_start(); // Start the session
include("header.php");
include("navbar.php");

// Call the authentication function
isAuthenticated();

// Fetch active auctions
$auctions = getActiveAuctions();
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auctions</title>
  <? include_once("../assets/link.html"); ?>
</head>
<body>
  <div class="container py-5">
    <div class="card">
      <div class="card-header">
        <i class="fa fa-balance-scale"></i>&nbsp;
        Ongoing Auctions
      </div>
      <div class="card-body">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
          <?php foreach ($auctions as $auction): ?>
          <div class="col mb-4">
            <div class="card shadow">
              <div class="position-relative">
                <!-- Badge for category -->
                <span class="badge bg-info position-absolute top-0 start-0 m-2">
                  <i class="bi bi-tag"></i> <?= $categ = getCategoryById($auction["auctionCategoryId"]); ?>
                </span>

                <img class="shadow-sm card-img-top rounded-2" src="../images/products/<?=$auction['auctionProductImg'] ?>" alt="Product Image">
              </div>

              <div class="card-body">
                <h5 class="card-title text-primary mt-1">
                  <?php echo substr(htmlspecialchars($auction['auctionTitle']), 0, 30); ?>...
                </h5>
                <table class="table table-sm table-borderless">
                  <tr>
                    <th>Base:</th>
                    <td><?="<b>&#8377;&nbsp;</b>" . htmlspecialchars($auction['auctionStartPrice']) ?></td>
                    <td></td>
                    <th>High:</th>
                    <td><?='<b>&#8377;&nbsp;</b>' . htmlspecialchars(getHighestBid($auction['auctionId']) ? getHighestBid($auction['auctionId']) : "not yet.") ?></td>
                  </tr>
                </table>
                <p>
                  <strong>End:</strong> <?= htmlspecialchars($auction['auctionEndDate']) ?>
                </p>
                <a href="bid.php?id=<?= $auction['auctionId'] ?>" class="btn btn-primary">Place Bid</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>