<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $payment_id = intval($_POST['payment_id']);
    $order_id = intval($_POST['order_id']);
    $payment_date = $_POST['payment_date'] !== '' ? $_POST['payment_date'] : null;
    $payment_amount = floatval($_POST['payment_amount']);
    $payment_method = trim($_POST['payment_method']);
    $status = trim($_POST['status']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Payment (payment_id, order_id, payment_date, payment_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?)";
    $params = array(&$payment_id, &$order_id, &$payment_date, &$payment_amount, &$payment_method, &$status);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Payment inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/payment/insert.html">Back to Insert Payment</a>
