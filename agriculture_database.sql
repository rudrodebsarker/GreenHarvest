-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 05:27 PM
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
-- Database: `agriculture`
--

-- --------------------------------------------------------

--
-- Table structure for table `agri_officer`
--

CREATE TABLE `agri_officer` (
  `officer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `District` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agri_officer`
--

INSERT INTO `agri_officer` (`officer_id`, `name`, `email`, `road`, `area`, `District`, `country`) VALUES
(1, 'Nurul Islam', 'nurul@example.com', '4/A Kazi Rd', 'Banani', NULL, 'Bangladesh'),
(2, 'Lutfur Rahman', 'lutfur@example.com', '8/B Chittagong Rd', 'Agargaon', 'Dhaka', 'Bangladesh'),
(3, 'Sultana Begum', 'sultana@example.com', '23/C Shibbari', 'Chattogram', 'Chittagong', 'Bangladesh'),
(4, 'Jamil Ahmed', 'jamil@example.com', '5/D Green Rd', 'Sylhet Sadar', 'Sylhet', 'Bangladesh'),
(5, 'Nasreen Akter', 'nasreen@example.com', '14/E Mollah Rd', 'Khulna', 'Khulna', 'Bangladesh'),
(6, 'Sayed Ahmed', 'sayed@gmail.com', '32 ', 'Mirpur', 'Dhaka', 'Bangladesh'),
(9, 'Sayed Ahmed', 'sayed@gmail.com', '32 ', 'Mirpur', 'Dhaka', 'Bangladesh'),
(122, 'aa', 'aaa@gmail.com', '123 High St', 'South', 'Dhaka', 'Bangladesh');

-- --------------------------------------------------------

--
-- Table structure for table `agri_officer_contact`
--

CREATE TABLE `agri_officer_contact` (
  `officer_id` int(11) NOT NULL,
  `contact` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agri_officer_contact`
--

INSERT INTO `agri_officer_contact` (`officer_id`, `contact`) VALUES
(1, '01712-345677'),
(1, '01712-345678'),
(2, '01819-876543'),
(3, '01923-456789'),
(4, '01678-123456'),
(5, '01511-234567'),
(6, '019672627');

-- --------------------------------------------------------

--
-- Table structure for table `agri_product`
--

CREATE TABLE `agri_product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `seasonality` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agri_product`
--

INSERT INTO `agri_product` (`product_id`, `name`, `seasonality`, `type`) VALUES
(1, 'Tomato', 'Spring/Summer', 'Vegetable'),
(2, 'Potato', 'Year-round', 'Vegetable'),
(3, 'Carrot', 'Spring', 'root vegetables'),
(4, 'Mango', 'summer', 'Fruit'),
(11, 'Jack fruit', 'spring', 'sesonal_fruit'),
(12, 'lichi', 'summer', 'fruits');

-- --------------------------------------------------------

--
-- Table structure for table `agri_product_variety`
--

CREATE TABLE `agri_product_variety` (
  `product_id` int(11) NOT NULL,
  `variety` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agri_product_variety`
--

INSERT INTO `agri_product_variety` (`product_id`, `variety`) VALUES
(1, 'Hard Red Winter Wheat'),
(2, 'Basmati Rice'),
(2, 'Jasmine Rice'),
(3, 'Nantes 5'),
(3, 'Red Core Nantes'),
(4, 'Kesar');

-- --------------------------------------------------------

--
-- Table structure for table `consumer`
--

CREATE TABLE `consumer` (
  `consumer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumer`
--

INSERT INTO `consumer` (`consumer_id`, `name`, `contact`, `email`) VALUES
(1, 'Jobbar Mia', '01834567890', 'jobbar@example.com'),
(2, 'Rahim Hossian', '01718763561', 'rahim@example.com'),
(3, 'Amina Begum', '01823456789', 'amina@example.com'),
(4, 'Fahim Uddin', '01834567890', 'fahim@example.com'),
(5, 'Shakil Ahmed', '01845678901', 'shakil@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `farmer`
--

CREATE TABLE `farmer` (
  `farmer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `road` varchar(255) DEFAULT NULL,
  `house` varchar(255) DEFAULT NULL,
  `District` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `years_of_experience` int(11) DEFAULT NULL,
  `weather_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer`
--

INSERT INTO `farmer` (`farmer_id`, `name`, `road`, `house`, `District`, `area`, `country`, `years_of_experience`, `weather_id`) VALUES
(1, 'Rashid Ali', '25/A Main Rd', 'House 3', 'Dhaka', 'Mohakhali', 'Bangladesh', 10, NULL),
(2, 'Shirin Sultana', '16/B Green St', 'House 10', 'Chittagong', 'Pahartali', 'Bangladesh', 7, NULL),
(3, 'Kamrul Hasan', '48/C New Rd', 'House 4', 'Rajshahi', 'Natore', 'Bangladesh', 5, NULL),
(4, 'Farida Begum', '12/D Riverside Rd', 'House 6', 'Sylhet', 'Jaflong', 'Bangladesh', 8, NULL),
(5, 'Abdul Motaleb', '5/E Old Rd', 'House 1', 'Khulna', 'Bagerhat', 'Bangladesh', 15, NULL),
(10, 'sadik', '123 High St', 'undef', 'Dhaka', '11', 'Bangladesh', 19, NULL),
(11, 'aa', '123 High St', 'a', '12', '11', 'Bangladesh', 11, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_agri_officer`
--

CREATE TABLE `farmer_agri_officer` (
  `officer_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `recommendation_info` text DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `transfer_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_agri_officer`
--

INSERT INTO `farmer_agri_officer` (`officer_id`, `farmer_id`, `recommendation_info`, `joining_date`, `transfer_date`) VALUES
(1, 3, 'Recommended for rice and wheat cultivation.', '2023-01-15', '2024-03-10'),
(2, 2, 'Recommended organic pesticide usage.', '2022-07-01', '2023-09-20'),
(3, 3, 'Suggested drip irrigation for efficiency.', '2021-11-10', '2023-05-15'),
(4, 1, 'Advice on post-harvest storage methods.', '2023-06-05', NULL),
(5, 5, 'Encouraged high-yield maize variety.', '2024-02-18', '2025-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `production_data`
--

CREATE TABLE `production_data` (
  `production_id` int(11) NOT NULL,
  `yield` decimal(10,2) DEFAULT NULL,
  `acreage` decimal(10,2) DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `per_acre_seeds_requirement` decimal(10,2) NOT NULL,
  `seeding_date` date DEFAULT NULL,
  `harvesting_date` date DEFAULT NULL,
  `data_input_date` date DEFAULT NULL,
  `farmer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `officer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `production_data`
--

INSERT INTO `production_data` (`production_id`, `yield`, `acreage`, `cost`, `per_acre_seeds_requirement`, `seeding_date`, `harvesting_date`, `data_input_date`, `farmer_id`, `product_id`, `officer_id`) VALUES
(1, 10.00, 100.00, 600000.00, 25.00, '2021-04-07', '2021-09-15', '2021-12-29', 1, 1, 1),
(2, 12.00, 80.00, 70000.00, 1000.00, '2020-12-16', '2021-08-03', '2021-12-01', 2, 2, 4),
(6, 50.00, 124.00, 780000.00, 80.00, '2025-03-31', '2025-04-16', '2025-05-09', 2, 2, 3),
(13, 22.00, 22.00, 100000.00, 120.00, '2025-04-27', '2025-05-06', '2025-05-14', 2, 1, 9),
(19, 12.00, 122.00, 23233.00, 123.00, '2025-05-05', '2025-05-21', '2025-05-30', 2, 1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `retailer`
--

CREATE TABLE `retailer` (
  `retailer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `road` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `District` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retailer`
--

INSERT INTO `retailer` (`retailer_id`, `name`, `contact`, `road`, `area`, `District`, `country`) VALUES
(1, 'Super Market', '555-4321', '123 High St', 'South', 'New York', 'USA'),
(2, 'Grocery Store', '555-8765', '456 Broadway', 'North', 'Los Angeles', 'USA'),
(3, 'Super Mart', '01811223344', '55/D Aftab Rd', 'Mohakhali', 'Dhaka', 'Bangladesh'),
(4, 'Grocery House', '01822334455', '22/C Kazi Rd', 'Dhanmondi', 'Dhaka', 'Bangladesh'),
(5, 'Daily Bazar', '01833445566', '14/F New Rd', 'Mirpur', 'Dhaka', 'Bangladesh'),
(6, 'Chittagong Fresh Store', '01844556677', '30/B Chittagong Rd', 'Chittagong', 'Chittagong', 'Bangladesh'),
(7, 'Sylhet Market', '01855667788', '7/C Jaflong Rd', 'Sylhet', 'Sylhet', 'Bangladesh');

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `sale_id` int(11) NOT NULL,
  `sale_date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `retailer_id` int(11) DEFAULT NULL,
  `consumer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`sale_id`, `sale_date`, `time`, `retailer_id`, `consumer_id`) VALUES
(1, '2025-04-18', '07:13:58', 3, 3),
(2, '2025-05-06', '09:00:00', 3, 1),
(3, '2025-05-07', '10:00:00', 2, 4),
(4, '2025-05-08', '01:00:00', 1, 2),
(5, '2025-05-26', '12:00:00', 3, 3),
(7, '2025-05-10', '13:28:20', 5, 1),
(8, '2025-05-15', '13:31:38', 5, 4),
(13, '2025-05-08', '13:24:21', 5, 3),
(15, '2025-05-12', '13:31:42', 5, 2),
(16, '2025-05-21', '16:35:56', 5, 3),
(19, '2025-05-22', '13:38:49', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `sale_details_id` int(11) NOT NULL,
  `quantity_sold` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`sale_details_id`, `quantity_sold`, `unit_price`, `sale_id`, `product_id`) VALUES
(3, 20, 67.00, 7, 3),
(5, 100, 65.00, 13, 1),
(6, 10, 72.00, 8, 3),
(10, 15, 300.00, 16, 12),
(11, 50, 15.50, 1, 1),
(12, 30, 20.00, 2, 2),
(13, 20, 18.50, 3, 3),
(14, 40, 12.00, 4, 4),
(17, 5, 110.00, 15, 4),
(21, 7, 100.00, 19, 4);

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE `shipment` (
  `shipment_id` int(11) NOT NULL,
  `ship_date` date DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipment`
--

INSERT INTO `shipment` (`shipment_id`, `ship_date`, `warehouse_id`) VALUES
(2, '2025-04-16', 5),
(5, '2025-04-05', 5),
(12, '2025-05-20', 2),
(19, '2025-04-29', 5),
(55, '2000-11-11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shipment_agri_product`
--

CREATE TABLE `shipment_agri_product` (
  `shipment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `quantity_shipped` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipment_agri_product`
--

INSERT INTO `shipment_agri_product` (`shipment_id`, `product_id`, `cost`, `quantity_shipped`) VALUES
(2, 1, 150000.00, 1000),
(2, 11, 12000.00, 50000),
(5, 1, 20000.00, 20000),
(12, 1, 200000.00, 1000),
(12, 4, 100000.00, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `track_agri_traders`
--

CREATE TABLE `track_agri_traders` (
  `product_id` int(11) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `wholesaler_id` int(11) NOT NULL,
  `retailer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `track_agri_traders`
--

INSERT INTO `track_agri_traders` (`product_id`, `unit_cost`, `quantity`, `date`, `wholesaler_id`, `retailer_id`) VALUES
(1, 60.00, 300, '2025-02-27', 3, 2),
(1, 60.00, 250, '2025-04-28', 4, 5),
(2, 50.00, 1500, '2025-04-02', 2, 2),
(3, 55.00, 80, '2025-01-06', 4, 3),
(3, 55.00, 600, '2025-05-03', 5, 5),
(4, 80.00, 500, '2025-05-04', 3, 5),
(4, 95.00, 122, '2025-05-01', 3, 7),
(11, 300.00, 1200, '2025-01-12', 2, 1),
(12, 250.00, 200, '2025-05-06', 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `user_id` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `email`, `password`, `user_type`, `user_id`) VALUES
('user', 'user@gmail.com', '202cb962ac59075b964b07152d234b70', '', 'active'),
('Sadi', 'sadi@gamil.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Admin', 'active'),
('retailer', 'retailer@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Retailer', 'active'),
('farmer', 'farmer@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Farmer', 'active'),
('wholesaler', 'wholesaler@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Wholesaler', 'active'),
('wmanager', 'wmanager@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Warehouse_manager', 'active'),
('consumer', 'consumer@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Consumer', 'active'),
('re', 're@gmail', 'c20ad4d76fe97759aa27a0c99bff6710', 'Retailer', 'active'),
('agri', 'agri@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Agricultural_Officer', 'active'),
('admin', 'admin@gmail.com', '202cb962ac59075b964b07152d234b70', 'Admin', 'active'),
('rudro', 'rudro@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Admin', 'active'),
('sarker', 'sarker@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Retailer', 'active'),
('rudra', 'rudra@gmail.com', 'c20ad4d76fe97759aa27a0c99bff6710', 'Retailer', 'RETCA955D58'),
('admin_a', 'admin_a@gmail.com', '202cb962ac59075b964b07152d234b70', 'Admin', 'ADM0689CD91');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `warehouse_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_num` varchar(255) DEFAULT NULL,
  `available_stock_of_product` int(11) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `wholesaler_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse`
--

INSERT INTO `warehouse` (`warehouse_id`, `name`, `location`, `contact_num`, `available_stock_of_product`, `last_updated`, `wholesaler_id`) VALUES
(1, 'Warehouse A', 'Sylhet', '01978789786', 10000, '2025-05-08 00:00:00', NULL),
(2, 'Warehouse B', 'California', '555-2222', 7000, '2025-04-27 00:00:00', 2),
(4, 'Warehouse R', 'Sylhet', '01978789786', 45000, '2025-05-06 00:00:00', NULL),
(5, 'Warehouse C', 'Rajshahi', '01934563456', 700, '2025-05-15 00:00:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `weather_info`
--

CREATE TABLE `weather_info` (
  `weather_id` int(11) NOT NULL,
  `station` varchar(255) NOT NULL,
  `Jan` decimal(5,2) DEFAULT NULL,
  `Feb` decimal(5,2) DEFAULT NULL,
  `Mar` decimal(5,2) DEFAULT NULL,
  `Apr` decimal(5,2) DEFAULT NULL,
  `May` decimal(5,2) DEFAULT NULL,
  `Jun` decimal(5,2) DEFAULT NULL,
  `Jul` decimal(5,2) DEFAULT NULL,
  `Aug` decimal(5,2) DEFAULT NULL,
  `Sep` decimal(5,2) DEFAULT NULL,
  `Oct` decimal(5,2) DEFAULT NULL,
  `Nov` decimal(5,2) DEFAULT NULL,
  `Decm` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weather_info`
--

INSERT INTO `weather_info` (`weather_id`, `station`, `Jan`, `Feb`, `Mar`, `Apr`, `May`, `Jun`, `Jul`, `Aug`, `Sep`, `Oct`, `Nov`, `Decm`) VALUES
(1, 'Dhaka', 15.00, 20.00, 25.00, 30.00, 35.00, 40.00, 45.00, 40.00, 35.00, 30.00, 25.00, 20.00),
(2, 'Chittagong', 18.00, 22.00, 27.00, 32.00, 37.00, 42.00, 47.00, 42.00, 38.00, 32.00, 27.00, 22.00),
(3, 'Rajshahi', 12.00, 17.00, 22.00, 27.00, 32.00, 37.00, 42.00, 37.00, 33.00, 27.00, 22.00, 17.00),
(4, 'Khulna', 16.00, 21.00, 26.00, 31.00, 36.00, 41.00, 46.00, 41.00, 37.00, 31.00, 26.00, 21.00),
(5, 'Sylhet', 14.00, 19.00, 24.00, 29.00, 34.00, 39.00, 44.00, 39.00, 34.00, 29.00, 24.00, 19.00),
(6, 'Sylhet', 12.00, 10.00, 5.00, 21.00, 16.00, 12.00, 11.00, 67.00, 0.00, 0.00, 0.00, 0.00),
(7, 'Joypuhat', 12.00, 11.00, 12.00, 12.00, 2.00, 4.00, 4.00, 34.00, 12.00, 2.00, 13.00, 12.00),
(8, 'Joypuhat', 11.00, 22.00, 22.00, 12.00, 13.00, 12.00, 13.00, 11.00, 12.00, 10.00, 0.00, 0.00),
(9, 'Cumilla', 12.00, 11.00, 12.00, 12.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(10, 'Barisal', 12.00, 12.00, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(11, 'Feni', 12.00, 10.00, 11.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(12, 'Nuakhali', 12.00, 12.00, 0.00, 0.00, 11.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `wholesaler`
--

CREATE TABLE `wholesaler` (
  `wholesaler_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `house` varchar(100) NOT NULL DEFAULT '',
  `road` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `District` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wholesaler`
--

INSERT INTO `wholesaler` (`wholesaler_id`, `name`, `contact`, `house`, `road`, `area`, `District`, `country`) VALUES
(1, 'Hifs Agro Food', '01746394639', '254', '2/C South Rd', '2/C South Rd', 'Dhaka', 'Bangladesh'),
(2, 'Green Goods Co.', '555-5678', '', '1012 Valley Rd', 'North', 'Chicago', 'USA'),
(3, 'Fresh Green Wholesale', '01912345678', '', '14/F Old Town Rd', 'Motijheel', 'Dhaka', 'Bangladesh'),
(4, 'Agro Supply Co.', '01923456789', '', '2/A South Rd', 'Uttara', 'Dhaka', 'Bangladesh'),
(5, 'Agro Products Ltd.', '01934567890', '', '3/B Mirpur Rd', 'Mirpur', 'Dhaka', 'Bangladesh');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agri_officer`
--
ALTER TABLE `agri_officer`
  ADD PRIMARY KEY (`officer_id`);

--
-- Indexes for table `agri_officer_contact`
--
ALTER TABLE `agri_officer_contact`
  ADD PRIMARY KEY (`officer_id`,`contact`);

--
-- Indexes for table `agri_product`
--
ALTER TABLE `agri_product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `agri_product_variety`
--
ALTER TABLE `agri_product_variety`
  ADD PRIMARY KEY (`product_id`,`variety`);

--
-- Indexes for table `consumer`
--
ALTER TABLE `consumer`
  ADD PRIMARY KEY (`consumer_id`);

--
-- Indexes for table `farmer`
--
ALTER TABLE `farmer`
  ADD PRIMARY KEY (`farmer_id`),
  ADD KEY `fk_weather_id` (`weather_id`);

--
-- Indexes for table `farmer_agri_officer`
--
ALTER TABLE `farmer_agri_officer`
  ADD PRIMARY KEY (`officer_id`,`farmer_id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `production_data`
--
ALTER TABLE `production_data`
  ADD PRIMARY KEY (`production_id`),
  ADD KEY `farmer_id` (`farmer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `officer_id` (`officer_id`);

--
-- Indexes for table `retailer`
--
ALTER TABLE `retailer`
  ADD PRIMARY KEY (`retailer_id`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `retailer_id` (`retailer_id`),
  ADD KEY `consumer_id` (`consumer_id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`sale_details_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shipment`
--
ALTER TABLE `shipment`
  ADD PRIMARY KEY (`shipment_id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- Indexes for table `shipment_agri_product`
--
ALTER TABLE `shipment_agri_product`
  ADD PRIMARY KEY (`shipment_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `track_agri_traders`
--
ALTER TABLE `track_agri_traders`
  ADD PRIMARY KEY (`product_id`,`wholesaler_id`,`retailer_id`),
  ADD KEY `wholesaler_id` (`wholesaler_id`),
  ADD KEY `retailer_id` (`retailer_id`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`warehouse_id`),
  ADD KEY `wholesaler_id` (`wholesaler_id`);

--
-- Indexes for table `weather_info`
--
ALTER TABLE `weather_info`
  ADD PRIMARY KEY (`weather_id`);

--
-- Indexes for table `wholesaler`
--
ALTER TABLE `wholesaler`
  ADD PRIMARY KEY (`wholesaler_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agri_officer`
--
ALTER TABLE `agri_officer`
  MODIFY `officer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `agri_product`
--
ALTER TABLE `agri_product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `consumer`
--
ALTER TABLE `consumer`
  MODIFY `consumer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `farmer`
--
ALTER TABLE `farmer`
  MODIFY `farmer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `production_data`
--
ALTER TABLE `production_data`
  MODIFY `production_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `retailer`
--
ALTER TABLE `retailer`
  MODIFY `retailer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `sale_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `shipment`
--
ALTER TABLE `shipment`
  MODIFY `shipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `warehouse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `weather_info`
--
ALTER TABLE `weather_info`
  MODIFY `weather_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wholesaler`
--
ALTER TABLE `wholesaler`
  MODIFY `wholesaler_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agri_officer_contact`
--
ALTER TABLE `agri_officer_contact`
  ADD CONSTRAINT `fk_officer_contact_officer_id_cascade` FOREIGN KEY (`officer_id`) REFERENCES `agri_officer` (`officer_id`) ON DELETE CASCADE;

--
-- Constraints for table `agri_product_variety`
--
ALTER TABLE `agri_product_variety`
  ADD CONSTRAINT `fk_product_variety_product_id_cascade` FOREIGN KEY (`product_id`) REFERENCES `agri_product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `farmer`
--
ALTER TABLE `farmer`
  ADD CONSTRAINT `fk_farmer_weather_id_cascade` FOREIGN KEY (`weather_id`) REFERENCES `weather_info` (`weather_id`) ON DELETE CASCADE;

--
-- Constraints for table `farmer_agri_officer`
--
ALTER TABLE `farmer_agri_officer`
  ADD CONSTRAINT `fk_farmer_agri_officer_farmer_id_cascade` FOREIGN KEY (`farmer_id`) REFERENCES `farmer` (`farmer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_farmer_agri_officer_officer_id_cascade` FOREIGN KEY (`officer_id`) REFERENCES `agri_officer` (`officer_id`) ON DELETE CASCADE;

--
-- Constraints for table `production_data`
--
ALTER TABLE `production_data`
  ADD CONSTRAINT `fk_production_data_farmer_id_cascade` FOREIGN KEY (`farmer_id`) REFERENCES `farmer` (`farmer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_production_data_officer_id_cascade` FOREIGN KEY (`officer_id`) REFERENCES `agri_officer` (`officer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_production_data_product_id_cascade` FOREIGN KEY (`product_id`) REFERENCES `agri_product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `sale`
--
ALTER TABLE `sale`
  ADD CONSTRAINT `fk_sale_consumer_id_cascade` FOREIGN KEY (`consumer_id`) REFERENCES `consumer` (`consumer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sale_retailer_id_cascade` FOREIGN KEY (`retailer_id`) REFERENCES `retailer` (`retailer_id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `fk_sale_details_product_id_cascade` FOREIGN KEY (`product_id`) REFERENCES `agri_product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sale_details_sale_id_cascade` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`sale_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipment`
--
ALTER TABLE `shipment`
  ADD CONSTRAINT `fk_shipment_warehouse_id_cascade` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`warehouse_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipment_agri_product`
--
ALTER TABLE `shipment_agri_product`
  ADD CONSTRAINT `fk_shipment_agri_product_product_id_cascade` FOREIGN KEY (`product_id`) REFERENCES `agri_product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_shipment_agri_product_shipment_id_cascade` FOREIGN KEY (`shipment_id`) REFERENCES `shipment` (`shipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `track_agri_traders`
--
ALTER TABLE `track_agri_traders`
  ADD CONSTRAINT `fk_track_agri_traders_product_id_cascade` FOREIGN KEY (`product_id`) REFERENCES `agri_product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_track_agri_traders_retailer_id_cascade` FOREIGN KEY (`retailer_id`) REFERENCES `retailer` (`retailer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_track_agri_traders_wholesaler_id_cascade` FOREIGN KEY (`wholesaler_id`) REFERENCES `wholesaler` (`wholesaler_id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD CONSTRAINT `fk_warehouse_wholesaler_id_cascade` FOREIGN KEY (`wholesaler_id`) REFERENCES `wholesaler` (`wholesaler_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
