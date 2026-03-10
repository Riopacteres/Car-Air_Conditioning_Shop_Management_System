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
$successMessage = "";

if (isset($_POST['submit'])) {
    $PartName = trim($_POST['PartName'] ?? "");
    $Description = trim($_POST['Description'] ?? "");
    $QuantityInStock = trim($_POST['QuantityInStock'] ?? "");
    $SupplierName = trim($_POST['SupplierName'] ?? "");

    if (empty($PartName) || empty($Description) || empty($QuantityInStock) || empty($SupplierName)) {
        $errorMessage = "All fields are required.";
    } elseif (!is_numeric($QuantityInStock) || $QuantityInStock < 0) {
        $errorMessage = "Quantity must be a valid positive number.";
    } else {
        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO inventorypart (PartName, Description, QuantityInStock, SupplierName) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $PartName, $Description, $QuantityInStock, $SupplierName);

        if ($stmt->execute()) {
            header("Location: inventorypart.php?success=Part%20added%20successfully");
            exit();
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inventory Part</title>
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
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #dde4f5;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0a43b4;
            box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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

        .success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
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
    <h1>Add Inventory Part</h1>
    <div class="header-actions">
      <a href="inventorypart.php" class="btn-back">← Back to Inventory</a>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="form-container">
      <h2>Add New Part</h2>
      <p>Enter inventory part details below.</p>

      <?php if (!empty($errorMessage)): ?>
        <div class="message error">✗ <?php echo htmlspecialchars($errorMessage); ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label for="PartName">Part Name *</label>
          <input type="text" id="PartName" name="PartName" placeholder="e.g., Compressor Belt" required>
        </div>

        <div class="form-group">
          <label for="Description">Description *</label>
          <textarea id="Description" name="Description" placeholder="Provide a detailed description of the part" required></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="QuantityInStock">Quantity in Stock *</label>
            <input type="number" id="QuantityInStock" name="QuantityInStock" placeholder="Enter quantity" min="0" required>
          </div>

          <div class="form-group">
            <label for="SupplierName">Supplier Name *</label>
            <input type="text" id="SupplierName" name="SupplierName" placeholder="Enter supplier name" required>
          </div>
        </div>

        <div class="button-group">
          <button type="submit" name="submit" class="submit-btn">💾 Save Part</button>
          <a href="inventorypart.php" class="cancel-btn" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">Cancel</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>
</body>
</html>