<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $retailer_id = intval($_POST['retailer_id']);
    $retailer_name = trim($_POST['retailer_name']);
    $retailer_location = trim($_POST['retailer_location']);
    $warehouse_capacity = intval($_POST['warehouse_capacity']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Retailer (retailer_id, retailer_name, retailer_location, warehouse_capacity) VALUES (?, ?, ?, ?)";
    $params = array(&$retailer_id, &$retailer_name, &$retailer_location, &$warehouse_capacity);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Retailer inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/retailer/insert.html">Back to Insert Retailer</a>
