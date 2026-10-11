<?php
require 'db.php';

// Security: Only allow Owners
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') { 
    header("Location: index.php"); 
    exit; 
}

// Handle Add New Item
if (isset($_POST['add_item'])) {
    $name = $_POST['item_name'];
    $qty = $_POST['quantity'];
    if (!empty($name) && $qty >= 0) {
        $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $qty);
        $stmt->execute();
    }
    header("Location: inventory.php");
    exit;
}

// Handle Delete Item
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM inventory WHERE id = $id");
    header("Location: inventory.php");
    exit;
}

// Handle Add/Subtract Quantity
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'add') {
        $conn->query("UPDATE inventory SET quantity = quantity + 1 WHERE id = $id");
    } elseif ($_GET['action'] == 'sub') {
        // Prevent quantity from going below 0
        $conn->query("UPDATE inventory SET quantity = quantity - 1 WHERE id = $id AND quantity > 0");
    }
    header("Location: inventory.php");
    exit;
}

// Fetch all inventory items
$inventory = $conn->query("SELECT * FROM inventory ORDER BY item_name ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory - Roohi Pan</title>
    <link rel="stylesheet" href="style.css">
        <style>
        /* Specific styles for the inventory cards - Softer, muted colors */
        .inv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .inv-card {
            background: #fcfdfc; /* Soft off-white */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.03); /* Very soft shadow */
            border: 1px solid #e0e8e0; /* Soft border */
            text-align: center;
            position: relative;
            transition: transform 0.2s;
        }
        .inv-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.06);
        }
        .inv-card h4 {
            color: #4a5a4a; /* Muted dark green/gray */
            margin-bottom: 8px;
            font-size: 1.05rem;
            font-weight: 600;
        }
        .inv-card .qty {
            font-size: 2.2rem;
            font-weight: 700;
            color: #3b5e3b; /* Softer, deeper green */
            margin-bottom: 15px;
        }
        .inv-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        .btn-sm {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }
        .btn-add { 
            background-color: #e8f5e9; /* Soft pastel green */
            color: #2e7d32; 
        }
        .btn-add:hover { background-color: #c8e6c9; }
        
        .btn-sub { 
            background-color: #f1f5f1; /* Soft gray-green */
            color: #4a5a4a; 
        }
        .btn-sub:hover { background-color: #e2e8e2; }
        
        .btn-del { 
            background-color: #fdecea; /* Soft faded red */
            color: #c62828; 
            padding: 6px 10px; 
        }
        .btn-del:hover { background-color: #f8d7da; }
        
        /* Print specific styles */
        @media print {
            .sidebar, .no-print, .add-form {
                display: none !important;
            }
            .main-content {
                margin: 0;
                padding: 0;
            }
            .inv-card {
                box-shadow: none;
                border: 1px solid #ccc;
                background: white;
            }
            body { background-color: white; }
        }
    </style>
</head>
<body>
    <!-- Sidebar (Updated to include Inventory) -->
    <div class="sidebar">
        <h2>Roohi Pan</h2>
        <a href="index.php">New Order</a>
        <a href="reports.php">Reports</a>
        <?php if($_SESSION['role'] == 'owner'): ?>
            <a href="staff.php">Staff</a>
            <a href="inventory.php" class="active">Inventory</a>
        <?php endif; ?>
        <a href="logout.php" style="margin-top:auto;">Sign Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Inventory Tracker</h2>
            <button class="btn no-print" onclick="window.print()">Print Inventory</button>
        </div>

        <!-- Add New Stock Form -->
        <div class="add-form" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; color: #0d2b1f;">Add New Stock Item</h3>
            <form method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
                <div style="flex: 2;">
                    <label style="font-size: 0.9rem; color: #666;">Item Name (e.g., Potatoes, Cooking Oil)</label>
                    <input type="text" name="item_name" placeholder="Enter item name" required style="margin-top: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 0.9rem; color: #666;">Initial Quantity</label>
                    <input type="number" name="quantity" value="1" min="0" required style="margin-top: 5px;">
                </div>
                <div>
                    <button type="submit" name="add_item" class="btn" style="padding: 10px 20px;">Add to Inventory</button>
                </div>
            </form>
        </div>

        <!-- Inventory Grid -->
        <?php if($inventory->num_rows > 0): ?>
            <div class="inv-grid">
                <?php while($item = $inventory->fetch_assoc()): ?>
                    <div class="inv-card">
                        <h4><?php echo htmlspecialchars($item['item_name']); ?></h4>
                        <div class="qty"><?php echo $item['quantity']; ?></div>
                        
                        <div class="inv-actions no-print">
                            <!-- Subtract Button -->
                            <a href="?action=sub&id=<?php echo $item['id']; ?>" class="btn-sm btn-sub">-</a>
                            <!-- Add Button -->
                            <a href="?action=add&id=<?php echo $item['id']; ?>" class="btn-sm btn-add">+</a>
                            <!-- Remove Item Button -->
                            <a href="?delete=<?php echo $item['id']; ?>" class="btn-sm btn-del" onclick="return confirm('Remove this item completely?');">X</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; color: #888;">
                <h3>No inventory tracked yet.</h3>
                <p>Use the form above to start adding your raw stock.</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>