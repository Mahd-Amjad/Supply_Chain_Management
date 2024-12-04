<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = intval($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $weight = $_POST['weight'] !== '' ? floatval($_POST['weight']) : null;
    $dimensions = trim($_POST['dimensions']);
    $supplier_id = $_POST['supplier_id'] !== '' ? intval($_POST['supplier_id']) : null;

    // Validate supplier_id if provided
    if ($supplier_id !== null) {
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
    }

    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];

    if ($product_name !== '') {
        $fields[] = "product_name = ?";
        $params[] = &$product_name;
    }
    if ($description !== '') {
        $fields[] = "description = ?";
        $params[] = &$description;
    }
    if ($category !== '') {
        $fields[] = "category = ?";
        $params[] = &$category;
    }
    if ($weight !== null) {
        $fields[] = "weight = ?";
        $params[] = &$weight;
    }
    if ($dimensions !== '') {
        $fields[] = "dimensions = ?";
        $params[] = &$dimensions;
    }
    if ($supplier_id !== null) {
        $fields[] = "supplier_id = ?";
        $params[] = &$supplier_id;
    }

    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }

    $sql = "UPDATE [Product] SET " . implode(", ", $fields) . " WHERE product_id = ?";
    $params[] = &$product_id;

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in preparing statement.";
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Product updated successfully.";
        } else {
            echo "No product found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/product/update.html">Back to Update Product</a>
