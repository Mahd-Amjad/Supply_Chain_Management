<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_id = intval($_POST['payment_id']);
    $order_id = isset($_POST['order_id']) && $_POST['order_id'] !== '' ? intval($_POST['order_id']) : null;
    $payment_date = $_POST['payment_date'] !== '' ? $_POST['payment_date'] : null;
    $payment_amount = isset($_POST['payment_amount']) && $_POST['payment_amount'] !== '' ? floatval($_POST['payment_amount']) : null;
    $payment_method = trim($_POST['payment_method']);
    $status = trim($_POST['status']);
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($order_id !== null) {
        $fields[] = "order_id = ?";
        $params[] = &$order_id;
    }
    if ($payment_date !== null) {
        $fields[] = "payment_date = ?";
        $params[] = &$payment_date;
    }
    if ($payment_amount !== null) {
        $fields[] = "payment_amount = ?";
        $params[] = &$payment_amount;
    }
    if ($payment_method !== '') {
        $fields[] = "payment_method = ?";
        $params[] = &$payment_method;
    }
    if ($status !== '') {
        $fields[] = "status = ?";
        $params[] = &$status;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Payment SET " . implode(", ", $fields) . " WHERE payment_id = ?";
    $params[] = &$payment_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Payment updated successfully.";
        } else {
            echo "No payment found with the provided ID or no changes made.";
        }
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/payment/update.html">Back to Update Payment</a>
