<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $production_id = intval($_POST['production_id']);
    $item_type = trim($_POST['item_type']);
    $start_date = $_POST['start_date'];
    $status = trim($_POST['status']);
    $order_id = intval($_POST['order_id']);
    
    $fields = [];
    $params = [];
    
    if ($item_type !== '') {
        $fields[] = "item_type = ?";
        $params[] = &$item_type;
    }
    if ($start_date !== '') {
        $fields[] = "start_date = ?";
        $params[] = &$start_date;
    }
    if ($status !== '') {
        $fields[] = "status = ?";
        $params[] = &$status;
    }
    if ($order_id !== 0) {
        $fields[] = "order_id = ?";
        $params[] = &$order_id;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Production SET " . implode(", ", $fields) . " WHERE production_id = ?";
    $params[] = &$production_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Production updated successfully.";
        } else {
            echo "No production found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/production/update.html">Back to Update Production</a>
