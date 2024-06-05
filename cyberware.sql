-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2024 at 03:23 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cyberware`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`) VALUES
(20, 5, 18, 3);

-- --------------------------------------------------------

--
-- Table structure for table `customer_address`
--

CREATE TABLE `customer_address` (
  `customer_address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `house_no` varchar(255) NOT NULL,
  `street_name` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL COMMENT '*Province',
  `zip_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_address`
--

INSERT INTO `customer_address` (`customer_address_id`, `user_id`, `contact_no`, `house_no`, `street_name`, `barangay`, `city`, `country`, `zip_code`) VALUES
(1, 2, '090909099', '046', 'Elm Street', 'Alnay', 'Polangui', 'Albay', '4506'),
(2, 3, '09221232121', '021', 'Purok 7', 'Kinale', 'Polangui', 'Albay', '4503'),
(3, 4, '09321232541', '123', 'Purok 7', 'Kinale', 'Polangui', 'Albay', '4503'),
(4, 5, '09519346722', '090', 'matamis', 'Ponso', 'Polangui', 'Albay', '4506');

-- --------------------------------------------------------

--
-- Table structure for table `gcash_payments`
--

CREATE TABLE `gcash_payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `reference_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gcash_payments`
--

INSERT INTO `gcash_payments` (`id`, `order_id`, `fullname`, `phone_number`, `reference_number`, `created_at`) VALUES
(1, 26, 'Demo', 'Demo', '123817241', '2024-06-04 13:16:30'),
(2, 28, 'Patrick Juarez', '09321232541', '9182912390', '2024-06-04 13:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `order_status` varchar(100) NOT NULL COMMENT '''To Pay'', \r\n''To Ship'',\r\n''To Receive'',\r\n''Completed'',\r\n''Canceled''',
  `shipping_fee` decimal(10,0) NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `order_reference_number` varchar(255) NOT NULL,
  `tracking_number` varchar(255) NOT NULL,
  `expected_delivery` datetime NOT NULL,
  `payment_method` varchar(100) NOT NULL COMMENT '''cod'',''gcash''',
  `gcash_confirm` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `order_status`, `shipping_fee`, `total_price`, `order_reference_number`, `tracking_number`, `expected_delivery`, `payment_method`, `gcash_confirm`) VALUES
(23, 2, '2024-06-04 13:21:53', 'Canceled', 100, 16800, '665ea471e3933', '', '2024-06-11 07:21:53', 'cod', 0),
(24, 2, '2024-06-04 13:26:27', 'Completed', 100, 51795, '665ea583895da', '12312313', '2024-06-11 07:26:27', 'cod', 0),
(25, 3, '2024-06-04 21:15:51', 'Canceled', 100, 16800, '665f1387328d9', '', '2024-06-11 15:15:51', 'cod', 0),
(26, 3, '2024-06-04 21:16:30', 'Completed', 100, 13830, '665f13ae1d9c5', '^%@$#ae1231po', '2024-06-11 15:16:30', 'gcash', 1),
(28, 4, '2024-06-04 21:47:26', 'Completed', 100, 64890, '665f1aee9e30d', '^%@$#ae123123qw', '2024-06-11 15:47:26', 'gcash', 1),
(29, 5, '2024-06-04 22:03:49', 'Canceled', 100, 5600, '665f1ec5bc903', 'example', '2024-06-11 16:03:49', 'cod', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`) VALUES
(24, 23, 18, 1),
(25, 24, 18, 1),
(26, 24, 19, 1),
(27, 25, 18, 1),
(28, 26, 43, 3),
(29, 26, 53, 1),
(30, 26, 57, 1),
(32, 28, 32, 2),
(33, 28, 47, 1),
(34, 28, 62, 1),
(35, 29, 22, 1),
(36, 29, 71, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `specification` text NOT NULL,
  `product_img` varchar(225) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `category_name`, `brand`, `model`, `price`, `stock_quantity`, `specification`, `product_img`, `status`) VALUES
(18, 'Intel Core i7-12700', 'Brand: Intel\r\nModel: Core i7-12700\r\nNumber of Cores: 12\r\nNumber of Threads: 20\r\nCache: 25 MB Intel Smart Cache\r\nBase Clock Speed: 2.5 GHz\r\nMax Turbo Frequency: Up to 4.90 GHz\r\nSocket Type: LGA 1700\r\nMemory Type: DDR5 / DDR4\r\nMax Memory Size (dependent on memory type): 128 GB\r\nIntegrated Graphics: Intel UHD Graphics\r\nGraphics Base Frequency: 400 MHz\r\nGraphics Max Dynamic Frequency: 1.3 GHz\r\nGraphics Video Max Memory: 64 GB\r\nGraphics Output: DisplayPort, HDMI, VGA', 'Processor', 'Intel', '12th Gen', 16700, 0, 'Brand: Intel\r\nModel: Core i7-12700\r\nNumber of Cores: 12\r\nNumber of Threads: 20\r\nCache: 25 MB Intel Smart Cache\r\nBase Clock Speed: 2.5 GHz\r\nMax Turbo Frequency: Up to 4.90 GHz\r\nSocket Type: LGA 1700\r\nMemory Type: DDR5 / DDR4\r\nMax Memory Size (dependent on memory type): 128 GB\r\nIntegrated Graphics: Intel UHD Graphics\r\nGraphics Base Frequency: 400 MHz\r\nGraphics Max Dynamic Frequency: 1.3 GHz\r\nGraphics Video Max Memory: 64 GB\r\nGraphics Output: DisplayPort, HDMI, VGA', 'Image/intel-i7 12th gen.png', 'inactive'),
(19, 'Intel Core i9-14900KF ', 'The Intel Core i9-14900KF is a powerful 14th generation desktop processor featuring 24 cores and 32 threads. It delivers exceptional performance for intensive tasks such as gaming, content creation, and high-end computing. This processor is designed without integrated graphics, indicated by the \"KF\" suffix, requiring a separate graphics card.\r\n', 'Processor', 'Intel', '14th Gen', 34995, 4, 'Brand: Intel\r\nModel: Core i9-14900KF\r\nGeneration: 14th Gen\r\nNumber of Cores: 24\r\nNumber of Threads: 32\r\nSocket Type: LGA 1700\r\nCache: 48 MB Intel Smart Cache\r\nBase Clock Speed: 2.5 GHz\r\nMax Turbo Frequency: Up to 5.5 GHz\r\nMemory Type: DDR5 / DDR4', 'Image/intel-i9 14th Gen.png', 'inactive'),
(20, 'AMD Ryzen5 5600G ', 'The AMD Ryzen 5 5600G is a desktop processor with 6 cores and 12 threads, designed for the AM4 socket. It includes integrated Radeon graphics, making it suitable for both general computing and light gaming. The processor comes with the original Ryzen heatsink fan for effective cooling.', 'Processor', 'Ryzen', '5600G', 7500, 5, 'Brand: AMD\r\nModel: Ryzen 5 5600G\r\nSocket Type: AM4\r\nCooler: Ryzen Original Heatsink Fan (included)\r\nNumber of Cores: 6\r\nNumber of Threads: 12\r\nBase Clock Speed: 3.9 GHz\r\nMax Boost Clock: Up to 4.4 GHz\r\nCache: 19MB\r\nMemory Support: DDR4\r\nIntegrated Graphics: Radeon Graphics\r\nGraphics Base Frequency: 1900 MHz\r\nGraphics Max Frequency: (unknown)\r\nTDP (Thermal Design Power): 65W\r\nAdvanced Technologies:', 'Image/ryzen 6500g.png', 'active'),
(21, 'AMD Ryzen 7 7700X', 'The AMD Ryzen 7 7700X is a desktop processor featuring 8 cores and 12 threads. It offers high performance for gaming and productivity tasks. This processor is designed for the AM4 socket and does not come with a cooler, so an aftermarket cooling solution is required.', 'Processor', 'Ryzen', '7 700X', 19895, 5, 'Brand: AMD\r\nModel: Ryzen 7 7700X\r\nSocket Type: AM4\r\nCooler: Not included (Cooler not included in box)\r\nNumber of Cores: 8\r\nNumber of Threads: 12\r\nBase Clock Speed: 3.8 GHz\r\nMax Boost Clock: Up to 4.6 GHz\r\nCache: 36 MB\r\nMemory Support: DDR4\r\nIntegrated Graphics: None\r\nTDP (Thermal Design Power): 105W', 'Image/ryzen 7 7700x.png', 'active'),
(22, 'Ryzen 3200g', 'The AMD Ryzen 5 5600G is a desktop processor with 6 cores and 12 threads, designed for the AM4 socket. It includes integrated Radeon graphics, making it suitable for both general computing and light gaming. The processor comes with the original Ryzen heatsink fan for effective cooling.', 'Processor', 'ryzen', '3200g', 4500, 4, 'AMD Ryzen 3 3200G with Radeon Vega 8 Graphics / # of CPU Cores 4 / # of Threads 4 / Base Clock 3.6GHz / Max Boost Clock 4GHz /', 'Image/ryzen 3200g.png', 'active'),
(23, 'Asus ROG Strix B760-I Gaming WiFi', 'high-performance mini-ITX motherboard designed for gaming enthusiasts. It supports the latest Intel 12th and 13th Gen processors and features robust power delivery, DDR5 memory support, and PCIe 5.0. The board includes WiFi 6E, 2.5 Gb Ethernet, and a comprehensive array of USB ports for seamless connectivity. It also boasts advanced cooling solutions, customizable RGB lighting, and premium audio components to enhance the gaming experience.', 'Motherboard', 'ASUS', 'B760', 12995, 5, 'Brand: Asus\r\nModel: ROG Strix B760-I Gaming WiFi\r\nForm Factor: Mini-ITX\r\nChipset: Intel B760\r\nCPU Socket: LGA 1700\r\nSupported CPU: 12th and 13th Gen Intel Core, Pentium Gold, and Celeron\r\nMemory Support: 2 x DIMM, Max. 64GB, DDR5 7800+(OC)\r\nExpansion Slots: 1 x PCIe 5.0 x16\r\nStorage: 2 x M.2 (PCIe 4.0 x4), 4 x SATA 6Gb/s\r\nNetworking: Intel WiFi 6E, 2.5G Ethernet\r\nAudio: ROG SupremeFX 7.1, S1220A CODEC\r\nUSB Ports:\r\nRear: 1 x USB 3.2 Gen 2x2 Type-C, 2 x USB 3.2 Gen 2 Type-A, 2 x USB 3.2 Gen 1 Type-A, 2 x USB 2.0 Type-A\r\nFront: 1 x USB 3.2 Gen 1 header, 1 x USB 3.2 Gen 1 Type-C header, 1 x USB 2.0 header\r\nInternal I/O: 1 x 24-pin EATX, 1 x 8-pin EATX 12V, 1 x 4-pin CPU Fan, 1 x 4-pin Chassis Fan, 1 x AAFP, 1 x RGB header, 1 x Addressable Gen 2 header\r\nBIOS: 256 Mb Flash ROM, UEFI AMI BIOS\r\nFeatures: Aura Sync RGB, AI Overclocking, AI Cooling, AI Networking, Armoury Crate\r\nPackage Contents: Motherboard, SATA cables, Support DVD, Sticker, Keychain, Thank You Card, M.2 screw, Wi-Fi antennas, Cable ties, User manual', 'Image/ROG- Strix B760.png', 'active'),
(25, 'Sapphire Pulse AMD Radeon RX 7600 Gaming OC 8GB GDDR6 Graphic Card', 'The Sapphire Pulse AMD Radeon RX 7600 Gaming OC is a high-performance graphics card with 8GB of GDDR6 memory. It is designed for gaming and offers enhanced cooling and overclocking capabilities. This model, identified by the part number SPR-11324-01-20G, provides excellent performance for 1080p and 1440p gaming, ensuring smooth and immersive gameplay experiences.', 'Graphics Card', 'Sapphire', 'Radeon RX 7600 Gaming OC', 16750, 5, 'Brand: Sapphire\r\nModel: Pulse AMD Radeon RX 7600 Gaming OC\r\nGPU Architecture: RDNA 3\r\nBus Interface: PCIe 4.0 x8\r\nMemory Size: 8 GB\r\nMemory Type: GDDR6\r\nMemory Interface: 128-bit\r\nBase Clock Speed: 1720 MHz\r\nGame Clock Speed: 2250 MHz\r\nBoost Clock Speed: Up to 2750 MHz\r\nStream Processors: 2048\r\nRay Accelerators: 32\r\nTexture Units: 128\r\nROPs: 64\r\nCompute Units: 32\r\nInfinity Cache: 32 MB\r\nMemory Bandwidth: Up to 288 GB/s\r\nMax Resolution: 7680 x 4320 (8K UHD)\r\nOutputs: 1x HDMI 2.1, 3x DisplayPort 1.4 with DSC\r\nPower Consumption: 165W\r\nRecommended PSU: 550W\r\nPower Connectors: 1x 8-pin\r\nDirectX Support: DirectX 12 Ultimate\r\nOpenGL Support: OpenGL 4.6\r\nOpenCL Support: OpenCL 2.2\r\nVulkan Support: Vulkan 1.2\r\nFreeSync Support: Yes\r\nCooling: Dual-X Cooling Technology\r\nDimensions: 240 x 118.75 x 40.1 mm\r\nWeight: 861 g', 'Image/AMD Radeon RX 7600.png', 'active'),
(32, 'Asus ROG Maximus Z790 Hero EVA-02 Edition', 'is a high-performance ATX gaming motherboard designed for Intel\'s latest processors. It features DDR5 memory support, PCIe 5.0 slots, and advanced connectivity options like WiFi 6E and 10 Gb Ethernet. With premium audio and cooling features, it\'s an excellent choice for gamers seeking top-tier performance and reliability.', 'Motherboard', 'Asus', ' ROG Maximus Z790', 29900, 3, 'Brand: Asus\r\nModel: ROG Maximus Z790 Hero EVA-02 Edition\r\nForm Factor: ATX\r\nChipset: Intel Z790\r\nCPU Socket: LGA 1700\r\nSupported CPU: 12th and 13th Gen Intel Core, Pentium Gold, and Celeron\r\nMemory Support: 4 x DIMM, Max. 128GB, DDR4 6400+(OC)\r\n', 'Image/Hero EVA-02 Edition.png', 'active'),
(33, ' Asus ROG Strix Z790-A Gaming WiFi D4 ', 'is a feature-rich ATX motherboard tailored for gaming and high-performance desktop builds. It supports Intel\'s 12th and 13th Gen processors, utilizing the Intel Z790 chipse', 'Motherboard', 'Asus', 'ROG Strix Z790-A', 22950, 5, 'Brand: Asus Model: ROG STRIX Z790-A GAMING WIFI D4 Form Factor: ATX Chipset: Intel Z790 CPU Socket: LGA 1700', 'Image/Asus ROG Strix Z790-E.png', 'active'),
(34, 'Asus ROG Strix Z790-A Gaming WiFi D4', 'Motherboard is a premium choice for gaming enthusiasts. It supports DDR5 memory, WiFi 6, and Bluetooth', 'Motherboard', 'Asus', ' ROG Strix Z790-E', 26995, 5, 'Brand: Asus Model: ROG Strix Z790-E Gaming WiFi Form Factor: ATX Chipset: Intel Z790 CPU Socket: LGA 1700 Supported CPU: 12th and 13th Gen Intel Core, Pentium Gold, and Celeron', 'Image/ROG Strix Z790-A.png', 'active'),
(35, 'B550 Steel Legend  Supports 3rd Gen ', 'Steel Legend represents the philosophical state of rock-solid durability and irresistible aesthetics. Built around most demanding specs and features, the Steel Legend series aims at daily users and mainstream enthusiasts!', 'Motherboard', 'Steel Legend', 'B550', 7799, 5, 'Supports AMD AM4 Socket Ryzen™ 3000, 3000 G-Series, 4000 G-Series, 5000 and 5000 G-Series Desktop Processors*\r\n14 Power Phase Design, Digi Power, Dr. MOS\r\nSupports DDR4 4733+ (OC)\r\n1 PCIe 4.0 x16, 1 PCIe 3.0 x16, 2 PCIe 3.0 x1, 1 M.2 Key E for WiFi\r\nGraphics Output Options: HDMI, DisplayPort\r\nAMD CrossFireX™', 'Image/b550 steel legend.png', 'active'),
(36, 'MSI A320M-A PRO SUPPORTS AMD AM4 ', 'MSI A320M-A Pro AM4 DDR4 Gaming Motherboard: Unlock gaming potential with support for up to 3200MHz DDR4 RAM. Reliable AMD AM4 socket. Stable performance and gaming-ready features in a budget-friendly package', 'Motherboard', 'MSI', 'A320M', 4300, 2, 'Supports 1st, 2nd and 3rd Gen AMD Ryzen™ / Ryzen™ with Radeon™ Vega Graphics and 2nd Gen AMD Ryzen™ with Radeon™ Graphics / Athlon™ with Radeon™ Vega Graphics and A-series / Athlon X4 Desktop Processors for Socket AM4\r\nSupports DDR4 Memory, up to 3200 (OC) MHz\r\nAudio Boost: Reward your ears with studio grade sound quality.\r\nDDR4 Boost: Advanced technology to deliver pure data signals for the best performance and stability', 'Image/a320m.png', 'active'),
(37, ' MSI GeForce RTX 4090 GAMING X TRIO 24G 24GB GDDR6X Graphics Card', 'The MSI GeForce RTX 4090 GAMING X TRIO 24G is a top-tier graphics card featuring 24GB of GDDR6X memory. With the part number 912-V510-006, this card is designed for ultimate gaming and professional graphics performance, supporting 4K and higher resolutions with ray tracing and AI-enhanced graphics. ', 'Graphics Card', ' MSI', 'GeForce RTX 4090', 106995, 3, 'Brand: MSI Model: GeForce RTX 4090 GAMING X TRIO 24G GPU Architecture: Ada Lovelace Bus Interface: PCIe 4.0 x16 Memory Size: 24 GB Memory Type: GDDR6X Memory Interface: 384-bit Base Clock Speed: 2235 MHz Boost Clock Speed: Up to 2610 MHz CUDA Cores: 16384 Ray Tracing Cores: 144 (Third generation) Tensor Cores: 576 (Fourth generation)Texture Units: 512 ROPs: 176Memory Bandwidth: 1008 GB/sMax Resolution: 7680 x 4320 (8K UHD) Outputs: 1x HDMI 2.1, 3x DisplayPort 1.4a Power Consumption: 450W Recommended PSU: 850W\r\n', 'Image/gtx 4090.png', 'active'),
(38, 'Gigabyte NVIDIA® GeForce RTX™ 4060Ti OC WindForce 8GB GDDR6 GPU', 'The Gigabyte NVIDIA GeForce RTX 4060Ti OC WindForce is a high-performance graphics card with 8GB of GDDR6 memory. Identified by the part number GV-N406TWF2OC-8GD, this card is designed for gaming and content creation, offering advanced features like ray tracing and AI-enhanced graphics\r\n', 'Graphics Card', 'Gigabyte ', 'GeForce RTX 4060Ti ', 33050, 5, '\r\nBrand: Gigabyte\r\nModel: NVIDIA® GeForce RTX™ 4060Ti OC WindForce\r\nGPU Architecture: Ada Lovelace\r\nBus Interface: PCIe 4.0 x16\r\nMemory Size: 8 GB\r\nMemory Type: GDDR6\r\nMemory Interface: 128-bit\r\nBase Clock Speed: 2310 MHz\r\nBoost Clock Speed: Up to 2580 MHz (OC mode)\r\nCUDA Cores: 4352\r\nRay Tracing Cores: 34 (Third generation)\r\nTensor Cores: 136 (Fourth generation)\r\nTexture Units: 136\r\nROPs: 48\r\nMemory Bandwidth: 288 GB/s\r\nMax Resolution: 7680 x 4320 (8K UHD)\r\nOutputs: 2x HDMI 2.1, 2x DisplayPort 1.4a\r\nPower Consumption: 160W\r\nRecommended PSU: 450W', 'Image/rtx 4060 ti.png', 'active'),
(39, 'MSI GeForce RTX 3050 VENTUS 2X XS 8G OC Graphics Card', 'The MSI GeForce RTX 3050 VENTUS 2X XS 8G OC is a mid-range graphics card featuring 8GB of GDDR6 memory. It is designed for 1080p gaming and offers support for ray tracing and AI-enhanced graphics. ', 'Graphics Card', 'MSI ', 'GeForce RTX 3050 ', 15299, 5, 'Brand: MSI\r\nModel: GeForce RTX 3050 VENTUS 2X XS 8G OC\r\nGPU Architecture: Ampere\r\nBus Interface: PCIe 4.0 x16\r\nMemory Size: 8 GB\r\nMemory Type: GDDR6\r\nMemory Interface: 128-bit\r\nBase Clock Speed: 1552 MHz\r\nBoost Clock Speed: 1807 MHz (OC mode)\r\nCUDA Cores: 2560\r\nRay Tracing Cores: 20 (Second generation)\r\nTensor Cores: 80 (Third generation)\r\nTexture Units: 80\r\nROPs: 32\r\nMemory Bandwidth: 224 GB/s\r\nMax Resolution: 7680 x 4320 (8K UHD)\r\nOutputs: 1x HDMI 2.1, 3x DisplayPort 1.4a\r\nPower Consumption: 130W', 'Image/rtx 3050.png', 'active'),
(40, 'Gigabyte GeForce® GTX 1650 OC Edition 4GB Graphic Card', 'The Gigabyte GeForce GTX 1650 OC Edition is a budget-friendly graphics card equipped with 4GB of GDDR5 memory. Designed for 1080p gaming and general use, it offers a good balance of performance and efficiency. ', 'Graphics Card', 'Gigabyte', 'GeForce GTX 1650 ', 8500, 5, 'Brand: Gigabyte\r\nModel: GeForce® GTX 1650 OC Edition\r\nGPU Architecture: Turing\r\nBus Interface: PCIe 3.0 x16\r\nMemory Size: 4 GB\r\nMemory Type: GDDR5\r\nMemory Interface: 128-bit\r\nBase Clock Speed: 1485 MHz\r\nBoost Clock Speed: 1710 MHz (OC mode)\r\nCUDA Cores: 896\r\nRay Tracing Cores: Not applicable\r\nTensor Cores: Not applicable\r\nTexture Units: 56\r\nROPs: 32\r\nMemory Bandwidth: 128 GB/s\r\nMax Resolution: 7680 x 4320 (8K UHD)\r\nOutputs: 1x HDMI 2.0b, 1x DisplayPort 1.4, 1x DVI-D\r\nPower Consumption: 75W\r\nRecommended PSU: 300W', 'Image/GTX 1650.png', 'active'),
(41, 'Gigabyte GeForce RTX 3060 Gaming OC 12GB GDDR6', 'NVIDIA Ampere Streaming Multiprocessors\r\n2nd Generation RT Cores\r\n3rd Generation Tensor Cores\r\nPowered by GeForce RTX™ 3060\r\nIntegrated with 12GB GDDR6 192-bit memory interface\r\nWINDFORCE 3X Cooling System with alternate spinning fans\r\nRGB Fusion 2.0\r\nProtection metal back plate', 'Graphics Card', 'Gigabyte', 'GeForce RTX 3060 ', 19795, 5, 'The lowest latency. The best responsiveness. Powered by GeForce RTX 30 Series GPUs and NVIDIA® G-SYNC® monitors. Acquire targets faster, react quicker, and increase aim precision through a revolutionary suite of technologies to measure and optimize system latency for competitive games.', 'Image/rtx 3060.png', 'active'),
(43, 'Kingston FURY Beast DDR4 32GB SingleKit', 'The Kingston FURY Beast DDR4 is high-performance desktop memory designed for gaming enthusiasts. Available in various capacities, including 16GB and 32GB single-kit configurations, it offers excellent speed and reliability for demanding gaming and multitasking needs. With its 16Gbit chips, it provides fast data transfer rates and smooth performance for gaming and content creation tasks.\r\n', 'Memory', 'Kingston', 'FURY Beast DDR4 32GB', 2350, 2, 'Brand: Kingston\r\nModel: FURY Beast\r\nMemory Type: DDR4\r\nCapacity: 16GB (Single) / 32GB (Kit of 2 x 16GB)\r\nSpeed: 3200MHz (other speeds may be available)\r\nCAS Latency: CL16 (other timings may be available depending on speed)\r\nTiming: 16-18-18 (typical for 3200MHz)\r\nVoltage: 1.35V\r\nECC: No\r\nBuffered/Unbuffered: Unbuffered\r\nMulti-Channel Kit: Available as single module or dual channel kit\r\nHeat Spreader: Yes, low-profile heat spreader', 'Image/kingston fury beast 32gb.png', 'active'),
(44, 'Skihotar DDR4 8G PC RAM Memory 3200Mhz ', 'The Skihotar DDR4 8GB PC RAM Memory is a desktop memory module designed to enhance the performance of your PC. Available in different speeds, including 2666MHz and 3200MHz, it offers improved data transfer rates for faster and smoother computing experiences.', 'Memory', 'Skihotar ', 'DDR4 8G ', 1497, 4, 'Brand: Skihotar\r\nMemory Type: DDR4\r\nCapacity: 8GB\r\nSpeed: 2666MHz or 3200MHz (please specify)\r\nCAS Latency: CL16\r\nTiming: 16-18-18-36 (for 2666MHz, timings may vary for 3200MHz)\r\nVoltage: 1.2V - 1.35V\r\nECC: No\r\nBuffered/Unbuffered: Unbuffered\r\nHeat Spreader: None\r\nCompatibility: Compatible with most desktop PCs\r\nForm Factor: DIMM\r\nHeight: Standard', 'Image/skihotar.png', 'active'),
(45, 'Ramsta Ram Memory  DDR4  8GB  3200Mhz', 'The Ramsta RAM Memory series offers DDR3 and DDR4 modules in various capacities ranging from 4GB to 16GB and speeds including 1600MHz, 2666MHz, and 3200MHz. These modules are designed to be auto-compatible with a wide range of PC configurations, ensuring ease of installation and compatibility with your system.', 'Memory', 'Ramsta ', ' DDR4  8GB  3200Mhz', 1399, 5, '\r\nBrand: Ramsta\r\nMemory Type: DDR4\r\nCapacity: 8GB\r\nSpeed: 3200MHz\r\nCAS Latency: Budget-friendly\r\nTiming: Standard\r\nVoltage: Low voltage for energy efficiency\r\nECC: No\r\nBuffered/Unbuffered: Unbuffered\r\nHeat Spreader: None\r\nCompatibility: Compatible with most desktop PCs\r\nForm Factor: DIMM\r\nHeight: Standard', 'Image/ramsta.png', 'active'),
(46, 'Seagate Barracuda ST2000DM008 2TB 3.5 SATA 6.0Gbs', 'The Seagate Barracuda ST2000DM008 is a 3.5-inch internal hard drive with a storage capacity of 2TB\r\n(terabytes). It utilizes the SATA 6.0Gbs interface for high-speed data transfer rates. This hard drive is\r\npart of the Barracuda series, known for its reliability and performance,', 'Hard Drive', 'Seagate Barracuda ', ' ST2000DM008 ', 2500, 5, 'Brand: Seagate\r\nSeries: Barracuda\r\nModel: ST2000DM008\r\nForm Factor: 3.5 inches\r\nInterface: SATA 6.0Gbps\r\nCapacity: 2TB (Terabytes)\r\nRotational Speed: 7200 RPM (Revolutions per Minute)\r\nCache: 256MB\r\nAverage Data Transfer Rate: Up to 210 MB/s\r\nMTBF (Mean Time Between Failures): 1 million hours\r\nOperating Temperature: 0°C to 60°C\r\nDimensions (H x W x D): 26.11mm x 101.6mm x 146.99mm\r\nWeight: 490 grams', 'Image/seagate baraccuda.png', 'active'),
(47, 'Western Digital WD Blue SATA 3.5 Internal HDD Storage 1TB', 'The Western Digital WD Blue SATA 3.5 Internal HDD Storage is available in various capacities including\r\n1TB and 8TB. These hard drives utilize the SATA interface for high-speed data\r\ntransfer rates. The WD Blue series is known for its reliability and versatility, making it suitable for a wide\r\nrange of applications such as desktop PCs,', 'Hard Drive', 'Western Digital ', ' Blue SATA 3.5 ', 2500, 4, 'Brand: Western Digital\r\nSeries: WD Blue\r\nForm Factor: 3.5 inches\r\nInterface: SATA 6.0Gbps\r\nAvailable Capacities: 1TB, 2TB, 3TB, 4TB, 6TB, 8TB\r\nRotational Speed: Varies based on capacity (typically 5400 RPM or 7200 RPM)\r\nCache: Varies based on capacity (typically 64MB or 256MB)\r\nAverage Data Transfer Rate: Varies based on capacity\r\nMTBF (Mean Time Between Failures): Varies based on capacity\r\nOperating Temperature: Varies based on capacity\r\nDimensions (H x W x D): Varies based on capacity\r\nWeight: Varies based on capacity', 'Image/western digital.png', 'active'),
(48, 'Seagate Skyhawk 4TB 5400RPM 256MB 2.3TS RPWs', 'The Seagate SkyHawk ST4000VX016 is a 3.5-inch internal hard drive designed specifically for surveillance\r\nsystems. It offers a storage capacity of 4TB (terabytes) and operates at a rotational speed of 5400RPM\r\n(revolutions per minute). With a large 256MB cache and SATA 6.0Gbs interface, it provides fast and\r\nreliable data transfer rates, essential for continuous recording and playback in surveillance\r\nenvironments.', 'Hard Drive', 'Seagate Skyhawk', ' 4TB 5400RPM 256MB', 5788, 5, 'Brand: Seagate\r\nSeries: Skyhawk\r\nModel: ST4000VX016\r\nForm Factor: 3.5 inches\r\nInterface: SATA 6.0Gbps\r\nCapacity: 4TB (Terabytes)\r\nRotational Speed: 5400 RPM (Revolutions per Minute)\r\nCache: 256MB\r\nData Transfer Rate: Up to 180 MB/s\r\nWorkload Rating: 180TB/year\r\nMTBF (Mean Time Between Failures): 1 million hours\r\nOperating Temperature: 0°C to 65°C\r\nDimensions (H x W x D): 26.11mm x 101.6mm x 146.99mm\r\nWeight: 610 grams', 'Image/seagate skyhawk.png', 'active'),
(49, 'Ramsta Full Modular ATX 3.0 850W 80 Plus Bronze', 'The Ramsta Full Modular ATX 3.0 power supply series offers a range of wattage options from 550W to 1000W. These power supplies are 80 Plus Bronze certified for efficiency and feature a full modular design for easy cable management.', 'Power Supply', 'Ramsta', '80 Plus Bronze', 5444, 5, 'Brand: Ramsta\r\nSeries: Full Modular ATX 3.0\r\nPower Output: 850W\r\nEfficiency Certification: 80 Plus Bronze Certified\r\nModularity: Full Modular design for easy cable management\r\nForm Factor: ATX\r\nInput Voltage: 100V - 240V\r\nInput Frequency: 50Hz - 60Hz\r\nCooling: Quiet fan cooling system\r\nProtection: Overvoltage protection, Overcurrent protection, Short circuit protection\r\n', 'Image/ramsta power supply.png', 'active'),
(50, 'Inplay 250W ATX Power Supply Long Wire PSU', 'The Inplay ATX Power Supply series offers wattage options of 200W, 250W, and 300W. These power supplies are specifically designed for computer motherboards, featuring long wires for easy installation and connectivity.', 'Power Supply', 'Inplay', '300W ATX ', 1624, 5, 'Brand: Inplay\r\nPower Output Options: 200W, 250W, 300W\r\nForm Factor: ATX\r\nCompatibility: Designed for use with computer motherboards\r\nCable Length: Long wire design for flexible cable management\r\nInput Voltage: 100V - 240V\r\nInput Frequency: 50Hz - 60Hz\r\nCooling: Efficient cooling system to maintain optimal temperature\r\nProtection: Overvoltage protection, Overcurrent protection, Short circuit protection\r\n', 'Image/inplay power supply.png', 'active'),
(51, 'Inplay ATX Power Supply  650W 80 PLUS Brozen ', 'The Inplay ATX Power Supply series provides options ranging from 450W to 750W, with 80 PLUS Bronze certification ensuring efficiency. Additionally, these power supplies feature RGB lighting for aesthetic customization, making them suitable for desktop builds.', 'Power Supply', 'Inplay', '80 PLUS Brozen', 1599, 5, 'Brand: Inplay\r\nPower Output Options: 450W, 550W, 650W, 750W\r\nEfficiency Certification: 80 PLUS Bronze\r\nRGB Lighting: Integrated RGB lighting for aesthetic customization\r\nForm Factor: ATX\r\nCompatibility: Designed for desktop computers\r\nInput Voltage: 100V - 240V\r\nInput Frequency: 50Hz - 60Hz\r\nCooling: Efficient cooling system to maintain optimal temperature\r\nProtection: Overvoltage protection, Overcurrent protection, Short circuit protection', 'Image/inplay gs 650w.png', 'active'),
(52, 'Coolman Ruby PC Case Black and White', 'The Coolman Ruby PC case is available in black and white variants and comes equipped with three color fans, adding a vibrant touch to your build. With its stylish design and customizable lighting, it offers both aesthetic appeal and efficient cooling performance for your components.', 'Pc Case', 'Coolman', 'Ruby', 1875, 3, 'Brand: Coolman\r\nModel: Ruby\r\nForm Factor: Mid Tower\r\nColor Options: Black, White\r\nMaterial: Steel, Plastic\r\nMotherboard Compatibility: ATX, Micro-ATX, Mini-ITX\r\nExpansion Slots: 7', 'Image/coolman pc case.png', 'active'),
(53, 'Msi Mag Forge M100R - Micro ATX Tower PC Case ', 'The MSI MAG Forge M100R is a Micro ATX Tower PC case available in black. It features a compact design optimized for Micro ATX motherboards, making it suitable for smaller builds while still offering ample space for components. With its sleek black exterior, it provides a stylish and modern look for your PC setup.', 'Pc Case', 'MSI', 'Mag Forge M100R', 2650, 4, 'Brand: MSI\r\nModel: MAG Forge M100R\r\nForm Factor: Micro ATX Tower\r\nColor: Black\r\nMaterial: Steel\r\nMotherboard Compatibility: Micro ATX, Mini-ITX\r\nExpansion Slots: 4', 'Image/msi pc case.png', 'active'),
(54, 'RAKK DULUS Gaming PC Case Black', 'The RAKK DULUS Gaming PC Case in black is designed for gamers seeking a sleek and functional chassis. It features the RAKK MARIS PRO CHASSIS FAN 3-in-1 KIT, ensuring optimal airflow and cooling for your components. With its thoughtful design and included fan kit, it provides both style and performance for your gaming setup.', 'Pc Case', 'RAKK ', 'DULUS ', 1395, 5, 'Brand: RAKK\r\nModel: DULUS\r\nColor: Black\r\nForm Factor: Mid Tower\r\nMaterial: Steel, Plastic\r\nMotherboard Compatibility: ATX, Micro-ATX, Mini-ITX\r\nExpansion Slots: 7\r\nDrive Bays:\r\n2 x 3.5\" (compatible with 2.5\" SSDs)\r\n2 x 2.5\"\r\nFront I/O Ports:\r\n1 x USB 3.0\r\n2 x USB 2.0\r\n1 x Audio In / Out', 'Image/rakk pc case.png', 'active'),
(56, 'Acer Nitro VG240  23.8” IPS 180Hz ', 'The Acer Nitro 23.8” IPS Gaming Monitor VG240Y M3bmiipx is a top-notch choice for gamers. Boasting a 180Hz refresh rate and IPS technology, it delivers incredibly smooth and vibrant visuals. Its 23.8-inch screen size provides ample space for gaming immersion, while the IPS panel ensures accurate color reproduction from wide viewing angles. Ideal for gamers who prioritize both speed and visual quality.', 'Monitor', 'Acer Nitro ', 'VG240 ', 9295, 5, 'Brand: Acer\r\nModel: Nitro VG240Y M3bmiipx\r\nScreen Size: 23.8\"\r\nMaximum Resolution: 1920 x 1080 (Full HD)\r\nAspect Ratio: 16:9\r\nContrast Ratio: 1,000:1 (max ACM)\r\nResponse Time: 1ms GTG (0.5 ms GTG, Min)\r\nColor Supported: 16.7 Million\r\nAdaptive Contrast Management (ACM): 100,000,000:1\r\nBrightness: 250 cd/m²\r\nBacklight: LED\r\nViewing Angles: 178° Horizontal, 178° Vertical\r\nPanel Type: IPS (In-plane Switching)\r\nStand: Tilt (-5°~20°)\r\nSpeakers: 2 x 2 W', 'Image/acer nitro monitor.png', 'active'),
(57, 'MSI Pro MP223 22 FHD 100Hz VA ', 'The MSI Pro MP223 is a 22-inch FHD monitor designed for productivity and entertainment. It features a VA panel with a 100Hz refresh rate, ensuring smooth visuals. The monitor boasts a 1ms (MPRT) and 4ms (GTG) response time for reduced motion blur, making it suitable for fast-paced tasks. Additionally, it has an anti-glare screen to minimize reflections and enhance viewing comfort. Released in 2023, the Pro MP223 combines performance and ergonomic features for an optimal user experience.', 'Monitor', 'MSI ', 'MP223 ', 4030, 4, 'Brand: MSI\r\nModel: Pro MP223\r\nPanel Size: 21.45\"\r\nPanel Resolution: 1920 x 1080 (Full HD)\r\nRefresh Rate: 100Hz\r\nResponse Time: 1ms (MPRT) / 4ms (GTG)\r\nPanel Type: VA\r\nViewing Angle: 178°(H) / 178°(V)\r\nAspect Ratio: 16:9\r\nContrast Ratio: 3000:1\r\nSRGB: 99% (CIE 1976)\r\nActive Display Area (mm): 478.656(H) x 260.28(V)\r\nPixel Pitch (H X V): 0.2493(H) x 0.241(V)\r\nSurface Treatment: Anti-glare\r\nDisplay Colors: 16.7M\r\nColor Bit: 8 bits\r\nVideo Ports: 1x HDMI (1.4b), 1x D-Sub (VGA)\r\nAudio Ports: 1x Headphone-out', 'Image/msi monitor.png', 'active'),
(58, 'MSI Monitor G27 23.8 165Hz IPS ', 'The MSI Gaming Monitor G24 G27 offers a top-notch gaming experience with its 23.8-inch screen and blazing-fast 165Hz refresh rate. With Fast IPS technology and Full HD resolution, it ensures sharp and smooth visuals for an immersive gaming session. Plus, HDR10 support enhances color and contrast, while covering 120% of the sRGB color gamut for vibrant and lifelike images. Its low blue light feature helps reduce eye strain during extended gaming sessions. Ideal for gamers who demand high performance and visual fidelity.', 'Monitor', 'MSI', 'G27 ', 6955, 5, 'Brand: MSI\r\nMonitor Screen Size :23 - 25 Inches\r\nGaming Focused: Yes\r\nPanel Type: IPS\r\nMonitor Interface Type :DP, HDMI, USB C\r\nResolution: Full HD\r\nCondition: New\r\nMonitor & LCD Model: MAG251RX\r\nMonitor Response Time: 1ms', 'Image/msi monitor 2.png', 'active'),
(60, 'Redragon K617 FIZZ 60 Keys', 'The Redragon K617 FIZZ 60% is a compact mechanical keyboard with red switches, featuring a sleek\r\nblack and grey design. Its 60% layout saves space while providing essential keys, making it ideal for\r\ngaming and minimalist setups. The red switches offer smooth and quiet keystrokes, enhancing the\r\noverall typing and gaming experience.', 'Keyboard', 'Redragon', 'K617', 1350, 5, 'Brand: Redragon\r\nModel: K617 FIZZ\r\nSwitch Type: Red Switch\r\nColor: Black and Grey\r\nConnection Type: USB-C Wired\r\nNumber of Keys: 61 Keys\r\nMaterial: ABS\r\nKeycap: Double Injection Keycap\r\nBacklight: RGB\r\nCompatibility: Windows and macOS', 'Image/keyboard red dragon.png', 'active'),
(61, 'RAKK TALA 81 Keys Red Switch', 'The RAKK Tala is an 81-key mechanical keyboard featuring a sleek, white design. It combines a\r\ncompact layout with customizable RGB backlighting and high-quality mechanical switches,\r\noffering a reliable and visually appealing option for both gamers and typists.', 'Keyboard', 'Rakk', 'Tala', 2100, 5, 'Color: Black and White\r\nDimension (Product): 325x138x20mm\r\nWeight (Product): 850g\r\nConnection Type: Triple Mode (wired, 2.4g, BT 5.0)\r\nNumber of Keys: 81 keys\r\nMounting: Gasket Mount\r\nOrientation of PCB: North Facing\r\nHotswap: Kailh Universal Socket 5pin\r\nSwitches: MS Brown (Tactile)\r\nKeycaps: ABS OEM Seamless Doubleshot\r\nKnob: Aluminum\r\nStabilizers: Plate Mounted', 'Image/keyboard rakk tala.png', 'active'),
(62, ' RAKK Lam-Ang Pro Max 87 Keys', 'The RAKK Lam-Ang Pro Max is a high-performance mechanical keyboard featuring 87 keys. It offers a\r\ncompact tenkeyless design, making it suitable for both gaming and professional use. This keyboard is\r\nknown for its customizable RGB backlighting, durable build quality, and responsive mechanical switches,\r\nproviding a satisfying typing and gaming experience.', 'Keyboard', 'Rakk', 'Lam-Ang ', 2490, 4, 'Dimension (Product): 362 x 135 mm\r\nWeight (Product): 745g\r\nConnection Type: Triple Mode (wired, 2.4g, BT 5.0)\r\nNumber of Keys: 87 Keys\r\nMounting: Gasket Mount\r\nOrientation of PCB: South Facing\r\nHotswap: Kailh Universal Socket 5pin\r\nSwitches: Outemu Black (for pre-built)\r\nKeycaps: Cherry Profile PBT Doubleshot (for prebuilt)\r\nKnob: None\r\nScreen: TFT 0.85 inches | 128x128 resolution\r\nCase Material: ABS Plastic\r\nPlate Material: Polycarbonate', 'Image/keyboard lam ang.png', 'active'),
(63, 'Rakk Talan White ', 'The RAKK Talan is a versatile wireless gaming mouse, offering a DPI range of 100 to 16,000 for precise sensitivity adjustments. It is equipped with durable Huano Blue switches rated for 20 million clicks, ensuring longevity and reliability. Additionally, the Talan can also be used in wired\r\nmode, providing flexibility for various gaming scenarios.', 'Mouse', 'Rakk', 'Talan', 1499, 5, 'Brand: RAKK\r\nModel: Talan\r\nSensor: 100-16000 DPI for precise tracking.\r\nModes: Wireless/Wired\r\nDPI Range: 100-16000\r\nButtons: Programmable\r\nDesign: Ergonomic\r\nBattery Life: Long-lasting\r\nCompatibility: Windows, macOS, Linux\r\nSoftware: Not specified\r\nAdditional Features: Huano Blue 20M switches, comfortable grip, customizable RGB lighting.', 'Image/mouse talan.png', 'active'),
(64, ' Logitech G402 ', 'The Logitech G402 is a wired gaming mouse designed for high-speed performance and\r\naccuracy. It features Logitech&#39;s exclusive Fusion Engine technology for ultra-fast tracking and a\r\ncomfortable, ergonomic design for extended gaming sessions. The G402 comes with a one-\r\nyear warranty, ensuring peace of mind and reliable support.', 'Mouse', ' Logitech', 'G402 ', 3290, 5, 'Sensor: Fusion Engine Hybrid Sensor\r\nButtons: 8 programmable buttons\r\nDPI Switching: Instant\r\nProcessor: 32-bit ARM\r\nReport Rate: 1ms\r\nUSB: Full speed\r\nResolution: 240-4000 DPI\r\nAcceleration: &gt;16G\r\nSpeed: &gt;500 ips\r\nDynamic Friction Coefficient: 0.09 µ (k)\r\nStatic Friction Coefficient: 0.14 µ (s)\r\nMicroprocessor: 32-bit\r\nResponse Capability: 16 bits/axis USB data format, 1000 Hz USB reporting rate', 'Image/mouse logitech.png', 'active'),
(65, ' CPSTECH M8', 'The CPSTECH M8 is a wired gaming mouse featuring a high-precision 12000 DPI sensor, making it suitable for competitive gaming. It is designed with an ultralight structure for quick and effortless movements. The mouse includes 16 RGB backlighting options for a customizable aesthetic and comes with a programmable driver, allowing users to configure buttons and settings to their preferences.', 'Mouse', 'Attack Shark', 'R3', 1270, 5, 'Brand: Attack Shark\r\nModel: R3\r\nSensor: Supports 8K resolution for precise tracking.\r\nModes: Wired, Bluetooth, and Wireless.\r\nDPI Range: Adjustable for sensitivity preference.\r\nButtons: Programmable for customization.\r\nDesign: Ergonomic and modern.\r\nBattery Life: Long-lasting for wireless and Bluetooth modes.\r\nCompatibility: Works with Windows, macOS, and Linux.', 'Image/mouse csptech.png', 'active'),
(66, 'Logitech ProX2 Lightspeed', 'The Logitech Pro X 2 Lightspeed is a black gaming headset known for its wireless Lightspeed\r\ntechnology, providing a low-latency, high-quality audio experience. It features comfortable ear\r\npads, a durable build, and a detachable microphone with Blue VO!CE technology for clear\r\ncommunication, making it ideal for immersive gaming sessions.', 'Headset', 'Logitech ', 'ProX2', 4360, 5, 'Brand: Logitech\r\nModel: Pro X 2 Lightspeed\r\nColor: Black\r\nConnection Type: Lightspeed Wireless, Bluetooth, Wired (3.5mm)\r\nDriver: Graphene 50mm\r\nFrequency Response: 20 Hz - 20 kHz\r\nImpedance: 32 Ohms\r\nSensitivity: 91.7 dB SPL @ 1 mW &amp; 1 cm\r\nMicrophone: Detachable Blue VO!CE Microphone Technology\r\nMicrophone Frequency Response: 100 Hz - 10 kHz\r\nBattery Life: Up to 50 hours (Lightspeed Wireless)\r\nCharging: USB-C\r\nCompatibility: PC, Mac, PS5, PS4, Xbox Series X|S, Xbox One, Nintendo Switch, Mobile', 'Image/headset logitech.png', 'active'),
(67, 'Logitech Astro A50 Wireless', 'The Logitech Astro A50 Wireless is a premium gaming headset that comes with a base station for easy charging and connectivity. It delivers high-fidelity audio with Dolby Surround Sound, offering an immersive gaming experience. The headset features a comfortable, adjustable design, a long-lasting battery, and a high-quality, flip-to-mute microphone, making it perfect for extended gaming sessions.', 'Headset', 'Logitech', 'Astro A50', 6270, 5, 'Brand: Logitech\r\nModel: Astro A50 Wireless + Base Station\r\nColor: Black\r\nConnection Type: Wireless (2.4GHz), Wired (USB, 3.5mm)\r\nDriver: 40mm Neodymium\r\nFrequency Response: 20 Hz - 20 kHz\r\nImpedance: 48 Ohms\r\nSensitivity: 118 dB SPL\r\nMicrophone: Uni-directional, noise-canceling\r\nMicrophone Frequency Response: 100 Hz - 10 kHz\r\nBattery Life: Up to 15 hours\r\nCharging: Magnetic charging base station\r\nCompatibility: PC, Mac, PS5, PS4, Xbox Series X|S, Xbox One\r\nWeight: 380g', 'Image/headset logitech 2.png', 'active'),
(68, 'Logitech-G633s Wireless Headset', 'The Logitech G633s is a wired gaming headset that delivers high-quality sound with Pro-G audio drivers and DTS Headphone:X 2.0 surround sound. It features customizable RGB lighting, programmable G-keys, and a foldable boom microphone with noise-canceling capabilities. The G633s is designed for comfort with breathable ear pads and supports multiple platforms, making it suitable for immersive gaming experiences across various devices.', 'Headset', 'Logitech', 'G633s', 4350, 5, 'Brand: Logitech\r\nModel: G633s\r\nColor: Black\r\nConnection Type: Wired (USB, 3.5mm)\r\nDriver: Pro-G 50mm\r\nFrequency Response: 20 Hz - 20 kHz\r\nImpedance: 39 Ohms\r\nSensitivity: 107 dB SPL/mW\r\nMicrophone: Foldable boom mic with noise-canceling\r\nMicrophone Frequency Response: 100 Hz - 20 kHz\r\nCable Length: 2.2 meters (USB), 1.3 meters (3.5mm)\r\nCompatibility: PC, Mac, PS5, PS4, Xbox Series X|S, Xbox One, Nintendo Switch, Mobile devices\r\nWeight: 366g', 'Image/headset logitech 3.png', 'active'),
(69, 'Acer Nitro V ANV15-51-519K ', 'The Acer Nitro V ANV15-51-519K is a 15.6-inch Full HD gaming laptop. It features an Intel i5\r\nprocessor, 8GB of DDR5 RAM, a 512GB SSD, and an NVIDIA RTX 2050 graphics card. It runs\r\non Windows 11, making it a solid choice for gaming and everyday use.', 'Laptop', 'Acer', 'ANV15-51-519K', 65099, 5, 'Brand: Acer\r\nModel: Nitro V ANV15-51-519K\r\nDisplay: 15.6-inch Full HD (1920 x 1080)\r\nProcessor: Intel Core i5\r\nRAM: 8GB DDR5\r\nStorage: 512GB SSD\r\nGraphics Card: NVIDIA GeForce RTX 2050\r\nOperating System: Windows 11\r\nConnectivity: Wi-Fi 6, Bluetooth 5.0\r\nPorts:\r\nUSB Type-C\r\nUSB 3.2 Gen 1\r\nUSB 3.2 Gen 2\r\nHDMI\r\nEthernet (RJ-45)', 'Image/laptop.png', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `comment`, `date_added`) VALUES
(1, 18, 2, 5, 'This Product is Awesome', '2024-06-04 20:45:26'),
(2, 32, 4, 5, 'amazing product', '2024-06-04 21:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `roles` char(1) NOT NULL DEFAULT 'A',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `roles`, `first_name`, `last_name`, `email`, `date_added`) VALUES
(1, 'admin', 'admin123', 'A', 'admin', 'first', 'admin@cyberware.com', '2024-06-01 21:18:52'),
(2, 'user', 'user123', 'U', 'user', 'first', 'user@cyberware.com', '2024-06-01 21:17:26'),
(3, 'demo', 'demo123', 'U', 'demo', 'demo', 'demo123@gmail.com', '2024-06-04 21:14:30'),
(4, 'demo1', 'demo543', 'U', 'demo1', 'demo1', 'demo12345@gmail.com', '2024-06-04 21:41:51'),
(5, 'user2', 'user123', 'U', 'user', 'user', 'user@gmail.com', '2024-06-04 22:02:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `c_user_id_fk` (`user_id`),
  ADD KEY `c_prod_id_fk` (`product_id`);

--
-- Indexes for table `customer_address`
--
ALTER TABLE `customer_address`
  ADD PRIMARY KEY (`customer_address_id`),
  ADD KEY `ca_user_id_fk` (`user_id`);

--
-- Indexes for table `gcash_payments`
--
ALTER TABLE `gcash_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `o_user_id_fk` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `oi_order_id_fk` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `r_user_id_fk` (`user_id`),
  ADD KEY `r_prod_id_fk` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `customer_address`
--
ALTER TABLE `customer_address`
  MODIFY `customer_address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gcash_payments`
--
ALTER TABLE `gcash_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `c_prod_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `c_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `customer_address`
--
ALTER TABLE `customer_address`
  ADD CONSTRAINT `ca_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `gcash_payments`
--
ALTER TABLE `gcash_payments`
  ADD CONSTRAINT `gcash_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `o_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `oi_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `r_prod_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `r_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
