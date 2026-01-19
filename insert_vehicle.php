<?php include("database.php"); ?>

<div class="form-wrapper">
    <h2>Vehicles Info</h2>
    <form method="post" action="">
      <div class="input-field">
    <label>Model:</label> <input type="text" name="Model" id="Model" placeholder="Enter your Model Car" required>
</div>
      <div class="input-field">
    <label>Plate Number:</label> <input type="string" name="PlateNumber" id="PlateNumber" placeholder="Enter your Plate number of your Car" required>
</div>
      <div class="input-field">
    <input type="submit" name="submit" value="Save">
</form>
<?php

if(isset($_POST['submit'])){
    $Model = $_POST['Model'];
    $PlateNumber  = $_POST['PlateNumber'];
   

    $sql = "INSERT INTO vehicle (Model, PlateNumber)
            VALUES ('$Model', '$PlateNumber')";
            

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
/* 1. CSS VARIABLES - Change colors easily here */
:root {
    --primary-color: #0a43b4;
    --primary-hover: #edeff2;
    --primary-light: #e5e9f5;
    --bg-gradient: linear-gradient(135deg, #fefefe);
    --text-dark: #333333;
    --text-label: #555555;
    --border-color: #d0d6e2;
    --white: #ffffff;
    --shadow: 0 10px 30px rgba(0,0,0,0.15); /* Softer, modern shadow */
}

* {
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    margin: 0;
    min-height: 100px;
    background: var(--bg-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px; /* Prevents card from touching edges on mobile */
}

/* Card */
.form-wrapper {
    background: var(--white);
    width: 60%;
    min-width: 300px;
    padding: 40px;
    border-radius: 20px; /* Slightly more rounded */
    box-shadow: var(--shadow);
    /* Animation: Slides up slightly when page loads */
    animation: slideUp 0.5s ease-out;
}

/* Title */
.form-wrapper h2 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 30px;
    font-weight: 700;
    letter-spacing: -0.5px;
}

/* Input Group */
.input-field {
    margin-bottom: 20px;
    position: relative;
}

.input-field label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    color: var(--text-label);
    font-weight: 500;
}

.input-field input {
    width: 100%;
    padding: 14px 16px; /* Larger touch target */
    border-radius: 10px;
    border: 1.5px solid var(--border-color); /* Slightly thicker border */
    font-size: 15px;
    color: var(--text-dark);
    outline: none;
    transition: all 0.3s ease;
    background: #f8f9fa; /* Slight grey background looks more modern */
}

.input-field input:focus {
    border-color: var(--primary-color);
    background: var(--white);
    box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1); /* Soft glow ring */
}

/* Submit Button */
input[type="submit"] {
    width: 100%;
    margin-top: 15px;
    padding: 16px;
    background: var(--primary-color);
    color: var(--white);
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(10, 67, 180, 0.2);
}

input[type="submit"]:hover {
    background: var(--primary-hover);
    transform: translateY(-2px); /* Moves up slightly */
    box-shadow: 0 6px 12px rgba(10, 67, 180, 0.3);
}

input[type="submit"]:active {
    transform: translateY(0); /* Moves back down on click */
}

/* Back Button */
.back-btn {
    margin-top: 20px;
    width: 100%;
    padding: 14px;
    background: var(--primary-light);
    color: var(--primary-color);
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: #dce3f5;
    transform: translateY(-2px);
}

 label{
    font-weight: 600;
    letter-spacing: -0.3px;
    text-align: left;
  }

/* Animation Keyframes */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile Adjustments */
@media (max-width: 480px) {
    .form-wrapper {
        padding: 25px;
    }
    
    .form-wrapper h2 {
        font-size: 22px;
    }
}
</style>
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