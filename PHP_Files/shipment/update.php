<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shipment_id = intval($_POST['shipment_id']);
    $shipment_date = $_POST['shipment_date'] !== '' ? $_POST['shipment_date'] : null;
    $tracking_status = trim($_POST['tracking_status']);
    $retailer_id = isset($_POST['retailer_id']) && $_POST['retailer_id'] !== '' ? intval($_POST['retailer_id']) : null;
    $warehouse_id = isset($_POST['warehouse_id']) && $_POST['warehouse_id'] !== '' ? intval($_POST['warehouse_id']) : null;
    $delivery_to_retailer_status = trim($_POST['delivery_to_retailer_status']);
    $delivery_to_customer_status = trim($_POST['delivery_to_customer_status']);
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($shipment_date !== null) {
        $fields[] = "shipment_date = ?";
        $params[] = &$shipment_date;
    }
    if ($tracking_status !== '') {
        $fields[] = "tracking_status = ?";
        $params[] = &$tracking_status;
    }
    if ($retailer_id !== null) {
        $fields[] = "retailer_id = ?";
        $params[] = &$retailer_id;
    }
    if ($warehouse_id !== null) {
        $fields[] = "warehouse_id = ?";
        $params[] = &$warehouse_id;
    }
    if ($delivery_to_retailer_status !== '') {
        $fields[] = "delivery_to_retailer_status = ?";
        $params[] = &$delivery_to_retailer_status;
    }
    if ($delivery_to_customer_status !== '') {
        $fields[] = "delivery_to_customer_status = ?";
        $params[] = &$delivery_to_customer_status;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Shipment SET " . implode(", ", $fields) . " WHERE shipment_id = ?";
    $params[] = &$shipment_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Shipment updated successfully.";
        } else {
            echo "No shipment found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/shipment/update.html">Back to Update Shipment</a>
