<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include('database.php');

$user_id = $_GET["id"] ?? null;
$error = "";
$success = "";
$user_data = null;

if (!$user_id) {
    header("Location: user.php");
    exit();
}

// Fetch user data
$fetch_sql = "SELECT * FROM user WHERE User_ID = ?";
$fetch_stmt = $conn->prepare($fetch_sql);
$fetch_stmt->bind_param("i", $user_id);
$fetch_stmt->execute();
$fetch_result = $fetch_stmt->get_result();

if ($fetch_result->num_rows > 0) {
    $user_data = $fetch_result->fetch_assoc();
} else {
    header("Location: user.php");
    exit();
}
$fetch_stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $role = trim($_POST["role"] ?? "");
    $new_password = trim($_POST["new_password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");

    if (empty($fullname) || empty($role)) {
        $error = "Full name and role are required.";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        // Update user information
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_sql = "UPDATE user SET FullName = ?, Email = ?, PhoneNumber = ?, Role = ?, Password = ? WHERE User_ID = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssssi", $fullname, $email, $phone, $role, $hashed_password, $user_id);
        } else {
            $update_sql = "UPDATE user SET FullName = ?, Email = ?, PhoneNumber = ?, Role = ? WHERE User_ID = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssi", $fullname, $email, $phone, $role, $user_id);
        }

        if ($update_stmt->execute()) {
            // Redirect to user view with success message
            header("Location: user.php?success=User%20updated%20successfully");
            exit();
        } else {
            $error = "Database error: " . $conn->error;
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link rel="icon" href="data:image/svg+xml,<svg></svg>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
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
      margin-bottom: 12px;
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

    .password-section {
      background: #f8f9ff;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      border-left: 4px solid #0a43b4;
    }

    .password-section h3 {
      color: #0a43b4;
      font-size: 16px;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .password-section p {
      color: #777;
      font-size: 13px;
      margin-bottom: 15px;
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
  </style>
</head>
<body>
  <header>
    <h1>Edit User Information</h1>
    <div class="header-actions">
      <a href="user.php" class="btn-back">← Back to Users</a>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="form-container">
      <h2>Update User Account</h2>
      <p>Edit staff member details and credentials.</p>

      <?php if ($success): ?>
        <div class="message success">✓ <?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="message error">✗ <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-row">
          <div class="form-group">
            <label for="fullname">Full Name *</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_data['FullName'] ?? ''); ?>" required>
          </div>
          <div class="form-group">
            <label for="role">Role *</label>
            <select id="role" name="role" required>
              <option value="">Select Role</option>
              <option value="Staff" <?php echo ($user_data['Role'] === 'Staff') ? 'selected' : ''; ?>>Staff</option>
              <option value="Admin" <?php echo ($user_data['Role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['Email'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['PhoneNumber'] ?? ''); ?>">
          </div>
        </div>

        <div class="password-section">
          <h3>Change Password</h3>
          <p>Leave blank if you don't want to change the password.</p>
          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password (minimum 6 characters)">
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
          </div>
        </div>

        <div class="button-group">
          <button type="submit" class="submit-btn">💾 Save Changes</button>
          <a href="user.php" class="cancel-btn" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">Cancel</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>
</body>
</html>
