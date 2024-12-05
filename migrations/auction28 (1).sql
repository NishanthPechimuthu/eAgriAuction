-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2024 at 10:27 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auction28`
--

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE `auctions` (
  `auctionId` int(11) NOT NULL,
  `auctionTitle` varchar(200) NOT NULL,
  `auctionStartPrice` decimal(10,2) NOT NULL,
  `auctionStartDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `auctionEndDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `auctionProductImg` varchar(300) NOT NULL,
  `auctionAddress` varchar(300) NOT NULL,
  `auctionDescription` longtext NOT NULL,
  `auctionCategoryId` int(11) NOT NULL,
  `auctionCreatedBy` int(11) NOT NULL,
  `auctionStatus` enum('activate','deactivate','suspend') DEFAULT 'activate',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auctions`
--

INSERT INTO `auctions` (`auctionId`, `auctionTitle`, `auctionStartPrice`, `auctionStartDate`, `auctionEndDate`, `auctionProductImg`, `auctionAddress`, `auctionDescription`, `auctionCategoryId`, `auctionCreatedBy`, `auctionStatus`, `createdAt`) VALUES
(5, 'Black wash the winner of the day of the day of the', 10000.00, '2024-11-23 06:09:00', '2024-11-25 06:09:00', 'prod_67457b1968c7f.webp', 'Udumalipettai, Tiruppur,Tamil Nadu,Indian-642205', 'This is the winner of the day of the day of the day of the day of the day of the day of the day of the day of the winner of BD ', 2, 1, 'activate', '2024-11-23 06:10:38'),
(6, 'He the hell bro', 1258.00, '2024-11-25 12:12:00', '2024-11-24 12:12:00', 'prod_67457b3317c9d.webp', 'Udumalipettai, Tiruppur,Tamil Nadu,Indian-642205', 'The data from the winner of the day of the day of the day to all city of with categoryname the winner is not upload any one of flood ', 2, 1, 'deactivate', '2024-11-25 12:13:30'),
(8, 'Black wash', 100.00, '2024-11-29 11:45:00', '2024-12-05 11:45:00', 'prod_6749ab799b9ac.webp', 'Udumalipettai, Tiruppur,Tamil Nadu,Indian-642205', 'The bootstrap message was automatically generated email from github ', 3, 2, 'activate', '2024-11-29 11:45:38'),
(10, 'He', 258.00, '2024-12-02 09:11:00', '2024-12-07 16:11:00', 'prod_674d79bcea4d8.webp', 'Udumalipettai, Tiruppur,Tamil Nadu,Indian-642205', 'categoryDropdownButton.textContent', 2, 1, 'activate', '2024-12-02 09:11:24'),
(11, 'No Buddy', 569.00, '2024-12-04 10:05:00', '2024-12-09 10:05:00', 'prod_6750297bb1d4e.webp', 'Udumalipettai, Tiruppur,Tamil Nadu,Indian-642205', 'Lorem Ipsum was the first time zone ', 3, 1, 'activate', '2024-12-04 10:05:47');

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `bidId` int(11) NOT NULL,
  `bidAuctionId` int(11) NOT NULL,
  `bidUserId` int(11) NOT NULL,
  `bidAmount` decimal(10,2) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`bidId`, `bidAuctionId`, `bidUserId`, `bidAmount`, `createdAt`) VALUES
(6, 5, 2, 12000.00, '2024-11-23 13:09:01'),
(7, 6, 2, 1259.00, '2024-11-26 07:42:24'),
(10, 8, 1, 101.00, '2024-12-02 09:11:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `categoryId` int(11) NOT NULL,
  `categoryName` varchar(100) NOT NULL,
  `categoryImg` varchar(300) NOT NULL,
  `categoryStatus` enum('activate','deactivate','suspend') NOT NULL DEFAULT 'activate',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`categoryId`, `categoryName`, `categoryImg`, `categoryStatus`, `createdAt`) VALUES
