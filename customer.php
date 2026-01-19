<?php
include('database.php'); 

$sql = "SELECT * FROM customer";
$query = mysqli_query($conn, $sql);


if (mysqli_num_rows($query) > 0) {
    echo "<h1>customer table </h1>";
    echo "<table border='1'>";
    echo "<tr><th>customer ID</th><th>FullName</th><th>PhoneNumber</th><th>Email</th><th>Address</th><th>Action</th></tr>";

    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>".$row["Customer_ID"]."</td>";
        echo "<td>".$row["FullName"]."</td>";
        echo "<td>".$row["PhoneNumber"]."</td>";
        echo "<td>".$row["Email"]."</td>";
        echo "<td>".$row["Address"]."</td>";
        echo "<td>
        <a href='edit_customer.php?id=".$row['Customer_ID']."'>Edit</a> |
        <a href='delete_customer.php?id=".$row['Customer_ID']."'
           onclick=\"return confirm('⚠ Are you sure you want to delete this Customer? This action cannot be undone!');\">
           Delete
        </a>
      </td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No results found.<br>";
}
?>
<?php

if(isset($_POST['submit'])){
    $FullName = $_POST['FullName'];
    $PhoneNumber  = $_POST['PhoneNumber'];
    $Email  = $_POST['Email'];
    $Address  = $_POST['Address'];

    $sql = "INSERT INTO customer (FullName, PhoneNumber, Email, Address)
            VALUES ('$FullName', '$PhoneNumber', '$Email', '$Address')";
            

    if(mysqli_query($conn, $sql)){
        header("Location:customer.php?status=success");
        exit();
        $msg = "✅ Customer added successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
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