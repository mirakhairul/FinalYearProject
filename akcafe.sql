-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 06, 2024 at 07:20 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `akcafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `cafe_category`
--

CREATE TABLE `cafe_category` (
  `category_id` int(100) NOT NULL,
  `category_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cafe_category`
--

INSERT INTO `cafe_category` (`category_id`, `category_name`) VALUES
(2, 'Signature'),
(3, 'Coffee'),
(4, 'Non Coffee'),
(5, 'Frappe'),
(6, 'Sparkling'),
(7, 'Cheese Foam'),
(13, 'Matcha'),
(15, 'Pattisseries'),
(16, 'Dessert');

-- --------------------------------------------------------

--
-- Table structure for table `cafe_customer`
--

CREATE TABLE `cafe_customer` (
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` varchar(10) NOT NULL,
  `customer_dob` date NOT NULL,
  `customer_gender` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_username` varchar(255) NOT NULL DEFAULT 'default_value',
  `customer_password` varchar(255) DEFAULT 'default_password'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cafe_customer`
--

INSERT INTO `cafe_customer` (`customer_name`, `customer_phoneno`, `customer_dob`, `customer_gender`, `customer_email`, `customer_username`, `customer_password`) VALUES
('farah husna', '0134181600', '2004-06-24', 'female', 'husna2@gmail.com', 'husna', '$2y$10$Z6mvtrDEwR7nkCGcodVdi./4vbzu4UI65wW3Y3/mcl6oYlcNmZPq.'),
('farah yasmine', '0118914511', '2004-10-03', 'female', 'farah12@gmail.com', 'farah', '$2y$10$7ETzALACCVM7jblwSk.Vr.skl/VxDyFrXgB9yvP9jiSbFB403Qfsq'),
('hanina', '194755755', '2002-06-17', 'female', 'nina.nzmn@gmail.com', 'hnn.nea', ''),
('Jules Blank', '1234567891', '2000-06-10', 'female', 'jules01@gmail.com', 'Jules', '111'),
('max', '114335674', '1992-05-12', 'male', 'max@gmail.com', 'default_value', 'default_password'),
('Nazran', '134181600', '1999-02-24', 'male', 'nazran24@gmail.com', 'Blaze', '2402'),
('Sarah Andri', '0197007151', '2006-11-06', 'female', 'sarah11@gmail.com', 'sarah', '');

-- --------------------------------------------------------

--
-- Table structure for table `cafe_feedback`
--

CREATE TABLE `cafe_feedback` (
  `comment` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cafe_order`
--

CREATE TABLE `cafe_order` (
  `order_id` varchar(255) NOT NULL,
  `order_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`order_items`)),
  `special_instructions` varchar(200) NOT NULL,
  `total_price` double NOT NULL,
  `order_date` datetime NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phoneno` int(10) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `status` enum('Incomplete','Complete','Pending') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cafe_order`
--

