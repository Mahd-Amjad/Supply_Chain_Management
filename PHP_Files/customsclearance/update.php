<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clearance_id = intval($_POST['clearance_id']);
    $custom_status = trim($_POST['custom_status']);
    $clearance_date = $_POST['clearance_date'] !== '' ? $_POST['clearance_date'] : null;
    $documents = trim($_POST['documents']);
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($custom_status !== '') {
        $fields[] = "custom_status = ?";
        $params[] = &$custom_status;
    }
    if ($clearance_date !== null) {
        $fields[] = "clearance_date = ?";
        $params[] = &$clearance_date;
    }
    if ($documents !== '') {
        $fields[] = "documents = ?";
        $params[] = &$documents;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE CustomsClearance SET " . implode(", ", $fields) . " WHERE clearance_id = ?";
    $params[] = &$clearance_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Customer Clearance updated successfully.";
        } else {
            echo "No customer clearance found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/customerclearance/update.html">Back to Update Customer Clearance</a>
