<?php
session_start();

// Clear all session variables
unset($_SESSION["admin_logged_in"]);
unset($_SESSION["staff_logged_in"]);
unset($_SESSION["admin_name"]);
unset($_SESSION["staff_name"]);
unset($_SESSION["admin_email"]);
unset($_SESSION["staff_email"]);

// Destroy session
session_destroy();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login
header("Location: login.php");
exit();
?>
