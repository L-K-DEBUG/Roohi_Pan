<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode($_POST['order_data'], true);
    $user_id = $_SESSION['user_id'];
    
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
    if (!$stmt) { die("Database Prepare Error: " . $conn->error); }

    $stmt->bind_param("isssds", $user_id, $order_type, $table_no, $customer_name, $total_amount, $payment_status);
    if (!$stmt->execute()) { die("Error saving order: " . $stmt->error); }
    
    $order_id = $conn->insert_id;

        // 2. Inventory Deduction Rules (Mapping Menu Item -> [Inventory Item, Quantity to Deduct])
    $deduction_rules = [
        'Chicken Nuggets' => ['Chicken Nuggets', 7],
        'Fish Fingers' => ['Fish Fingers', 6],
        'Half Possin Chicken & Chips' => ['Possin Chicken Portions', 1],
        'Full Possin Chicken & Chips' => ['Possin Chicken Portions', 2],
        
        // UPDATED RULE: Deduct 0.5 instead of 1
        '½ Fried Chicken Breast' => ['Chicken Breast', 0.5], 
        
        '½ Fried Chicken Leg' => ['Fried Chicken Portions', 1],
        'Half Fried Chicken' => ['Fried Chicken Portions', 1],
        'Full Fried Chicken' => ['Fried Chicken Portions', 2],
        'Possin Wings' => ['Wings', 6],
        'Fried Wings' => ['Wings', 6],
        'Masala Wings' => ['Wings', 6],
        'Mabuyu Lollies' => ['Ice Lolly', 1],
        'Choc Sticks' => ['Ice Lolly', 1],
        'Milk Banana Lollies' => ['Ice Lolly', 1],
        'Keema Chapati' => ['Chapati', 1],
        'Chicken Chapati' => ['Chapati', 1],
        'Chapati' => ['Chapati', 1],
        'Sausage' => ['Sausage', 1],
        'Mandazi' => ['Mandazi', 1],
        'Mutton Samosa' => ['Mutton Samosa', 4],
        'Veg Samosa (Per Plate)' => ['Veg Samosa', 3],
        'Daal Bhajia (Per Plate)' => ['Daal Bhajia', 6],
        'Fried Drumsticks (3)' => ['Drumsticks', 3],
        'Possin Drumsticks (3)' => ['Drumsticks', 3],
        'Masala Drumsticks (3)' => ['Drumsticks', 3]
    ];

    // 3. Insert Order Items, Update POS Stock, and Update Raw Inventory
        // 3. Insert Order Items, Update POS Stock, and Update Raw Inventory
    foreach ($cart as $item) {
        $item_id = $item['id'];
        $qty = $item['quantity'];
        $price = $item['price'];

        // Get the item's name and category from the database
        $item_info = $conn->query("SELECT name, category FROM items WHERE id = $item_id")->fetch_assoc();
        $item_name = $item_info['name'];
        $item_category = $item_info['category'];

        // Insert order item
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_item->bind_param("iiid", $order_id, $item_id, $qty, $price);
        $stmt_item->execute();

        // Update the POS Menu stock (subtract)
        $conn->query("UPDATE items SET stock = stock - $qty WHERE id = $item_id");

        // Update the Raw Inventory based on the rules
        if (isset($deduction_rules[$item_name])) {
            // Apply specific rule for food items (Nuggets, Wings, etc.)
            $inv_item = $deduction_rules[$item_name][0];
            $deduct_amount = $deduction_rules[$item_name][1] * $qty;
            $conn->query("UPDATE inventory SET quantity = quantity - $deduct_amount WHERE item_name = '$inv_item'");
            
        } elseif (strpos($item_category, 'SOFT DRINKS') !== false) {
            // NEW LOGIC: Deduct 1 from the specific drink name (e.g., "Coke (Can)")
            $conn->query("UPDATE inventory SET quantity = quantity - $qty WHERE item_name = '$item_name'");
        }
    }

    // Redirect to receipt page
    header("Location: receipt.php?order_id=" . $order_id);
    exit;
}
?>