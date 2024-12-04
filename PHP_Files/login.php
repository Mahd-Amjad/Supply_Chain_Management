<?php
require_once 'config.php';

// Replace with your actual username and password verification logic
// For demonstration, using hardcoded credentials.
// In production, fetch user data from a Users table with hashed passwords.

$valid_username = "sqlpad";
$valid_password = "abc"; // In production, use hashed passwords

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Simple authentication check
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: ../HTML_Files/index.html");
        exit;
    } else {
        echo "Invalid credentials.";
        echo "<br><a href='../HTML_Files/login.html'>Back to Login</a>";
    }
} else {
    echo "Invalid request method.";
}
?>
