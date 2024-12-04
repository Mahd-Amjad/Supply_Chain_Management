<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $customer_id = intval($_POST['customer_id']);
    $customer_name = trim($_POST['customer_name']);
    $customer_address = trim($_POST['customer_address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validate email if provided
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Customer (customer_id, customer_name, customer_address, email, phone) VALUES (?, ?, ?, ?, ?)";
    $params = array(&$customer_id, &$customer_name, &$customer_address, &$email, &$phone);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing statement.";
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        echo "Customer inserted successfully.";
    } else {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in executing statement.";
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML Files/customer/insert.html">Back to Insert Customer</a>
