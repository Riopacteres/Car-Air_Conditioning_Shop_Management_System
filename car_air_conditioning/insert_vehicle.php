<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Allow both admin and staff access
$is_admin = isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;
$is_staff = isset($_SESSION["staff_logged_in"]) && $_SESSION["staff_logged_in"] === true;

if (!$is_admin && !$is_staff) {
    header("Location: login.php");
    exit();
}

$dashboard_link = $is_admin ? "admin_dashboard.php" : "Dashboard.php";

include("database.php");

$errorMessage = "";

if (isset($_POST['submit'])) {
    $Customer_ID = trim($_POST['Customer_ID'] ?? "");
    $Model = trim($_POST['Model'] ?? "");
    $PlateNumber = trim($_POST['PlateNumber'] ?? "");

    if (empty($Customer_ID) || empty($Model) || empty($PlateNumber)) {
        $errorMessage = "All fields are required.";
    } else {
        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO vehicle (Customer_ID, Model, PlateNumber) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $Customer_ID, $Model, $PlateNumber);

        if ($stmt->execute()) {
            header("Location: vehicle.php?success=Vehicle%20added%20successfully");
            exit();
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch customers for dropdown
$cust_sql = "SELECT Customer_ID, FullName FROM customer ORDER BY FullName ASC";
$cust_query = mysqli_query($conn, $cust_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Vehicle</title>
    <link rel="icon" href="data:image/svg+xml,<svg></svg>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
        }

        body {
            background-color: #f0f3ff;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ---- HEADER ---- */
        header {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            padding: 18px 30px;
            box-shadow: 0 3px 12px rgba(10, 67, 180, 0.35);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        header h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
            flex: 1;
            min-width: 250px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .header-actions a {
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .header-actions .btn-back:hover {
            background-color: rgba(255, 255, 255, 0.25);
        }

        .header-actions .btn-logout {
            background-color: #d11a2a;
            border-color: #d11a2a;
        }

        .header-actions .btn-logout:hover {
            background-color: #a31321;
            border-color: #a31321;
        }

        /* ---- MAIN ---- */
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .form-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
        }

        .form-container h2 {
            color: #0a43b4;
            font-size: 26px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .form-container p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #dde4f5;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0a43b4;
            box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .message {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .submit-btn,
        .cancel-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            flex: 1;
        }

        .submit-btn {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(10, 67, 180, 0.3);
        }

        .cancel-btn {
            background: #f0f0f0;
            color: #333;
            border: 1.5px solid #dde4f5;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cancel-btn:hover {
            background: #e8e8e8;
        }

        /* ---- FOOTER ---- */
        footer {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            text-align: center;
            padding: 14px 0;
            font-size: 13px;
        }

        @media (max-width: 720px) {
            header {
                flex-direction: column;
                gap: 12px;
            }

            header h1 {
                font-size: 18px;
            }

            main {
                padding: 20px 15px;
            }

            .form-container {
                padding: 30px 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
  <header>
    <h1>Add New Vehicle</h1>
    <div class="header-actions">
      <a href="vehicle.php" class="btn-back">← Back to Vehicles</a>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="form-container">
      <h2>Vehicle Information</h2>
      <p>Enter vehicle details to add a new vehicle to the system.</p>

      <?php if (!empty($errorMessage)): ?>
        <div class="message error">✗ <?php echo htmlspecialchars($errorMessage); ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label for="Customer_ID">Customer Name *</label>
          <select id="Customer_ID" name="Customer_ID" required>
            <option value="">-- Select a Customer --</option>
            <?php 
            if ($cust_query && mysqli_num_rows($cust_query) > 0) {
                while ($cust = mysqli_fetch_assoc($cust_query)): 
            ?>
              <option value="<?php echo htmlspecialchars($cust['Customer_ID']); ?>">
                <?php echo htmlspecialchars($cust['FullName']); ?>
              </option>
            <?php 
                endwhile; 
            } 
            ?>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="Model">Car Model *</label>
            <input type="text" id="Model" name="Model" placeholder="e.g., Toyota Camry" required>
          </div>

          <div class="form-group">
            <label for="PlateNumber">License Plate *</label>
            <input type="text" id="PlateNumber" name="PlateNumber" placeholder="e.g., ABC-1234" required>
          </div>
        </div>

        <div class="button-group">
          <button type="submit" name="submit" class="submit-btn">💾 Save Vehicle</button>
          <a href="vehicle.php" class="cancel-btn">Cancel</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>
</body>
</html>
