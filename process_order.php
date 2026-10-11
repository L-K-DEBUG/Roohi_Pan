<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($_POST['order_data'], true);
    $user_id = $_SESSION['user_id'];
    $order_type = $data['order_type'];
    $table_no = $data['table_no'];
    $customer_name = $data['customer_name'];
    $cart = $data['cart'];

    if (empty($cart)) { header("Location: index.php"); exit; }

    // Calculate total
    $total_amount = 0;
    foreach ($cart as $item) {
        $total_amount += ($item['price'] * $item['quantity']);
    }

    // Insert into Orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_type, table_no, customer_name, total_amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssd", $user_id, $order_type, $table_no, $customer_name, $total_amount);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert into Order Items and Update Stock
    foreach ($cart as $item) {
        $item_id = $item['id'];
        $qty = $item['quantity'];
        $price = $item['price'];

        // Insert order item
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_item->bind_param("iiid", $order_id, $item_id, $qty, $price);
        $stmt_item->execute();

        // Update stock (subtract)
        $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");
    }

    // Redirect to receipt page
    header("Location: receipt.php?order_id=" . $order_id);
    exit;
}
?>