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

$dashboard_link = $is_admin ? "admin_dashboard.php" : "Dashboard.php";

include('database.php');

// Pagination setup
$records_per_page = 10;
$current_page = max(1, intval($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $records_per_page;

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $count_sql = "SELECT COUNT(*) as total FROM customer WHERE FullName LIKE '%$search_escaped%' OR Email LIKE '%$search_escaped%' OR PhoneNumber LIKE '%$search_escaped%'";
    $sql = "SELECT * FROM customer WHERE FullName LIKE '%$search_escaped%' OR Email LIKE '%$search_escaped%' OR PhoneNumber LIKE '%$search_escaped%' ORDER BY Customer_ID DESC LIMIT $records_per_page OFFSET $offset";
} else {
    $count_sql = "SELECT COUNT(*) as total FROM customer";
    $sql = "SELECT * FROM customer ORDER BY Customer_ID DESC LIMIT $records_per_page OFFSET $offset";
}

$count_query = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_query);
$total_customers = $count_row['total'];
$total_pages = ceil($total_customers / $records_per_page);

$query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Management</title>
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
            padding: 20px 30px;
            box-shadow: 0 3px 12px rgba(10, 67, 180, 0.35);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .header-actions a {
            color: white;
            text-decoration: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .header-actions a:hover {
            background-color: #ffffff;
            color: #0a43b4;
            border-color: #ffffff;
        }

        main {
            flex: 1;
            padding: 40px 30px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .page-title {
            font-size: 28px;
            color: #0a43b4;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
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

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            padding: 11px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 3px 10px rgba(10, 67, 180, 0.25);
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: linear-gradient(135deg, #052a78, #0a43b4);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(10, 67, 180, 0.35);
        }

        .table-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 14px rgba(0, 0, 0, 0.07);
            border: 1px solid #e8ecf5;
            overflow-x: auto;
        }

        .stats {
            margin-bottom: 20px;
            padding: 0 0 15px 0;
            border-bottom: 1px solid #eef1f8;
            color: #666;
            font-size: 14px;
        }

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

        .action-cell {
            font-size: 12px;
            white-space: nowrap;
        }

        .action-cell a {
            color: #0a43b4;
            text-decoration: none;
            margin-right: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .action-cell a:hover {
            color: #1560e8;
            text-decoration: underline;
        }

        .action-cell .delete-link {
            color: #d11a2a;
        }

        .action-cell .delete-link:hover {
            color: #a31321;
        }

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

        footer {
            background: linear-gradient(135deg, #0a43b4, #1560e8);
            color: white;
            text-align: center;
            padding: 16px 0;
            font-size: 13px;
            box-shadow: 0 -3px 12px rgba(10, 67, 180, 0.15);
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
            }

            header h1 {
                font-size: 18px;
                text-align: center;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            main {
                padding: 20px 15px;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: unset;
            }

            .btn {
                width: 100%;
            }

            table th, table td {
                padding: 10px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Customers Management</h1>
        <div class="header-actions">
            <a href="<?php echo $dashboard_link; ?>">← Back to Dashboard</a>
        </div>
    </header>

    <main>
        <h2 class="page-title">👥 Customers</h2>

        <div class="controls">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Search by name, email, or phone..." onkeyup="filterTable()">
            </div>
            <a href="insert_customer.php" class="btn">+ Add New Customer</a>
        </div>

        <div class="table-section">
            <div class="stats">
                <strong id="resultCount"><?php echo "Showing " . (($current_page - 1) * $records_per_page + 1) . " - " . min($current_page * $records_per_page, $total_customers) . " of " . $total_customers . " customer(s)"; ?></strong>
            </div>

            <?php if (mysqli_num_rows($query) > 0): ?>
                <table id="customerTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr class="customer-row" data-search="<?php echo strtolower(htmlspecialchars($row['FullName'] . ' ' . $row['Email'] . ' ' . $row['PhoneNumber'])); ?>">
                                <td><?php echo htmlspecialchars($row["Customer_ID"]); ?></td>
                                <td><?php echo htmlspecialchars($row["FullName"]); ?></td>
                                <td><?php echo htmlspecialchars($row["PhoneNumber"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Email"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Address"]); ?></td>
                                <td class="action-cell">
                                    <a href="edit_customer.php?id=<?php echo $row['Customer_ID']; ?>">✎ Edit</a>
                                    <a href="delete_customer.php?id=<?php echo $row['Customer_ID']; ?>" class="delete-link" onclick="return confirm('⚠️ Are you sure you want to delete this customer?');">🗑 Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No Customers Found</h3>
                    <p>Start by <a href="insert_customer.php" style="color: #0a43b4; text-decoration: underline;">adding a new customer</a>.</p>
                </div>
            <?php endif; ?>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="customer.php?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">« First</a>
                        <a href="customer.php?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">‹ Previous</a>
                    <?php endif; ?>
                    
                    <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="customer.php?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">Next ›</a>
                        <a href="customer.php?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">Last »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Car Air Conditioning Shop Management System</p>
    </footer>

    <script>
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.customer-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (searchData.includes(searchInput)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = visibleCount + ' customer(s) found';
        }
    </script>
</body>
</html>