INSERT INTO `cafe_order` (`order_id`, `order_items`, `special_instructions`, `total_price`, `order_date`, `customer_name`, `customer_phoneno`, `customer_email`, `payment_method`, `status`) VALUES
('663fac2acc', '{\"F06\":{\"prod_name\":\"Fragola\",\"prod_price\":\"15.00\",\"quantity\":\"3\"},\"A01\":{\"prod_name\":\"Americano\",\"prod_price\":\"7.00\",\"quantity\":\"1\"}}', '', 52, '2024-05-12 01:34:34', 'Muhamad Nazran', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('664581ddda', '{\"F10\":{\"prod_name\":\"latte\",\"prod_price\":\"14.00\",\"quantity\":\"3\"}}', '', 42, '2024-05-16 11:47:41', 'silia james', 194760600, 'sil1a@gmail.com', 'Pay at cashier', 'Complete'),
('6646237470', '{\"F10\":{\"prod_name\":\"latte\",\"prod_price\":\"14.00\",\"quantity\":\"1\"},\"F06\":{\"prod_name\":\"Fragola\",\"prod_price\":\"15.00\",\"quantity\":\"2\"}}', '', 44, '2024-05-16 23:17:08', 'Jules', 112345678, 'Jules02@gmail.com', 'Pay at cashier', 'Complete'),
('66462daab5', '{\"c01\":{\"prod_name\":\"cinnamon\",\"prod_price\":\"10.00\",\"quantity\":\"5\"}}', '', 50, '2024-05-17 00:00:42', 'khalish', 194751900, 'khalish55@gmail.com', 'Online Transfer', 'Complete'),
('664633f3df', '{\"F03\":{\"prod_name\":\"hazelnut frappe\",\"prod_price\":\"14.00\",\"quantity\":\"1\"}}', '', 14, '2024-05-17 00:27:31', 'nurin', 13456890, 'nrnsbriena@gmail.com', 'Pay at cashier', 'Complete'),
('66649193c5', '{\"S04\":{\"prod_name\":\"French Toast Latte\",\"prod_price\":\"15.00\",\"quantity\":\"1\",\"special_instructions\":\"Choose: Hot. Ice Level: No. Instructions: less sweet\"}}', 'Choose: Hot. Ice Level: No. Instructions: less sweet', 15, '2024-06-09 01:14:59', 'Nazran Akmal ', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('666e65edd1', '{\"C02\":{\"prod_name\":\"Americano\",\"prod_price\":\"6.00\",\"quantity\":\"2\",\"special_instructions\":\"Hot\"},\"D05\":{\"prod_name\":\"Burnt Cheese Cake\",\"prod_price\":\"4.50\",\"quantity\":\"3\",\"special_instructions\":\"\"},\"S04\":{\"prod_name\":\"French Toast Latte\",\"prod_price\":\"15.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nHalf Ice\"}}', 'Hot; ; Cold\r\nHalf Ice', 40.5, '2024-06-16 12:11:25', 'Akmal', 134181600, 'nazran24@gmail.com', 'Pay at cashier', 'Complete'),
('666f1f06a6', '{\"S03\":{\"prod_name\":\"Blueberry Latte\",\"prod_price\":\"11.00\",\"quantity\":\"1\",\"special_instructions\":\"Hot\"}}', 'Hot', 11, '2024-06-17 01:21:10', 'Jules Blank', 123456789, 'jules01@gmail.com', 'Pay at cashier', 'Complete'),
('666f411047', '{\"S04\":{\"prod_name\":\"French Toast Latte\",\"prod_price\":\"15.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nNormal Ice\"}}', 'Cold\r\nNormal Ice', 15, '2024-06-17 03:46:24', 'Husnina Ishak', 124762033, 'husninan@gmail.com', 'Online Transfer', 'Complete'),
('666fa90e0b', '{\"R05\":{\"prod_name\":\"Fragola Smash\",\"prod_price\":\"14.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nNo Ice\"}}', 'Cold\r\nNo Ice', 14, '2024-06-17 11:10:06', 'Hanina', 194755755, 'nina.nzmn@gmail.com', 'Pay at cashier', 'Complete'),
('66726aada8', '{\"P05\":{\"prod_name\":\"Crispy Chicken Sandwich\",\"prod_price\":\"7.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 7.5, '2024-06-19 13:20:45', 'Suzy lee', 123456789, 'suzy1@gmail.com', 'Pay at cashier', 'Complete'),
('66727057b3', '{\"P07\":{\"prod_name\":\"Donut Cheese\",\"prod_price\":\"5.00\",\"quantity\":\"1\",\"special_instructions\":\"\"},\"CF01\":{\"prod_name\":\"Chocolate\",\"prod_price\":\"12.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nHalf Ice\"}}', '; Cold\r\nHalf Ice', 17, '2024-06-19 13:44:55', 'nuha', 115668123, 'nuha06@gmail.com', 'Online Transfer', 'Complete'),
('667277a3e7', '{\"C02\":{\"prod_name\":\"Americano\",\"prod_price\":\"6.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nHalf Ice\"}}', 'Cold\r\nHalf Ice', 6, '2024-06-19 14:16:03', 'Farah', 123335134, 'farah02@gmail.com', 'Online Transfer', 'Complete'),
('6673a8bbde', '{\"NC03\":{\"prod_name\":\"Fragola\",\"prod_price\":\"10.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nNormal Ice\"},\"D05\":{\"prod_name\":\"Burnt Cheese Cake\",\"prod_price\":\"4.50\",\"quantity\":\"2\",\"special_instructions\":\"\"}}', 'Cold\r\nNormal Ice; ', 19, '2024-06-20 11:57:47', 'june', 1234567890, 'june03@gmail.com', 'Online Transfer', 'Complete'),
('6673abdf61', '{\"C04\":{\"prod_name\":\"Caffe Latte\",\"prod_price\":\"8.00\",\"quantity\":\"1\",\"special_instructions\":\"Hot\"}}', 'Hot', 8, '2024-06-20 12:11:11', 'Muhamad Nazran Akmal Khairul Nazmi', 134181600, 'mirakhalish15@gmail.com', 'Online Transfer', 'Complete'),
('667a758784', '{\"C03\":{\"prod_name\":\"Con Panna\",\"prod_price\":\"7.00\",\"quantity\":\"2\",\"special_instructions\":\"Hot\"},\"P02\":{\"prod_name\":\"Plain Crossaint\",\"prod_price\":\"7.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', 'Hot; ', 21, '2024-06-25 15:45:11', 'Nazran Akmal ', 134181600, 'naz24@gmail.com', 'Online Transfer', 'Complete'),
('667babc2f0', '{\"C05\":{\"prod_name\":\"Cappuccino\",\"prod_price\":\"8.00\",\"quantity\":\"1\",\"special_instructions\":\"Hot\"}}', 'Hot', 8, '2024-06-26 13:48:50', 'Nazran', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('667bfdc412', '{\"F03\":{\"prod_name\":\"Chocolate\",\"prod_price\":\"12.50\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nNormal Ice\"}}', 'Cold\r\nNormal Ice', 12.5, '2024-06-26 19:38:44', 'Husnina Ishak', 1256631255, 'husninan@gmail.com', 'Pay at cashier', 'Complete'),
('667cbffd8c', '{\"P04\":{\"prod_name\":\"Chicken Slice Sandwich\",\"prod_price\":\"5.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 5.5, '2024-06-27 09:27:25', 'Alia ', 134111500, 'alia24@gmail.com', 'Pay at cashier', 'Complete'),
('667cc6762b', '{\"P01\":{\"prod_name\":\"Croissant Sandwich\",\"prod_price\":\"8.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 8, '2024-06-27 09:55:02', 'Nazran', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('667ccac182', '{\"P01\":{\"prod_name\":\"Croissant Sandwich\",\"prod_price\":\"8.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 8, '2024-06-27 10:13:21', 'Nazran', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('667ccd3929', '{\"P03\":{\"prod_name\":\"Mini Crossaint\",\"prod_price\":\"4.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 4, '2024-06-27 10:23:53', 'Max', 1631255278, 'max@gmail.com', 'Pay at cashier', 'Complete'),
('667cf1276e', '{\"P01\":{\"prod_name\":\"Croissant Sandwich\",\"prod_price\":\"8.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 8, '2024-06-27 12:57:11', 'Nazran', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('667e841753', '{\"P07\":{\"prod_name\":\"Donut Cheese\",\"prod_price\":\"5.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 5, '2024-06-28 17:36:23', 'Aisha ', 194755755, 'aisha15@gmail.com', 'Pay at cashier', 'Complete'),
('66837f22b0', '{\"NC01\":{\"prod_name\":\"Chocolate\",\"prod_price\":\"7.00\",\"quantity\":\"1\",\"special_instructions\":\"Hot\"},\"P05\":{\"prod_name\":\"Crispy Chicken Sandwich\",\"prod_price\":\"7.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', 'Hot; ', 14.5, '2024-07-02 12:16:34', 'Nuha Amani', 115416111, 'nuha30@gmail.com', 'Pay at cashier', 'Complete'),
('668387d0db', '{\"P02\":{\"prod_name\":\"Plain Crossaint\",\"prod_price\":\"7.00\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 7, '2024-07-02 12:53:36', 'Sofia Arissa', 163115714, 'sofia01@gmail.com', 'Online Transfer', 'Complete'),
('66838b7806', '{\"P04\":{\"prod_name\":\"Chicken Slice Sandwich\",\"prod_price\":\"5.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 5.5, '2024-07-02 13:09:12', 'Izzah Amanina', 192143371, 'izz08nina@gmail.com', 'Pay at cashier', 'Complete'),
('668391c62d', '{\"P04\":{\"prod_name\":\"Chicken Slice Sandwich\",\"prod_price\":\"5.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 5.5, '2024-07-02 13:36:06', 'Nur Amira Hanina Bt Nazmi', 194760600, 'mirakhalish15@gmail.com', 'Online Transfer', 'Complete'),
('6683b07a3e', '{\"P06\":{\"prod_name\":\"Mini Fruity Croffle\",\"prod_price\":\"4.90\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 4.9, '2024-07-02 15:47:06', 'Liana Sarah', 113657190, 'liana12sarah@gmail.com', 'Pay at cashier', 'Complete'),
('6683bb9299', '{\"P04\":{\"prod_name\":\"Chicken Slice Sandwich\",\"prod_price\":\"5.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', '', 5.5, '2024-07-02 16:34:26', 'Nazran', 134181600, 'nazran24@gmail.com', 'Online Transfer', 'Complete'),
('6686385e79', '{\"C02\":{\"prod_name\":\"Americano\",\"prod_price\":\"6.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nNormal Ice\"},\"P05\":{\"prod_name\":\"Crispy Chicken Sandwich\",\"prod_price\":\"7.50\",\"quantity\":\"1\",\"special_instructions\":\"\"}}', 'Cold\r\nNormal Ice; ', 13.5, '2024-07-04 13:51:26', 'Sarah', 197007151, 'sarah11@gmail.com', 'Online Transfer', 'Complete'),
('66875fe860', '{\"NC03\":{\"prod_name\":\"Fragola\",\"prod_price\":\"10.00\",\"quantity\":\"1\",\"special_instructions\":\"Cold\\r\\nHalf Ice\"},\"P04\":{\"prod_name\":\"Chicken Slice Sandwich\",\"prod_price\":\"5.50\",\"quantity\":\"2\",\"special_instructions\":\"\"}}', 'Cold\r\nHalf Ice; ', 21, '2024-07-05 10:52:24', 'Farah', 134181600, 'farah02@gmail.com', 'Online Transfer', 'Complete');

--
-- Triggers `cafe_order`
--
DELIMITER $$
CREATE TRIGGER `check_order_items_format` BEFORE INSERT ON `cafe_order` FOR EACH ROW BEGIN
    IF JSON_VALID(NEW.order_items) <> 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid JSON format for order_items';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cafe_product`
--

CREATE TABLE `cafe_product` (
  `prod_id` int(11) NOT NULL,
  `img` text DEFAULT NULL,
  `prod_code` varchar(100) NOT NULL,
  `prod_name` varchar(200) NOT NULL,
  `prod_desc` varchar(200) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `prod_price` double(10,2) NOT NULL,
  `availability` enum('Available','Out of Stock') DEFAULT 'Available',
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cafe_product`
--

INSERT INTO `cafe_product` (`prod_id`, `img`, `prod_code`, `prod_name`, `prod_desc`, `category_name`, `prod_price`, `availability`, `stock`, `created_at`) VALUES
(26, 'uploads/espresso.jpg', 'C01', 'Espresso', 'Double shot espresso served in a shot glass', 'Coffee', 5.50, 'Available', 0, '2024-06-28 09:11:28'),
(27, 'uploads/americano.jpg', 'C02', 'Americano', 'Double shot espresso mixed with 150ml of hot water', 'Coffee', 6.00, 'Available', 0, '2024-06-28 09:11:28'),
(28, 'uploads/con_panna.jpg', 'C03', 'Con Panna', 'Espresso (30ml) served with whipping cream', 'Coffee', 7.00, 'Available', 0, '2024-06-28 09:11:28'),
(29, 'uploads/latte.jpg', 'C04', 'Caffe Latte', 'Espresso served with frothed milk ', 'Coffee', 8.00, 'Available', 0, '2024-06-28 09:11:28'),
(30, 'uploads/cappucino.jpg', 'C05', 'Cappuccino', 'Espresso with creamy thick frothed milk', 'Coffee', 8.00, 'Available', 0, '2024-06-28 09:11:28'),
(31, 'uploads/mocha.jpg', 'C06', 'Mocha', 'Espresso with milk and cocoa powder', 'Coffee', 9.00, 'Available', 0, '2024-06-28 09:11:28'),
(32, 'uploads/freddo.jpg', 'C07', 'Caffe Freddo', 'Bottled and chilled caffe latte', 'Coffee', 10.00, 'Available', 0, '2024-06-28 09:11:28'),
(33, 'uploads/caramel_macchiato.jpg', 'C08', 'Caramel Macchiato', 'Prepared with 2 shots of espresso, cold foam milk and caramel drizzle', 'Coffee', 11.00, 'Available', 0, '2024-06-28 09:11:28'),
(42, 'uploads/black_orange.jpg', 'R01', 'Black Orange Splash', 'Espresso with soda and a slice of lemon', 'Sparkling', 10.00, 'Available', 0, '2024-06-28 09:11:28'),
(43, 'uploads/apple.jpeg', 'R02', 'Apple Fizz', 'Green apple flavored soda drink with a slice of lemon', 'Sparkling', 10.00, 'Available', 0, '2024-06-28 09:11:28'),
(44, 'uploads/blue.jpg', 'R03', 'Blueberry Peach Paradise', 'A refreshing fizzy blueberry peach drink', 'Sparkling', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(45, 'uploads/peach_hibuscus.jpg', 'R04', 'Peach Hibiscus Sunrise', 'Fizzy peach drink with hints of hibiscus syrup', 'Sparkling', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(46, 'uploads/fragola_smash.jpg', 'R05', 'Fragola Smash', 'Fizzy strawberry with fresh strawberry pulp and syrup', 'Sparkling', 14.00, 'Available', 0, '2024-06-28 09:11:28'),
(47, 'uploads/azure_bliss.jpg', 'R06', 'Azure Bliss', 'Blueberry with blue syrup and soda', 'Sparkling', 13.00, 'Available', 0, '2024-06-28 09:11:28'),
(48, 'uploads/chocolates.jpg', 'NC01', 'Chocolate', 'Premium chocolate, rich, sweet in taste', 'Non Coffee', 7.00, 'Available', 0, '2024-06-28 09:11:28'),
(49, 'uploads/rose.jpeg', 'NC02', 'Enchanted Rose', 'Rose flavored milk with dried petals', 'Non Coffee', 8.00, 'Available', 0, '2024-06-28 09:11:28'),
(50, 'uploads/fragola.jpg', 'NC03', 'Fragola', 'Mashed strawberry with milk + popping strawberry sprinkled with dried strawberry ', 'Non Coffee', 10.00, 'Available', 0, '2024-06-28 09:11:28'),
(51, 'uploads/redvelvet.jpg', 'NC04', 'Red Velvet', 'Sweet and creamy red velvet with milk', 'Non Coffee', 10.00, 'Available', 0, '2024-06-28 09:11:28'),
(52, 'uploads/fragolachoc.jpg', 'NC05', 'Fragola Choc', 'Mashed strawberry with chocolate powder', 'Non Coffee', 13.00, 'Available', 0, '2024-06-28 09:11:28'),
(53, 'uploads/matcha.jpg', 'M01', 'Matcha Latte', 'Matcha with milk', 'Matcha', 8.00, 'Available', 0, '2024-06-28 09:11:28'),
(54, 'uploads/matchachoc.jpg', 'M02', 'Matcha Choc', 'Matcha latte with chocolate syrup', 'Matcha', 9.50, 'Available', 0, '2024-06-28 09:11:28'),
(55, 'uploads/matcha_blueberry.jpg', 'M03', 'Matcha Blueberry', 'Matcha latte with blueberry jam', 'Matcha', 10.00, 'Available', 0, '2024-06-28 09:11:28'),
(56, 'uploads/matchaTaro.jpeg', 'M04', 'Matcha Taro', 'Matcha Taro Latte', 'Matcha', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(57, 'uploads/matcha_alitalia.jpg', 'M05', 'Matcha Alitalia', 'Matcha mixed with milk and mashed strawberry', 'Matcha', 13.00, 'Available', 0, '2024-06-28 09:11:28'),
(59, 'uploads/masala.jpeg', 'NC06', 'Black Masala Tea', 'Teabag masala tea with no milk', 'Non Coffee', 2.50, 'Available', 0, '2024-06-28 09:11:28'),
(60, 'uploads/lemontea.jpeg', 'NC07', 'Lemon Tea', 'Tea with slice of lemon', 'Non Coffee', 3.50, 'Available', 0, '2024-06-28 09:11:28'),
(62, 'uploads/lemongrass.jpeg', 'NC08', 'Lemon Grass Tea', 'Lemon grass tea with chopped lemon grass stalk', 'Non Coffee', 4.50, 'Available', 0, '2024-06-28 09:11:28'),
(63, 'uploads/vanigliafrappe.jpg', 'F01', 'Vaniglia', 'Vanilla Frappe', 'Frappe', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(64, 'uploads/cappuccinofrappe.jpg', 'F02', 'Cappuccino', 'Cappuccino frappe', 'Frappe', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(66, 'uploads/chocfrappe.jpg', 'F03', 'Chocolate', 'Sweet milky chocolate frappe', 'Frappe', 12.50, 'Available', 0, '2024-06-28 09:11:28'),
(67, 'uploads/hazelnutfrappe.jpg', 'F04', 'Hazelnut', 'Hazelnut latte frappe', 'Frappe', 13.00, 'Available', 0, '2024-06-28 09:11:28'),
(68, 'uploads/fragolafrappe.jpg', 'F05', 'Fragola', 'Strawberry frappe', 'Frappe', 15.00, 'Available', 0, '2024-06-28 09:11:28'),
(69, 'uploads/matchafrappe.jpg', 'F06', 'Matcha', 'Matcha frappe', 'Frappe', 15.00, 'Available', 0, '2024-06-28 09:11:28'),
(71, 'uploads/snowsalt.jpg', 'F07', 'Snow Salt Oreo', 'Blue vanilla frappe with sea salt, oreo crumbs & sweet drizzle', 'Frappe', 15.00, 'Available', 0, '2024-06-28 09:11:28'),
(72, 'uploads/cfchoc.jpeg', 'CF01', 'Chocolate', 'Chocolate with cheese foam', 'Cheese Foam', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(73, 'uploads/matchacf.jpeg', 'CF02', 'Matcha', 'Matcha with cheese foam', 'Cheese Foam', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(74, 'uploads/rv.jpeg', 'CF03', 'Red Velvet', 'Red velvet with cheese foam', 'Cheese Foam', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(75, 'uploads/crossaint.jpg', 'P01', 'Croissant Sandwich', 'Chicken ham crossaint', 'Pattisseries', 8.00, 'Available', 4, '2024-06-28 09:11:28'),
(76, 'uploads/pcrossaint.jpg', 'P02', 'Plain Crossaint', 'Buttery croissant drizzled with maple syrup', 'Pattisseries', 7.00, 'Available', 4, '2024-06-28 09:11:28'),
(77, 'uploads/minicrossaint.jpeg', 'P03', 'Mini Crossaint', 'Mini crossaint with drizzle sauce', 'Pattisseries', 4.00, 'Available', 1, '2024-06-28 09:11:28'),
(78, 'uploads/chicken_sandwich.jpg', 'P04', 'Chicken Slice Sandwich', ' served with chicken ham, sliced cheese, salad, tomatoes & sauce', 'Pattisseries', 5.50, 'Available', 5, '2024-06-28 09:11:28'),
(79, 'uploads/crispy.jpeg', 'P05', 'Crispy Chicken Sandwich', 'Served with crispy chicken, cheese slice, salad, sliced tomatoes, lettuce, dressing sauce', 'Pattisseries', 7.50, 'Available', 4, '2024-06-28 09:11:28'),
(80, 'uploads/minifruity.jpg', 'P06', 'Mini Fruity Croffle', 'Plain croffle dressed with whipped cream and fresh fruit', 'Pattisseries', 4.90, 'Available', 8, '2024-06-28 09:11:28'),
(83, 'uploads/lotus.jpg', 'D01', 'Lotus Biscoff Crepe Cake', 'Sweet mille crepe cake with crunchy biscoff bits', 'Dessert', 12.00, 'Available', 0, '2024-06-28 09:11:28'),
(84, 'uploads/tiramisu.jpg', 'D02', 'Tiramisu Crepe Cake', 'Sweet mille crepe cake with tiramisu flavor', 'Dessert', 12.00, 'Available', 5, '2024-06-28 09:11:28'),
(85, 'uploads/mango.jpg', 'D03', 'Mango Crepe Cake', 'Sweet mille crepe cake with fresh mango', 'Dessert', 12.00, 'Available', 5, '2024-06-28 09:11:28'),
(86, 'uploads/rose_lychee.jpg', 'D04', 'Rose Lychee Crepe Cake', 'Sweet mille crepe cake with rose and lychee flavor', 'Dessert', 12.00, 'Available', 3, '2024-06-28 09:11:28'),
(87, 'uploads/burnt.jpeg', 'D05', 'Burnt Cheese Cake', 'Topped with strawberry & bits of choc bar', 'Dessert', 4.50, 'Available', 6, '2024-06-28 09:11:28'),
(88, 'uploads/brownies.jpg', 'D06', 'Choc Drizzled Brownies', 'Fudgy brownie cake', 'Dessert', 5.50, 'Available', 10, '2024-06-28 09:11:28'),
(89, 'uploads/donut.jpeg', 'P07', 'Donut Cheese', 'Donut with slice cheese and drizzled sauce', 'Pattisseries', 5.00, 'Available', 5, '2024-06-28 09:11:28'),
(97, 'uploads/a4cf14099932728e9e1a48b78e38d67b.jpg', 'S01', 'Hazelnut Latte', 'Espresso with milk fragrance of hazelnut', 'Signature', 9.00, 'Available', 0, '2024-06-28 09:11:28'),
(98, 'uploads/vaniglia corsa_latte.jpg', 'S02', 'Vaniglia Corsa Latte', 'Espresso with milk and fragrance of vanilla', 'Signature', 9.00, 'Available', 0, '2024-06-28 09:15:26'),
(99, 'uploads/toffee.jpeg', 'S03', 'Toffee Nut Latte', 'Espresso with milk and fragrance of toffee nut', 'Signature', 9.00, 'Available', 0, '2024-06-28 09:17:52'),
(100, 'uploads/salted_caramel_latte.jpg', 'S04', 'Salted Caramel Latte', 'Latte with salted caramel syrup', 'Signature', 9.00, 'Available', 0, '2024-06-28 09:19:28'),
(101, 'uploads/fragola_latte.jpg', 'S05', 'Fragola Latte', 'Latte served with mashed strawberry', 'Signature', 11.00, 'Available', 0, '2024-06-28 09:20:27'),
(102, 'uploads/blue_latte.jpg', 'S06', 'Blueberry Latte', 'Latte with blueberry jam', 'Signature', 11.00, 'Available', 0, '2024-06-28 09:21:09'),
(103, 'uploads/french_toast_latte.jpg', 'S07', 'French Toast Latte', 'Latte with mixture of maple & vanilla syrup topped with a light layer of whipped cream and cinnamon drizzled', 'Signature', 15.00, 'Available', 0, '2024-06-28 09:21:51'),
(104, 'uploads/samoan_rock_latte.jpg', 'S08', 'Samoan Rock Latte', 'Latte with coconut milk and grated coconut', 'Signature', 15.00, 'Available', 0, '2024-06-28 09:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `cafe_staff`
--

CREATE TABLE `cafe_staff` (
  `staff_id` int(100) NOT NULL,
  `staff_name` varchar(200) NOT NULL,
  `staff_phoneno` varchar(10) NOT NULL,
  `staff_email` varchar(200) NOT NULL,
  `staff_password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cafe_staff`
--

INSERT INTO `cafe_staff` (`staff_id`, `staff_name`, `staff_phoneno`, `staff_email`, `staff_password`) VALUES
(1, 'amira', '0112456890', 'amir15@gmail.com', '156'),
(24, 'Nazran Akmal', '0134181600', 'nazranblaze@gmail.com', 'akhaute99'),
(171, 'anita', '0194121755', 'anjimi318@gmail.com', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cafe_category`
--
ALTER TABLE `cafe_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `cafe_customer`
--
ALTER TABLE `cafe_customer`
  ADD PRIMARY KEY (`customer_name`,`customer_username`),
  ADD UNIQUE KEY `customer_username` (`customer_username`);

--
-- Indexes for table `cafe_feedback`
--
ALTER TABLE `cafe_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cafe_order`
--
ALTER TABLE `cafe_order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `cafe_product`
--
ALTER TABLE `cafe_product`
  ADD PRIMARY KEY (`prod_id`),
  ADD UNIQUE KEY `prod_code` (`prod_code`);

--
-- Indexes for table `cafe_staff`
--
ALTER TABLE `cafe_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cafe_category`
--
ALTER TABLE `cafe_category`
  MODIFY `category_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `cafe_feedback`
--
ALTER TABLE `cafe_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cafe_product`
--
ALTER TABLE `cafe_product`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `cafe_staff`
--
ALTER TABLE `cafe_staff`
  MODIFY `staff_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
