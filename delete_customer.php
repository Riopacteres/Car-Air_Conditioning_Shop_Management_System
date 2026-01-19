<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarian delete</title>
</head>
<body>
    <h2>✅Customer deleted successfully.</h2>
</body>
</html>
<?php
include('database.php');

if (isset($_GET['id'])) {
    $Customer_ID = $_GET['id'];
    mysqli_query($conn, "DELETE FROM Customer WHERE Customer_ID = $Customer_ID");

}
?>
<br>
<div style="text-align: center; margin-top: 20px;">
    <button 
        onclick="window.history.back()" 
        style="padding: 10px 20px; 
               background-color: #0a43b4ff; 
               color: white; 
               border: none; 
               border-radius: 5px; 
               cursor: pointer;">
        ⬅ Go Back
    </button>
</div>