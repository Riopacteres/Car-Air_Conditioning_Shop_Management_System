<?php
session_start();

// Prevent caching - ensure page cannot be accessed after logout via back button
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

include('database.php');

// Service Requests Query
$sr_sql = "SELECT sr.SR_ID, sr.Description, sr.AppointmentDate, sr.Status, sr.ServiceHistory,
                  c.Customer_ID, c.FullName, c.PhoneNumber, c.Email,
                  v.Vehicle_ID, v.Model, v.PlateNumber,
                  ip.Part_ID, ip.PartName
           FROM servicerequests sr
           LEFT JOIN customer c ON sr.Customer_ID = c.Customer_ID
           LEFT JOIN vehicle v ON sr.Vehicle_ID = v.Vehicle_ID
           LEFT JOIN inventorypart ip ON sr.Part_ID = ip.Part_ID
           ORDER BY sr.SR_ID DESC";
$sr_query = mysqli_query($conn, $sr_sql);

// Service Request Statistics
$stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN Status LIKE '%Pending%' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN Status LIKE '%Progress%' OR Status LIKE '%Ongoing%' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN Status LIKE '%Complet%' THEN 1 ELSE 0 END) as completed
              FROM servicerequests";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

// Low Stock Parts (quantity < 10)
$inventory_sql = "SELECT Part_ID, PartName, QuantityInStock, SupplierName FROM inventorypart ORDER BY QuantityInStock ASC";
$inventory_query = mysqli_query($conn, $inventory_sql);

// All inventory for table
$all_inventory_sql = "SELECT Part_ID, PartName, Description, QuantityInStock, SupplierName FROM inventorypart ORDER BY Part_ID DESC LIMIT 10";
$all_inventory_query = mysqli_query($conn, $all_inventory_sql);

