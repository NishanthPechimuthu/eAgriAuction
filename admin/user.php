<?php
session_start(); // Start the session at the very top
ob_start(); // Start output buffering

include("header.php");

ob_end_flush(); // End buffering and flush output
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Users</title>
    <? include_once("../assets/link.html"); ?>
    <link href="../assets/styles.css" rel="stylesheet" />
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
            td{
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
  <div class="container mt-5">
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-table me-1"></i>
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
              <th>UPI ID</th>
              <th>View</th>
              <th>Delete</th>
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
              <th>UPI ID</th>
              <th>View</th>
              <th>Delete</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            // Fetch user data from the database
            $users = getAllUsers();
            $counter = 1;
            foreach ($users as $user) {
              echo "<tr>
                      <td>". $counter++ ."</td>
                      <td>{$user['userName']}</td>
                      <td>
                        <img src='../images/profiles/". htmlspecialchars($user['userProfileImg']) ."' alt='User Profile' class='rounded-1 border border-dark' width='50' height='50'>
                      </td>
                      <td>".($user['userFirstName'] ?? 'NULL')."</td>
                      <td>".($user['userLastName'] ?? 'NULL')."</td>
                      <td>".($user['userPhone'] ?? 'NULL')."</td>
                      <td>{$user['userEmail']}</td>
                      <td>".($user['userAddress'] ?? 'NULL')."</td>
                      <td>".($user['userUpiId'] ?? 'NULL')."</td>
                      <td><a class='btn btn-primary fw-bold'  href='./view-profile.php?id=".base64_encode($user['userId'])."'>View</a></td>
                  <td>
                    <form method='POST'>
                      <input type='hidden' value='".$user['userUpiId']."'/>
                      <input class='btn btn-danger fw-bold' type='submit' value='Delete'/>
                    </form>
                  </td>
                    </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
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