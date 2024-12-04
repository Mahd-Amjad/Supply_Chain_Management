<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clearance_id = intval($_POST['clearance_id']);
    
    // Prepare the SQL DELETE statement
    $sql = "DELETE FROM CustomsClearance WHERE clearance_id = ?";
    $params = array(&$clearance_id);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Customer Clearance deleted successfully.";
        } else {
            echo "No customer clearance found with the provided ID.";
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
<a href="../../HTML_Files/customerclearance/delete.html">Back to Delete Customer Clearance</a>
