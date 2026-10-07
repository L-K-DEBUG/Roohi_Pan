<?php
session_start();
require 'db.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
$daily_stock_id = intval($_POST['daily_stock_id']);
$quantity_sold = intval($_POST['quantity_sold']);

if ($quantity_sold <= 0) {
    header("Location: dashboard.php?msg=" . urlencode("Please enter a valid quantity."));
    exit;
}

// Get current remaining stock and item price
$stmt = $conn->prepare("
    SELECT ds.quantity_remaining, i.price
    FROM daily_stock ds
    JOIN items i ON ds.item_id = i.id
    WHERE ds.id = ?
");
$stmt->bind_param("i", $daily_stock_id);
$stmt->execute();
$result = $stmt->get_result();
$stockRow = $result->fetch_assoc();

if (!$stockRow) {
    header("Location: dashboard.php?msg=" . urlencode("Item not found."));
    exit;
}

if ($quantity_sold > $stockRow['quantity_remaining']) {
    header("Location: dashboard.php?msg=" . urlencode("Not enough stock remaining for that quantity."));
    exit;
}

// Generate a simple receipt number
$receipt_no = "RPH-" . date("Ymd-His") . "-" . $employee_id;

// Insert the sale
$insert = $conn->prepare("
    INSERT INTO sales (daily_stock_id, employee_id, quantity_sold, unit_price, receipt_no)
    VALUES (?, ?, ?, ?, ?)
");
$insert->bind_param("iiids", $daily_stock_id, $employee_id, $quantity_sold, $stockRow['price'], $receipt_no);
$insert->execute();

// Reduce the remaining stock
$update = $conn->prepare("
    UPDATE daily_stock SET quantity_remaining = quantity_remaining - ? WHERE id = ?
");
$update->bind_param("ii", $quantity_sold, $daily_stock_id);
$update->execute();

header("Location: dashboard.php?msg=" . urlencode("Sale recorded successfully."));
exit;