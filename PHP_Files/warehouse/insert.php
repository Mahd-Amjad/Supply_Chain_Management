<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $inventory_id = intval($_POST['inventory_id']);
    $item_type = trim($_POST['item_type']);
    $quantity = intval($_POST['quantity']);
    $warehouse_id = intval($_POST['warehouse_id']);
    $product_id = intval($_POST['product_id']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Inventory (inventory_id, item_type, quantity, warehouse_id, product_id) VALUES (?, ?, ?, ?, ?)";
    $params = array(&$inventory_id, &$item_type, &$quantity, &$warehouse_id, &$product_id);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Inventory record inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/inventory/insert.html">Back to Insert Inventory</a>
