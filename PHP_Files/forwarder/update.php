<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $forwarder_id = intval($_POST['forwarder_id']);
    $name = trim($_POST['name']);
    $warehouse_location = trim($_POST['warehouse_location']);
    
    $fields = [];
    $params = [];
    
    if ($name !== '') {
        $fields[] = "name = ?";
        $params[] = &$name;
    }
    if ($warehouse_location !== '') {
        $fields[] = "warehouse_location = ?";
        $params[] = &$warehouse_location;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Forwarder SET " . implode(", ", $fields) . " WHERE forwarder_id = ?";
    $params[] = &$forwarder_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Forwarder updated successfully.";
        } else {
            echo "No forwarder found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/forwarder/update.html">Back to Update Forwarder</a>
