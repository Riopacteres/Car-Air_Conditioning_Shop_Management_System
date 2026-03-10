<?php
$servername = "localhost";
$username = "root";
$password = "";
$database_name = "car";

// First, connect without specifying database
$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$create_db = "CREATE DATABASE IF NOT EXISTS `$database_name`";
mysqli_query($conn, $create_db);

// Now select the database
mysqli_select_db($conn, $database_name);

if (!$conn) {
    die("Database selection failed: " . mysqli_error($conn));
}

// Create tables if they don't exist
$create_customer = "CREATE TABLE IF NOT EXISTS `customer` (
  `Customer_ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `FullName` varchar(40) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  `Address` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$create_inventorypart = "CREATE TABLE IF NOT EXISTS `inventorypart` (
  `Part_ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `PartName` varchar(40) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `QuantityInStock` int(11) DEFAULT NULL,
  `SupplierName` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$create_user = "CREATE TABLE IF NOT EXISTS `user` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `UserName` varchar(40) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Role` varchar(40) DEFAULT NULL,
  `FullName` varchar(40) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(40) DEFAULT NULL,
  `ServiceRecords` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$create_vehicle = "CREATE TABLE IF NOT EXISTS `vehicle` (
  `Vehicle_ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Customer_ID` int(11) NOT NULL,
  `Model` varchar(40) DEFAULT NULL,
  `PlateNumber` varchar(40) DEFAULT NULL,
  FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$create_servicerequests = "CREATE TABLE IF NOT EXISTS `servicerequests` (
  `SR_ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Customer_ID` int(11) NOT NULL,
  `Vehicle_ID` int(11) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `AppointmentDate` date DEFAULT NULL,
  `Status` varchar(40) DEFAULT NULL,
  `ServiceHistory` varchar(100) DEFAULT NULL,
  `Part_ID` int(11),
  `User_ID` int(11),
  FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`),
  FOREIGN KEY (`Vehicle_ID`) REFERENCES `vehicle` (`Vehicle_ID`),
  FOREIGN KEY (`Part_ID`) REFERENCES `inventorypart` (`Part_ID`),
  FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Execute table creation queries
mysqli_query($conn, $create_customer);
mysqli_query($conn, $create_inventorypart);
mysqli_query($conn, $create_user);
mysqli_query($conn, $create_vehicle);
mysqli_query($conn, $create_servicerequests);

?>