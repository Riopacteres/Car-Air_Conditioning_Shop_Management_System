<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Allow both admin and staff access
$is_admin = isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true;
$is_staff = isset($_SESSION["staff_logged_in"]) && $_SESSION["staff_logged_in"] === true;

if (!$is_admin && !$is_staff) {
    header("Location: login.php");
    exit();
}

$dashboard_link = $is_admin ? "admin_dashboard.php" : "Dashboard.php";

include('database.php');

// Pagination setup
$records_per_page = 10;
$current_page = max(1, intval($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $records_per_page;

$search = trim($_GET['search'] ?? "");
$successMessage = isset($_GET['success']) ? urldecode($_GET['success']) : "";

$sql = "SELECT sr.*, c.FullName, v.Model FROM servicerequests sr 
        LEFT JOIN customer c ON sr.Customer_ID = c.Customer_ID 
        LEFT JOIN vehicle v ON sr.Vehicle_ID = v.Vehicle_ID";

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $sql .= " WHERE c.FullName LIKE '%$search_escaped%' OR v.Model LIKE '%$search_escaped%' OR sr.Description LIKE '%$search_escaped%' OR sr.Status LIKE '%$search_escaped%'";
}

$count_sql = str_replace("SELECT sr.*, c.FullName, v.Model", "SELECT COUNT(*) as total", $sql);
$count_query = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_query);
$total_requests = $count_row['total'];
$total_pages = ceil($total_requests / $records_per_page);

$sql .= " ORDER BY sr.SR_ID DESC LIMIT $records_per_page OFFSET $offset";
$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Requests</title>
    <link rel="icon" href="data:image/svg+xml,<svg></svg>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
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
            padding: 30px 20px;
        }

        .content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 14px 16px;
            border-radius: 8px;
            border-left: 4px solid #4caf50;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            display: flex;
            gap: 8px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid #dde4f5;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #0a43b4;
            box-shadow: 0 0 0 4px rgba(10, 67, 180, 0.1);
        }

        .search-box button {
            padding: 10px 18px;
            background: #0a43b4;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .search-box button:hover {
            background: #1560e8;
            transform: translateY(-1px);
        }

        .add-btn {
            padding: 10px 18px;
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 67, 180, 0.3);
        }

        .table-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
        }

        thead th {
            padding: 14px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.3px;
        }

        tbody td {
            padding: 14px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-in-progress {
            background: #cfe2ff;
            color: #084298;
        }

        .status-completed {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .actions a {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1.5px solid #dde4f5;
        }

        .edit-link {
            background: #e3f2fd;
            color: #0a43b4;
            border-color: #0a43b4;
        }

        .edit-link:hover {
            background: #0a43b4;
            color: white;
        }

        .delete-link {
            background: #ffebee;
            color: #c62828;
            border-color: #f44336;
        }

        .delete-link:hover {
            background: #c62828;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 20px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .page-btn {
            display: inline-block;
            padding: 10px 14px;
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(10, 67, 180, 0.2);
        }

        .page-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 67, 180, 0.3);
        }

        .page-info {
            padding: 10px 14px;
            background: #e5e9f5;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #0a43b4;
        }

        /* ---- FOOTER ---- */
        footer {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            text-align: center;
            padding: 14px 0;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 12px;
            }

            header h1 {
                font-size: 18px;
            }

            .controls {
                flex-direction: column;
                gap: 10px;
            }

            .search-box {
                width: 100%;
                min-width: unset;
            }

            .add-btn {
                width: 100%;
                justify-content: center;
            }

            table {
                font-size: 12px;
            }

            thead th,
            tbody td {
                padding: 10px;
            }

            .actions {
                flex-direction: column;
                gap: 4px;
            }

            .actions a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
  <header>
    <h1>Service Requests</h1>
    <div class="header-actions">
      <a href="<?php echo $dashboard_link; ?>" class="btn-back">← Back to Dashboard</a>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="content">
      <?php if (!empty($successMessage)): ?>
        <div class="success-message">✓ <?php echo htmlspecialchars($successMessage); ?></div>
      <?php endif; ?>

      <div class="controls">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search by customer, vehicle, description, or status..." value="<?php echo htmlspecialchars($search); ?>">
          <button onclick="document.querySelector('form').submit();">🔍 Search</button>
        </div>
        <a href="insert_servicerequests.php" class="add-btn">+ Add Service Request</a>
      </div>

      <form method="GET" style="display:none;">
        <input type="hidden" name="search" id="searchField" value="<?php echo htmlspecialchars($search); ?>">
      </form>

      <div class="table-wrapper">
        <?php if (mysqli_num_rows($query) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>SR ID</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Description</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Service History</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                  <td>#<?php echo htmlspecialchars($row["SR_ID"]); ?></td>
                  <td><?php echo htmlspecialchars($row["FullName"] ?? "N/A"); ?></td>
                  <td><?php echo htmlspecialchars($row["Model"] ?? "N/A"); ?></td>
                  <td><?php echo htmlspecialchars(substr($row["Description"], 0, 30)) . (strlen($row["Description"]) > 30 ? "..." : ""); ?></td>
                  <td><?php echo htmlspecialchars($row["AppointmentDate"]); ?></td>
                  <td>
                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row["Status"])); ?>">
                      <?php echo htmlspecialchars($row["Status"]); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars(substr($row["ServiceHistory"] ?? "N/A", 0, 20)) . (strlen($row["ServiceHistory"] ?? "") > 20 ? "..." : ""); ?></td>
                  <td>
                    <div class="actions">
                      <a href="edit_sr.php?id=<?php echo htmlspecialchars($row['SR_ID']); ?>" class="edit-link">Edit</a>
                      <a href="delete_sr.php?id=<?php echo htmlspecialchars($row['SR_ID']); ?>" class="delete-link" onclick="return confirm('⚠ Are you sure you want to delete this service request? This action cannot be undone!');​">Delete</a>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h3>No Service Requests Found</h3>
            <p><?php echo !empty($search) ? "Try adjusting your search criteria or " : ""; ?>Click the button below to add a new service request.</p>
            <a href="insert_servicerequests.php" class="add-btn">+ Add Service Request</a>
          </div>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
          <div class="pagination">
            <?php if ($current_page > 1): ?>
              <a href="servicerequests.php?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">« First</a>
              <a href="servicerequests.php?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">‹ Previous</a>
            <?php endif; ?>
            
            <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
            
            <?php if ($current_page < $total_pages): ?>
              <a href="servicerequests.php?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">Next ›</a>
              <a href="servicerequests.php?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">Last »</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>

  <script>
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        document.getElementById('searchField').value = this.value;
        document.querySelector('form').submit();
      }
    });
  </script>
</body>
</html>
