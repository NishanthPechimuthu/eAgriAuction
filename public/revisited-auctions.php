<?php
ob_start();
session_start(); // Start the session
include("header.php");
include("navbar.php");

// Call the authentication function
isAuthenticated();

$user_id = $_SESSION['userId'] ?? null;

$auctions = getAuctionsParticipate($user_id);
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
    <div class="card mb-4">
      <div class="card-header">
        <i class="fa fa-user-check"></i>&nbsp;
        Participated Auctions
      </div>
      <div class="card-body">
        <?php if (empty($auctions)): ?>
        <p>
          No auctions participated in.
        </p>
        <?php else : ?>
        <div class="row">
          <?php foreach ($auctions as $auction): ?>
          <div class="col-12 col-md-6 col-lg-3 mb-4">
            <!-- Responsive column -->
            <div class="card shadow">
              <div class="position-relative">
                <!-- Badge for category -->
                <span class="badge bg-info position-absolute top-0 start-0 m-2">
                  <i class="bi bi-tag"></i> <?= htmlspecialchars(getCategoryById($auction["auctionCategoryId"])); ?>
                </span>
                <img class="shadow-sm card-img-top rounded-bottom" src="../images/products/<?=$auction['auctionProductImg'] ?>" alt="Product Image">
              </div>
              <div class="card-body">
                <h5 class="card-title text-primary mt-1">
                  <?= substr(htmlspecialchars($auction['auctionTitle']), 0, 30); ?>...
                </h5>
                <table>
                  <tr>
                    <th>Base:</th>
                    <td>&nbsp;<?="<b>&#8377;&nbsp;</b>". htmlspecialchars($auction['auctionStartPrice']) ?></td>
                    <td style="width: 20px;"></td>
                    <th>High:</th>
                    <td>&nbsp;<?='<b>&#8377;&nbsp;</b>' . htmlspecialchars(getHighestBid($auction['auctionId']) ?: "not yet.") ?></td>
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
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>