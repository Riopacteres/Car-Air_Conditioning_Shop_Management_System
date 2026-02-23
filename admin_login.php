<?php
session_start();

$admin_username = "admin";
$admin_password = "admin123";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION["admin_logged_in"] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body {
      background-color: #f0f3ff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    header {
      background-color: #0a43b4ff;
      color: white;
      text-align: center;
      padding: 20px 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    header h1 { font-size: 28px; letter-spacing: 1px; }
    .login-wrapper {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }
    .login-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      padding: 40px 35px;
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    .login-card h2 {
      color: #0a43b4ff;
      margin-bottom: 8px;
      font-size: 24px;
    }
    .login-card .subtitle {
      color: #777;
      font-size: 14px;
      margin-bottom: 25px;
    }
    .form-group {
      margin-bottom: 18px;
      text-align: left;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #333;
      font-size: 14px;
    }
    .form-group input {
      width: 100%;
      padding: 11px 14px;
      border: 2px solid #dde1ea;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      transition: border-color 0.3s;
    }
    .form-group input:focus {
      border-color: #0a43b4ff;
    }
    .error-msg {
      background: #ffe0e0;
      color: #c0392b;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
      font-weight: 600;
    }
    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #0a43b4ff;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: background 0.3s;
    }
    .login-btn:hover { background-color: #052a78; }
    .back-link {
      display: inline-block;
      margin-top: 18px;
      color: #0a43b4ff;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
    }
    .back-link:hover { text-decoration: underline; }
    footer {
      background-color: #0a43b4ff;
      color: white;
      text-align: center;
      padding: 10px 0;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <header>
    <h1>Car Air Conditioning Shop Management System</h1>
  </header>

  <div class="login-wrapper">
    <div class="login-card">
      <h2>Admin Login</h2>
      <p class="subtitle">Enter your credentials to access the admin panel</p>

      <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="login-btn">Log In</button>
      </form>

      <a href="Dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>
</body>
</html>
