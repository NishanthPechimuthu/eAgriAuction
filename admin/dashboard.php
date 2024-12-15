<?php
session_start();
include("header.php");
include("navbar.php");
// Retrieve counts for overview
$totalAuctions = count(getAllAuctions());
$totalUsers = count(getAllUsers());
$totalInactivateUsers = count(getInactivateUsers());
$totalBids = count(getAllBid());

//get All Users
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <? include("../assets/link.html"); ?>
    <link rel="stylesheet" href="../assets/css/table-styles.css">
    <style>
        td {
            height: 50px;
            line-height: 50px;
        }
        td, th {
            min-width: 100px;
            max-width: 140px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            overflow: auto;
            padding: 10px;
        }
        @media (max-width: 768px) {
            td {
                height: 40px;
                line-height: 40px;
            }
            th, td {
                font-size: 12px;
                padding: 5px;
            }
            td img {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
  <div class="container-fluid px-4">
      <h1 class="mt-4">Dashboard</h1>
      <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item active">Dashboard</li>
      </ol>
      <div class="row">
          <div class="col-xl-3 col-md-6">
              <div class="card bg-primary text-white mb-4">
                  <div class="card-body">
                      <i
                          class="fa fa-user"
                          style="font-size: 2rem"
                      ></i>
                      &nbsp;&nbsp;
                      <span
                          style="font-size: 2rem"
                          class="fw-bold"
                          ><?=$totalUsers?></span
                      >
                  </div>
                  <div
                      class="card-footer d-flex align-items-center justify-content-between"
                  >
                      <a
                          class="small text-white stretched-link"
                          href="./manage-user.php"
                          >View Users</a
                      >
                      <div class="small text-white">
                          <i class="fas fa-angle-right"></i>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-xl-3 col-md-6">
              <div class="card bg-warning text-white mb-4">
                  <div class="card-body">
                      <i
                          class="fa fa-gavel"
                          style="font-size: 2rem"
                      ></i>
                      &nbsp;&nbsp;
                      <span
                          style="font-size: 2rem"
                          class="fw-bold"
                          ><?=$totalAuctions?></span
                      >
                  </div>
                  <div
                      class="card-footer d-flex align-items-center justify-content-between"
                  >
                      <a
                          class="small text-white stretched-link"
                          href="./manage-auction.php"
                          >View Auctions</a
                      >
                      <div class="small text-white">
                          <i class="fas fa-angle-right"></i>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-xl-3 col-md-6">
              <div class="card bg-success text-white mb-4">
                  <div class="card-body">
                      <i
                          class="fa fa-line-chart"
                          style="font-size: 2rem"
                      ></i>
                      &nbsp;&nbsp;
                      <span
                          style="font-size: 2rem"
                          class="fw-bold"
                          ><?=$totalBids?></span
                      >
                  </div>
                  <div
                      class="card-footer d-flex align-items-center justify-content-between"
                  >
                      <a
                          class="small text-white stretched-link"
                          href="./manage-bid.php"
                          >View Bids</a
                      >
                      <div class="small text-white">
                          <i class="fas fa-angle-right"></i>
                      </div>
                  </div>
              </div>
          </div>
          <div class="col-xl-3 col-md-6">
              <div class="card bg-danger text-white mb-4">
                  <div class="card-body">
                      <i
                          class="fa fa-user-times"
                          style="font-size: 2rem"
                      ></i>
                      &nbsp;&nbsp;
                      <span
                          style="font-size: 2rem"
                          class="fw-bold"
                          ><?=$totalInactivateUsers?></span
                      >
                  </div>
                  <div
                      class="card-footer d-flex align-items-center justify-content-between"
                  >
                      <a
                          class="small text-white stretched-link"
                          href="./manage-inactivate.php"
                          >View Inactive Users</a
                      >
                      <div class="small text-white">
                          <i class="fas fa-angle-right"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="row">
       <? include("registration-chart.php"); ?>
       <? include("bid-chart.php"); ?>
     </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="card mb-4">
          <div class="card-header">
              <i class="fas fa-user  me-1"></i>
              Users Table
          </div>
          <div class="card-body">
              <table id="usersTable">
                  <thead>
                      <tr>
                          <th>S/No</th>
                          <th>Name</th>
                          <th>Profile</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Phone</th>
                          <th>Email</th>
                          <th>Address</th>
                      </tr>
                  </thead>
                  <tfoot>
                      <tr>
                          <th>S/No</th>
                          <th>Name</th>
                          <th>Profile</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Phone</th>
                          <th>Email</th>
                          <th>Address</th>
                      </tr>
                  </tfoot>
                  <tbody>
                      <?php
                      $counter = 1;
                      foreach ($users as $user) {
                          echo "<tr>
                                  <td>{$counter}</td>
                                  <td>{$user['userName']}</td>
                                  <td>
                                      <img src='../images/profiles/" . htmlspecialchars($user['userProfileImg']) . "' 
                                           alt='User Profile' class='rounded-1 border border-dark' 
                                           width='50' height='50'>
                                  </td>
                                  <td>" . ($user['userFirstName'] ?? 'NULL') . "</td>
                                  <td>" . ($user['userLastName'] ?? 'NULL') . "</td>
                                  <td>" . ($user['userPhone'] ?? 'NULL') . "</td>
                                  <td>{$user['userEmail']}</td>
                                  <td>" . ($user['userAddress'] ?? 'NULL') . "</td>
              </tr>";
                          $counter++;
                      }
                      ?>
                  </tbody>
              </table>
          </div>
      </div>
        </div>
     </div>
</div>
  <? include("./footer.php"); ?>
<script>
  
</script>
<script>
    window.addEventListener('DOMContentLoaded', event => {
        const datatablesSimple = document.getElementById('usersTable');
        if (datatablesSimple) {
            new simpleDatatables.DataTable(datatablesSimple);
        }
    });
</script>
</body>
</html>