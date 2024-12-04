<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $retailer_id = intval($_POST['retailer_id']);
    $retailer_name = trim($_POST['retailer_name']);
    $retailer_location = trim($_POST['retailer_location']);
    $warehouse_capacity = isset($_POST['warehouse_capacity']) && $_POST['warehouse_capacity'] !== '' ? intval($_POST['warehouse_capacity']) : null;
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($retailer_name !== '') {
        $fields[] = "retailer_name = ?";
        $params[] = &$retailer_name;
    }
    if ($retailer_location !== '') {
        $fields[] = "retailer_location = ?";
        $params[] = &$retailer_location;
    }
    if ($warehouse_capacity !== null) {
        $fields[] = "warehouse_capacity = ?";
        $params[] = &$warehouse_capacity;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Retailer SET " . implode(", ", $fields) . " WHERE retailer_id = ?";
    $params[] = &$retailer_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Retailer updated successfully.";
        } else {
            echo "No retailer found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/retailer/update.html">Back to Update Retailer</a>
