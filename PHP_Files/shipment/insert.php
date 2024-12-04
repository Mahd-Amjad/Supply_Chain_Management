<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $shipment_id = intval($_POST['shipment_id']);
    $shipment_date = $_POST['shipment_date'] !== '' ? $_POST['shipment_date'] : null;
    $tracking_status = trim($_POST['tracking_status']);
    $retailer_id = intval($_POST['retailer_id']);
    $warehouse_id = intval($_POST['warehouse_id']);
    $delivery_to_retailer_status = trim($_POST['delivery_to_retailer_status']);
    $delivery_to_customer_status = trim($_POST['delivery_to_customer_status']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Shipment (shipment_id, shipment_date, tracking_status, retailer_id, warehouse_id, delivery_to_retailer_status, delivery_to_customer_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = array(&$shipment_id, &$shipment_date, &$tracking_status, &$retailer_id, &$warehouse_id, &$delivery_to_retailer_status, &$delivery_to_customer_status);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Shipment inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/shipment/insert.html">Back to Insert Shipment</a>
