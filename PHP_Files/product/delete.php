<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);

    // Check if product is referenced in other tables to prevent foreign key constraint errors
    $referenceCheckSql = "SELECT COUNT(*) as count FROM [Order] WHERE product_id = ?";
    $referenceCheckStmt = sqlsrv_prepare($conn, $referenceCheckSql, array(&$product_id));

    if (!$referenceCheckStmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing reference check statement.";
        exit;
    }

    if (sqlsrv_execute($referenceCheckStmt)) {
        $result = sqlsrv_fetch_array($referenceCheckStmt, SQLSRV_FETCH_ASSOC);
        if ($result['count'] > 0) {
            echo "Cannot delete product. It is referenced in the Order table.";
            sqlsrv_free_stmt($referenceCheckStmt);
            exit;
        }
    } else {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in executing reference check statement.";
        sqlsrv_free_stmt($referenceCheckStmt);
        exit;
    }

    sqlsrv_free_stmt($referenceCheckStmt);

    // Prepare the SQL DELETE statement
    $sql = "DELETE FROM [Product] WHERE product_id = ?";
    $params = array(&$product_id);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing statement.";
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Product deleted successfully.";
        } else {
            echo "No product found with the provided ID.";
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
<a href="../../HTML_Files/product/delete.html">Back to Delete Product</a>
