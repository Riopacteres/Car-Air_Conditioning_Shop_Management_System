<?php
session_start();

include('database.php');

// Check if already logged in
if ((isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) ||
    (isset($_SESSION["staff_logged_in"]) && $_SESSION["staff_logged_in"] === true)) {
    // Redirect based on role
    if (isset($_SESSION["admin_logged_in"])) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: Dashboard.php");
    }
    exit();
}

// Check if any admin exists
$admin_check_sql = "SELECT COUNT(*) as admin_count FROM user WHERE Role = 'Admin'";
$admin_check_result = mysqli_query($conn, $admin_check_sql);
$admin_check_row = mysqli_fetch_assoc($admin_check_result);
$has_admin = $admin_check_row['admin_count'] > 0;

$error = "";
$success = "";
$is_registration = !$has_admin; // Show registration form if no admin exists

// Handle registration (only if no admin exists)
if ($is_registration && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $fullname = trim($_POST["fullname"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $password_confirm = trim($_POST["password_confirm"] ?? "");

    if (empty($fullname) || empty($username) || empty($password) || empty($password_confirm)) {
        $error = "All fields are required.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long.";
    } else {
        // Check if username already exists
        $check_sql = "SELECT User_ID FROM user WHERE UserName = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // Create admin account
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = "Admin";

            $sql = "INSERT INTO user (FullName, UserName, Email, Password, Role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $fullname, $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "✓ Admin account created successfully! Please log in.";
                $is_registration = false; // Show login form now
            } else {
                $error = "Error creating admin account: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// Handle login
if (!$is_registration && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        // Check credentials in database
        $sql = "SELECT * FROM user WHERE UserName = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $user['Password'])) {
                    // Login successful - determine role
                    if ($user['Role'] === 'Admin') {
                        $_SESSION["admin_logged_in"] = true;
                        $_SESSION["admin_username"] = $user['UserName'];
                        $_SESSION["admin_id"] = $user['User_ID'];
                        header("Location: admin_dashboard.php");
                    } else {
                        $_SESSION["staff_logged_in"] = true;
                        $_SESSION["staff_username"] = $user['UserName'];
                        $_SESSION["staff_id"] = $user['User_ID'];
                        $_SESSION["staff_fullname"] = $user['FullName'];
                        $_SESSION["staff_role"] = $user['Role'];
                        header("Location: Dashboard.php");
                    }
                    exit();
                } else {
                    $error = "Incorrect password. Please try again.";
                }
            } else {
                $error = "Username not found in the system.";
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Login - Car Management System</title>
  <link rel="icon" href="data:image/svg+xml,<svg></svg>">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; }
    
    body {
      background: linear-gradient(135deg, #0a43b4 0%, #1560e8 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background-color: rgba(0, 0, 0, 0.1);
      color: white;
      text-align: center;
      padding: 30px 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    header h1 { 
      font-size: 32px; 
      letter-spacing: 1px; 
      font-weight: 700;
    }

    header p {
      font-size: 14px;
      margin-top: 8px;
      opacity: 0.9;
    }

    .login-wrapper {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .login-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      padding: 50px 40px;
      width: 100%;
      max-width: 450px;
    }

    .login-card h2 {
      color: #0a43b4;
      font-size: 26px;
      margin-bottom: 8px;
      text-align: center;
      font-weight: 700;
    }

    .login-card .subtitle {
      color: #666;
      text-align: center;
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

    .form-group input {
      width: 100%;
      padding: 12px 14px;
      border: 2px solid #dde4f5;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: #0a43b4;
      box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
    }

    .error-msg {
      background: #ffebee;
      color: #c62828;
      padding: 14px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
      border-left: 4px solid #f44336;
      font-weight: 500;
    }

    .info-box {
      background: #e3f2fd;
      border-left: 4px solid #0a43b4;
      padding: 14px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 13px;
      color: #0d47a1;
      font-weight: 500;
    }

    .submit-btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #0a43b4, #1560e8);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 20px rgba(10, 67, 180, 0.4);
    }

    footer {
      background: rgba(0, 0, 0, 0.1);
      color: white;
      text-align: center;
      padding: 20px;
      font-size: 13px;
    }

    @media (max-width: 600px) {
      header h1 {
        font-size: 24px;
      }

      .login-card {
        padding: 30px 20px;
      }

      .login-card h2 {
        font-size: 22px;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>🚗 Car Air Conditioning Shop Management System</h1>
    <p>System Login Portal</p>
  </header>

  <div class="login-wrapper">
    <div class="login-card">
      <?php if ($is_registration): ?>
        <h2>Create Admin Account</h2>
        <p class="subtitle">Set up the first admin account for the system</p>

        <div class="info-box">
          ℹ️ This is the initial setup. Create your admin account to access the system.
        </div>

        <?php if (!empty($error)): ?>
          <div class="error-msg">✗ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label for="fullname">Full Name *</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required autofocus>
          </div>
          <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" placeholder="Enter your username (min 3 characters)" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email (optional)">
          </div>
          <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" placeholder="Enter password (min 6 characters)" required>
          </div>
          <div class="form-group">
            <label for="password_confirm">Confirm Password *</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Re-enter password" required>
          </div>
          <button type="submit" name="register" class="submit-btn">✓ Create Admin Account</button>
        </form>
      <?php else: ?>
        <h2>System Login</h2>
        <p class="subtitle">Enter your credentials to access the system</p>

        <div class="info-box">
          ℹ️ Your role will determine your access level upon login.
        </div>

        <?php if (!empty($success)): ?>
          <div style="background: #e8f5e9; color: #2e7d32; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border-left: 4px solid #4caf50; font-weight: 500;">
            <?php echo htmlspecialchars($success); ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
          <div class="error-msg">✗ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>
          <button type="submit" name="login" class="submit-btn">🔓 Login to System</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System. All rights reserved.</p>
  </footer>

</body>
</html>
