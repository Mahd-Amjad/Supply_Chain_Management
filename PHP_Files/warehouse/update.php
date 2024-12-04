<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inventory_id = intval($_POST['inventory_id']);
    $item_type = trim($_POST['item_type']);
    $quantity = isset($_POST['quantity']) && $_POST['quantity'] !== '' ? intval($_POST['quantity']) : null;
    $warehouse_id = isset($_POST['warehouse_id']) && $_POST['warehouse_id'] !== '' ? intval($_POST['warehouse_id']) : null;
    $product_id = isset($_POST['product_id']) && $_POST['product_id'] !== '' ? intval($_POST['product_id']) : null;
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($item_type !== '') {
        $fields[] = "item_type = ?";
        $params[] = &$item_type;
    }
    if ($quantity !== null) {
        $fields[] = "quantity = ?";
        $params[] = &$quantity;
    }
    if ($warehouse_id !== null) {
        $fields[] = "warehouse_id = ?";
        $params[] = &$warehouse_id;
    }
    if ($product_id !== null) {
        $fields[] = "product_id = ?";
        $params[] = &$product_id;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Inventory SET " . implode(", ", $fields) . " WHERE inventory_id = ?";
    $params[] = &$inventory_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Inventory record updated successfully.";
        } else {
            echo "No inventory record found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/inventory/update.html">Back to Update Inventory</a>
