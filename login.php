<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        }
    }
    $error = "Invalid credentials!";
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Roohi Pan</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="auth-card">
        <h2 style="color: #0d2b1f;">Roohi Pan House</h2>
        <p>Login to your account</p>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <p style="margin-top:15px;"><a href="register.php" style="color: #0d2b1f;">Register here</a></p>
    </div>
</body>
</html>