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

$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $count_sql = "SELECT COUNT(*) as total FROM inventorypart WHERE PartName LIKE '%$search_escaped%' OR Description LIKE '%$search_escaped%' OR SupplierName LIKE '%$search_escaped%'";
    $sql = "SELECT * FROM inventorypart WHERE PartName LIKE '%$search_escaped%' OR Description LIKE '%$search_escaped%' OR SupplierName LIKE '%$search_escaped%' ORDER BY Part_ID DESC LIMIT $records_per_page OFFSET $offset";
} else {
    $count_sql = "SELECT COUNT(*) as total FROM inventorypart";
    $sql = "SELECT * FROM inventorypart ORDER BY Part_ID DESC LIMIT $records_per_page OFFSET $offset";
}

$count_query = mysqli_query($conn, $count_sql);
$count_row = mysqli_fetch_assoc($count_query);
$total_parts = $count_row['total'];
$total_pages = ceil($total_parts / $records_per_page);

$query = mysqli_query($conn, $sql);
$success_msg = $_GET["success"] ?? "";
$error_msg = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Parts Management</title>
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

        .action-links {
            display: flex;
            gap: 8px;
        }

        .action-links a {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .edit-btn {
            background: #e3f2fd;
            color: #1976d2;
        }

        .edit-btn:hover {
            background: #1976d2;
            color: white;
        }

        .delete-btn {
            background: #ffebee;
            color: #d32f2f;
        }

        .delete-btn:hover {
            background: #d32f2f;
            color: white;
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

        .message {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
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
            padding: 14px 0;
            font-size: 13px;
        }

        @media (max-width: 720px) {
            header {
                flex-direction: column;
                gap: 12px;
            }

            header h1 {
                font-size: 20px;
            }

            main {
                padding: 20px 15px;
            }

            .controls {
                flex-direction: column;
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
                gap: 4px;
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
        <h1>Inventory Parts Management</h1>
        <div class="header-actions">
            <a href="<?php echo $dashboard_link; ?>">← Back to Dashboard</a>
        </div>
    </header>

    <main>
        <h2 class="page-title">📦 Inventory Parts</h2>

        <div class="controls">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Search by part name, description, or supplier..." onkeyup="filterTable()">
            </div>
            <a href="insert_inventorypart.php" class="btn">+ Add New Part</a>
        </div>

        <?php if ($success_msg): ?>
            <div class="message success">✓ <?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="message error">✗ <?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="table-section">
            <?php if ($query && mysqli_num_rows($query) > 0): ?>
                <table id="partsTable">
                    <thead>
                        <tr>
                            <th>Part ID</th>
                            <th>Part Name</th>
                            <th>Description</th>
                            <th>Quantity in Stock</th>
                            <th>Supplier Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr class="part-row" data-search="<?php echo strtolower(htmlspecialchars($row['PartName'] . ' ' . $row['Description'] . ' ' . $row['SupplierName'])); ?>">
                                <td><?php echo htmlspecialchars($row["Part_ID"]); ?></td>
                                <td><strong><?php echo htmlspecialchars($row["PartName"]); ?></strong></td>
                                <td><?php echo htmlspecialchars(substr($row["Description"], 0, 30)) . (strlen($row["Description"]) > 30 ? '...' : ''); ?></td>
                                <td>
                                    <strong style="<?php echo ($row["QuantityInStock"] < 10) ? 'color: #d32f2f;' : ''; ?>">
                                        <?php echo htmlspecialchars($row["QuantityInStock"]); ?>
                                    </strong>
                                    <?php if ($row["QuantityInStock"] < 10): ?>
                                        <span style="color: #ff9800; font-size: 11px;"> ⚠ Low Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row["SupplierName"]); ?></td>
                                <td>
                                    <div class="action-links">
                                        <a href="edit_inventorypart.php?id=<?php echo htmlspecialchars($row['Part_ID']); ?>" class="edit-btn">Edit</a>
                                        <a href="delete_inventorypart.php?id=<?php echo htmlspecialchars($row['Part_ID']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this part?');"> Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No Parts Found</h3>
                    <p>Start by adding a new inventory part to the system.</p>
                </div>
            <?php endif; ?>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="inventorypart.php?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">« First</a>
                        <a href="inventorypart.php?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">‹ Previous</a>
                    <?php endif; ?>
                    
                    <span class="page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="inventorypart.php?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">Next ›</a>
                        <a href="inventorypart.php?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="page-btn">Last »</a>
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
            const searchInput = document.getElementById('searchInput');
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.part-row');
            
            rows.forEach(row => {
                const searchText = row.getAttribute('data-search');
                if (searchText.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>