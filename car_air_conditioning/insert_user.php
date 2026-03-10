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

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");
    $role = trim($_POST["role"] ?? "");
    $fullname = trim($_POST["fullname"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if (empty($username) || empty($password) || empty($confirm_password) || empty($role) || empty($fullname)) {
        $error_msg = "Username, password, role, and full name are required.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } else {
        // Check if username already exists
        $check_sql = "SELECT User_ID FROM user WHERE UserName = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_msg = "Username already exists.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $insert_sql = "INSERT INTO user (UserName, Password, Role, FullName, PhoneNumber, Email) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssssss", $username, $hashed_password, $role, $fullname, $phone, $email);

            if ($insert_stmt->execute()) {
                // Redirect to user view with success message
                header("Location: user.php?success=User%20added%20successfully");
                exit();
            } else {
                $error_msg = "Database error: " . $conn->error;
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New User</title>
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
      max-width: 500px;
    }

    .form-container h2 {
      color: #0a43b4;
      font-size: 26px;
      margin-bottom: 8px;
      text-align: center;
    }

    .form-container .subtitle {
      color: #777;
      text-align: center;
      margin-bottom: 25px;
      font-size: 14px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: #333;
      font-size: 14px;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px 14px;
      border: 2px solid #dde1ea;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      transition: border-color 0.3s;
      font-family: 'Poppins', sans-serif;
    }

    .form-group input:focus,
    .form-group select:focus {
      border-color: #0a43b4;
    }

    .form-group select {
      cursor: pointer;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .form-row .form-group {
      margin-bottom: 0;
    }

    .success-msg {
      background: #d4edda;
      color: #155724;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
      font-weight: 600;
      border: 1px solid #c3e6cb;
    }

    .error-msg {
      background: #ffe0e0;
      color: #c0392b;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
      font-weight: 600;
      border: 1px solid #ffcdd2;
    }

    .submit-btn {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #0a43b4, #1560e8);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(10, 67, 180, 0.25);
    }

    .submit-btn:hover {
      background: linear-gradient(135deg, #052a78, #0a43b4);
      box-shadow: 0 4px 14px rgba(10, 67, 180, 0.35);
      transform: translateY(-2px);
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      color: #0a43b4;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      text-align: center;
      width: 100%;
    }

    .back-link:hover {
      text-decoration: underline;
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

      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Add New User</h1>
    <div class="header-actions">
      <a href="admin_dashboard.php" class="btn-back">← Dashboard</a>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="form-container">
      <h2>Create User Account</h2>
      <p class="subtitle">Add a new staff member to the system</p>

      <?php if ($success_msg): ?>
        <div class="success-msg"><?php echo $success_msg; ?></div>
      <?php endif; ?>

      <?php if ($error_msg): ?>
        <div class="error-msg"><?php echo $error_msg; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="fullname">Full Name *</label>
          <input type="text" id="fullname" name="fullname" autocomplete="name" placeholder="Enter full name" required>
        </div>

        <div class="form-group">
          <label for="username">Username *</label>
          <input type="text" id="username" name="username" autocomplete="username" placeholder="Enter username" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" autocomplete="new-password" placeholder="At least 6 characters" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" placeholder="Re-enter password" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="role">Role *</label>
            <select id="role" name="role" required>
              <option value="">-- Select Role --</option>
              <option value="Staff">Staff</option>
              <option value="Manager">Manager</option>
              <option value="Technician">Technician</option>
            </select>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" autocomplete="email" placeholder="Enter email">
          </div>
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone" autocomplete="tel" placeholder="Enter phone number">
        </div>

        <button type="submit" class="submit-btn">Add User</button>
      </form>

      <a href="user.php" class="back-link">View All Users</a>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>
</body>
</html>
