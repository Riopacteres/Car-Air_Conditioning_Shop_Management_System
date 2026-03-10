<?php
session_start();

// Check if admin already exists
include('database.php');

// Check if admin_users table exists, if not create it
$table_check = "CREATE TABLE IF NOT EXISTS admin_users (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $table_check);

// Check if any admin exists
$admin_check = "SELECT COUNT(*) as count FROM admin_users";
$result = mysqli_query($conn, $admin_check);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    // Admin already exists, redirect to login
    header("Location: admin_login.php");
    exit();
}

$error = "";
$success = "";

// Handle registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $password_confirm = trim($_POST["password_confirm"] ?? "");
    $email = trim($_POST["email"] ?? "");

    // Validation
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error = "Username and password are required.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert admin
        $sql = "INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                $success = "✓ Admin account created successfully! Redirecting to login...";
                $_SESSION["admin_logged_in"] = true;
                $_SESSION["admin_username"] = $username;
                header("refresh:2;url=admin_login.php");
            } else {
                $error = "Error creating admin account: " . $stmt->error;
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
    <title>Create Admin Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container h1 {
            text-align: center;
            color: #0a43b4;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #dde4f5;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0a43b4;
            box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(10, 67, 180, 0.2);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #052a78, #0a43b4);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(10, 67, 180, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            animation: slideDown 0.3s ease-out;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .password-rules {
            background: #f0f3ff;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            margin-top: 15px;
            border-left: 3px solid #0a43b4;
        }

        .password-rules ul {
            margin: 5px 0 0 18px;
            padding: 0;
        }

        .password-rules li {
            margin: 3px 0;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #999;
            font-size: 13px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-container h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 Create Admin Account</h1>
        <p class="subtitle">Set up your first administrator account</p>

        <?php if (!empty($error)): ?>
            <div class="error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Administrator Username *</label>
                <input type="text" id="username" name="username" autocomplete="username" placeholder="Enter admin username" required minlength="3">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" autocomplete="email" placeholder="Enter admin email (optional)">
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" autocomplete="new-password" placeholder="Enter a strong password" required minlength="6">
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm Password *</label>
                <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" placeholder="Re-enter your password" required minlength="6">
            </div>

            <div class="password-rules">
                <strong>Password Requirements:</strong>
                <ul>
                    <li>✓ At least 6 characters</li>
                    <li>✓ Combination of uppercase, lowercase, numbers for strength</li>
                </ul>
            </div>

            <button type="submit" class="submit-btn">✓ Create Admin Account</button>
        </form>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
        </footer>
    </div>
</body>
</html>
