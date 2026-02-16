<?php
include('database.php'); 

$sql = "SELECT * FROM user";
$query = mysqli_query($conn, $sql);


if (mysqli_num_rows($query) > 0) {
    echo "<h1>user table </h1>";
    echo "<table border='1'>";
    echo "<tr><th>user ID</th><th>UserName</th><th>Password</th><th>Role</th><th>FullName</th><th>PhoneNumber</th><th>Email</th><th>ServiceRecords</th><th>Action</th></tr>";

    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>".$row["User_ID"]."</td>";
        echo "<td>".$row["UserName"]."</td>";
        echo "<td>".$row["Password"]."</td>";
        echo "<td>".$row["Role"]."</td>";
        echo "<td>".$row["FullName"]."</td>";   
        echo "<td>".$row["PhoneNumber"]."</td>";
        echo "<td>".$row["Email"]."</td>";
        echo "<td>".$row["ServiceRecords"]."</td>";
        echo "<td>
        <a href='edit_user.php?id=".$row['User_ID']."'>Edit</a> |
        <a href='delete_user.php?id=".$row['User_ID']."'
           onclick=\"return confirm('⚠ Are you sure you want to delete this User? This action cannot be undone!');\">
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
    $UserName = $_POST['UserName'];
    $Password  = $_POST['Password'];
    $Role  = $_POST['Role'];                
    $FullName = $_POST['FullName'];
    $PhoneNumber  = $_POST['PhoneNumber'];
    $Email  = $_POST['Email'];
    $ServiceRecords  = $_POST['ServiceRecords'];

    $sql = "INSERT INTO user (UserName, Password, Role, FullName, PhoneNumber, Email, ServiceRecords)
            VALUES ('$UserNname', '$Password', '$Role', '$FullName', '$PhoneNumber', '$Email', '$ServiceRecords')";
            
    
    if(mysqli_query($conn, $sql)){
        header("Location:user.php?status=success");
        exit();
        $msg = "✅ User added successfully!";
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