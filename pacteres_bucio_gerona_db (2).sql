-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2026 at 09:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pacteres_bucio_gerona.db`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL,
  `FullName` varchar(40) DEFAULT NULL,
  `PhoneNumber` decimal(10,0) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  `Address` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Customer_ID`, `FullName`, `PhoneNumber`, `Email`, `Address`) VALUES
(1, 'RIO D. PACTERES', 9630331469, 'pacteresrio@gmail.com', 'iligan city'),
(2, 'RIO D. PACTERES', 9630331469, 'pacteresrio@gmail.com', 'iligan city'),
(3, 'RIO D. PACTERES', 9630331469, 'pacteresrio@gmail.com', 'iligan city'),
(4, 'dela pinya', 54565, 'dela@gmail.com', 'tominobo'),
(5, 'dela pinya', 4543213123, 'pacteresrio@gmail.com', 'tominobo'),
(6, 'RIO D. PACTERES', 9999999999, 'angbu@gmail.com', 'iligan city'),
(7, 'Arvy Alarcon', 9566234435, 'tiyo@gmail.com', 'iligan city'),
(8, 'Jemrix Jala', 9263303547, 'jem@gmail.com', 'iligan city'),
(9, '', 0, '', ''),
(10, '', 0, '', ''),
(11, 'Arvy Alarcon', 9999999999, 'angbu@gmail.com', 'linamon'),
(12, 'Arvy Alarcon', 9999999999, 'angbu@gmail.com', 'linamon'),
(13, 'Arvy Alarcon', 9999999999, 'angbu@gmail.com', 'linamon'),
(14, 'dela pinya', 9554222211, 'dela@gmail.com', 'tominobo'),
(15, 'Jemrix Jala', 9999999999, 'jem@gmail.com', 'iligan city'),
(16, 'Jemrix Jala', 9999999999, 'jem@gmail.com', 'iligan city');

-- --------------------------------------------------------

--
-- Table structure for table `inventorypart`
--

CREATE TABLE `inventorypart` (
  `Part_ID` int(11) NOT NULL,
  `PartName` varchar(40) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `QuantityInStock` decimal(40,0) DEFAULT NULL,
  `SupplierName` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inventorypart`
--

INSERT INTO `inventorypart` (`Part_ID`, `PartName`, `Description`, `QuantityInStock`, `SupplierName`) VALUES
(1, 'Compressor', 'foryourcoldlove', 5, 'Jeric'),
(2, 'Pang-guba', 'a mechanical device that increases the p', 3, 'angbu'),
(3, 'Pang-guba', 'a mechanical device that increases the p', 3, 'angbu'),
(4, 'pfkjf', 'fffwefweffwfwfwfwfwfwfwfwfwfwffwf', 452, 'jeric'),
(5, 'pfkjf', 'fffwefweffwfwfwfwfwfwfwfwfwfwffwf', 452, 'jeric'),
(6, 'pfkjf', 'fffwefweffwfwfwfwfwfwfwfwfwfwffwf', 452, 'jeric'),
(7, 'Pang-guba', 'a mechanical device that increases the pressure of a gas by reducing its volume', 56221, 'rio');

-- --------------------------------------------------------

--
-- Table structure for table `servicerequests`
--

CREATE TABLE `servicerequests` (
  `SR_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Vehicle_ID` int(11) NOT NULL,
  `Description` text DEFAULT NULL,
  `AppointmentDate` date DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  `ServiceHistory` text DEFAULT NULL,
  `Part_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(11) NOT NULL,
  `UserName` varchar(40) DEFAULT NULL,
  `Password` varchar(40) DEFAULT NULL,
  `Role` varchar(40) DEFAULT NULL,
  `FullName` varchar(40) DEFAULT NULL,
  `PhoneNumber` decimal(10,0) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  `ServiceRecords` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `Vehicle_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Model` varchar(40) DEFAULT NULL,
  `PlateNumber` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`Vehicle_ID`, `Customer_ID`, `Model`, `PlateNumber`) VALUES
(2, 1, 'sports car', 'THQ243');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Customer_ID`);

--
-- Indexes for table `inventorypart`
--
ALTER TABLE `inventorypart`
  ADD PRIMARY KEY (`Part_ID`);

--
-- Indexes for table `servicerequests`
--
ALTER TABLE `servicerequests`
  ADD PRIMARY KEY (`SR_ID`),
  ADD KEY `Customer_ServiceRequests` (`Customer_ID`),
  ADD KEY `Vehicle_ServiceRequests` (`Vehicle_ID`),
  ADD KEY `InventoryPart_ServiceRequests` (`Part_ID`),
  ADD KEY `User_ServiceRequests` (`User_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_ID`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`Vehicle_ID`),
  ADD KEY `Customer_Vehicle` (`Customer_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `inventorypart`
--
ALTER TABLE `inventorypart`
  MODIFY `Part_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `servicerequests`
--
ALTER TABLE `servicerequests`
  MODIFY `SR_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `Vehicle_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `servicerequests`
--
ALTER TABLE `servicerequests`
  ADD CONSTRAINT `Customer_ServiceRequests` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`),
  ADD CONSTRAINT `InventoryPart_ServiceRequests` FOREIGN KEY (`Part_ID`) REFERENCES `inventorypart` (`Part_ID`),
  ADD CONSTRAINT `User_ServiceRequests` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `Vehicle_ServiceRequests` FOREIGN KEY (`Vehicle_ID`) REFERENCES `vehicle` (`Vehicle_ID`);

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `Customer_Vehicle` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
