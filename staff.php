<?php
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') { header("Location: index.php"); exit; }

// Handle Remove Employee
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if ($id != $_SESSION['user_id']) { // Prevent self-deletion
        $conn->query("DELETE FROM users WHERE id = $id");
        header("Location: staff.php");
        exit;
    }
}

// Handle Promote to Owner
if (isset($_GET['promote'])) {
    $id = $_GET['promote'];
    $conn->query("UPDATE users SET role = 'owner' WHERE id = $id");
    header("Location: staff.php");
    exit;
}

$users = $conn->query("SELECT * FROM users ORDER BY role ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff - Roohi Pan</title>
    <link rel="stylesheet" href="style.css">
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
            <h2>Staff Management</h2>
        </div>
        <p style="margin-bottom: 20px; color: #666;">New team members sign up on the sign-in page. You can promote them here or remove them.</p>

        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f4f7f4; text-align: left;">
                    <th style="padding: 15px;">Name</th>
                    <th style="padding: 15px;">Role</th>
                    <th style="padding: 15px;">Actions</th>
                </tr>
                <?php while($user = $users->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;">
                        <strong><?php echo $user['username']; ?></strong><br>
                        <small style="color:#888;">ID: <?php echo $user['id']; ?></small>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background: <?php echo $user['role'] == 'owner' ? '#a3e635' : '#eee'; ?>; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: bold;">
                            <?php echo strtoupper($user['role']); ?>
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <?php if ($user['role'] == 'employee'): ?>
                                <a href="?promote=<?php echo $user['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Promote to Owner</a>
                            <?php endif; ?>
                            <a href="?remove=<?php echo $user['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to remove this user?');">Remove</a>
                        <?php else: ?>
                            <em style="color:#888;">Current User</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>