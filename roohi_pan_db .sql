-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2026 at 02:06 PM
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
-- Database: `roohi_pan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `quantity`, `updated_at`) VALUES
(1, 'Chicken Nuggets', 100.00, '2026-11-07 10:43:19'),
(2, 'Fish Fingers', 100.00, '2026-11-07 10:43:19'),
(3, 'Possin Chicken Portions', 47.00, '2026-11-07 11:41:56'),
(4, 'Chicken Breast', 46.50, '2026-11-07 11:25:06'),
(5, 'Fried Chicken Portions', 49.00, '2026-11-07 11:41:22'),
(6, 'Drumsticks', 150.00, '2026-11-07 10:43:19'),
(7, 'Wings', 120.00, '2026-11-07 10:43:19'),
(8, 'Ice Lolly', 100.00, '2026-11-07 10:43:19'),
(9, 'Chapati', 187.00, '2026-11-07 10:54:27'),
(10, 'Sausage', 100.00, '2026-11-07 10:43:19'),
(11, 'Mandazi', 100.00, '2026-11-07 10:43:19'),
(12, 'Mutton Samosa', 200.00, '2026-11-07 10:43:19'),
(13, 'Veg Samosa', 150.00, '2026-11-07 10:43:19'),
(14, 'Daal Bhajia', 94.00, '2026-11-07 10:50:35'),
(17, 'Coke (Can)', 100.00, '2026-11-07 10:47:56'),
(18, 'Coke Zero (Can)', 100.00, '2026-11-07 10:47:56'),
(19, 'Fanta (Can)', 100.00, '2026-11-07 10:47:56'),
(20, 'Sprite (Can)', 100.00, '2026-11-07 10:47:56'),
(21, 'Pepsi (Can)', 100.00, '2026-11-07 10:47:56'),
(22, 'Mirinda (Can)', 100.00, '2026-11-07 10:47:56'),
(23, 'Mountain Dew (Can)', 100.00, '2026-11-07 10:47:56'),
(24, '7UP (Can)', 100.00, '2026-11-07 10:47:56'),
(25, 'Lemonade (Can)', 100.00, '2026-11-07 10:47:56'),
(26, 'Coca-Cola (Bottle)', 100.00, '2026-11-07 10:47:56'),
(27, 'Coke Zero (Bottle)', 100.00, '2026-11-07 10:47:56'),
(28, 'Fanta (Bottle)', 100.00, '2026-11-07 10:47:56'),
(29, 'Sprite (Bottle)', 100.00, '2026-11-07 10:47:56'),
(30, 'Pepsi (Bottle)', 100.00, '2026-11-07 10:47:56'),
(31, 'Mirinda (Bottle)', 100.00, '2026-11-07 10:47:56'),
(32, 'Mountain Dew (Bottle)', 100.00, '2026-11-07 10:47:56'),
(33, '7UP (Bottle)', 100.00, '2026-11-07 10:47:56'),
(34, 'Lemonade (Bottle)', 100.00, '2026-11-07 10:47:56');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `category`, `price`, `stock`) VALUES
(1, 'Vanilla Faluda', 'FALUDA', 400.00, 47),
(2, 'Strawberry Faluda', 'FALUDA', 400.00, 49),
(3, 'Chocolate Faluda', 'FALUDA', 400.00, 46),
(4, 'Pistachio Faluda', 'FALUDA', 500.00, 47),
(5, 'Kesar Badam Faluda', 'FALUDA', 500.00, 48),
(6, 'Shredded Kulfi (Original)', 'FALUDA', 300.00, 50),
(7, 'Vanilla Milkshake', 'MILKSHAKE', 300.00, 50),
(8, 'Strawberry Milkshake', 'MILKSHAKE', 300.00, 49),
(9, 'Chocolate Milkshake', 'MILKSHAKE', 300.00, 49),
(10, 'Pistachio Milkshake', 'MILKSHAKE', 400.00, 50),
(11, 'Kesar Badam Milkshake', 'MILKSHAKE', 400.00, 49),
(12, 'Kitkat Milkshake', 'MILKSHAKE', 500.00, 50),
(13, 'Oreo Milkshake', 'MILKSHAKE', 450.00, 50),
(14, 'Coke (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(15, 'Coke Zero (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(16, 'Fanta (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(17, 'Sprite (Can)', 'SOFT DRINKS (CAN)', 250.00, 49),
(18, 'Pepsi (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(19, 'Mirinda (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(20, 'Mountain Dew (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(21, '7UP (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(22, 'Lemonade (Can)', 'SOFT DRINKS (CAN)', 250.00, 50),
(23, 'Coca-Cola (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(24, 'Coke Zero (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(25, 'Fanta (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(26, 'Sprite (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(27, 'Pepsi (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(28, 'Mirinda (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(29, 'Mountain Dew (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(30, '7UP (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(31, 'Lemonade (Bottle)', 'SOFT DRINKS (BOTTLE)', 60.00, 50),
(32, 'Mabuyu Lollies', 'ICE LOLLIES', 60.00, 50),
(33, 'Choc Sticks', 'ICE LOLLIES', 80.00, 50),
(34, 'Milk Banana Lollies', 'ICE LOLLIES', 80.00, 50),
(35, 'Mutton Samosa', 'SNACKS', 350.00, 50),
(36, 'Keema Chapati', 'SNACKS', 350.00, 50),
(37, 'Chicken Chapati', 'SNACKS', 300.00, 50),
(38, 'Hot Dogs', 'SNACKS', 200.00, 50),
(39, 'Sausage', 'SNACKS', 65.00, 49),
(40, 'Mandazi', 'SNACKS', 30.00, 50),
(41, 'Chapati', 'SNACKS', 30.00, 50),
(42, 'Chips', 'SNACKS', 150.00, 50),
(43, 'Tea Special', 'HOT BEVERAGES', 150.00, 50),
(44, 'Black Tea', 'HOT BEVERAGES', 50.00, 48),
(45, 'Black Coffee', 'HOT BEVERAGES', 150.00, 50),
(46, 'White Coffee', 'HOT BEVERAGES', 200.00, 49),
(47, 'Plain Chips', 'FRIES', 180.00, 49),
(48, 'Masala Chips', 'FRIES', 250.00, 49),
(49, 'Possin Chips', 'FRIES', 300.00, 50),
(50, 'Veg Samosa (Per Plate)', 'VEGETERIAN', 300.00, 50),
(51, 'Maru Bhajia (Per Plate)', 'VEGETERIAN', 350.00, 50),
(52, 'Daal Bhajia (Per Plate)', 'VEGETERIAN', 250.00, 49),
(53, 'Fish Fingers', 'FISH & CHICKEN BITES', 450.00, 47),
(54, 'Chicken Nuggets', 'FISH & CHICKEN BITES', 450.00, 47),
(55, 'Half Possin Chicken & Chips', 'POSSIN CHICKEN', 700.00, 47),
(56, 'Full Possin Chicken & Chips', 'POSSIN CHICKEN', 1200.00, 48),
(57, '½ Fried Chicken Breast', 'FRIED CHICKEN', 350.00, 49),
(58, '½ Fried Chicken Leg', 'FRIED CHICKEN', 300.00, 50),
(59, 'Half Fried Chicken', 'FRIED CHICKEN', 600.00, 47),
(60, 'Full Fried Chicken', 'FRIED CHICKEN', 1000.00, 49),
(61, 'Possin Wings', 'WINGS', 500.00, 50),
(62, 'Fried Wings', 'WINGS', 350.00, 50),
(63, 'Masala Wings', 'WINGS', 450.00, 50),
(64, 'Fried Drumsticks (3)', 'DRUMSTICKS', 350.00, 50),
(65, 'Possin Drumsticks (3)', 'DRUMSTICKS', 500.00, 50),
(66, 'Masala Drumsticks (3)', 'DRUMSTICKS', 450.00, 50);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_type` enum('Dine-in','Delivery') NOT NULL,
  `table_no` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('completed','cancelled') DEFAULT 'completed',
  `payment_status` enum('paid','unpaid') DEFAULT 'paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_type`, `table_no`, `customer_name`, `total_amount`, `created_at`, `status`, `payment_status`) VALUES
