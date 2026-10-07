<?php
session_start();
require 'db.php';

// Store employee_id in session on first login (coming from index.php form)
if (isset($_POST['employee_id'])) {
    $_SESSION['employee_id'] = intval($_POST['employee_id']);
}

// If no session at all, send back to login
if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];

// Get employee name for greeting
$empStmt = $conn->prepare("SELECT name FROM employees WHERE id = ?");
$empStmt->bind_param("i", $employee_id);
$empStmt->execute();
$empResult = $empStmt->get_result();
$employee = $empResult->fetch_assoc();

// Get today's stock items (only items that have stock set for today)
$itemsQuery = $conn->query("
    SELECT ds.id AS daily_stock_id, i.id AS item_id, i.name, i.unit, i.price, ds.quantity_remaining
    FROM daily_stock ds
    JOIN items i ON ds.item_id = i.id
    WHERE ds.stock_date = CURDATE() AND i.active = 1
    ORDER BY i.name ASC
");

// Message after a sale attempt (passed via URL)
$message = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Roohi Pan House - Dashboard</title>
<style>
  :root { --primary-green: #1e7a34; --light-green: #e8f5e9; }
  * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
  body { background: var(--light-green); min-height: 100vh; }
  header { background: var(--primary-green); color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; }
  header h1 { font-size: 18px; }
  header a { color: #fff; text-decoration: none; font-size: 13px; background: rgba(255,255,255,0.15); padding: 6px 12px; border-radius: 6px; margin-left: 8px; }
  .container { padding: 24px; max-width: 800px; margin: 0 auto; }
  .card { background: #fff; border-radius: 10px; padding: 18px; margin-bottom: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; }
  .item-name { font-weight: bold; font-size: 15px; }
  .item-sub { color: #666; font-size: 13px; }
  .sell-form { display: flex; gap: 6px; align-items: center; }
  input[type=number] { width: 70px; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
  button { padding: 8px 14px; border: none; border-radius: 6px; background: var(--primary-green); color: #fff; cursor: pointer; }
  button:hover { background: #16602a; }
  .message { background: #fff3cd; color: #856404; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
  .empty { text-align: center; color: #777; padding: 40px 0; }
</style>
</head>
<body>

<header>
  <h1>Hi, <?= htmlspecialchars($employee['name']) ?> 👋</h1>
  <div>
    <a href="stock.php">Update Stock</a>
    <a href="report.php">Reports</a>
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="container">
  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if ($itemsQuery->num_rows === 0): ?>
    <div class="empty">No stock has been set for today yet. Go to "Update Stock" to add today's items.</div>
  <?php else: ?>
    <?php while ($item = $itemsQuery->fetch_assoc()): ?>
      <div class="card">
        <div>
          <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
          <div class="item-sub"><?= $item['quantity_remaining'] ?> <?= htmlspecialchars($item['unit']) ?> remaining</div>
        </div>
        <form class="sell-form" action="sell_process.php" method="POST">
          <input type="hidden" name="daily_stock_id" value="<?= $item['daily_stock_id'] ?>">
          <input type="number" name="quantity_sold" min="1" max="<?= $item['quantity_remaining'] ?>" required placeholder="Qty">
          <button type="submit">Sell</button>
        </form>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

</body>
</html>