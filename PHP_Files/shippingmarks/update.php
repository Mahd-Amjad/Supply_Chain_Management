<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_id = intval($_POST['shipping_id']);
    $shipping_marks = trim($_POST['shipping_marks']);
    
    $sql = "UPDATE ShippingMarks SET shipping_marks = ? WHERE shipping_id = ?";
    $params = array(&$shipping_marks, &$shipping_id);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Shipping Marks updated successfully.";
        } else {
            echo "No shipping marks found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/shippingmarks/update.html">Back to Update Shipping Marks</a>
