<?php
require 'db.php';

// Fetch active employees for the dropdown
$result = $conn->query("SELECT id, name FROM employees WHERE active = 1 ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Roohi Pan House - Login</title>
<style>
  :root {
    --primary-green: #1e7a34;
    --light-green: #e8f5e9;
    --white: #ffffff;
  }

  * { box-sizing: border-box; font-family: Arial, sans-serif; margin: 0; padding: 0; }

  body {
    background: linear-gradient(to bottom, #1e7a34 0%, #e8f5e9 55%, #ffffff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }

  .login-card {
    background: var(--white);
    border-radius: 14px;
    padding: 40px 36px;
    width: 340px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.1);
    text-align: center;
  }

  .logo {
    width: 90px;
    height: 90px;
    margin: 0 auto 16px auto;
    display: block;
  }

  h1 {
    color: var(--primary-green);
    font-size: 20px;
    margin-bottom: 4px;
  }

  p.subtitle {
    color: #666;
    font-size: 13px;
    margin-bottom: 24px;
  }

  select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 16px;
  }

  button {
    width: 100%;
    padding: 10px;
    background: var(--primary-green);
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
  }

  button:hover {
    background: #16602a;
  }

  .error-msg {
    color: #c0392b;
    font-size: 13px;
    margin-top: 10px;
  }
</style>
</head>
<body>

  <div class="login-card">
    <!-- Replace logo.png with the actual Roohi Pan House logo file -->
    <img src="logo.png" alt="Roohi Pan House Logo" class="logo" />
    <h1>Roohi Pan House</h1>
    <p class="subtitle">Select your name to continue</p>

    <form action="dashboard.php" method="POST">
      <select name="employee_id" required>
        <option value="" disabled selected>-- Choose your name --</option>
        <?php while ($row = $result->fetch_assoc()): ?>
          <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit">Continue</button>
    </form>
  </div>

</body>
</html>