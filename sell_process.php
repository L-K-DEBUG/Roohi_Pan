<?php
session_start();
require 'db.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id    = $_SESSION['employee_id'];
$daily_stock_id = intval($_POST['daily_stock_id']);
$quantity_sold  = intval($_POST['quantity_sold']);

if ($quantity_sold <= 0) {
    header("Location: dashboard.php?msg=" . urlencode("Please enter a valid quantity."));
    exit;
}

$conn->begin_transaction();

try {
    // Lock the stock row so two cashiers can't oversell the last item
    $stmt = $conn->prepare("
        SELECT ds.quantity_remaining, ds.item_id, i.price
        FROM daily_stock ds
        JOIN items i ON ds.item_id = i.id
        WHERE ds.id = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $daily_stock_id);
    $stmt->execute();
    $stockRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$stockRow) {
        throw new Exception("Item not found.");
    }

    if ($quantity_sold > $stockRow['quantity_remaining']) {
        throw new Exception("Not enough stock remaining for that quantity.");
    }

    $item_id     = (int) $stockRow['item_id'];
    $unit_price  = (float) $stockRow['price'];
    $total       = $unit_price * $quantity_sold;

    // 1. Create the receipt row
    $receipt_no = "RPH-" . date("Ymd-His") . "-" . $employee_id;

    $rec = $conn->prepare("
        INSERT INTO receipts (receipt_no, employee_id, total_amount, payment_method)
        VALUES (?, ?, ?, 'cash')
    ");
    $rec->bind_param("sid", $receipt_no, $employee_id, $total);
    $rec->execute();
    $receipt_id = $conn->insert_id;   // ← the missing piece
    $rec->close();

    if (!$receipt_id) {
        throw new Exception("Failed to create receipt.");
    }

    // 2. Insert the sales line
    $insert = $conn->prepare("
        INSERT INTO sales
            (receipt_id, daily_stock_id, item_id, employee_id, quantity_sold, unit_price)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $insert->bind_param(
        "iiiidi",
        $receipt_id,
        $daily_stock_id,
        $item_id,
        $employee_id,
        $quantity_sold,
        $unit_price
    );
    $insert->execute();
    $insert->close();

    // 3. Reduce the remaining stock
    $update = $conn->prepare("
        UPDATE daily_stock
        SET quantity_remaining = quantity_remaining - ?
        WHERE id = ?
    ");
    $update->bind_param("ii", $quantity_sold, $daily_stock_id);
    $update->execute();
    $update->close();

    $conn->commit();

    header("Location: receipt.php?id=" . $receipt_id);
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    header("Location: dashboard.php?msg=" . urlencode($e->getMessage()));
    exit;
}