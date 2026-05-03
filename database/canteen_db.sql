-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 08:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `canteen_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `pickup_time` varchar(50) NOT NULL,
  `total` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer`, `pickup_time`, `total`, `status`) VALUES
(2, 'Ehsaan', '10:20', 5150, 'Ready'),
(3, 'Ehsaan', '10:20', 5150, 'Ready'),
(4, 'elif', '00:10', 3450, 'Ready'),
(5, 'elif', '00:10', 3450, 'Ready'),
(6, 'Naheed', '00:45', 1850, 'Ready'),
(7, 'Ahmed', '14:30', 1400, 'Ready'),
(8, 'atif', '00:01', 1300, 'Ready'),
(9, 'atif', '01:20', 1050, 'Ready'),
(10, 'saad', '15:01', 350, 'Ready'),
(11, 'hassan', '13:15', 250, 'Ready'),
(12, 'Aeny', '15:30', 2540, 'Ready'),
(13, 'Meli', '16:00', 350, 'Ready'),
(14, 'Moni', '18:30', 350, 'Ready'),
(15, 'NONY', '13:00', 250, 'Ready'),
(16, 'NONY', '13:00', 250, 'Ready'),
(17, 'QWE', '13:00', 800, 'Ready'),
(18, 'elif', '10:00', 270, 'Ready'),
(19, 'popo', '11:11', 430, 'Ready'),
(20, 'xyz', '17:30', 450, 'Preparing'),
(21, 'NONY', '15:03', 840, 'Ready'),
(22, 'pomo', '13:01', 450, 'Preparing'),
(23, 'w', '13:02', 130, 'Pending'),
(24, 'y', '14:01', 50, 'Pending'),
(25, 'ansari', '15:00', 5350, 'Preparing');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product`, `quantity`) VALUES
(1, 1, 'Burger', 1),
(2, 1, 'Burger', 1),
(3, 1, 'Pizza', 1),
(4, 1, 'Drink', 1),
(5, 1, 'Burger', 1),
(6, 1, 'Burger', 1),
(7, 1, 'Burger', 1),
(8, 1, 'Burger', 1),
(9, 1, 'Pizza', 1),
(10, 1, 'Fries', 1),
(11, 1, 'Burger', 1),
(12, 1, 'Burger', 1),
(13, 1, 'Burger', 1),
(14, 1, 'Burger', 1),
(15, 1, 'Pizza', 1),
(16, 2, 'Burger', 1),
(17, 2, 'Burger', 1),
(18, 2, 'Pizza', 1),
(19, 2, 'Drink', 1),
(20, 2, 'Burger', 1),
(21, 2, 'Burger', 1),
(22, 2, 'Burger', 1),
(23, 2, 'Burger', 1),
(24, 2, 'Pizza', 1),
(25, 2, 'Fries', 1),
(26, 2, 'Burger', 1),
(27, 2, 'Burger', 1),
(28, 2, 'Burger', 1),
(29, 2, 'Burger', 1),
(30, 2, 'Pizza', 1),
(31, 3, 'Burger', 1),
(32, 3, 'Burger', 1),
(33, 3, 'Pizza', 1),
(34, 3, 'Drink', 1),
(35, 3, 'Burger', 1),
(36, 3, 'Burger', 1),
(37, 3, 'Burger', 1),
(38, 3, 'Burger', 1),
(39, 3, 'Pizza', 1),
(40, 3, 'Fries', 1),
(41, 3, 'Burger', 1),
(42, 3, 'Burger', 1),
(43, 3, 'Burger', 1),
(44, 3, 'Burger', 1),
(45, 3, 'Pizza', 1),
(46, 4, 'Burger', 3),
(47, 4, 'Pizza', 2),
(48, 4, 'Fries', 4),
(49, 4, 'Drink', 5),
(50, 5, 'Burger', 3),
(51, 5, 'Pizza', 2),
(52, 5, 'Fries', 4),
(53, 5, 'Drink', 5),
(54, 6, 'Pizza', 2),
(55, 6, 'Fries', 1),
(56, 6, 'Drink', 1),
(57, 7, 'Burger', 1),
(58, 7, 'Pizza', 1),
(59, 7, 'Fries', 1),
(60, 7, 'Drink', 2),
(61, 8, 'Burger', 1),
(62, 8, 'Pizza', 1),
(63, 8, 'Fries', 1),
(64, 8, 'Drink', 1),
(65, 9, 'Drink', 1),
(66, 9, 'Fries', 1),
(67, 9, 'Pizza', 1),
(68, 10, 'Burger', 1),
(69, 10, 'Drink', 1),
(70, 11, 'Burger', 1),
(71, 12, 'Burger', 2),
(72, 12, 'Pizza', 2),
(73, 12, 'Fries', 2),
(74, 12, 'Drink', 1),
(75, 13, 'Biryani', 1),
(76, 13, 'Drink', 1),
(77, 14, 'Burger', 1),
(78, 14, 'Drink', 1),
(79, 15, 'Burger', 1),
(80, 16, 'Burger', 1),
(81, 17, 'Pizza', 1),
(82, 18, 'Fries', 1),
(83, 18, 'Drink', 1),
(84, 19, 'Chicken Paratha', 1),
(85, 19, 'Shawarma', 1),
(86, 19, 'Sandwich', 1),
(87, 20, 'Chicken Paratha', 1),
(88, 20, 'Shawarma', 1),
(89, 20, 'Drink', 1),
(90, 21, 'Burger', 2),
(91, 21, 'Fries', 2),
(92, 22, 'Samosa', 3),
(93, 22, 'Drink', 3),
(94, 23, 'Samosa', 1),
(95, 23, 'Sandwich', 1),
(96, 24, 'Samosa', 1),
(97, 25, 'Shawarma', 1),
(98, 25, 'Chicken Paratha', 1),
(99, 25, 'Samosa', 100);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`) VALUES
(1, 'Burger', 250, ''),
(2, 'Pizza', 800, ''),
(3, 'Fries', 170, ''),
(4, 'Drink', 100, ''),
(6, 'Biryani', 250, ''),
(7, 'Sandwich', 80, ''),
(8, 'Shawarma', 200, ''),
(9, 'Chicken Paratha', 150, ''),
(10, 'Samosa', 50, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
