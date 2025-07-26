-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `airline_db`;

-- Select the database to use for the following commands
USE `airline_db`;

-- Set foreign key checks to 0 to avoid errors during table creation
SET FOREIGN_KEY_CHECKS=0;

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `flight_routes`
--
DROP TABLE IF EXISTS `flight_routes`;
CREATE TABLE `flight_routes` (
  `route_id` int(11) NOT NULL AUTO_INCREMENT,
  `flight_name` varchar(50) NOT NULL,
  `departure_location` varchar(100) NOT NULL,
  `arrival_location` varchar(100) NOT NULL,
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `flight_schedules`
--
DROP TABLE IF EXISTS `flight_schedules`;
CREATE TABLE `flight_schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) NOT NULL,
  `departure_datetime` datetime NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `total_seats` int(11) NOT NULL DEFAULT 250,
  `available_seats` int(11) NOT NULL DEFAULT 250,
  PRIMARY KEY (`schedule_id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `flight_schedules_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `flight_routes` (`route_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `bookings`
--
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `seat_class` varchar(20) NOT NULL,
  `seat_type` varchar(20) NOT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `schedule_id` (`schedule_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `flight_schedules` (`schedule_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `payments`
--
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_id` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

--
-- Dumping data for table `flight_routes`
--
INSERT INTO `flight_routes` (`flight_name`, `departure_location`, `arrival_location`) VALUES
('IND-101', 'Delhi (DEL)', 'Mumbai (BOM)'), ('AIR-330', 'Delhi (DEL)', 'Bengaluru (BLR)'),
('VIST-450', 'Kolkata (CCU)', 'Delhi (DEL)'), ('IND-445', 'Indore (IDR)', 'Delhi (DEL)'),
('JET-555', 'Chandigarh (IXC)', 'Mumbai (BOM)'), ('SPICE-110', 'Jaipur (JAI)', 'Delhi (DEL)'),
('AIR-120', 'Lucknow (LKO)', 'Delhi (DEL)'), ('GO-130', 'Srinagar (SXR)', 'Delhi (DEL)'),
('VIST-140', 'Amritsar (ATQ)', 'Mumbai (BOM)'), ('IND-150', 'Varanasi (VNS)', 'Delhi (DEL)'),
('AIR-310', 'Bengaluru (BLR)', 'Chennai (MAA)'), ('IND-115', 'Hyderabad (HYD)', 'Bengaluru (BLR)'),
('JET-505', 'Chennai (MAA)', 'Kochi (COK)'), ('SPICE-610', 'Visakhapatnam (VTZ)', 'Hyderabad (HYD)'),
('IND-620', 'Vijayawada (VGA)', 'Hyderabad (HYD)'), ('AIR-630', 'Tirupati (TIR)', 'Hyderabad (HYD)'),
('VIST-640', 'Coimbatore (CJB)', 'Chennai (MAA)'), ('GO-650', 'Madurai (IXM)', 'Bengaluru (BLR)'),
('JET-660', 'Mangalore (IXE)', 'Bengaluru (BLR)'), ('SPICE-670', 'Trivandrum (TRV)', 'Chennai (MAA)'),
('IND-680', 'Vijayawada (VGA)', 'Bengaluru (BLR)'), ('SPICE-220', 'Mumbai (BOM)', 'Goa (GOI)'),
('GO-601', 'Ahmedabad (AMD)', 'Delhi (DEL)'), ('VIST-480', 'Pune (PNQ)', 'Delhi (DEL)'),
('AIR-710', 'Nagpur (NAG)', 'Mumbai (BOM)'), ('IND-720', 'Surat (STV)', 'Delhi (DEL)'),
('JET-730', 'Raipur (RPR)', 'Mumbai (BOM)'), ('SPICE-930', 'Guwahati (GAU)', 'Kolkata (CCU)'),
('AIR-820', 'Patna (PAT)', 'Delhi (DEL)'), ('IND-830', 'Bhubaneswar (BBI)', 'Kolkata (CCU)'),
('VIST-840', 'Ranchi (IXR)', 'Delhi (DEL)'), ('GO-850', 'Bagdogra (IXB)', 'Kolkata (CCU)'),
('JET-860', 'Imphal (IMF)', 'Guwahati (GAU)');

--
-- Dumping data for table `flight_schedules`
--
INSERT INTO `flight_schedules` (`route_id`, `departure_datetime`, `base_price`) VALUES
(1, '2025-08-15 06:00:00', 4800.00), (1, '2025-08-15 12:30:00', 5100.00), (1, '2025-08-15 21:00:00', 5400.00),
(2, '2025-08-16 07:15:00', 5500.00), (2, '2025-08-16 17:45:00', 5900.00), (3, '2025-09-21 19:00:00', 2600.00),
(4, '2025-09-22 08:30:00', 5300.00), (5, '2025-08-25 07:00:00', 2950.00), (5, '2025-08-25 19:00:00', 3300.00),
(6, '2025-08-17 10:00:00', 3200.00), (6, '2025-08-18 19:30:00', 3400.00), (7, '2025-08-19 08:00:00', 3100.00),
(8, '2025-08-20 11:45:00', 4500.00), (9, '2025-09-27 19:45:00', 3950.00), (10, '2025-08-21 15:00:00', 2900.00),
(11, '2025-09-24 12:00:00', 2950.00), (12, '2025-08-22 09:20:00', 2600.00), (12, '2025-08-23 16:00:00', 2850.00),
(13, '2025-09-25 10:00:00', 2850.00), (14, '2025-09-05 13:00:00', 3600.00), (15, '2025-08-24 14:00:00', 2400.00),
(16, '2025-09-18 20:00:00', 2900.00), (17, '2025-08-27 18:00:00', 2300.00), (18, '2025-08-28 12:30:00', 2700.00),
(19, '2025-09-23 15:00:00', 3500.00), (20, '2025-08-26 11:00:00', 3000.00), (21, '2025-09-01 10:30:00', 3200.00),
(21, '2025-09-01 15:00:00', 3500.00), (22, '2025-09-02 06:00:00', 3150.00), (23, '2025-09-03 14:00:00', 4200.00),
(24, '2025-09-26 16:30:00', 4500.00), (25, '2025-09-28 07:15:00', 4800.00), (26, '2025-09-07 20:00:00', 4100.00),
(27, '2025-09-06 18:30:00', 3800.00), (28, '2025-09-08 11:15:00', 2900.00), (29, '2025-09-10 09:45:00', 4300.00),
(30, '2025-09-12 17:00:00', 2800.00), (31, '2025-09-14 12:00:00', 2500.00), (1, '2025-09-15 18:00:00', 5250.00),
(2, '2025-09-16 11:30:00', 5750.00), (6, '2025-09-17 14:00:00', 3350.00), (21, '2025-09-19 13:20:00', 3400.00),
(14, '2025-09-20 09:00:00', 3700.00);