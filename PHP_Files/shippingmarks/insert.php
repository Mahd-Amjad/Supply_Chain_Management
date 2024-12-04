<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipping_id = intval($_POST['shipping_id']);
    $shipment_id = intval($_POST['shipment_id']);
    $shipping_marks = trim($_POST['shipping_marks']);
    
    $sql = "INSERT INTO ShippingMarks (shipping_id, shipment_id, shipping_marks) VALUES (?, ?, ?)";
    $params = array(&$shipping_id, &$shipment_id, &$shipping_marks);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Shipping Marks inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/shippingmarks/insert.html">Back to Insert Shipping Marks</a>