(1, 'Electronic', 'cat_6740450b783ce.webp', 'activate', '2024-11-22 08:47:07'),
(2, 'Food', 'cat_67415d694fa49.webp', 'activate', '2024-11-23 04:43:21'),
(3, 'Crypto', 'cat_67489931afe15.webp', 'activate', '2024-11-28 16:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `passResets`
--

CREATE TABLE `passResets` (
  `passResetId` int(11) NOT NULL,
  `passResetUserId` int(11) NOT NULL,
  `passResetToken` varchar(64) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `passResets`
--

INSERT INTO `passResets` (`passResetId`, `passResetUserId`, `passResetToken`, `createdAt`) VALUES
(1, 3, '7fd8fdf4ccc2810e35a976983b9c0c2a32957422cebdad6c25f9998106d789d7', '2024-11-28 14:12:21'),
(2, 3, '544eb97a94096fb11e5843ff102d109e6315d1974050119ec9babd04e2402a29', '2024-11-28 14:13:07'),
(3, 3, 'd62db4feea32f2a0ea88839322f9255d1ba0ad1b5c85525b9417484b049462e1', '2024-11-28 14:14:45'),
(4, 3, '6d5c8a6d1f8605b7fc94aee6499c4a984f55a99612b678e4d61709edb7207022', '2024-11-28 14:18:33'),
(5, 3, '7ed83bcc9e8178cf8d97b7f7e1d87c6a', '2024-11-30 15:50:21'),
(6, 3, '7d28bc7dc216849f5ed9033247051a9e', '2024-11-30 15:51:48'),
(7, 3, 'EXPIRED', '2024-11-30 16:00:45'),
(8, 1, '90b166991f4c9555f5f502c112c919b6', '2024-11-30 16:19:05'),
(9, 1, 'e71ff286310aff75cfdc930b46fb5362', '2024-11-30 16:19:55'),
(10, 1, 'f4265b723e8b579901c27670ef57a011', '2024-11-30 16:20:40'),
(11, 3, '00a1c1c0f31014ce9a9765d0b5f2c08f', '2024-12-04 12:35:42'),
(12, 3, '85c4f405bae9a4c3c94eac4f338ca9c9', '2024-12-04 12:36:20'),
(13, 3, '3858c99617ce9a769f60902d3b51cbfe', '2024-12-04 12:36:31'),
(14, 3, '3708954544e71f4cfe91f6ba735017ea', '2024-12-04 12:36:54'),
(15, 3, '2e54824c891f7bfd19430ffe26192ecf', '2024-12-04 13:03:19'),
(16, 3, 'EXPIRED', '2024-12-04 13:12:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `userName` varchar(100) NOT NULL,
  `userFirstName` varchar(100) DEFAULT NULL,
  `userLastName` varchar(100) DEFAULT NULL,
  `userEmail` varchar(100) NOT NULL,
  `userPassword` varchar(255) NOT NULL,
  `userPhone` varchar(100) NOT NULL,
  `userAddress` varchar(250) NOT NULL,
  `userProfileImg` varchar(300) DEFAULT 'profile.webp',
  `userUpiId` varchar(500) DEFAULT NULL,
  `userRole` enum('user','admin') NOT NULL DEFAULT 'user',
  `userStatus` enum('activate','deactivate','suspend') DEFAULT 'activate',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `userName`, `userFirstName`, `userLastName`, `userEmail`, `userPassword`, `userPhone`, `userAddress`, `userProfileImg`, `userUpiId`, `userRole`, `userStatus`, `createdAt`) VALUES
(1, 'blk', 'Nishanth', 'Pechimuthu', 'black@black.in', '$2y$10$HWW4lymluIu7nK.4RyprBumuf7b6i8MeI5pM4OBJe5F94FQm53Fye', '+91 8015864344', 'Udumalipettai, Tiruppur,Tamil Nadu', 'img_674561fd68014.webp', 'vanjisunil123@okhdfcbank', 'user', 'activate', '2024-11-22 08:46:29'),
(2, 'root', 'Nishanth', 'Root', 'root@root.com', '$2y$10$hdJIVulgWHZj.RL7UyCqm.yigqyBsZP6Qkk3YceNQ43/xCloDWffC', '+91 9500814344', 'Udumalipettai, Tiruppur,Tamil Nadu,Indian-642205', 'img_674b2bcaafe57.webp', 'root@root56', 'user', 'activate', '2024-11-23 05:15:40'),
(3, 'black', NULL, NULL, 'nishanthpechimuthu@gmail.com', '$2y$10$QiirJV1S8KSjbT59BPI1geJRP/mcyoxya0X2KXndcXbVJAlVVF8ga', '', '', 'profile.webp', NULL, 'user', 'activate', '2024-11-28 07:54:13'),
(4, '22ct19', NULL, NULL, '22ct19nishanth@gmail.com', '$2y$10$izGcmfVhW29bJYIjNnOAOutb8FcEiwciE1NLdRDvoBos1F3jHEMtO', '', '', 'profile.webp', NULL, 'user', 'activate', '2024-11-30 09:48:24'),
(8, 'yellow', NULL, NULL, 'yellow@gmail.com', '$2y$10$YHXU9AbUcpNYjfV9tawDtOqnLgUIsqH75T6s4FWSAvKKYFQPzYYFG', '', '', 'profile.webp', NULL, 'user', 'activate', '2024-11-30 15:15:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`auctionId`),
  ADD KEY `auctionCategoryId` (`auctionCategoryId`),
  ADD KEY `auctionCreatedBy` (`auctionCreatedBy`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`bidId`),
  ADD KEY `bidAuctionId` (`bidAuctionId`),
  ADD KEY `bidUserId` (`bidUserId`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryId`),
  ADD UNIQUE KEY `categoryName` (`categoryName`);

--
-- Indexes for table `passResets`
--
ALTER TABLE `passResets`
  ADD PRIMARY KEY (`passResetId`),
  ADD KEY `passResets_passRestUserId_users_userId` (`passResetUserId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `userEmail` (`userEmail`),
  ADD UNIQUE KEY `userName` (`userName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `auctionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `bidId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `passResets`
--
ALTER TABLE `passResets`
  MODIFY `passResetId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auctions`
--
ALTER TABLE `auctions`
  ADD CONSTRAINT `auctions_ibfk_1` FOREIGN KEY (`auctionCategoryId`) REFERENCES `categories` (`categoryId`),
  ADD CONSTRAINT `auctions_ibfk_2` FOREIGN KEY (`auctionCreatedBy`) REFERENCES `users` (`userId`);

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`bidAuctionId`) REFERENCES `auctions` (`auctionId`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`bidUserId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `passResets`
--
ALTER TABLE `passResets`
  ADD CONSTRAINT `passResets_passRestUserId_users_userId` FOREIGN KEY (`passResetUserId`) REFERENCES `users` (`userId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
