<?php
ob_start();
include 'db.php';

if (!function_exists('isAuthenticated')) {
    function isAuthenticated() {
        if (isset($_SESSION['userId'])) {
          $user=getUserById($_SESSION['userId']);
          $_SESSION['userId'] = $user['userId'];
          $_SESSION['userName'] = $user['userName'];
          $_SESSION['userRole'] = $user['userRole'];
          $_SESSION['userProfileImg'] = $user['userProfileImg'];
          return true;
        } else {
            header("Location: ../public/login.php");
            exit();
        }
    }
}

// Login function
function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT userId, userName, userRole, userEmail, userProfileImg, userPassword FROM users WHERE userName = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Log user data for debugging
    error_log("User Data: " . print_r($user, true));

    // Verify password
    if ($user && password_verify($password, $user['userPassword'])) {
        // Set session and cookies for persistence
        $_SESSION['userId'] = $user['userId'];
        $_SESSION['userName'] = $user['userName'];
        $_SESSION['userRole'] = $user['userRole'];
        $_SESSION['userEmail'] = $user['userEmail'];
        $_SESSION['userProfileImg'] = $user['userProfileImg'];
        return true;
    }
    return false;
}

// Admin Login function
function AdminLogin($username, $password) {
    global $pdo;

    if (empty($username) || empty($password)) {
        return false; // Or handle as needed
    }

    $role = "admin";
    try {
        $stmt = $pdo->prepare("SELECT userId, userName, userRole, userPassword FROM users WHERE userName = :username AND userRole = :role");
        $stmt->execute(['username' => $username, 'role' => $role]);
        $user = $stmt->fetch();

        // Verify password
        if ($user && password_verify($password, $user['userPassword'])) {
            $_SESSION['userId'] = $user['userId'];
            $_SESSION['userName'] = $user['userName'];
            $_SESSION['userRole'] = $user['userRole'];
            return true;
        }
} catch (PDOException $e) {
    // Log error or handle accordingly
    echo '
      <p class="alert alert-warning alert-dismissible fade show d-flex align-items-center" 
         role="alert" data-bs-dismiss="alert" 
         aria-label="Close" 
         style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
        ' . 'Database error: ' . $e->getMessage() . '
      </p>
    ';
}
    
    return false;
}

// Register function
function register($username, $email, $password) {
    global $pdo;

    try {
        // Check if the username already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE userName = :username");
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() > 0) {
            echo '
                <p class="alert alert-warning alert-dismissible fade show d-flex align-items-center" 
                   role="alert" data-bs-dismiss="alert" 
                   aria-label="Close" 
                   style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
                   User with that username already exists.
                </p>
            ';
            return false; // Exit function early if username already exists
        }

        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE userEmail = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            echo '
                <p class="alert alert-warning alert-dismissible fade show d-flex align-items-center" 
                   role="alert" data-bs-dismiss="alert" 
                   aria-label="Close" 
                   style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
                   User with that email already exists.
                </p>
            ';
            return false; // Exit function early if email already exists
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user
        $stmt = $pdo->prepare("INSERT INTO users (userName, userEmail, userPassword, userOldPassword) VALUES (:username, :email, :password, :oldpassword)");

        // Execute the insert query
        if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword, 'oldpassword' => $hashedPassword])) {
            return true; // Registration successful
        } else {
            throw new Exception("An error occurred while inserting the data.");
        }

    } catch (PDOException $e) {
        // Handle PDOException (database issues)
        echo '
            <p class="alert alert-danger alert-dismissible fade show d-flex align-items-center" 
               role="alert" data-bs-dismiss="alert" 
               aria-label="Close" 
               style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
               Database error: ' . $e->getMessage() . '
            </p>
        ';
    } catch (Exception $e) {
        // Handle other exceptions (e.g., errors during the execution)
        echo '
            <p class="alert alert-danger alert-dismissible fade show d-flex align-items-center" 
               role="alert" data-bs-dismiss="alert" 
               aria-label="Close" 
               style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
               Error: ' . $e->getMessage() . '
            </p>
        ';
    }
}

// Logout function
    function logout() {
        // Clear session and cookies
        session_unset();
        session_destroy();
        
        header("Location: index.php");
        exit();
    }

function deleteUser($user){
    global $pdo;
    // Check if the username or email already exists
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user");
    $stmt->execute(['user' => $user]);
    }
    ob_end_flush();
?>
