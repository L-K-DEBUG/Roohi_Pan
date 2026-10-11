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
  :root {
    --primary-green: #1e7a34;
    --primary-green-dark: #16602a;
    --primary-green-light: #2a9c47;
    --light-green: #e8f5e9;
    --bg-soft: #f4faf5;
    --text-dark: #1a2a1e;
    --text-muted: #5f7165;
    --card-shadow: 0 1px 3px rgba(20, 60, 30, 0.06), 0 4px 12px rgba(20, 60, 30, 0.04);
    --card-shadow-hover: 0 2px 6px rgba(20, 60, 30, 0.08), 0 8px 20px rgba(20, 60, 30, 0.08);
    --radius: 12px;
  }

  * {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                 "Helvetica Neue", Arial, sans-serif;
    margin: 0;
    padding: 0;
  }

  body {
    background: var(--bg-soft);
    min-height: 100vh;
    color: var(--text-dark);
    -webkit-font-smoothing: antialiased;
    line-height: 1.5;
  }

  /* ---------- HEADER ---------- */
  header {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
    color: #fff;
    padding: 18px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 2px 12px rgba(20, 60, 30, 0.15);
    position: sticky;
    top: 0;
    z-index: 10;
  }

  header h1 {
    font-size: 17px;
    font-weight: 600;
    letter-spacing: 0.2px;
  }

  header > div {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  header a {
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.14);
    padding: 7px 14px;
    border-radius: 8px;
    margin-left: 0;
    transition: background 0.15s ease, transform 0.15s ease;
    border: 1px solid rgba(255, 255, 255, 0.12);
  }

  header a:hover {
    background: rgba(255, 255, 255, 0.24);
    transform: translateY(-1px);
  }

  header a:active {
    transform: translateY(0);
  }

  /* ---------- CONTAINER ---------- */
  .container {
    padding: 28px 24px;
    max-width: 780px;
    margin: 0 auto;
  }

  /* ---------- CARD ---------- */
  .card {
    background: #fff;
    border-radius: var(--radius);
    padding: 18px 20px;
    margin-bottom: 12px;
    box-shadow: var(--card-shadow);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    border: 1px solid rgba(30, 122, 52, 0.06);
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
  }

  .card:hover {
    box-shadow: var(--card-shadow-hover);
    transform: translateY(-1px);
    border-color: rgba(30, 122, 52, 0.15);
  }

  .item-name {
    font-weight: 600;
    font-size: 16px;
    color: var(--text-dark);
    margin-bottom: 2px;
  }

  .item-sub {
    color: var(--text-muted);
    font-size: 13px;
  }

  /* ---------- SELL FORM ---------- */
  .sell-form {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
  }

  input[type=number] {
    width: 78px;
    padding: 9px 10px;
    border: 1.5px solid #d7e3da;
    border-radius: 8px;
    font-size: 14px;
    background: #fafffb;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    -moz-appearance: textfield;
  }

  input[type=number]:focus {
    outline: none;
    border-color: var(--primary-green);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(30, 122, 52, 0.12);
  }

  input[type=number]::-webkit-outer-spin-button,
  input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  button {
    padding: 9px 18px;
    border: none;
    border-radius: 8px;
    background: var(--primary-green);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
    box-shadow: 0 1px 3px rgba(30, 122, 52, 0.25);
  }

  button:hover {
    background: var(--primary-green-dark);
    box-shadow: 0 3px 8px rgba(30, 122, 52, 0.3);
  }

  button:active {
    transform: translateY(1px);
    box-shadow: 0 1px 2px rgba(30, 122, 52, 0.25);
  }

  /* ---------- MESSAGE ---------- */
  .message {
    background: #fff8e1;
    color: #8a6d1a;
    padding: 12px 18px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-size: 14px;
    border-left: 4px solid #f0b429;
    box-shadow: 0 1px 3px rgba(138, 109, 26, 0.08);
  }

  /* ---------- EMPTY STATE ---------- */
  .empty {
    text-align: center;
    color: var(--text-muted);
    padding: 56px 24px;
    background: #fff;
    border-radius: var(--radius);
    border: 2px dashed #cfe3d4;
    font-size: 14px;
  }

  /* ---------- RESPONSIVE ---------- */
  @media (max-width: 600px) {
    header {
      padding: 14px 16px;
    }

    header h1 {
      font-size: 15px;
    }

    header a {
      font-size: 12px;
      padding: 6px 10px;
    }

    .container {
      padding: 16px;
    }

    .card {
      flex-direction: column;
      align-items: stretch;
      gap: 12px;
    }

    .sell-form {
      justify-content: flex-end;
    }

    input[type=number] {
      flex: 1;
      width: auto;
    }
  }
</style>
</head>
<body>

<header>
  <h1>Hi, <?= htmlspecialchars($employee['name']) ?> 👋</h1>
  <div>
    <a href="manage_items.php">Manage Items</a>
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