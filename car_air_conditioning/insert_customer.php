<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Allow both admin and staff access
$is_admin = isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;
$is_staff = isset($_SESSION["staff_logged_in"]) && $_SESSION["staff_logged_in"] === true;

if (!$is_admin && !$is_staff) {
    header("Location: admin_login.php");
    exit();
}

include("database.php");

$errorMessage = "";
$successMessage = "";

if (isset($_POST['submit'])) {
    $FullName = trim($_POST['FullName'] ?? "");
    $PhoneNumber = trim($_POST['PhoneNumber'] ?? "");
    $Email = trim($_POST['Email'] ?? "");
    $Address = trim($_POST['Address'] ?? "");

    if (empty($FullName) || empty($PhoneNumber) || empty($Email) || empty($Address)) {
        $errorMessage = "All fields are required";
    } else {
        $sql = "INSERT INTO customer (FullName, PhoneNumber, Email, Address) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $FullName, $PhoneNumber, $Email, $Address);

        if ($stmt->execute()) {
            header("Location: customer.php?status=success");
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
    <title>Add New Customer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0a43b4;
            --primary-hover: #edeff2;
            --primary-light: #e5e9f5;
            --bg-gradient: linear-gradient(135deg, #f0f3ff, #ffffff);
            --text-dark: #333333;
            --text-label: #555555;
            --border-color: #d0d6e2;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.15);
            --error-color: #f8d7da;
        }

        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg-gradient);
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            padding: 20px 30px;
            text-align: center;
            box-shadow: 0 3px 12px rgba(10, 67, 180, 0.35);
        }

        header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-wrapper {
            background: var(--white);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            border: 1px solid #e8ecf5;
            animation: slideUp 0.5s ease-out;
        }

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

        .form-wrapper h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 22px;
        }

        .input-field {
            margin-bottom: 22px;
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
            padding: 14px 16px;
            border-radius: 10px;
            border: 1.5px solid var(--border-color);
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            outline: none;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-field input:focus {
            border-color: var(--primary-color);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
        }

        .form-buttons {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        input[type="submit"],
        .btn {
            flex: 1;
            padding: 16px 20px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        input[type="submit"] {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(10, 67, 180, 0.2);
        }

        input[type="submit"]:hover {
            background: linear-gradient(135deg, #052a78, #0a43b4);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(10, 67, 180, 0.3);
        }

        input[type="submit"]:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background: var(--primary-light);
            color: var(--primary-color);
            border: 1.5px solid var(--border-color);
        }

        .btn-cancel:hover {
            background: #e0e6ff;
            border-color: var(--primary-color);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
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

        .alert-error {
            background: var(--error-color);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        footer {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            text-align: center;
            padding: 16px 0;
            font-size: 13px;
            box-shadow: 0 -3px 12px rgba(10, 67, 180, 0.15);
        }

        @media (max-width: 720px) {
            header h1 {
                font-size: 20px;
            }

            main {
                padding: 20px 15px;
            }

            .form-wrapper {
                padding: 30px 20px;
            }

            .form-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Add New Customer</h1>
    </header>

    <main>
        <div class="form-wrapper">
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <h2>Customer Information</h2>

            <form method="POST" action="">
                <div class="input-field">
                    <label for="FullName">Full Name *</label>
                    <input type="text" id="FullName" name="FullName" autocomplete="name" placeholder="Enter full name" required>
                </div>

                <div class="input-field">
                    <label for="PhoneNumber">Phone Number *</label>
                    <input type="tel" id="PhoneNumber" name="PhoneNumber" autocomplete="tel" placeholder="Enter phone number" required>
                </div>

                <div class="input-field">
                    <label for="Email">Email *</label>
                    <input type="email" id="Email" name="Email" autocomplete="email" placeholder="Enter email address" required>
                </div>

                <div class="input-field">
                    <label for="Address">Address *</label>
                    <input type="text" id="Address" name="Address" autocomplete="street-address" placeholder="Enter address" required>
                </div>

                <div class="form-buttons">
                    <input type="submit" name="submit" value="✓ Save Customer">
                    <a href="customer.php" class="btn btn-cancel">✕ Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
    </footer>
</body>
</html>