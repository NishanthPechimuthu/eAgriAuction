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
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            // Fetch user data from the database
            $users = getAllUsers();
            foreach ($users as $user) {
              echo "<tr>
                            <td>{$user['userId']}</td>
                            <td>{$user['userName']}</td>
                            <td>{$user['userEmail']}</td>
                            <td>{$user['userRole']}</td>
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