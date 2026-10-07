<?php
require 'auth_check.php'; // password gate - adding items is an admin-level action

$message = '';

// Handle new item submission
if (isset($_POST['add_item'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $unit = trim($_POST['unit']);
    $price = floatval($_POST['price']);

    if ($name && $category && $unit && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO items (name, category, unit, price, active) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("sssd", $name, $category, $unit, $price);
        $stmt->execute();
        $message = "$name added successfully.";
    } else {
        $message = "Please fill in all fields correctly.";
    }
}

// Handle deactivating an item (soft delete, keeps sales history intact)
if (isset($_GET['deactivate'])) {
    $id = intval($_GET['deactivate']);
    $stmt = $conn->prepare("UPDATE items SET active = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_items.php?msg=" . urlencode("Item removed from active menu."));
    exit;
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// Get all active items to display in the list
$items = $conn->query("SELECT * FROM items WHERE active = 1 ORDER BY category ASC, name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Menu Items</title>
<style>
  :root { --primary-green: #1e7a34; --light-green: #e8f5e9; }
  * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
  body { background: var(--light-green); }
  header { background: var(--primary-green); color: #fff; padding: 16px 24px; display: flex; justify-content: space-between; }
  header a { color: #fff; text-decoration: none; font-size: 13px; background: rgba(255,255,255,0.15); padding: 6px 12px; border-radius: 6px; }
  .container { padding: 24px; max-width: 700px; margin: 0 auto; }
  .card { background: #fff; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
  input { padding: 9px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 10px; width: 100%; }
  button { padding: 10px 16px; background: var(--primary-green); color: #fff; border: none; border-radius: 6px; cursor: pointer; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { text-align: left; padding: 8px; border-bottom: 1px solid #eee; font-size: 14px; }
  .remove-link { color: #c0392b; font-size: 13px; text-decoration: none; }
  .message { background: #d4edda; color: #155724; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
</style>
</head>
<body>

<header>
  <h2>Manage Menu Items</h2>
  <a href="dashboard.php">← Back to Dashboard</a>
</header>

<div class="container">
  <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

  <div class="card">
    <h3>Add New Item</h3>
    <form method="POST">
      <input type="text" name="name" placeholder="Item name (e.g. Chapati)" required>
      <input type="text" name="category" placeholder="Category (e.g. Bread, Drinks)" required>
      <input type="text" name="unit" placeholder="Unit (e.g. pcs, cup, plate)" required>
      <input type="number" step="0.01" name="price" placeholder="Price per unit (KES)" required>
      <button type="submit" name="add_item">Add Item</button>
    </form>
  </div>

  <div class="card">
    <h3>Current Menu Items</h3>
    <table>
      <thead>
        <tr><th>Name</th><th>Category</th><th>Unit</th><th>Price</th><th></th></tr>
      </thead>
      <tbody>
        <?php if ($items->num_rows === 0): ?>
          <tr><td colspan="5">No items added yet.</td></tr>
        <?php else: ?>
          <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= htmlspecialchars($item['category']) ?></td>
              <td><?= htmlspecialchars($item['unit']) ?></td>
              <td>KES <?= number_format($item['price'], 2) ?></td>
              <td><a class="remove-link" href="manage_items.php?deactivate=<?= $item['id'] ?>" onclick="return confirm('Remove this item from the menu?')">Remove</a></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>