$cust_sql = "SELECT * FROM customer ORDER BY Customer_ID DESC LIMIT 10";
$cust_query = mysqli_query($conn, $cust_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
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

    header {
      background: linear-gradient(135deg, #0a43b4, #1560e8);
      color: white;
      padding: 18px 30px;
      box-shadow: 0 3px 12px rgba(10, 67, 180, 0.35);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
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

    .header-nav {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
    }

    .header-nav a {
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

    .header-nav .btn-primary {
      background-color: rgba(255, 255, 255, 0.25);
    }

    .header-nav .btn-primary:hover {
      background-color: #ffffff;
      color: #0a43b4;
      border-color: #ffffff;
      transform: translateY(-2px);
    }

    .header-actions {
      display: flex;
      gap: 12px;
    }

    .header-actions a {
      color: #fff;
      text-decoration: none;
      padding: 9px 20px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 13px;
      border: 1.5px solid rgba(255, 255, 255, 0.4);
      transition: all 0.3s ease;
    }

    .header-actions .btn-back {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .header-actions .btn-back:hover {
      background-color: #ffffff;
      color: #0a43b4;
      border-color: #ffffff;
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
      padding: 40px 30px;
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
    }

    .welcome-section {
      text-align: center;
      margin-bottom: 40px;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
      border: 1px solid #e8ecf5;
    }

    .welcome-section h2 {
      font-size: 26px;
      color: #0a43b4;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .welcome-section p {
      font-size: 15px;
      color: #777;
    }

    /* ---- STATISTICS SECTION ---- */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 40px;
    }

    .stat-card {
      background: white;
      border-radius: 16px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
      border: 1px solid #e8ecf5;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 20px rgba(10, 67, 180, 0.15);
    }

    .stat-card .stat-value {
      font-size: 32px;
      font-weight: 700;
      color: #0a43b4;
      margin-bottom: 6px;
    }

    .stat-card .stat-label {
      font-size: 14px;
      color: #777;
      font-weight: 500;
    }

    /* ---- SECTION TITLE ---- */
    .section-title {
      color: #0a43b4;
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 2px solid #dde4f5;
    }

    /* ---- TABLE & LIST SECTION ---- */
    .table-section {
      background: white;
      border-radius: 16px;
      padding: 25px;
      margin-bottom: 35px;
      box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
      border: 1px solid #e8ecf5;
    }

    .search-box {
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
    }

    .search-box input {
      flex: 1;
      padding: 12px 16px;
      border: 1.5px solid #dde4f5;
      border-radius: 10px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .search-box input:focus {
      outline: none;
      border-color: #0a43b4;
      box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
    }

    /* ---- ALERTS & NOTIFICATIONS ---- */
    .low-stock-alert {
      background: #fff3cd;
      border-left: 4px solid #ffc107;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .low-stock-alert-content {
      color: #856404;
      font-size: 14px;
    }

    .low-stock-alert-title {
      font-weight: 600;
      margin-bottom: 4px;
    }

    .low-stock-alerts-section {
      margin-bottom: 30px;
    }

    /* ---- TABLES ---- */
    table {
      width: 100%;
      border-collapse: collapse;
    }

    table thead tr {
      background: linear-gradient(135deg, #0a43b4, #1560e8);
      color: white;
    }

    table th {
      padding: 14px 16px;
      text-align: left;
      font-size: 13px;
      font-weight: 600;
      white-space: nowrap;
    }

    table td {
      padding: 14px 16px;
      font-size: 13px;
      border-bottom: 1px solid #eef1f8;
      color: #555;
    }

    table tbody tr:hover {
      background: #f8f9ff;
    }

    table tbody tr:last-child td {
      border-bottom: none;
    }

    /* ---- STATUS BADGES ---- */
    .status-badge {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 16px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
    }

    .status-completed {
      background: #d4edda;
      color: #155724;
    }

    .status-progress {
      background: #cce5ff;
      color: #004085;
    }

    .status-default {
      background: #e2e3e5;
      color: #383d41;
    }

    /* ---- EMPTY STATE ---- */
    .empty-state {
      text-align: center;
      padding: 50px 20px;
      color: #999;
    }

    .empty-state h3 {
      margin-bottom: 8px;
      color: #aaa;
      font-size: 18px;
    }

    /* ---- FOOTER ---- */
    footer {
      background: linear-gradient(135deg, #0a43b4, #1560e8);
      color: white;
      text-align: center;
      padding: 16px 0;
      font-size: 13px;
      box-shadow: 0 -3px 12px rgba(10, 67, 180, 0.15);
    }

    @media (max-width: 720px) {
      header {
        flex-direction: column;
        gap: 12px;
      }

      header h1 {
        font-size: 18px;
      }

      .header-nav {
        width: 100%;
        gap: 6px;
      }

      .header-nav a {
        padding: 6px 12px;
        font-size: 11px;
      }

      .header-actions {
        width: 100%;
        justify-content: center;
        gap: 8px;
      }

      .header-actions a {
        padding: 8px 14px;
        font-size: 12px;
      }
  </style>
</head>
<body>
  <header>
    <h1>Admin Dashboard</h1>
    <div class="header-nav">
      <a href="customer.php" class="btn-primary">👥 View Customers</a>
      <a href="insert_customer.php" class="btn-primary">+ Add Customer</a>
      <a href="vehicle.php" class="btn-primary">🚗 View Vehicles</a>
      <a href="insert_vehicle.php" class="btn-primary">+ Add Vehicle</a>
      <a href="inventorypart.php" class="btn-primary">📦 View Inventory</a>
      <a href="insert_inventorypart.php" class="btn-primary">+ Add Part</a>
      <a href="servicerequests.php" class="btn-primary">🔧 Service Requests</a>
      <a href="user.php" class="btn-primary">👤 View Users</a>
      <a href="insert_user.php" class="btn-primary">+ Add User</a>
    </div>
    <div class="header-actions">
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="welcome-section">
      <h2>Welcome, Admin</h2>
      <p>Manage your system with comprehensive dashboard controls and inventory management.</p>
    </div>

    <!-- SERVICE REQUEST STATISTICS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?php echo htmlspecialchars($stats['total'] ?? 0); ?></div>
        <div class="stat-label">Total Requests</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" style="color: #ff9800;"><?php echo htmlspecialchars($stats['pending'] ?? 0); ?></div>
        <div class="stat-label">Pending</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" style="color: #2196f3;"><?php echo htmlspecialchars($stats['in_progress'] ?? 0); ?></div>
        <div class="stat-label">In Progress</div>
      </div>
      <div class="stat-card">
        <div class="stat-value" style="color: #4caf50;"><?php echo htmlspecialchars($stats['completed'] ?? 0); ?></div>
        <div class="stat-label">Completed</div>
      </div>
    </div>

    <!-- INVENTORY - LOW STOCK ALERTS -->
    <div class="table-section">
      <h3 class="section-title">🔔 Low Stock Alerts</h3>
      <div class="low-stock-alerts-section">
        <?php
          $has_low_stock = false;
          while ($inv = mysqli_fetch_assoc($inventory_query)) {
              if ($inv['QuantityInStock'] < 10) {
                  $has_low_stock = true;
                  echo '<div class="low-stock-alert">';
                  echo '<div class="low-stock-alert-content">';
                  echo '<div class="low-stock-alert-title">⚠️ ' . htmlspecialchars($inv['PartName']) . '</div>';
                  echo '<div>Current Stock: ' . htmlspecialchars($inv['QuantityInStock']) . ' units | Supplier: ' . htmlspecialchars($inv['SupplierName']) . '</div>';
                  echo '</div>';
                  echo '</div>';
              }
          }
          if (!$has_low_stock) {
              echo '<p style="color: #4caf50; text-align: center; padding: 15px;">✓ All inventory items have sufficient stock</p>';
          }
        ?>
      </div>
    </div>

    <!-- INVENTORY PARTS MANAGEMENT -->
    <div class="table-section">
      <h3 class="section-title">📦 Inventory Parts Management</h3>

      <div class="search-box">
        <input type="text" id="inventorySearch" placeholder="🔍 Search by part name or supplier..." style="width: 100%;">
      </div>

      <?php if ($all_inventory_query && mysqli_num_rows($all_inventory_query) > 0): ?>
        <table id="inventoryTable">
          <thead>
            <tr>
              <th>Part ID</th>
              <th>Part Name</th>
              <th>Description</th>
              <th>Stock Quantity</th>
              <th>Supplier</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($inv = mysqli_fetch_assoc($all_inventory_query)): ?>
              <tr class="inventory-row" data-search="<?php echo strtolower(htmlspecialchars($inv['PartName'] . ' ' . $inv['SupplierName'])); ?>">
                <td><?php echo htmlspecialchars($inv['Part_ID']); ?></td>
                <td><?php echo htmlspecialchars($inv['PartName']); ?></td>
                <td><?php echo htmlspecialchars($inv['Description']); ?></td>
                <td>
                  <strong><?php echo htmlspecialchars($inv['QuantityInStock']); ?></strong>
                  <?php if ($inv['QuantityInStock'] < 10): ?>
                    <span style="color: #ff9800; margin-left: 5px;">⚠️ Low Stock</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($inv['SupplierName']); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <h3>No Inventory Parts</h3>
          <p>Start by adding inventory parts to track your stock.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- SERVICE REQUESTS -->
    <div class="table-section">
      <h3 class="section-title">🔧 Service Requests</h3>

      <div class="search-box">
        <input type="text" id="srSearch" placeholder="🔍 Search by customer name, plate number, or status..." style="width: 100%;">
      </div>

      <?php if ($sr_query && mysqli_num_rows($sr_query) > 0): ?>
        <table id="srTable">
          <thead>
            <tr>
              <th>SR ID</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Vehicle</th>
              <th>Plate #</th>
              <th>Description</th>
              <th>Appointment Date</th>
              <th>Status</th>
              <th>Part</th>
            </tr>
          </thead>
          <tbody>
            <?php
              // Reset pointer
              mysqli_data_seek($sr_query, 0);
              while ($row = mysqli_fetch_assoc($sr_query)):
                $status_raw = strtolower(trim($row["Status"] ?? ""));
                if (strpos($status_raw, "complet") !== false) {
                    $status_class = "status-completed";
                } elseif (strpos($status_raw, "progress") !== false || strpos($status_raw, "ongoing") !== false) {
                    $status_class = "status-progress";
                } elseif (strpos($status_raw, "pending") !== false) {
                    $status_class = "status-pending";
                } else {
                    $status_class = "status-default";
                }
            ?>
              <tr class="sr-row" data-search="<?php echo strtolower(htmlspecialchars($row['FullName'] . ' ' . $row['PlateNumber'] . ' ' . $row['Status'])); ?>">
                <td><?php echo htmlspecialchars($row["SR_ID"]); ?></td>
                <td><?php echo htmlspecialchars($row["FullName"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["PhoneNumber"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["Model"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["PlateNumber"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["Description"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["AppointmentDate"] ?? ""); ?></td>
                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row["Status"] ?? "N/A"); ?></span></td>
                <td><?php echo htmlspecialchars($row["PartName"] ?? "N/A"); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <h3>No Service Requests</h3>
          <p>Service requests will appear here once they are submitted.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- RECENT CUSTOMERS -->
    <div class="table-section">
      <h3 class="section-title">👥 Recent Customers</h3>

      <div class="search-box">
        <input type="text" id="custSearch" placeholder="🔍 Search by customer name or email..." style="width: 100%;">
      </div>

      <?php if ($cust_query && mysqli_num_rows($cust_query) > 0): ?>
        <table id="custTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Phone Number</th>
              <th>Email</th>
              <th>Address</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($c = mysqli_fetch_assoc($cust_query)): ?>
              <tr class="cust-row" data-search="<?php echo strtolower(htmlspecialchars($c['FullName'] . ' ' . $c['Email'])); ?>">
                <td><?php echo htmlspecialchars($c["Customer_ID"]); ?></td>
                <td><?php echo htmlspecialchars($c["FullName"]); ?></td>
                <td><?php echo htmlspecialchars($c["PhoneNumber"]); ?></td>
                <td><?php echo htmlspecialchars($c["Email"]); ?></td>
                <td><?php echo htmlspecialchars($c["Address"]); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <h3>No Customers Yet</h3>
          <p>Customer records will appear here once added.</p>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System. All rights reserved.</p>
  </footer>

  <script>
    // Inventory Search Filter
    const inventorySearch = document.getElementById('inventorySearch');
    const inventoryRows = document.querySelectorAll('.inventory-row');
    if (inventorySearch) {
      inventorySearch.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        inventoryRows.forEach(row => {
          if (row.getAttribute('data-search').includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }

    // Service Request Search Filter
    const srSearch = document.getElementById('srSearch');
    const srRows = document.querySelectorAll('.sr-row');
    if (srSearch) {
      srSearch.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        srRows.forEach(row => {
          if (row.getAttribute('data-search').includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }

    // Customer Search Filter
    const custSearch = document.getElementById('custSearch');
    const custRows = document.querySelectorAll('.cust-row');
    if (custSearch) {
      custSearch.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        custRows.forEach(row => {
          if (row.getAttribute('data-search').includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  </script>
</html>
