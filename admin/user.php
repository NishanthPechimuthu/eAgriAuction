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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="../assets/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <style>
    td{
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

    /* Improve the responsiveness for smaller screens */
    @media (max-width: 768px) {
      th, td {
        font-size: 12px;
        padding: 5px;
      }

      td img {
        width: 30px;
        height: 30px;
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
        <table id="datatablesSimple">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="js/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
  <script src="../assets/js/datatables-simple-demo.js"></script>
</body>
</html>