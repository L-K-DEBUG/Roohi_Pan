<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Handle "Mark as Paid"
if (isset($_GET['settle'])) {
    $id = $_GET['settle'];
    $conn->query("UPDATE orders SET payment_status = 'paid' WHERE id = $id");
    header("Location: credit.php?msg=Settled+Successfully");
    exit;
}

// Fetch all unpaid orders
$unpaid_orders = $conn->query("
    SELECT o.id, o.customer_name, o.total_amount, o.created_at, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.payment_status = 'unpaid' AND o.status = 'completed' 
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Credit Book - Roohi Pan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Roohi Pan</h2>
        <a href="index.php">New Order</a>
        <a href="credit.php" class="active">Credit Book</a>
        <a href="reports.php">Reports</a>
        <?php if($_SESSION['role'] == 'owner'): ?>
            <a href="staff.php">Staff</a>
            <a href="inventory.php">Inventory</a>
        <?php endif; ?>
        <a href="logout.php" style="margin-top:auto;">Sign Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Credit Book (Unpaid Orders)</h2>
            <button class="btn" onclick="window.print()">Print Credit List</button>
        </div>

        <?php if(isset($_GET['msg'])) echo "<p style='color:green; font-weight:bold;'>".$_GET['msg']."</p>"; ?>

        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f4f7f4; text-align: left;">
                    <th style="padding: 15px;">Order ID</th>
                    <th style="padding: 15px;">Date</th>
                    <th style="padding: 15px;">Customer Name</th>
                    <th style="padding: 15px;">Served By</th>
                    <th style="padding: 15px;">Amount Owed</th>
                    <th style="padding: 15px;">Action</th>
                </tr>
                <?php if($unpaid_orders->num_rows > 0): ?>
                    <?php while($order = $unpaid_orders->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; font-weight: bold;">#<?php echo $order['id']; ?></td>
                        <td style="padding: 15px;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td style="padding: 15px; color: #c62828; font-weight: bold;">
                            <?php echo $order['customer_name'] ? htmlspecialchars($order['customer_name']) : 'Walk-in Customer'; ?>
                        </td>
                        <td style="padding: 15px;"><?php echo $order['username']; ?></td>
                        <td style="padding: 15px; font-weight: bold;">KSh <?php echo number_format($order['total_amount']); ?></td>
                        <td style="padding: 15px;">
                            <a href="?settle=<?php echo $order['id']; ?>" class="btn" style="background: #e8f5e9; color: #2e7d32; padding: 8px 15px;" onclick="return confirm('Confirm payment received?');">Mark as Paid</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="padding: 20px; text-align: center; color: #888;">No outstanding credit. All customers have paid! 🎉</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>