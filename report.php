<?php
require 'auth_check.php'; // password gate - stops here until correct password entered

// Handle stock submission
if (isset($_POST['submit_stock'])) {
    $employee_id = $_SESSION['employee_id'];
    foreach ($_POST['quantity'] as $item_id => $qty) {
        $qty = intval($qty);
        if ($qty <= 0) continue;

        $item_id = intval($item_id);
        $stmt = $conn->prepare("
            INSERT INTO daily_stock (item_id, quantity_available, quantity_remaining, employee_id, stock_date)
            VALUES (?, ?, ?, ?, CURDATE())
            ON DUPLICATE KEY UPDATE quantity_available = VALUES(quantity_available),
                                     quantity_remaining = VALUES(quantity_available),
                                     employee_id = VALUES(employee_id)
        ");
        $stmt->bind_param("iiii", $item_id, $qty, $qty, $employee_id);
        $stmt->execute();
    }
    header("Location: stock.php?msg=" . urlencode("Stock updated for today."));
    exit;
}

$message = $_GET['msg'] ?? '';

// Get all active items, with today's stock if already set
$items = $conn->query("
    SELECT i.id, i.name, i.unit,
           COALESCE(ds.quantity_available, 0) AS quantity_available
    FROM items i
    LEFT JOIN daily_stock ds ON ds.item_id = i.id AND ds.stock_date = CURDATE()
    WHERE i.active = 1
    ORDER BY i.name ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Update Stock</title>
<style>
  :root { --primary-green: #1e7a34; --light-green: #e8f5e9; }
  * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
  body { background: var(--light-green); }
  header { background: var(--primary-green); color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; }
  header a { color: #fff; text-decoration: none; font-size: 13px; background: rgba(255,255,255,0.15); padding: 6px 12px; border-radius: 6px; }
  .container { padding: 24px; max-width: 700px; margin: 0 auto; }
  .card { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
  .row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
  input[type=number] { width: 90px; padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
  button { margin-top: 16px; padding: 10px 18px; background: var(--primary-green); color: #fff; border: none; border-radius: 8px; cursor: pointer; }
  .message { background: #d4edda; color: #155724; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
</style>
</head>
<body>

<header>
  <h2>Update Today's Stock</h2>
  <a href="dashboard.php">← Back to Dashboard</a>
</header>

<div class="container">
  <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

  <div class="card">
    <form method="POST">
      <?php while ($item = $items->fetch_assoc()): ?>
        <div class="row">
          <span><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['unit']) ?>)</span>
          <input type="number" name="quantity[<?= $item['id'] ?>]" min="0" value="<?= $item['quantity_available'] ?>">
        </div>
      <?php endwhile; ?>
      <button type="submit" name="submit_stock">Save Today's Stock</button>
    </form>
  </div>
</div>

</body>
</html>