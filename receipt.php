<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$order_id = $_GET['order_id'];
$order_query = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = $order_id");
$order = $order_query->fetch_assoc();

$items_query = $conn->query("SELECT oi.*, i.name FROM order_items oi JOIN items i ON oi.item_id = i.id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .receipt-box { width: 300px; margin: 50px auto; background: white; padding: 20px; border: 1px dashed #ccc; }
        .receipt-header { text-align: center; margin-bottom: 20px; }
        .receipt-header h2 { color: #0d2b1f; }
        .receipt-item { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 5px; }
        .receipt-total { display: flex; justify-content: space-between; font-weight: bold; margin-top: 10px; border-top: 1px solid #000; padding-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="receipt-box">
                <div class="receipt-header">
            <h2>ROOHI PAN HOUSE</h2>
            <p style="font-style: italic; color: #555; margin-bottom: 10px;">Fed your body, fed your soul- thank you!</p>
            <hr>
            <p>Order #<?php echo $order['id']; ?> | <?php echo $order['order_type']; ?></p>
            <p>Date: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <?php if($order['table_no']) echo "<p>Table: " . $order['table_no'] . "</p>"; ?>
            <?php if($order['customer_name']) echo "<p>Customer: " . $order['customer_name'] . "</p>"; ?>
            <p>Served by: <?php echo $order['username']; ?></p>
        </div>
        <hr>
        <?php while($item = $items_query->fetch_assoc()): ?>
            <div class="receipt-item">
                <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                <span>KSh <?php echo number_format($item['price'] * $item['quantity']); ?></span>
            </div>
        <?php endwhile; ?>
        <div class="receipt-total">
            <span>TOTAL</span>
            <span>KSh <?php echo number_format($order['total_amount']); ?></span>
        </div>
                <div style="text-align: center; margin-top: 30px;">
            <p>Thank you for dining with us!</p>
            
            <button class="btn no-print" onclick="window.print()" style="margin-top:10px;">Print Receipt</button>
            
            <!-- NEW CANCEL BUTTON -->
            <a href="cancel_order.php?order_id=<?php echo $order['id']; ?>" 
               class="btn btn-danger no-print" 
               style="margin-top:10px; margin-left: 10px; padding: 10px 20px;"
               onclick="return confirm('Are you sure you want to cancel this order? This will return the items to stock and remove it from reports.');">
               Cancel Order
            </a>
            
            <br><br>
            <a href="index.php" class="no-print" style="color:#0d2b1f;">Back to POS</a>
        </div>
    </div>
</body>
</html>