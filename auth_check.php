<?php
// Include this at the very top of any page you want password-protected.
// Usage: require 'auth_check.php';

session_start();
require_once 'db.php';

// Change this to whatever shared admin password you want
define('ADMIN_PASSWORD', 'roohi2026');

// Must be logged in (picked a name on index.php) before even seeing the password prompt
if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// If already verified this session, let them through without asking again
if (isset($_SESSION['admin_verified']) && $_SESSION['admin_verified'] === true) {
    return; // continue loading the page that included this file
}

$error = '';

// Handle password submission
if (isset($_POST['admin_password'])) {
    if ($_POST['admin_password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_verified'] = true;

        // Log this access: who, when, which page
        $log = $conn->prepare("INSERT INTO admin_access_log (employee_id, page_accessed) VALUES (?, ?)");
        $log->bind_param("is", $employee_id, $current_page);
        $log->execute();

        return; // continue loading the page that included this file
    } else {
        $error = "Incorrect password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Access Required</title>
<style>
  * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }
  body { background: #e8f5e9; display: flex; align-items: center; justify-content: center; height: 100vh; }
  .box { background: #fff; padding: 32px; border-radius: 12px; width: 300px; text-align: center; box-shadow: 0 4px 14px rgba(0,0,0,0.1); }
  h2 { color: #1e7a34; margin-bottom: 14px; font-size: 18px; }
  input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 12px; }
  button { width: 100%; padding: 10px; background: #1e7a34; color: #fff; border: none; border-radius: 8px; cursor: pointer; }
  .error { color: #c0392b; font-size: 13px; margin-bottom: 10px; }
  a { display: block; margin-top: 14px; font-size: 13px; color: #1e7a34; }
</style>
</head>
<body>
  <div class="box">
    <h2>Admin Access Required</h2>
    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
      <input type="password" name="admin_password" placeholder="Enter admin password" required>
      <button type="submit">Unlock</button>
    </form>
    <a href="dashboard.php">← Back to Dashboard</a>
  </div>
</body>
</html>
<?php
exit; // stop the including page from loading further until password is correct