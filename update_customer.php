<?php
    include("database.php");

    if(isset($_GET['Customer_ID'])) {
        $customer_ID = $_GET['Customer_ID'];
    }

    $sql = "SELECT * FROM customer WHERE Customer_ID = '$customer_ID'";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($query);
?>

 <form method="post" action="">
      <div class="input-field">
    FullName: <input type="text" name="FullName" id="FullName" placeholder="Enter your full name" required>
</div>
      <div class="input-field">
    PhoneNumber: <input type="number" name="PhoneNumber" id="PhoneNumber" placeholder="Enter your phone number" required>
</div>
      <div class="input-field">
    Email: <input type="text" name="Email" id="Email" placeholder="Enter your email" required>
    </div>
      <div class="input-field">
    Address:<input type="text" name="Address" id="Address" placeholder="Enter your address" required>
</div>
      <div class="input-field">
    <input type="submit" name="submit" value="Save">
</form>

<?php

if(isset($_POST['submit'])){
    $Fullname       = $_POST['FullName'];
    $Phonenumber    = $_POST['PhoneNumber'];
    $Email          = $_POST['Email'];
    $Address        = $_POST['Address'];

    $sql = "UPDATE customer SET Fullname='$Fullname', Phonenumber='$Phonenumber', Email='$Email', Address='$Address' WHERE Customer_ID = $Customer_ID";

    if(mysqli_query($conn, $sql)){
        // $msg = "✅ Student added successfully!";
        echo "<script>alert('Customer updated successfully'); window.location='customer.php';</script>";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>





<?php
// if(isset($msg)){
//     echo "<p>$msg</p>";
// }
?>


