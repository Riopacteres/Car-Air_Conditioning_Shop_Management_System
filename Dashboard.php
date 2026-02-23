<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <a href="admin_login.php" class="admin-login-btn">Admin Login</a>
    <h1>Car Air Conditioning Shop Management System</h1>
  </header>

  <main class="dashboard">
    <div class="welcome-section">
      <h2>Welcome to the Main Dashboard</h2>
      <p>Select an option below to manage your data.</p>
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
}

header h1 {
  font-size: 26px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.admin-login-btn {
  position: absolute;
  right: 24px;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(255, 255, 255, 0.15);
  color: #fff;
  padding: 9px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  font-size: 13px;
  border: 1.5px solid rgba(255, 255, 255, 0.4);
  transition: all 0.3s ease;
}

.admin-login-btn:hover {
  background-color: #ffffff;
  color: #0a43b4;
  border-color: #ffffff;
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
  .card-grid {
    grid-template-columns: 1fr;
    max-width: 440px;
  }
  header h1 {
    font-size: 18px;
    padding: 0 80px;
  }
}
</style>
