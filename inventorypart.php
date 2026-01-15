<?php
include('database.php'); 

$sql = "SELECT * FROM inventorypart";
$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    echo "<h1>inventorypart table </h1>";
    echo "<table border='1'>";
    echo "<tr><th>Part ID</th><th>PartName</th><th>Description</th><th>QuantityInStock</th><th>SupplierName</th></tr>";

    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>".$row["Part_ID"]."</td>";
        echo "<td>".$row["PartName"]."</td>";
        echo "<td>".$row["Description"]."</td>";
        echo "<td>".$row["QuantityInStock"]."</td>";
        echo "<td>".$row["SupplierName"]."</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No results found.<br>";
}
?>
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