<?php include("database.php"); 

    $FullName = "";
    $PhoneNumber  = "";
    $Email  = "";
    $Address  = ;

$errorMessage = "";
$successMessage = "";

// ✅ GET Customer ID
if (!isset($_GET['ID'])) {
    header("location: edit_customer.php");
    exit;
}

$Customer_ID = $_GET['ID'];

// ✅ FETCH existing customer data
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM customer WHERE  = $Customer_ID";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    if (!$row) {
        header("location: edit_customer.php");
        exit;
    }

    $FullName = $_POST['FullName'];
    $PhoneNumber  = $_POST['PhoneNumber'];
    $Email  = $_POST['Email'];
    $Address  = $_POST['Address'];
}

// ✅ UPDATE veterinarian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FullName = $_POST['FullName'];
    $PhoneNumber  = $_POST['PhoneNumber'];
    $Email  = $_POST['Email'];
    $Address  = $_POST['Address'];

    if (empty($FullName) || empty($PhoneNumber) || empty($email) || empty($phonenumber) || empty($licensenumber)) {
        $errorMessage = "All fields are required";
    } else {
        $sql = "UPDATE veterinarian SET
                first_name='$firstname',
                last_name='$lastname',
                email='$email',
                phone_number='$phonenumber',
                license_number='$licensenumber'
                WHERE vet_id=$vet_id";

        if ($connection->query($sql)) {
            header("location: edit_veterinarian.php");
            exit;
        } else {
            $errorMessage = "Error: " . $connection->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Customer</h2>

    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-warning"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" class="form-control" name="first_name" value="<?= $firstname ?>" required>
        </div>

        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" class="form-control" name="last_name" value="<?= $lastname ?>" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="<?= $email ?>" required>
        </div>

        <div class="mb-3">
            <label>Phone Number</label>
            <input type="text" class="form-control" name="phone_number" value="<?= $phonenumber ?>" required>
        </div>

        <div class="mb-3">
            <label>License Number</label>
            <input type="text" class="form-control" name="license_number" value="<?= $licensenumber ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="Edit_Delete.php" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
<div style="text-align: center; margin-top: 20px;">
    <button 
        onclick="window.history.back()" 
        style="padding: 10px 20px; 
               background-color: #156335ff; 
               color: white; 
               border: none; 
               border-radius: 5px; 
               cursor: pointer;">
        ⬅ Go Back
    </button>
</div>
</body>
</html>