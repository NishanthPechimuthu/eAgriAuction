CREATE TABLE categories (
    categoryId INT PRIMARY KEY AUTO_INCREMENT NOT NULL ,
    categoryName VARCHAR(100) NOT NULL UNIQUE ,
    categoryImg VARCHAR(300) NOT NULL,
    categoryStatus ENUM('activate', 'deactivate', 'suspend') NOT NULL DEFAULT 'activate',
    createdAt TIMESTAMP NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE users (
    userId INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    userName VARCHAR(100) NOT NULL,
    userFirstName VARCHAR(100),
    userLastName VARCHAR(100),
    userEmail VARCHAR(100) NOT NULL UNIQUE,
    userPassword VARCHAR(255) NOT NULL,
    userOldPassword VARCHAR(255) NOT NULL,
    userProfileImg VARCHAR(300),
    userUpiId VARCHAR(500), -- Changed INT(500) to VARCHAR(500) for UPI ID
    userRole ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    userStatus ENUM('activate', 'deactivate', 'suspend') DEFAULT 'activate',
    createdAt TIMESTAMP NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE auctions (
    auctionId INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    auctionTitle VARCHAR(200) NOT NULL,
    auctionStartPrice DECIMAL(10,2) NOT NULL,
    auctionStartDate TIMESTAMP NOT NULL,
    auctionEndDate TIMESTAMP NOT NULL,
    auctionProductImg VARCHAR(300) NOT NULL,
    auctionAddress VARCHAR(300) NOT NULL,
    auctionDescription LONGTEXT NOT NULL,
    auctionCategoryId int(11) NOT NULL,
    auctionCreatedBy INT NOT NULL,
    auctionStatus ENUM('activate', 'deactivate', 'suspend') DEFAULT 'activate',
    createdAt TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (auctionCategoryId) REFERENCES categories(categoryId),
    FOREIGN KEY (auctionCreatedBy) REFERENCES users(userId)
);

CREATE TABLE bids (
    bidId INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    bidAuctionId INT NOT NULL,
    bidUserId INT NOT NULL,
    bidAmount DECIMAL(10,2) NOT NULL,
    createdAt TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    FOREIGN KEY (bidAuctionId) REFERENCES auctions(auctionId),
    FOREIGN KEY (bidUserId) REFERENCES users(userId)
);