(11, 5, 'Dine-in', '', '', 600.00, '2026-10-10 13:49:16', 'completed', 'paid'),
(12, 5, 'Dine-in', '', '', 1450.00, '2026-10-10 13:51:47', 'completed', 'paid'),
(13, 5, 'Dine-in', '', '', 900.00, '2026-10-10 14:06:58', 'completed', 'paid'),
(14, 5, 'Dine-in', '', '', 500.00, '2026-10-11 18:26:44', 'cancelled', 'paid'),
(15, 5, 'Dine-in', '', '', 850.00, '2026-10-11 18:36:48', 'cancelled', 'paid'),
(16, 5, 'Dine-in', '', '', 950.00, '2026-10-11 18:56:36', 'completed', 'paid'),
(17, 5, 'Dine-in', '', '', 950.00, '2026-10-11 18:59:21', 'completed', 'paid'),
(18, 5, 'Dine-in', '', '', 600.00, '2026-10-11 19:00:42', 'completed', 'paid'),
(19, 5, 'Dine-in', '', '', 1800.00, '2026-10-11 19:02:46', 'completed', 'paid'),
(20, 5, 'Delivery', '', '', 1730.00, '2026-10-11 19:03:09', 'completed', 'paid'),
(21, 5, 'Dine-in', '', '', 500.00, '2026-10-11 19:11:01', 'cancelled', 'paid'),
(22, 5, 'Dine-in', '78', '', 2900.00, '2026-10-22 08:32:03', 'completed', 'paid'),
(23, 5, 'Dine-in', '', '', 80.00, '2026-10-22 08:33:12', 'cancelled', 'paid'),
(24, 5, 'Dine-in', '', '', 50.00, '2026-10-31 08:52:17', 'completed', 'paid'),
(25, 5, 'Dine-in', '', '', 400.00, '2026-10-31 08:52:46', 'completed', 'paid'),
(26, 5, 'Dine-in', '', '', 400.00, '2026-10-31 08:53:03', 'completed', 'paid'),
(27, 5, 'Dine-in', '', '', 850.00, '2026-10-31 13:48:13', 'completed', 'paid'),
(28, 5, 'Dine-in', '', '', 1000.00, '2026-10-31 13:49:16', 'completed', 'paid'),
(29, 5, 'Dine-in', '', '', 450.00, '2026-10-31 13:53:27', 'cancelled', 'paid'),
(30, 5, 'Dine-in', '', 'john', 400.00, '2026-11-07 07:36:28', 'completed', 'paid'),
(31, 5, 'Dine-in', '', 'jane', 65.00, '2026-11-07 07:49:22', 'completed', 'paid'),
(32, 5, 'Dine-in', '', 'kk', 700.00, '2026-11-07 10:46:17', 'completed', 'paid'),
(33, 5, 'Dine-in', '', 'fred', 250.00, '2026-11-07 10:50:35', 'completed', 'paid'),
(34, 5, 'Dine-in', '', 'yeee', 350.00, '2026-11-07 11:21:51', 'completed', 'paid'),
(35, 5, 'Dine-in', '', 'mk', 600.00, '2026-11-07 11:41:21', 'completed', 'unpaid'),
(36, 5, 'Dine-in', '', 'eden', 700.00, '2026-11-07 11:41:56', 'completed', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `price`) VALUES
(7, 3, 4, 2, 250.00),
(8, 3, 7, 1, 300.00),
(9, 3, 9, 1, 30.00),
(10, 4, 4, 1, 250.00),
(11, 4, 13, 1, 350.00),
(12, 5, 12, 1, 150.00),
(13, 5, 7, 1, 300.00),
(14, 6, 14, 1, 1000.00),
(15, 6, 4, 1, 250.00),
(16, 7, 54, 1, 450.00),
(17, 8, 4, 1, 500.00),
(18, 9, 54, 1, 450.00),
(19, 10, 17, 1, 250.00),
(20, 11, 59, 1, 600.00),
(21, 12, 3, 1, 400.00),
(22, 12, 53, 1, 450.00),
(23, 12, 59, 1, 600.00),
(24, 13, 3, 1, 400.00),
(25, 13, 5, 1, 500.00),
(26, 14, 5, 1, 500.00),
(27, 15, 3, 1, 400.00),
(28, 15, 54, 1, 450.00),
(29, 17, 4, 1, 500.00),
(30, 17, 53, 1, 450.00),
(31, 18, 48, 1, 250.00),
(32, 18, 44, 1, 50.00),
(33, 18, 9, 1, 300.00),
(34, 19, 1, 1, 400.00),
(35, 19, 46, 1, 200.00),
(36, 19, 56, 1, 1200.00),
(37, 20, 2, 1, 400.00),
(38, 20, 53, 1, 450.00),
(39, 20, 47, 1, 180.00),
(40, 20, 55, 1, 700.00),
(41, 21, 4, 1, 500.00),
(42, 22, 5, 1, 500.00),
(43, 22, 4, 1, 500.00),
(44, 22, 11, 1, 400.00),
(45, 22, 8, 1, 300.00),
(46, 22, 56, 1, 1200.00),
(47, 23, 34, 1, 80.00),
(48, 24, 44, 1, 50.00),
(49, 25, 1, 1, 400.00),
(50, 26, 3, 1, 400.00),
(51, 27, 3, 1, 400.00),
(52, 27, 54, 1, 450.00),
(53, 28, 60, 1, 1000.00),
(54, 29, 63, 1, 450.00),
(55, 30, 1, 1, 400.00),
(56, 31, 39, 1, 65.00),
(57, 32, 55, 1, 700.00),
(58, 33, 52, 1, 250.00),
(59, 34, 57, 1, 350.00),
(60, 35, 59, 1, 600.00),
(61, 36, 55, 1, 700.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','employee') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$wT0rYvWl.sFjZkR9z2q9e.rN5wz1m3XgZ4k5Y6u7I8o9p0a1s2d3f', 'owner', '2026-10-07 18:47:29'),
(5, 'akib', '$2y$10$yeVT0xA71nDGjYB37vu1yeMVEI4bJXa0lfKLvxR29cOSexoVFEhYW', 'owner', '2026-10-10 13:48:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
