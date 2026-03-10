<?php
session_start();

// Destroy session
session_destroy();

// Redirect to staff login
header("Location: staff_login.php");
exit();
?>
