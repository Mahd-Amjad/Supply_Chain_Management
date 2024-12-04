<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = intval($_POST['customer_id']);

    // Prepare the SQL DELETE statement
    $sql = "DELETE FROM Customer WHERE customer_id = ?";
    $params = array(&$customer_id);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing statement.";
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Customer deleted successfully.";
        } else {
            echo "No customer found with the provided ID.";
        }
    } else {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in executing statement.";
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/customer/delete.html">Back to Delete Customer</a>
