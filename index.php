<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Fetch all items
$items_query = $conn->query("SELECT * FROM items WHERE stock > 0 ORDER BY category, name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>POS - Roohi Pan</title>
    <link rel="stylesheet" href="style.css">
    <script>
    let cart = [];

    function addToCart(id, name, price) {
        let existing = cart.find(item => item.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            cart.push({ id: id, name: name, price: price, quantity: 1 });
        }
        updateCartUI();
    }

    function removeFromCart(index) {
        // Remove the item from the cart array
        cart.splice(index, 1);
        // Re-render the cart
        updateCartUI();
    }

    function updateCartUI() {
        let cartContainer = document.getElementById('cart-items');
        let totalDisplay = document.getElementById('total');
        cartContainer.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            cartContainer.innerHTML = '<p style="color:#888;">Tap items to add to the order.</p>';
            totalDisplay.innerText = 'KSh 0';
            return;
        }

        cart.forEach((item, index) => {
            let itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            // Added the remove button ("X") here
            cartContainer.innerHTML += `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:5px;">
                    <span style="flex: 1;">${item.name} x${item.quantity}</span>
                    <span style="margin-right: 15px; font-weight: bold;">KSh ${itemTotal}</span>
                    <button onclick="removeFromCart(${index})" style="background: #ff4d4d; color: white; border: none; border-radius: 4px; cursor: pointer; padding: 3px 8px; font-weight: bold;">X</button>
                </div>
            `;
        });
        totalDisplay.innerText = 'KSh ' + total;
    }

    function placeOrder() {
        if (cart.length === 0) { alert('Cart is empty!'); return; }
        let orderType = document.getElementById('order_type').value;
        let tableNo = document.getElementById('table_no').value;
        let customer = document.getElementById('customer').value;

        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'process_order.php';

        let data = {
            order_type: orderType,
            table_no: tableNo,
            customer_name: customer,
            cart: cart
        };

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'order_data';
        input.value = JSON.stringify(data);
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
    </script>
</head>
<body>
        <div class="sidebar">
        <h2>Roohi Pan</h2>
        <a href="index.php">New Order</a>
        <a href="reports.php">Reports</a>
        <?php if($_SESSION['role'] == 'owner'): ?>
            <a href="staff.php">Staff</a>
            <a href="inventory.php">Inventory</a> <!-- ADD THIS LINE -->
        <?php endif; ?>
        <a href="logout.php" style="margin-top:auto;">Sign Out</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>New Order</h2>
        </div>
        
        <div style="display: flex; gap: 20px;">
            <!-- Menu Section -->
            <div style="flex: 3;">
                <div class="grid-container">
                    <?php 
                    $current_category = '';
                    while($item = $items_query->fetch_assoc()): 
                        // Check if the category has changed to print a new header
                        if ($current_category != $item['category']) {
                            $current_category = $item['category'];
                            echo "<h3 style='grid-column: 1 / -1; margin-top: 25px; margin-bottom: 5px; color: #0d2b1f; border-bottom: 2px solid #a3e635; padding-bottom: 5px;'>" . htmlspecialchars($current_category) . "</h3>";
                        }
                    ?>
                        <!-- Stock display is removed below -->
                        <div class="item-card" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $item['name']; ?>', <?php echo $item['price']; ?>)">
                            <h4><?php echo $item['name']; ?></h4>
                            <p class="price">KSh <?php echo $item['price']; ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Cart Section -->
            <div style="flex: 1; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); height: fit-content; position: sticky; top: 20px;">
                <h3 style="margin-bottom:15px; color:#0d2b1f;">Current Order</h3>
                
                <label>Order Type:</label>
                <select id="order_type">
                    <option value="Dine-in">Dine-in</option>
                    <option value="Delivery">Delivery</option>
                </select>

                <input type="text" id="table_no" placeholder="Table No. (Optional)">
                <input type="text" id="customer" placeholder="Customer Name (Optional)">

                <hr style="margin: 15px 0;">
                
                <div id="cart-items" style="max-height: 300px; overflow-y: auto;">
                    <p style="color:#888;">Tap items to add to the order.</p>
                </div>

                <hr style="margin: 15px 0;">
                <div style="display:flex; justify-content:space-between; font-size:1.2rem; font-weight:bold; margin-bottom:15px;">
                    <span>Total</span>
                    <span id="total">KSh 0</span>
                </div>

                <button onclick="placeOrder()" class="btn" style="width:100%; padding:15px;">Place Order & Print</button>
            </div>
        </div>
    </div>
</body>
</html>