
<?php
// Database connection settings
$host = "localhost";
$port = 3306;          // Change this if your MySQL port is different
$username = "root";    // Default XAMPP username
$password = "";        // Default XAMPP password (blank)
$database = "Roohi_pan";

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to avoid encoding issues with names/text
$conn->set_charset("utf8mb4");
?>