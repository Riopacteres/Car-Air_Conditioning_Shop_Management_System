<?php include("database.php"); ?>
<div class="form-wrapper">
    <h2>Inventorypart Details</h2>
    
      <form method="post" action="">
        <div class="input-field">
    <label>Part Name</label>
       <input type="text" name="PartName" placeholder="Enter the part name" required><br><br>
    </div>
      <div class="input-field">
    <label>Description</label>
     <input type="text" name="Description" placeholder="Provide a brief description"required><br><br>
    </div>
      <div class="input-field">
    <label>Quantity In Stock</label>
    <input type="number" name="QuantityInStock" placeholder="Enter stock quantity"required><br><br>
    </div>
      <div class="input-field">
    <label>Supplier Name</label>
    <input type="text" name="SupplierName" placeholder="Enter the supplier name"required><br><br>
    </div>
      <div class="input-field">
    <input type="submit" name="submit" value="Save">
</form>
<?php

if(isset($_POST['submit'])){
    $PartName = $_POST['PartName'];
    $Description     = $_POST['Description'];
    $QuantityInStock    = $_POST['QuantityInStock'];
    $SupplierName  = $_POST['SupplierName'];

    $sql = "INSERT INTO inventorypart (PartName, Description, QuantityInStock, SupplierName)
            VALUES ('$PartName', '$Description', '$QuantityInStock', '$SupplierName')";

    if(mysqli_query($conn, $sql)){
        $msg = "✅ Inventorypart added successfully!";
        header("Location:inventorypart.php?status=success");
        exit();
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<?php
if(isset($msg)){
    echo "<p>$msg</p>";
}
?>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    
  }
  
  body {
    background-color: rgb(242, 244, 247);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-weight: 1000;
  font-size: 18px;

  }

  .form-wrapper {
   background-color: #eeeef0ff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
  }

  h2 {
    font-size: 22px;
    color: #0c46c4ff;
    margin-bottom: 30px;
    border-bottom: 3px solid #1196d3ff;
    padding-bottom: 50px;
    letter-spacing: 0.5px;
  }

  .input-field {
   display: flex;
    align-items: center;
    background: #e0e4ebff;
    padding: 10px 15px;
    border-radius: 25px;
}

.input-field label {
  display: flex;
    align-items: center;
    background: #f8fafcff;
    padding: 10px 15px;
    border-radius: 25px;
   
}

.input-field input {
  background: white;
    border: none;
    color: #2051bbff;
    outline: none;
    margin-left: 8px;   
    width: 80%;
    font-size: 16px;
    padding: 10px;
    border-radius: 25px;
    background: #f2f2f8ff;
   
} 
input[type="submit"] {
    width: 10%;
    background-color: #0a43b4ff;
    color: white;
    padding: 12px;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s ease;
  }

  .input-field.success input {
    border-color: #4CAF50;
  }
  .input-field.success small {
    color: #4CAF50;
  }

  .input-field.error input {
    border-color: #f44336;
  }
  .input-field.error small {
    color: #f44336;
  }

  .input-field input:disabled {
    background-color: #f1f1f1;
    border-color: #ddd;
    cursor: not-allowed;
  }
  button {
    width: 30%;
    background-color: #3f51b5;
    color: white;
    padding: 12px;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s ease;
  }

  button:hover {
    background-color: #2e3c9a;
  }

</style>