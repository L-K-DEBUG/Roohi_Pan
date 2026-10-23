<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($_POST['order_data'], true);
    $user_id = $_SESSION['user_id'];
    
    // Extract data with defaults to prevent null errors
    $order_type = $data['order_type'] ?? 'Dine-in';
    $table_no = $data['table_no'] ?? '';
    $customer_name = $data['customer_name'] ?? '';
    $payment_status = $data['payment_status'] ?? 'paid';
    $cart = $data['cart'] ?? [];

    if (empty($cart)) { header("Location: index.php"); exit; }

    // Calculate total
    $total_amount = 0;
    foreach ($cart as $item) {
        $total_amount += ($item['price'] * $item['quantity']);
    }

    // 1. Insert into Orders Table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, table_no, customer_name, total_amount, payment_status) VALUES (?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Database Prepare Error (Orders): " . $conn->error);
    }

    // "isssds" means: Integer, String, String, String, Double, String
    $stmt->bind_param("isssds", $user_id, $order_type, $table_no, $customer_name, $total_amount, $payment_status);
    
    if (!$stmt->execute()) {
        die("Error saving order. Check if 'payment_status' column exists in 'orders' table. Error: " . $stmt->error);
    }
    
    // Get the ID of the newly created order
    $order_id = $conn->insert_id;

    if (!$order_id) {
        die("Failed to get new Order ID. The order was not saved.");
    }

    // 2. Insert into Order Items Table and Update Stock
    foreach ($cart as $item) {
        $item_id = $item['id'];
        $qty = $item['quantity'];
        $price = $item['price'];

        // Insert order item
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        if (!$stmt_item) {
            die("Database Prepare Error (Order Items): " . $conn->error);
        }
        
        // "iiid" means: Integer, Integer, Integer, Double
        $stmt_item->bind_param("iiid", $order_id, $item_id, $qty, $price);
        
        if (!$stmt_item->execute()) {
            die("Error saving order item: " . $stmt_item->error);
        }

        // Update stock (subtract)
        $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
    }

    // Redirect to receipt page
    header("Location: receipt.php?order_id=" . $order_id);
    exit;
}
?>