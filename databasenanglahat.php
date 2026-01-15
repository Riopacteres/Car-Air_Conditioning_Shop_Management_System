<?php
session_start(); 
// You can add login-session validation here if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Main Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>Car-Air Conditioning SHOP
    Management System</h1>
  </header>

  <main class="dashboard">
    <h2>Welcome to the Main Dashboard</h2>
    <p>Select an option below to manage your data.</p>

    <div class="card-container">
      <div class="card">
        <h3>Customers</h3>
        <p>Manage customer information and contact details.</p>
        <div class="card-buttons">
          <a href="insert_customer.php" class="btn add">➕ Add Customer</a>
          <a href="customer.php" class="btn view">👁️ View Customers</a>
        </div>
      </div>

      <div class="card">
        <h3>Inventory Parts</h3>
        <p>Track stock levels and supplier details efficiently.</p>
        <div class="card-buttons">
          <a href="insert_inventorypart.php" class="btn add">➕ Add Inventory Part</a>
          <a href="inventorypart.php" class="btn view">👁️ View Inventory Parts</a>
        </div>
      </div>
    </div>

    <a href="logout.php" class="logout-btn">Log Out</a>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Inventory Management System</p>
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

/* --- BODY --- */
body {
  background-color: #f0f3ff;
  color: #333;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* --- HEADER --- */
header {
  background-color: #0a43b4ff;
  color: white;
  text-align: center;
  padding: 20px 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

header h1 {
  font-size: 28px;
  letter-spacing: 1px;
}

/* --- MAIN --- */
main.dashboard {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px;
  text-align: center;
}

main h2 {
  font-size: 24px;
  color: #0a43b4ff;
  margin-bottom: 10px;
}

main p {
  font-size: 16px;
  margin-bottom: 30px;
  color: #555;
}

/* --- CARD CONTAINER --- */
.card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 30px;
  width: 100%;
  max-width: 900px;
}

/* --- CARD --- */
.card {
  background-color: #ffffff;
  border-radius: 15px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  padding: 30px;
  width: 350px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.2);
}

.card h3 {
  color: #0a43b4ff;
  margin-bottom: 15px;
}

.card p {
  color: #555;
  margin-bottom: 20px;
  font-size: 14px;
}

/* --- BUTTONS --- */
.card-buttons {
  display: flex;
  justify-content: space-between;
}

.btn {
  display: inline-block;
  text-decoration: none;
  padding: 10px 18px;
  border-radius: 25px;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn.add {
  background-color: #0a43b4ff;
  color: white;
}

.btn.add:hover {
  background-color: #052a78;
}

.btn.view {
  background-color: #f1f2f6;
  color: #0a43b4ff;
  border: 2px solid #0a43b4ff;
}

.btn.view:hover {
  background-color: #e0e6ff;
}

/* --- LOGOUT BUTTON --- */
.logout-btn {
  display: inline-block;
  margin-top: 40px;
  padding: 12px 25px;
  background-color: #d11a2a;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
  transition: background 0.3s ease;
}

.logout-btn:hover {
  background-color: #a31321;
}

/* --- FOOTER --- */
footer {
  background-color: #0a43b4ff;
  color: white;
  text-align: center;
  padding: 10px 0;
  font-size: 14px;
}

</style>


