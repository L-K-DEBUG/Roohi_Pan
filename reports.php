<?php
require 'db.php';

// FIX 1: Allow both Owners and Employees to access this page
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$date_condition = "WHERE o.status = 'completed' "; // Add base condition to only show completed orders

switch ($filter) {
    case 'week':
        $date_condition .= "AND YEARWEEK(o.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $date_condition .= "AND MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())";
        break;
    case 'year':
        $date_condition .= "AND YEAR(o.created_at) = YEAR(CURDATE())";
        break;
    case 'today':
    default:
        $date_condition .= "AND DATE(o.created_at) = CURDATE()";
        break;
}

// Stats (Top Cards)
$stats = $conn->query("SELECT COUNT(id) as orders, SUM(total_amount) as revenue, 
                        SUM(CASE WHEN order_type = 'Dine-in' THEN 1 ELSE 0 END) as dine_in, 
                        SUM(CASE WHEN order_type = 'Delivery' THEN 1 ELSE 0 END) as delivery 
                        FROM orders o $date_condition")->fetch_assoc();

// Most Used Items
$most_used = $conn->query("SELECT i.name, SUM(oi.quantity) as total_qty 
                           FROM order_items oi 
                           JOIN items i ON oi.item_id = i.id 
                           JOIN orders o ON oi.order_id = o.id 
                           $date_condition 
                           GROUP BY i.id ORDER BY total_qty DESC LIMIT 5");

// FIX 2: Fetch Detailed Order History (with Date, Employee, and Items)
$orders_list = $conn->query("
    SELECT o.id, o.created_at, o.order_type, o.total_amount, u.username,
           GROUP_CONCAT(CONCAT(i.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN items i ON oi.item_id = i.id
    $date_condition
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - Roohi Pan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
        <div class="sidebar">
        <h2>Roohi Pan</h2>
        <a href="index.php">New Order</a>
        <a href="reports.php">Reports</a>
        <?php if($_SESSION['role'] == 'owner'): ?>
            <a href="staff.php">Staff</a>
            <a href="inventory.php">Inventory</a> <!-- ADD THIS LINE -->
        <?php endif; ?>
        <a href="logout.php" style="margin-top:auto;">Sign Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Stock Usage Report</h2>
            <button class="btn" onclick="window.print()">Print Report</button>
        </div>

        <div style="margin-bottom: 20px;">
            <a href="?filter=today" class="btn <?php echo $filter=='today'?'':'btn-primary'; ?>">Today</a>
            <a href="?filter=week" class="btn <?php echo $filter=='week'?'':'btn-primary'; ?>">This Week</a>
            <a href="?filter=month" class="btn <?php echo $filter=='month'?'':'btn-primary'; ?>">This Month</a>
            <a href="?filter=year" class="btn <?php echo $filter=='year'?'':'btn-primary'; ?>">This Year</a>
        </div>

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
            <div style="background: #0d2b1f; color: white; padding: 20px; border-radius: 10px;">
                <h3>REVENUE</h3>
                <h2>KSh <?php echo number_format($stats['revenue'] ?? 0); ?></h2>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h3>ORDERS</h3>
                <h2><?php echo $stats['orders'] ?? 0; ?></h2>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h3>DINE-IN</h3>
                <h2><?php echo $stats['dine_in'] ?? 0; ?></h2>
            </div>
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <h3>DELIVERY</h3>
                <h2><?php echo $stats['delivery'] ?? 0; ?></h2>
            </div>
        </div>

        <!-- Most Used Items -->
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h3>Most Used Items</h3>
            <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                <tr style="background: #f4f7f4; text-align: left;">
                    <th style="padding: 10px;">Item Name</th>
                    <th style="padding: 10px;">Quantity Sold</th>
                </tr>
                <?php if($most_used->num_rows > 0): ?>
                    <?php while($item = $most_used->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo $item['name']; ?></td>
                        <td style="padding: 10px; font-weight: bold;"><?php echo $item['total_qty']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="padding: 10px; text-align: center;">No items sold yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- NEW SECTION: Detailed Order History -->
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h3>Detailed Order History</h3>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">This shows exactly who took the order, when, and what was sold.</p>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="background: #f4f7f4; text-align: left;">
                        <th style="padding: 10px;">Order ID</th>
                        <th style="padding: 10px;">Date & Time</th>
                        <th style="padding: 10px;">Employee Name</th>
                        <th style="padding: 10px;">Type</th>
                        <th style="padding: 10px;">Items Sold</th>
                        <th style="padding: 10px;">Total Amount</th>
                    </tr>
                    <?php if($orders_list->num_rows > 0): ?>
                        <?php while($order = $orders_list->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-weight: bold;">#<?php echo $order['id']; ?></td>
                            <td style="padding: 10px;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td style="padding: 10px; color: #0d2b1f; font-weight: bold;"><?php echo $order['username']; ?></td>
                            <td style="padding: 10px;"><?php echo $order['order_type']; ?></td>
                            <td style="padding: 10px; font-size: 0.85rem; color: #555;"><?php echo $order['items']; ?></td>
                            <td style="padding: 10px; font-weight: bold;">KSh <?php echo number_format($order['total_amount']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="padding: 10px; text-align: center;">No orders found for this period.</td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

    </div>
</body>
</html>