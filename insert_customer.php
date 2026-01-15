<?php include("database.php"); ?>

<div class="form-wrapper">
    <h2>Customer Info</h2>
    <form method="post" action="">
      <div class="input-field">
    Full Name <input type="text" name="FullName" id="FullName" placeholder="Enter your full name" required>
</div>
      <div class="input-field">
    Phone Number <input type="number" name="PhoneNumber" id="PhoneNumber" placeholder="Enter your phone number" required>
</div>
      <div class="input-field">
    Email <input type="text" name="Email" id="Email" placeholder="Enter your email" required>
    </div>
      <div class="input-field">
    Address<input type="text" name="Address" id="Address" placeholder="Enter your address" required>
</div>
      <div class="input-field">
    <input type="submit" name="submit" value="Save">
</form>
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
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    
  }
  
  body {
    background-color: #0a43b4ff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-weight: 1000;
  font-size: 18px;

  }

  .form-wrapper {
   background-color: #eeeef0ff;
    padding: 3px;
    border-radius: 16px;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    height: 60%;
  }

  h2 {
    font-size: 22px;
    color: #0c46c4ff;
    margin-bottom: 30px;
    border-bottom: 3px solid #1196d3ff;
    padding-bottom: 1px;
    letter-spacing: 0.5px;
  }

  .input-field {
   display: flex;
    align-items: center;
    background: #e0e4ebff;
    padding: 1px 1px;
    border-radius: 25px;
    
}

.input-field label {
  display: flex;
    align-items: center;
    background: #f8fafcff;
    padding: 1px 1px;
    border-radius: 25px;
   
}

.input-field input {
  background: white;
    border: none;
    color: #2051bbff;
    outline: none;
    margin: 8px;   
    width: 80%;
    font-size: 16px;
    padding: 1px;
    border-radius: 25px;
    background: #f2f2f8ff;
} 
input[type="submit"] {
    width: 10%;
    background-color: #0a43b4ff;
    color: white;
    padding: 1px;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
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