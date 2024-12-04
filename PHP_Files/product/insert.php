<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $product_id = intval($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $weight = floatval($_POST['weight']);
    $dimensions = trim($_POST['dimensions']);
    $supplier_id = intval($_POST['supplier_id']);

    // Validate supplier_id exists
    $supplierCheckSql = "SELECT COUNT(*) as count FROM Supplier WHERE supplier_id = ?";
    $supplierCheckStmt = sqlsrv_prepare($conn, $supplierCheckSql, array(&$supplier_id));

    if (!$supplierCheckStmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing supplier validation statement.";
        exit;
    }

    if (sqlsrv_execute($supplierCheckStmt)) {
        $result = sqlsrv_fetch_array($supplierCheckStmt, SQLSRV_FETCH_ASSOC);
        if ($result['count'] == 0) {
            echo "Invalid Supplier ID.";
            sqlsrv_free_stmt($supplierCheckStmt);
            exit;
        }
    } else {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in executing supplier validation statement.";
        sqlsrv_free_stmt($supplierCheckStmt);
        exit;
    }

    sqlsrv_free_stmt($supplierCheckStmt);

    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO [Product] (product_id, product_name, description, category, weight, dimensions, supplier_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = array(&$product_id, &$product_name, &$description, &$category, &$weight, &$dimensions, &$supplier_id);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing statement.";
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        echo "Product inserted successfully.";
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
<a href="../../HTML_Files/product/insert.html">Back to Insert Product</a>
