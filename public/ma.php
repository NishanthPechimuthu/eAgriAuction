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

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  
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
    .dataTables_wrapper {
      overflow-x: auto;
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
          <table id="auctionTable" class="table table-bordered m-2">
            <thead class="table-dark">
              <tr>
                <th style="min-width: 40px;">S. No</th>
                <th style="min-width: 100px;">Title</th>
                <th style="min-width: 50px;">Image</th>
                <th style="min-width: 100px;">Base Price (₹)</th>
                <th style="min-width: 100px;">High Price (₹)</th>
                <th style="min-width: 100px;">Start Date</th>
                <th style="min-width: 100px;">End Date</th>
                <th style="min-width: 80px;">Status</th>
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
                <td><?= $count++ ?></td>
                <td>
                  <div style="max-width: 100px;
                  white-space: nowrap;
                  overflow-x: auto;">
                    <?= htmlspecialchars($auction['auctionTitle']); ?>
                  </div>
                </td>
                <td>
                  <img src="../images/products/<?= htmlspecialchars($auction['auctionProductImg']) ?>" alt="Product Image" class="rounded-1 border border-dark" width="50" height="50">
                </td>
                <td><?= htmlspecialchars($auction['auctionStartPrice']) ?></td>
                <td><?= htmlspecialchars(getHighestBid($auction['auctionId'])) ?></td>
                <td>
                  <?php
                  $startDate = new DateTime($auction['auctionStartDate']);
                  echo $startDate->format('d/m/Y');
                  ?>
                </td>
                <td>
                  <?php
                  $endDate = new DateTime($auction['auctionEndDate']);
                  echo $endDate->format('d/m/Y');
                  ?>
                </td>
                <td>
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
                <td>
                  <a href="bid.php?id=<?= $auction['auctionId'] ?>" class="btn btn-info btn-sm fw-bold text-white align-items-center">View</a>
                </td>
                <td>
                  <a href="edit-auction.php?auctionId=<?= htmlspecialchars($auction['auctionId']) ?>" class="btn btn-warning btn-sm fw-bold text-dark">Edit</a>
                </td>
                <td>
                  <form action="manage-auction.php" method="POST" class="d-inline">
                    <input type="hidden" name="auction_id" value="<?= htmlspecialchars($auction['auctionId']) ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm fw-bold text-white" onclick="return confirm('Are you sure?')">Delete</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php else : ?>
              <tr>
                <td colspan="11" class="text-center">No auctions found.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#auctionTable').DataTable({
        "lengthMenu": [5, 10, 15, 20, 25], // Options for rows per page
        "paging": true, // Enable pagination
        "searching": true, // Enable search
        "info": true // Show information
      });
    });
  </script>
</body>
</html>

<?
  include_once("./footer.php");
  ob_end_flush();
?>