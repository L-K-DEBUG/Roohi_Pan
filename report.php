<?php
require 'auth_check.php'; // password gate

$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Per-item totals
$perItemStmt = $conn->prepare("
    SELECT i.name, i.unit, SUM(s.quantity_sold) AS total_qty, SUM(s.quantity_sold * s.unit_price) AS total_value
    FROM sales s
    JOIN daily_stock ds ON s.daily_stock_id = ds.id
    JOIN items i ON ds.item_id = i.id
    WHERE DATE(s.sale_time) BETWEEN ? AND ?
    GROUP BY i.id
    ORDER BY total_qty DESC
");
$perItemStmt->bind_param("ss", $start_date, $end_date);
$perItemStmt->execute();
$perItemResult = $perItemStmt->get_result();

// Overall totals
$overallStmt = $conn->prepare("
    SELECT SUM(s.quantity_sold) AS total_items_sold, SUM(s.quantity_sold * s.unit_price) AS total_revenue
    FROM sales s
    WHERE DATE(s.sale_time) BETWEEN ? AND ?
");
$overallStmt->bind_param("ss", $start_date, $end_date);
$overallStmt->execute();
$overall = $overallStmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Sales Report</title>
<style>
  :root { --primary-green: #1e7a34; --light-green: #e8f5e9; }
  * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
  body { background: var(--light-green); }
  header { background: var(--primary-green); color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; }
  header a { color: #fff; text-decoration: none; font-size: 13px; background: rgba(255,255,255,0.15); padding: 6px 12px; border-radius: 6px; }
  .container { padding: 24px; max-width: 800px; margin: 0 auto; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
  .filters { display: flex; gap: 10px; align-items: center; margin-bottom: 10px; }
  input[type=date] { padding: 8px; border: 1px solid #ccc; border-radius: 6px; }
  button { padding: 8px 14px; background: var(--primary-green); color: #fff; border: none; border-radius: 6px; cursor: pointer; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; font-size: 14px; }
  .totals { font-size: 16px; font-weight: bold; color: var(--primary-green); }

  /* Logo + print header - hidden on screen, shown when printing/downloading */
  .print-header { display: none; text-align: center; margin-bottom: 20px; }
  .print-header img { width: 70px; margin-bottom: 8px; }
  .print-header h1 { color: var(--primary-green); font-size: 20px; }
  .print-header p { color: #666; font-size: 13px; }

  @media print {
    header, .filters, .no-print { display: none !important; }
    .print-header { display: block !important; }
    body { background: #fff; }
    .card { box-shadow: none; border: 1px solid #ddd; }
  }
</style>
</head>
<body>

<header>
  <h2>Sales Report</h2>
  <div>
    <button class="no-print" onclick="window.print()" style="margin-right:10px;">Download Report</button>
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</header>

<div class="print-header">
  <img src="logo.png" alt="Roohi Pan House Logo">
  <h1>Roohi Pan House - Sales Report</h1>
  <p>Period: <?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?></p>
</div>

<div class="container">
  <div class="card no-print">
    <div style="margin-bottom: 12px; display: flex; gap: 8px; flex-wrap: wrap;">
      <a href="?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>"><button type="button">Today</button></a>
      <a href="?start_date=<?= date('Y-m-d', strtotime('monday this week')) ?>&end_date=<?= date('Y-m-d') ?>"><button type="button">This Week</button></a>
      <a href="?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-d') ?>"><button type="button">This Month</button></a>
    </div>
    <form class="filters" method="GET">
      <label>From: <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></label>
      <label>To: <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></label>
      <button type="submit">Custom Filter</button>
    </form>
  </div>

  <div class="card">
    <p class="totals">Total items sold: <?= $overall['total_items_sold'] ?? 0 ?></p>
    <p class="totals">Total revenue: KES <?= number_format($overall['total_revenue'] ?? 0, 2) ?></p>
  </div>

  <div class="card">
    <h3>Per-Item Breakdown</h3>
    <table>
      <thead>
        <tr><th>Item</th><th>Qty Sold</th><th>Unit</th><th>Total Value</th></tr>
      </thead>
      <tbody>
        <?php if ($perItemResult->num_rows === 0): ?>
          <tr><td colspan="4">No sales found for this date range.</td></tr>
        <?php else: ?>
          <?php while ($row = $perItemResult->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= $row['total_qty'] ?></td>
              <td><?= htmlspecialchars($row['unit']) ?></td>
              <td>KES <?= number_format($row['total_value'], 2) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>