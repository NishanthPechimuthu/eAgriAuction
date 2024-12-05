<?php
ob_start(); // Start output buffering

include 'db.php';

// Set a session variable for user ID
function setUserSession($userId) {
    $_SESSION['$userId'] = $userId;
}

// Get user ID from session
function getUserFromSession() {
    if (isset($_SESSION["userId"])) {
        return $_SESSION["userId"]; 
    } else {
        return null; 
    }
}


// Fetch all active auctions
function getActiveAuctions() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM auctions WHERE auctionEndDate > NOW() AND auctionStatus = 'activate'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all participated auctions
function getAuctionsParticipate() {
    global $pdo;
    $user_id = getUserFromSession();
    
    if (!$user_id) {
        header("Location: ../local/login.php"); // User not authenticated
    }

    $sql = "SELECT DISTINCT a.* FROM auctions a JOIN bids b ON a.auctionId = b.bidAuctionId WHERE NOW() > a.auctionEndDate AND b.bidUserId = :user_id AND a.auctionStatus = 'activate';";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch a single auction by its ID
function getAuctionById($auction_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE auctionId = :auction_id");
    $stmt->execute(['auction_id' => $auction_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch a single auction by its ID
function getCategoryById($category_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE categoryId = :category_id");
    $stmt->execute(['category_id' => $category_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result["categoryName"];
}

// Fetch a all categories
function getCategories() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all categories as an array
}

//Get the user maximium number bid for a specific auction 
function getNumberBid($user_id, $auction_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(bidAuctionId) as count FROM bids WHERE bidAuctionId = :auction_id AND bidUserId = :user_id");
    $stmt->execute(['auction_id' => $auction_id, 'user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['count'])) {
        return (int) $result['count']; 
    } else {
        return 0; 
    }
}

// Get the highest bid for a specific auction
function getHighestBid($auction_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT MAX(bidAmount) AS highest_bid FROM bids WHERE bidAuctionId = :auction_id");
    $stmt->execute(['auction_id' => $auction_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['highest_bid'] ?? 0;
}

// Place a bid
function placeBid($auction_id, $user_id, $bid_amount) {
    global $pdo;
     // Get user_id from session
    if (!$user_id) {
        return false; // User not authenticated
    }

    $highest_bid = getHighestBid($auction_id);

    // Ensure the bid is higher than the highest bid
    if ($bid_amount > $highest_bid) {
        $stmt = $pdo->prepare("INSERT INTO bids (bidAuctionId, bidUserId, bidAmount) VALUES (:auction_id, :user_id, :bid_amount)");
        $stmt->execute([
            'auction_id' => $auction_id,
            'user_id' => $user_id,
            'bid_amount' => $bid_amount
        ]);
        return true;
    }
    return false;
}

// Check if a user is the highest bidder
function isHighestBidder($auction_id) {
    global $pdo;
    $user_id = getUserFromSession(); // Get user_id from session

    if (!$user_id) {
        return false; // User not authenticated
    }

    $stmt = $pdo->prepare("SELECT bidUserId FROM bids WHERE bidAuctionId = :auction_id ORDER BY bidAmount DESC LIMIT 1");
    $stmt->execute(['auction_id' => $auction_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['user_id'] == $user_id;
}

// Fetch user data by ID
function getUserById($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE userId = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch user data by ID
function getUserName($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE userId = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result["userName"];
}

// Fetch all auctions
function getAllAuctions() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM auctions");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all auctions by the user
function getUsersAuctions() {
    global $pdo;
    $user_id = getUserFromSession();

    if (!$user_id) {
        return []; // User not authenticated
    }

    try {
        $sql = "SELECT * FROM auctions WHERE auctionCreatedBy = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Return empty array if no results
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return []; // Handle the error appropriately
    }
}

// Fetch all users
function getAllUsers() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE userRole = 'user'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Add a new auction
function addAuction($title, $start_price, $start_time, $end_date, $category_id, $address, $description, $product_img, $user_id) {
    global $pdo;

    try {
        // Prepare the SQL insert statement with correct column names
        $stmt = $pdo->prepare("INSERT INTO auctions 
            (auctionTitle, auctionStartPrice, auctionStartDate, auctionEndDate, auctionAddress, auctionDescription, auctionCategoryId, auctionProductImg, auctionCreatedBy) 
            VALUES (:title, :start_price, :start_time, :end_date, :address, :description, :category_id, :product_img, :user_id)");

        // Execute the query with the correct named parameters
        $success = $stmt->execute([
            'title' => $title,
            'start_price' => $start_price,
            'start_time' => $start_time,
            'end_date' => $end_date,
            'address' => $address,
            'description' => $description,
            'category_id' => $category_id, // Note: Changed from 'category_name' to 'category_id'
            'product_img' => $product_img,
            'user_id' => (int)$user_id // Ensure user_id is an integer
        ]);

        // Check if the query executed successfully
        if ($success) {
            return "Auction added successfully!";
        } else {
            return "Error: Failed to add auction.";
        }

    } catch (PDOException $e) {
        // Catch any database errors and return the error message
        return "Database error: " . $e->getMessage();
    }
}

// Add a new auction
function addCategory($category_name, $unique_name) {
    global $pdo;

    try {
        // Prepare the SQL insert statement
        $stmt = $pdo->prepare("INSERT INTO categories 
        (categoryName,categoryImg) 
        VALUES (:category_name,:unique_name)");

        // Execute the query
        $success = $stmt->execute([
            'category_name' => $category_name,
            'unique_name' => $unique_name
        ]);

        // Check if the query executed successfully
        if ($success) {
            return "Category added successfully!";
        } else {
            return "Error: Failed to add auction.";
        }

    } catch (PDOException $e) {
        // Catch any database errors and return the error message
        return "Database error: " . $e->getMessage();
    }
}

// Delete an auction
function deleteAuction($auction_id) {
    global $pdo;

    // Check if the auction exists
    $stmt = $pdo->prepare("SELECT * FROM auctions WHERE auctionId = :auctionId");
    $stmt->execute(['auctionId' => $auction_id]);
    $auction = $stmt->fetch();

    if ($auction) {
        // First, delete all bids related to the auction
        $deleteBidsStmt = $pdo->prepare("DELETE FROM bids WHERE bidAuctionId = :auctionId");
        $deleteBidsStmt->execute(['auctionId' => $auction_id]);

        // Now, delete the auction itself
        $deleteAuctionStmt = $pdo->prepare("DELETE FROM auctions WHERE auctionId = :auctionId");
        $deleteAuctionStmt->execute(['auctionId' => $auction_id]);

        // Optionally, check if deletion was successful
        if ($deleteAuctionStmt->rowCount() > 0) {
            echo "Auction and related bids deleted successfully.";
        } else {
            echo "Failed to delete auction.";
        }
    } else {
        echo "Auction not found.";
    }
}

// Fetch bids for a specific auction
function getBidsForAuction($auction_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM bids WHERE bidAuctionId = :auction_id ORDER BY bidAmount DESC");
    $stmt->execute(['auction_id' => $auction_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch top bidders for a specific auction
function getTopBidders($auction_id, $limit = 10) {
    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT u.userName AS userId, MAX(b.bidAmount) AS highestBid
          FROM bids b
          JOIN users u ON b.bidUserId = u.userId
          WHERE b.bidAuctionId = :auction_id
          GROUP BY u.userId, u.userName
          ORDER BY highestBid DESC
          LIMIT :limit;
        "
    );
    $stmt->bindParam(':auction_id', $auction_id);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Update user profile
function updateUserProfile($user_id, $fname, $lname, $upi_id, $image, $phone,$address) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users 
                           SET userFirstName = :fname, 
                               userLastName = :lname, 
                               userUpiId = :upi_id, 
                       userProfileImg = :image,
                       userPhone = :phone, 
                       userAddress = :address 
                           WHERE userId = :user_id");

    $result = $stmt->execute([
        'fname' => $fname,
        'lname' => $lname,
        'upi_id' => $upi_id,
        'image' => $image,
        'phone' => $phone,
        'address' => $address,
        'user_id' => $user_id
    ]);

    return $result;
}

// Update auction details
function updateAuction($auctionId, $title, $start_price, $start_time, $end_date, $category_id, $address, $description, $image, $status) {
    global $pdo;

    // Validate that the status is one of the valid ENUM values
    $validStatuses = ['activate', 'deactivate', 'suspend'];
    if (!in_array($status, $validStatuses)) {
        return "Invalid status value. Allowed values are 'activate', 'deactivate', or 'suspend'.";
    }

    // Prepare the SQL query to update the auction details
    $sql = "UPDATE auctions 
            SET auctionTitle = :title, 
                auctionStartPrice = :start_price, 
                auctionStartDate = :start_time, 
                auctionEndDate = :end_date, 
                auctionCategoryId = :category_id, 
                auctionAddress = :address, 
                auctionDescription = :description, 
                auctionProductImg = :image, 
                auctionStatus = :status
            WHERE auctionId = :auctionId";

    // Prepare and execute the statement
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':start_price' => $start_price,
        ':start_time' => $start_time,
        ':end_date' => $end_date,
        ':category_id' => $category_id,
        ':address' => $address,
        ':description' => $description,
        ':image' => $image,
        ':status' => $status, // Valid ENUM value: 'activate', 'deactivate', 'suspend'
        ':auctionId' => $auctionId
    ]);

    return "Auction updated successfully";
}

// Fetch only the UPI ID of a user by their ID
function getUserUpiId($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT userUpiId FROM users WHERE userId = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['userUpiId'] ?? null;
}

// Fetch only the Image of a user by their ID
function getUserImg($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT userProfileImg FROM users WHERE userId = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['userProfileImg'] ?? null;
}

// Function to create UPI payment link
function createUpiRequest($upi_id, $amount, $payee_name) {
    return "upi://pay?pa={$upi_id}&pn=" . urlencode($payee_name) . "&am={$amount}&cu=INR";
}

function getHighestBidder($auction_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT bidUserId FROM bids WHERE bidAuctionId = :auction_id ORDER BY bidAmount DESC LIMIT 1");
    $stmt->bindValue(':auction_id', $auction_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get user old password
function getUserPassword($user_id) {
    global $pdo;
    $result=getUserById($user_id);
    return $result["userPassword"];
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE userEmail = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}

function validateResetToken($user_id, $token) {
    global $pdo;

    // Query to validate the reset token
    $sql = "SELECT 1 FROM passResets 
            WHERE passResetUserId = :user_id 
              AND passResetToken = :token 
              AND DATE_ADD(createdAt, INTERVAL 10 MINUTE) > NOW()";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $user_id,
        'token' => $token
    ]);

    // Return true if the token is valid, false otherwise
    return $stmt->rowCount() === 1;
}

function updatePassResetToken($user_id, $token) {
    global $pdo;
    $sql = "UPDATE passResets SET passResetToken = 'EXPIRED' WHERE passResetUserId = :user_id and passResetToken = :token";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
      'user_id' => $user_id,
      'token' => $token
      ]);
}

function updateUserPassword($user_id, $hashed_password) {
    global $pdo;
    $sql = "UPDATE users SET userPassword = :password WHERE userId = :user_id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['password' => $hashed_password, 'user_id' => $user_id]);
}

function createPassResetToken($user_id, $token) {
    global $pdo;

    try {
        // Prepare the SQL statement without including the auto-increment passRestId
        $stmt = $pdo->prepare("INSERT INTO passResets (passResetUserId, passResetToken, createdAt) VALUES (:id, :token, NOW())");

        // Execute the statement with the provided user_id and token
        $stmt->execute([
            ':id' => $user_id,
            ':token' => $token
        ]);

        // Check if the insert was successful by checking the row count
        if ($stmt->rowCount() > 0) {
            return true; // Insert was successful
        } else {
            // Log if no rows were affected
                echo '
                <p class="alert alert-warning alert-dismissible fade show d-flex align-items-center" 
                       role="alert"  data-bs-dismiss="alert" 
                              aria-label="Close" 
                       style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
              Error: Failed to insert token into the database. No rows affected.
            </p>
         ';
            return false; // Insert failed
        }

    } catch (PDOException $e) {
        // Log error to file or server log for debugging
                        echo '
                <p class="alert alert-warning alert-dismissible fade show d-flex align-items-center" 
                       role="alert"  data-bs-dismiss="alert" 
                              aria-label="Close" 
                       style="white-space:nowrap; max-width: 100%; overflow-y: auto;">
              Error inserting password reset token: ' . $e->getMessage() . '
            </p>
         ';
        
        // Return false if there's an error
        return false;
    }
}
?>