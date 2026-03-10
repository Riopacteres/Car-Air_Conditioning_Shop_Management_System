<?php
session_start();

// Prevent caching - ensure page cannot be accessed after logout via back button
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Check if staff is logged in
if (!isset($_SESSION["staff_logged_in"]) || $_SESSION["staff_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

$staff_name = htmlspecialchars($_SESSION["staff_fullname"] ?? $_SESSION["staff_username"] ?? "Staff");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Dashboard</title>
  <link rel="icon" href="data:image/svg+xml,<svg></svg>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <h1>Car Air Conditioning Shop Management System</h1>
    <div style="display: flex; gap: 15px; align-items: center;">
      <span style="color: white; font-weight: 600;">👤 <?php echo $staff_name; ?></span>
      <a href="logout.php" class="logout-btn">Log Out</a>
    </div>
  </header>

  <main class="dashboard">
    <div class="welcome-section">
      <h2>Welcome to Staff Dashboard, <?php echo $staff_name; ?>!</h2>
      <p>Access tools and information for managing services and customers.</p>
    </div>

    <div class="card-grid">

      <div class="card">
        <div class="card-icon">
          <span>&#128100;</span>
        </div>
        <h3>Customers</h3>
        <p>Manage customer information and contact details.</p>
        <div class="card-buttons">
          <a href="insert_customer.php" class="btn add">Add Customer</a>
          <a href="customer.php" class="btn view">View Customers</a>
        </div>
      </div>

      <div class="card">
        <div class="card-icon">
          <span>&#128230;</span>
        </div>
        <h3>Inventory Parts</h3>
        <p>Track stock levels and supplier details efficiently.</p>
        <div class="card-buttons">
          <a href="insert_inventorypart.php" class="btn add">Add Inventory Part</a>
          <a href="inventorypart.php" class="btn view">View Inventory Parts</a>
        </div>
      </div>

      <div class="card">
        <div class="card-icon">
          <span>&#128663;</span>
        </div>
        <h3>Vehicles</h3>
        <p>Customer vehicles and registration details.</p>
        <div class="card-buttons">
          <a href="insert_vehicle.php" class="btn add">Add Vehicle</a>
          <a href="vehicle.php" class="btn view">View Vehicles</a>
        </div>
      </div>

      <div class="card">
        <div class="card-icon">
          <span>&#128736;</span>
        </div>
        <h3>Service Requests</h3>
        <p>Track service appointments and repair history.</p>
        <div class="card-buttons">
          <a href="insert_servicerequests.php" class="btn add">Add Request</a>
          <a href="servicerequests.php" class="btn view">View Requests</a>
        </div>
      </div>

    </div>

  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>

</body>
</html>

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
  text-align: center;
  padding: 22px 30px;
  box-shadow: 0 3px 12px rgba(10, 67, 180, 0.35);
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

header h1 {
  font-size: 26px;
  font-weight: 700;
  letter-spacing: 0.5px;
  flex: 1;
}

.logout-btn {
  background-color: #d11a2a;
  color: #fff;
  padding: 9px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 13px;
  border: 1.5px solid #d11a2a;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background-color: #a31321;
  border-color: #a31321;
}

/* ---- MAIN ---- */
main.dashboard {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px 30px 50px;
}

.welcome-section {
  text-align: center;
  margin-bottom: 35px;
}

.welcome-section h2 {
  font-size: 26px;
  color: #0a43b4;
  font-weight: 700;
  margin-bottom: 6px;
}

.welcome-section p {
  font-size: 15px;
  color: #777;
}

/* ---- CARD GRID ---- */
.card-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 28px;
  width: 100%;
  max-width: 960px;
}

/* ---- CARD ---- */
.card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
  padding: 32px 28px 28px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #e8ecf5;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 28px rgba(10, 67, 180, 0.13);
}

.card-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 16px;
  background: linear-gradient(135deg, #e8eeff, #d4deff);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
}

.card h3 {
  color: #0a43b4;
  font-size: 19px;
  font-weight: 700;
  margin-bottom: 8px;
}

.card p {
  color: #777;
  font-size: 13.5px;
  margin-bottom: 22px;
  line-height: 1.5;
}

/* ---- BUTTONS ---- */
.card-buttons {
  display: flex;
  gap: 12px;
  justify-content: center;
}

.btn {
  display: inline-block;
  text-decoration: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  transition: all 0.25s ease;
  letter-spacing: 0.2px;
}

.btn.add {
  background: linear-gradient(135deg, #0a43b4, #1560e8);
  color: white;
  box-shadow: 0 3px 10px rgba(10, 67, 180, 0.25);
}

.btn.add:hover {
  background: linear-gradient(135deg, #052a78, #0a43b4);
  box-shadow: 0 4px 14px rgba(10, 67, 180, 0.35);
}

.btn.view {
  background-color: #f3f5fb;
  color: #0a43b4;
  border: 1.5px solid #c9d2eb;
}

.btn.view:hover {
  background-color: #e0e6ff;
  border-color: #0a43b4;
}

/* ---- LOGOUT ---- */
.logout-btn {
  display: inline-block;
  margin-top: 40px;
  padding: 12px 30px;
  background-color: #d11a2a;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  transition: background 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 3px 10px rgba(209, 26, 42, 0.2);
}

.logout-btn:hover {
  background-color: #a31321;
  box-shadow: 0 4px 14px rgba(209, 26, 42, 0.35);
}

/* ---- FOOTER ---- */
footer {
  background: linear-gradient(135deg, #0a43b4, #1560e8);
  color: white;
  text-align: center;
  padding: 14px 0;
  font-size: 13px;
}

/* ---- RESPONSIVE ---- */
@media (max-width: 720px) {
  header {
    flex-direction: column;
    gap: 15px;
  }
  
  header h1 {
    font-size: 18px;
  }

  .logout-btn {
    padding: 8px 16px;
    font-size: 12px;
  }
  
  .card-grid {
    grid-template-columns: 1fr;
    max-width: 440px;
  }
  
  main.dashboard {
    padding: 20px 15px 30px;
  }
  
  .welcome-section h2 {
    font-size: 20px;
  }
  
  .card {
    padding: 24px 20px;
  }
  
  .card-buttons {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
  }
}
</style>
