<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
    } else {
        $error = "Username already exists!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register - Roohi Pan</title><link rel="stylesheet" href="style.css"></head>
<body>
    <div class="auth-card">
        <h2 style="color: #0d2b1f;">Roohi Pan House</h2>
        <p>Create an Account</p>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="employee">Employee</option>
                <option value="owner">Owner</option>
            </select>
            <button type="submit" class="btn">Register</button>
        </form>
        <p style="margin-top:15px;"><a href="login.php" style="color: #0d2b1f;">Already have an account? Login</a></p>
    </div>
</body>
</html>