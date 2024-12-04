<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $forwarder_id = intval($_POST['forwarder_id']);
    $name = trim($_POST['name']);
    $warehouse_location = trim($_POST['warehouse_location']);
    
    // Prepare the SQL INSERT statement
    $sql = "INSERT INTO Forwarder (forwarder_id, name, warehouse_location) VALUES (?, ?, ?)";
    $params = array(&$forwarder_id, &$name, &$warehouse_location);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Forwarder inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/forwarder/insert.html">Back to Insert Forwarder</a>
