<?php
session_start();

// Check if admin is logged in
if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) {
    // Admin logged in - go to admin dashboard
    header("Location: admin_dashboard.php");
    exit();
}
// Check if staff is logged in
elseif (isset($_SESSION["staff_logged_in"]) && $_SESSION["staff_logged_in"] === true) {
    // Staff logged in - go to staff dashboard
    header("Location: Dashboard.php");
    exit();
} else {
    // Not logged in - redirect to unified login
    header("Location: login.php");
    exit();
}
?>
