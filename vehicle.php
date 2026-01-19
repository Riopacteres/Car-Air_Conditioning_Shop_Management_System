<?php
include('database.php'); 

$sql = "SELECT * FROM vehicle";
$query = mysqli_query($conn, $sql);


if (mysqli_num_rows($query) > 0) {
    echo "<h1>vehicle table </h1>";
    echo "<table border='1'>";
    echo "<tr><th>Vehicle ID</th><th>Customer ID</th><th>Model</th><th>Plate Number</th></tr>";

    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>".$row["Vehicle_ID"]."</td>";
        echo "<td>".$row["Customer_ID"]."</td>";
        echo "<td>".$row["Model"]."</td>";
        echo "<td>".$row["PlateNumber"]."</td>";
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