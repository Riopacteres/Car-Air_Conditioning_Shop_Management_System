<?php
include('database.php'); 

$sql = "SELECT * FROM servicerequests";
$query = mysqli_query($conn, $sql);


if (mysqli_num_rows($query) > 0) {
    echo "<h1>service requests table </h1>";
    echo "<table border='1'>";
    echo "<tr><th>SR ID</th><th>Customer ID</th><th>Vehicle ID</th><th>Descrition</th><th>Appointment Date</th><th>Status</th><th>Service History</th><th>Part ID</th><th>User ID</th><th>Action</th></tr>";

    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>".$row["SR_ID"]."</td>";
        echo "<td>".$row["Customer_ID"]."</td>";
        echo "<td>".$row["Vehicle_ID"]."</td>";
        echo "<td>".$row["Description"]."</td>";
        echo "<td>".$row["AppointmentDate"]."</td>";   
        echo "<td>".$row["Status"]."</td>";
        echo "<td>".$row["ServiceHistory"]."</td>";
        echo "<td>".$row["Part_ID"]."</td>";
        echo "<td>".$row["User_ID"]."</td>";
        echo "<td>
        <a href='edit_sr.php?id=".$row['SR_ID']."'>Edit</a> |
        <a href='delete_sr.php?id=".$row['SR_ID']."'
           onclick=\"return confirm('⚠ Are you sure you want to delete this Servicerequests? This action cannot be undone!');\">
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