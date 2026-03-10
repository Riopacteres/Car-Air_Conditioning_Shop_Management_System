<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

include('database.php');

$success_msg = $_GET["success"] ?? "";
$error_msg = $_GET["error"] ?? "";

// Pagination setup
$records_per_page = 10;
$current_page = max(1, intval($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $records_per_page;

$count_sql = "SELECT COUNT(*) as total FROM user";
$sql = "SELECT * FROM user ORDER BY User_ID DESC LIMIT $records_per_page OFFSET $offset";

$count_query = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_query);
$total_users = $count_row['total'];
$total_pages = ceil($total_users / $records_per_page);

$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users</title>
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
      flex-wrap: wrap;
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

    .header-actions .btn-add {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1));
    }

    .header-actions .btn-add:hover {
      background-color: #4caf50;
      border-color: #4caf50;
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

    /* ---- TABLE SECTION ---- */
    .table-section {
      background: white;
      border-radius: 16px;
      padding: 25px;
      margin-bottom: 35px;
      box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
      border: 1px solid #e8ecf5;
    }

    .section-title {
      color: #0a43b4;
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 2px solid #dde4f5;
    }

    .search-box {
      margin-bottom: 20px;
    }

    .search-box input {
      width: 100%;
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

    /* ---- ACTION LINKS ---- */
    .action-links {
      display: flex;
      gap: 10px;
    }

    .action-links a {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 12px;
      font-weight: 600;
      transition: all 0.25s ease;
      white-space: nowrap;
    }

    .action-links .edit-btn {
      background: #e3f2fd;
      color: #1976d2;
    }

    .action-links .edit-btn:hover {
      background: #1976d2;
      color: white;
    }

    .action-links .delete-btn {
      background: #ffebee;
      color: #d32f2f;
    }

    .action-links .delete-btn:hover {
      background: #d32f2f;
      color: white;
    }

    /* ---- MESSAGES ---- */
    .message {
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
      font-weight: 600;
      border-left: 4px solid;
    }

    .message.success {
      background: #e8f5e9;
      color: #2e7d32;
      border-color: #4caf50;
    }

    .message.error {
      background: #ffebee;
      color: #c62828;
      border-color: #f44336;
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

    @media (max-width: 720px) {
      header {
        flex-direction: column;
        gap: 12px;
      }

      header h1 {
        font-size: 18px;
      }

      main {
        padding: 20px 15px;
      }

      .header-actions {
        width: 100%;
        justify-content: center;
        gap: 8px;
      }

      .table-section {
        overflow-x: auto;
      }

      table th, table td {
        padding: 10px 8px;
        font-size: 12px;
      }

      .action-links {
        flex-direction: column;
        gap: 6px;
      }

      .action-links a {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Manage Users</h1>
    <div class="header-actions">
      <a href="insert_user.php" class="btn-add">+ Add User</a>
      <a href="admin_dashboard.php" class="btn-back">← Dashboard</a>
      <a href="logout.php" class="btn-logout">Log Out</a>
    </div>
  </header>

  <main>
    <div class="welcome-section">
      <h2>User Management System</h2>
      <p>View, edit, and manage staff member accounts and their roles.</p>
    </div>

    <?php if ($success_msg): ?>
      <div class="message success">✓ <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
      <div class="message error">✗ <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="table-section">
      <h3 class="section-title">👤 All Users</h3>

      <div class="search-box">
        <input type="text" id="userSearch" placeholder="🔍 Search by name, username, email, or role..." style="width: 100%;">
      </div>

      <?php if ($query && mysqli_num_rows($query) > 0): ?>
        <table id="userTable">
          <thead>
            <tr>
              <th>User ID</th>
              <th>Full Name</th>
              <th>Username</th>
              <th>Role</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
              <tr class="user-row" data-search="<?php echo strtolower(htmlspecialchars($row['FullName'] . ' ' . $row['UserName'] . ' ' . $row['Email'] . ' ' . $row['Role'])); ?>">
                <td><?php echo htmlspecialchars($row["User_ID"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["FullName"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["UserName"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["Role"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["Email"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($row["PhoneNumber"] ?? ""); ?></td>
                <td>
                  <div class="action-links">
                    <a href="edit_user.php?id=<?php echo htmlspecialchars($row['User_ID']); ?>" class="edit-btn">Edit</a>
                    <a href="delete_user.php?id=<?php echo htmlspecialchars($row['User_ID']); ?>" class="delete-btn" onclick="return confirm('⚠ Are you sure you want to delete this user? This action cannot be undone!');"> Delete</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <h3>No Users Found</h3>
          <p>Start by adding a new user to the system.</p>
        </div>
      <?php endif; ?>

      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php if ($current_page > 1): ?>
            <a href="user.php?page=1" class="page-btn">« First</a>
            <a href="user.php?page=<?php echo $current_page - 1; ?>" class="page-btn">‹ Previous</a>
          <?php endif; ?>
          
          <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
          
          <?php if ($current_page < $total_pages): ?>
            <a href="user.php?page=<?php echo $current_page + 1; ?>" class="page-btn">Next ›</a>
            <a href="user.php?page=<?php echo $total_pages; ?>" class="page-btn">Last »</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
  </footer>

  <script>
    // User Search Filter
    const userSearch = document.getElementById('userSearch');
    const userRows = document.querySelectorAll('.user-row');
    if (userSearch) {
      userSearch.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        userRows.forEach(row => {
          if (row.getAttribute('data-search').includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  </script>
</body>
</html>