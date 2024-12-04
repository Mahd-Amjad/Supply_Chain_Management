<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];

    if ($customer_name !== '') {
        $fields[] = "customer_name = ?";
        $params[] = &$customer_name;
    }
    if ($customer_address !== '') {
        $fields[] = "customer_address = ?";
        $params[] = &$customer_address;
    }
    if ($email !== '') {
        $fields[] = "email = ?";
        $params[] = &$email;
    }
    if ($phone !== '') {
        $fields[] = "phone = ?";
        $params[] = &$phone;
    }

    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }

    $sql = "UPDATE Customer SET " . implode(", ", $fields) . " WHERE customer_id = ?";
    $params[] = &$customer_id;

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing statement.";
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Customer updated successfully.";
        } else {
            echo "No customer found with the provided ID or no changes made.";
        }
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
<a href="../../HTML_Files/customer/update.html">Back to Update Customer</a>
