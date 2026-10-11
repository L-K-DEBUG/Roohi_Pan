<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // 1. Check if the order exists and is NOT already cancelled
    $check = $conn->query("SELECT status FROM orders WHERE id = $order_id");
    $order = $check->fetch_assoc();

    if ($order && $order['status'] == 'completed') {
        
        // 2. Get all items in this order to restore stock
        $items = $conn->query("SELECT item_id, quantity FROM order_items WHERE order_id = $order_id");

        // 3. Add the quantities back to the item inventory
        while($item = $items->fetch_assoc()) {
            $conn->query("UPDATE items SET stock = stock + {$item['quantity']} WHERE id = {$item['item_id']}");
        }

        // 4. Mark the order as cancelled
        $conn->query("UPDATE orders SET status = 'cancelled' WHERE id = $order_id");
    }

    // Redirect back to the POS with a message
    header("Location: index.php?msg=Order+Cancelled+Successfully");
    exit;
}
?>