-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 02:24 PM
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
-- Database: `bookstore_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `name`, `author`, `quantity`, `price`, `description`, `image_path`, `category_id`) VALUES
(8, 'English Question', 'Education Department', 25, 112.00, 'Include English Question', 'English.jpg', 3),
(10, 'Nuto the Curious - The Leaf Cafe', 'Nuwan Thitawaththa', 61, 405.00, 'This book is published by M.D. Gunasena. This is 20 pages book. ', 'leafthecafe.jpg', 1),
(11, 'Arya\'s Dream', 'Savithri Jayasingha', 22, 2000.00, 'Arya\'s Dream\" follows the intertwined journeys of three resilient Sri Lankan family members across generations and continents, as they confront personal struggles and societal challenges in their quest to inspire change and embrace the transformative power of goodness.', 'arya.jpg', 3),
(30, 'as', 'as', 0, 12.00, '12', '', 3),
(32, 'asss', 'ass', 12, 12.00, 'ass', '', 9);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `categoryid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`categoryid`, `name`) VALUES
(1, 'Fiction'),
(3, 'Education'),
(7, 'Finance'),
(9, 'Children'),
(10, 'Europe'),
(11, 'asia'),
(12, 'aussy'),
(13, 'sri');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `firstname`, `lastname`, `address`, `contact`, `email`, `password`, `photo`) VALUES
(1, 'Kavindu', 'Somarathna', 'pabahinnaaas, ba', '0779128882', 'kavindumds@gmail.com', '1234', 'uploads/sea.jpg'),
(5, 'Kavindu', 'Somarathna', 'pabahinna, balangoda', '0779128881', 'kavindumadusha96@gmail.com', '1234', 'uploads/sea.jpg'),
(6, 'Kavindu', 'Somarathna', 'pabahinna, balangoda', '0779128882', 'e2240183@bit.uom.lk', '1234', 'uploads/Screenshot (3).png'),
(7, 'Kavindu', 'Somarathna', 'pabahinna, balangoda', '0779128881', 'km@gamil.com', '1234', 'uploads/Screenshot (358).png'),
(8, 'Kavindu', 'Somarathna', 'pabahinna, balangoda', '0779128882', '123@gamil.com', '1234', '2241326 - Copy.jpg'),
(9, 'madusha', 'Somarathna', 'pabahinna, balangoda', '0779128882', 'e224018@gmail.com', '12345', '2241326 - Copy.jpg'),
(10, 'Kamala', 'Somarathna', 'Kadugannawa,kandy', '0779128812', 'smk@gmail.com', '1234', 'Screenshot (5).png'),
(11, 'Kavindu', 'Somarathna', 'pabahinna, balangoda', '077711111', 'kms@gmail.com', 'Kavindu!23', 'Screenshot (5).png'),
(14, 'Mahela', 'Jayawardhana', ' colombo', '0771234560', 'mahel@gmail.com', 'Kmds!234', 'Untitled2.png'),
(15, 'Kavindu', 'Somarathna', 'dq', '0779128888', 'qwert@gmail.com', 'Kmds!234', 'Screenshot (114).png'),
(16, 'Kavindu', 'Somarathna', 'colombo', '0771234560', 'wert@gmail.com', 'Kmds!234', 'uploads/Untitled5.png');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `courier_number` varchar(255) DEFAULT NULL,
  `status` enum('delivered','rejected') NOT NULL,
  `reason` text DEFAULT NULL,
  `delivery_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`delivery_id`, `order_id`, `courier_number`, `status`, `reason`, `delivery_date`) VALUES
(22, 28, '1234 refer promptx', 'delivered', NULL, '2024-12-09 05:39:40'),
(23, 32, NULL, 'rejected', 'Transport problem will send u', '2024-12-09 07:09:10'),
(25, 34, NULL, 'rejected', 'The receipt is invalid plz contact us', '2024-12-09 09:33:38'),
(26, 36, '456 from promptx', 'delivered', NULL, '2024-12-09 11:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EID` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','order','inventory') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`EID`, `firstname`, `lastname`, `address`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'User', '123 Admin St, City, Country', 'admin@gmail.com', '1234', 'admin'),
(2, 'Order', 'Handler', '456 Order Ave, City, Country', 'order@gmail.com', '1234', 'order'),
(3, 'Inventory', 'Manager', '789 Inventory Blvd, City, Country', 'inventory@gmail.com', '1234', 'inventory'),
(8, 'Kumar', 'Sangakkara', 'pabahinna, balangoda', 'kumar@gmail.com', 'Kmds!234', 'order');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `fid` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fid`, `sender_id`, `subject`, `description`, `reply`) VALUES
(1, 8, 'hi', 'assas', 'ok'),
(2, 8, 'hi', 'assas', NULL),
(3, 8, '12', 'as', NULL),
(4, 1, 'My instruction not update', 'adad', NULL),
(5, 1, 'Budhika', 'Kuasekara', 'Kavindu'),
(6, 10, 'My message doesn\'t show', 'Hi my name', 'yes ok');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `receipt` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `userid` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `grand_total`, `receipt`, `order_date`, `userid`, `status`) VALUES
(28, 224.00, 'uploadscart/RichDadPoorDad.jpg', '2024-12-09 05:39:15', 10, 'Delivered'),
(32, 2224.00, 'uploadscart/Pay_Slip_E2240183.pdf', '2024-12-09 07:08:24', 10, 'Rejected'),
(34, 629.00, 'uploadscart/Pay_Slip_E2240183.pdf', '2024-12-09 09:31:38', 14, 'Rejected'),
(36, 922.00, 'uploadscart/Pay_Slip_E2240183.pdf', '2024-12-09 11:18:37', 14, 'Delivered'),
(38, 0.00, NULL, '2024-12-11 07:02:38', 1, 'Pending'),
(39, 0.00, NULL, '2024-12-11 07:04:59', 1, 'Pending'),
(40, 0.00, NULL, '2024-12-11 07:06:09', 1, 'Pending'),
(41, 0.00, NULL, '2024-12-11 07:06:18', 1, 'Pending'),
(42, 0.00, 'uploadscart/20240205_102522.jpg', '2024-12-11 07:06:32', 1, 'Pending'),
(43, 810.00, 'uploadscart/20240205_102522.jpg', '2024-12-11 07:08:16', 1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `bookid` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `bookid`, `name`, `price`, `quantity`) VALUES
(25, 28, 8, 'English Question', 112.00, 2),
(30, 32, 8, 'English Question', 112.00, 2),
(31, 32, 11, 'Arya\'s Dream', 2000.00, 1),
(33, 34, 8, 'English Question', 112.00, 2),
(34, 34, 10, 'Nuto the Curious - The Leaf Cafe', 405.00, 1),
(36, 36, 10, 'Nuto the Curious - The Leaf Cafe', 405.00, 2),
(39, 43, 10, 'Nuto the Curious - The Leaf Cafe', 405.00, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `fk_category_book` (`category_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`categoryid`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`fid`),
  ADD KEY `fk_customer_id` (`sender_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `bookid` (`bookid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `categoryid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fk_category_book` FOREIGN KEY (`category_id`) REFERENCES `category` (`categoryid`);

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_customer_id` FOREIGN KEY (`sender_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `customer` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`bookid`) REFERENCES `book` (`book_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
