<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $clearance_id = intval($_POST['clearance_id']);
    $shipment_id = intval($_POST['shipment_id']);
    $custom_status = trim($_POST['custom_status']);
    $clearance_date = $_POST['clearance_date'];
    $documents = trim($_POST['documents']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO CustomsClearance (clearance_id, shipment_id, custom_status, clearance_date, documents) VALUES (?, ?, ?, ?, ?)";
    $params = array(&$clearance_id, &$shipment_id, &$custom_status, &$clearance_date, &$documents);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Customer Clearance inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/customerclearance/insert.html">Back to Insert Customer Clearance</a>
