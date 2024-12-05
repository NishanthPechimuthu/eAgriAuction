<?php
ob_start(); // Start output buffering

include("header.php");
include("navbar.php");

// Call the authentication function
isAuthenticated();

// Retrieve user ID from the session
$user_id = $_SESSION['user_id']; // Assuming 'user_id' is stored in the session
$auctions = getUsersAuctions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete'])) {
    deleteAuction($_POST['auction_id']);
    header("Location: manage-auction.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Manage Auction</title>
  <? include_once("../assets/link.html"); ?>
  <style>
    /* Ensures the table container allows horizontal scrolling */
    .table-container {
      overflow-x: auto;
    }
    table {
      border-collapse: collapse;
    }
    th, td {
      text-align: center;
      vertical-align: middle;
    }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div class="card mb-4">
      <div class="card-header">
        <i class="fa fa-cogs"></i>&nbsp;
        Manage Auction
      </div>
      <div class="card-body">
        <div class="table-container">
          <table class="table table-bordered">
            <thead class="table-dark">
              <tr>
                <th rowspan="2" style="white-space: nowrap; text-align: center; vertical-align: middle;">S. No</th>
                <th rowspan="2" style="white-space: nowrap; text-align: center; vertical-align: middle;">Title</th>
                <th colspan="2" rowspan="2" style="white-space: nowrap; text-align: center; vertical-align: middle;">Image</th>
                <th colspan="2">Price (<span>&#8377;</span>)</th>
                <th colspan="2">Date</th>
                <th rowspan="2" style="white-space: nowrap; text-align: center; vertical-align: middle;">Status</th>
                <th colspan="3">Actions</th>
              </tr>
              <tr>
                <th>Base</th>
                <th>High</th>
                <th>Start</th>
                <th>End</th>
                <th>View</th>
                <th>Edit</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $count = 1;
              if (is_array($auctions) && count($auctions) > 0): ?>
              <?php foreach ($auctions as $auction): ?>
              <tr>
                <td style="text-align: center; vertical-align: middle;"><?= $count++ ?></td>
                <td style="white-space: nowrap; text-align: center; vertical-align: middle;">
                  <div style="max-height: 50px; overflow-y: auto; max-width: 100px;">
                    <?=$auction['auctionTitle']; ?>
                  </div>
                </td>
                <td colspan="2">
                  <img src="../images/products/<?= htmlspecialchars($auction['auctionProductImg']) ?>" alt="Profile" class="rounded-1 border border-dark" width="50" height="50">
                </td>
                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars($auction['auctionStartPrice']) ?></td>
                <td style="text-align: center; vertical-align: middle;"><?= htmlspecialchars(getHighestBid($auction['auctionId'])) ?></td>
                <td style="text-align: center; vertical-align: middle;">
                  <?php
                  $startDate = new DateTime($auction['auctionStartDate']);
                  echo $startDate->format('d/m/Y');
                  ?>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                  <?php
                  $endDate = new DateTime($auction['auctionEndDate']);
                  echo $endDate->format('d/m/Y');
                  ?>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                  <p class="badge rounded-pill
                    <?php
                    if ($auction['auctionStatus'] == "activate") {
                      echo "bg-success text-white";
                    } elseif ($auction['auctionStatus'] == "deactivate") {
                      echo "bg-warning text-dark";
                    } else {
                      echo "bg-danger text-white";
                    }
                    ?> m-0">
                    <?= htmlspecialchars($auction['auctionStatus']) ?>
                  </p>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                  <a href="bid.php?id=<?= $auction['auctionId'] ?>" class="btn btn-info btn-sm fw-bold text-white align-items-center">View</a>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                  <a href="edit-auction.php?auctionId=<?= htmlspecialchars($auction['auctionId']) ?>" class="btn btn-warning btn-sm fw-bold text-dark">Edit</a>
                </td>
                <td style="text-align: center; vertical-align: middle;">
                  <form action="manage-auction.php" method="POST" class="d-inline">
                    <input type="hidden" name="auction_id" value="<?= htmlspecialchars($auction['auctionId']) ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm fw-bold text-white" onclick="return confirm('Are you sure?')">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php else : ?>
              <tr>
                <td colspan="12" class="text-center">No auctions found.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?
  include_once("./footer.php");
  ob_end_flush();
?>