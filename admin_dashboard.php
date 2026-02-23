<?php
session_start();

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include('database.php');

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

$cust_sql = "SELECT * FROM customer ORDER BY Customer_ID DESC LIMIT 10";
$cust_query = mysqli_query($conn, $cust_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background-color: #f0f3ff; color: #333; min-height: 100vh; display: flex; flex-direction: column; }

    header {
      background-color: #0a43b4ff;
      color: white;
      padding: 18px 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 { font-size: 22px; letter-spacing: 1px; }
    .header-actions a {
      color: #fff;
      text-decoration: none;
      padding: 8px 18px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
      margin-left: 10px;
      transition: background 0.3s;
    }
    .header-actions .btn-back { background: rgba(255,255,255,0.15); }
    .header-actions .btn-back:hover { background: rgba(255,255,255,0.25); }
    .header-actions .btn-logout { background: #d11a2a; }
    .header-actions .btn-logout:hover { background: #a31321; }

    .container {
      flex: 1;
      padding: 30px 40px;
      max-width: 1300px;
      margin: 0 auto;
      width: 100%;
    }

    .welcome-bar {
      background: #fff;
      padding: 20px 25px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .welcome-bar h2 { color: #0a43b4ff; font-size: 22px; }
    .welcome-bar .badge {
      background: #0a43b4ff;
      color: #fff;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
    }

    .section-title {
      color: #0a43b4ff;
      font-size: 20px;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid #dde4f5;
    }

    .table-wrap {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
      overflow-x: auto;
      margin-bottom: 35px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th {
      background: #0a43b4ff;
      color: #fff;
      padding: 13px 14px;
      text-align: left;
      font-size: 14px;
      white-space: nowrap;
    }
    table td {
      padding: 12px 14px;
      font-size: 14px;
      border-bottom: 1px solid #eef1f8;
      color: #444;
    }
    table tr:hover td { background: #f5f7ff; }

    .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
    }
    .status-pending   { background: #fff3cd; color: #856404; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-progress  { background: #cce5ff; color: #004085; }
    .status-default   { background: #e2e3e5; color: #383d41; }

    .empty-state {
      text-align: center;
      padding: 50px 20px;
      color: #888;
    }
    .empty-state h3 { margin-bottom: 8px; color: #aaa; font-size: 18px; }

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
    <h1>Admin Dashboard</h1>
    <div class="header-actions">
      <a href="Dashboard.php" class="btn-back">Back to Main</a>
      <a href="admin_logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <div class="container">
    <div class="welcome-bar">
      <h2>Welcome, Admin</h2>
      <span class="badge">Administrator</span>
    </div>

    <h3 class="section-title">Customer Service Requests</h3>
    <div class="table-wrap">
      <?php if ($sr_query && mysqli_num_rows($sr_query) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>SR ID</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Vehicle</th>
              <th>Plate #</th>
              <th>Description</th>
              <th>Appointment</th>
              <th>Status</th>
              <th>Service History</th>
              <th>Part</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($sr_query)): ?>
              <?php
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
              <tr>
                <td><?php echo htmlspecialchars($row["SR_ID"]); ?></td>
                <td><?php echo htmlspecialchars($row["FullName"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["PhoneNumber"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["Email"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["Model"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["PlateNumber"] ?? "N/A"); ?></td>
                <td><?php echo htmlspecialchars($row["Description"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["AppointmentDate"] ?? ""); ?></td>
                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row["Status"] ?? "N/A"); ?></span></td>
                <td><?php echo htmlspecialchars($row["ServiceHistory"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["PartName"] ?? "N/A"); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <h3>No Service Requests Yet</h3>
          <p>Customer service requests will appear here once they are submitted.</p>
        </div>
      <?php endif; ?>
    </div>

    <h3 class="section-title">Recent Customers</h3>
    <div class="table-wrap">
      <?php if ($cust_query && mysqli_num_rows($cust_query) > 0): ?>
        <table>
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
              <tr>
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
  </div>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>
</body>
</html>
