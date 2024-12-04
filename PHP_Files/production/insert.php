<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $production_id = intval($_POST['production_id']);
    $item_type = trim($_POST['item_type']);
    $start_date = $_POST['start_date'];
    $status = trim($_POST['status']);
    $order_id = intval($_POST['order_id']);
    
    // Prepare the SQL INSERT statement
    $sql = "INSERT INTO Production (production_id, item_type, start_date, status, order_id) VALUES (?, ?, ?, ?, ?)";
    $params = array(&$production_id, &$item_type, &$start_date, &$status, &$order_id);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Production inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/production/insert.html">Back to Insert Production</a>
