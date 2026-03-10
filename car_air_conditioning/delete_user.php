<?php
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include('database.php');

$user_id = $_GET["id"] ?? null;

if (!$user_id) {
    header("Location: user.php");
    exit();
}

// Verify user exists before deleting
$verify_sql = "SELECT User_ID, FullName, UserName FROM user WHERE User_ID = ?";
$verify_stmt = $conn->prepare($verify_sql);
$verify_stmt->bind_param("i", $user_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows === 0) {
    // User not found
    header("Location: user.php");
    exit();
}

$user_data = $verify_result->fetch_assoc();
$verify_stmt->close();

// Delete the user
$delete_sql = "DELETE FROM user WHERE User_ID = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $user_id);

if ($delete_stmt->execute()) {
    // Deletion successful
    header("Location: user.php?success=User%20deleted%20successfully");
    exit();
} else {
    // Deletion failed
    header("Location: user.php?error=Failed%20to%20delete%20user");
    exit();
}

$delete_stmt->close();
?>
