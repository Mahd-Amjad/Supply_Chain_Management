<?php
// PHP_Files/complex/inventory_management.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'low_stock':
            runLowStockQuery($conn);
            break;
        
        case 'update_inventory':
            $orderId = intval($_POST['order_id']);
            runUpdateInventoryQuery($conn, $orderId);
            break;
        
        case 'suggest_reorder':
            runSuggestReorderQuery($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runLowStockQuery($conn) {
    $sql = "
        SELECT p.product_name, i.quantity, w.warehouse_location 
        FROM Inventory i
        LEFT JOIN Product p ON i.product_id = p.product_id
        LEFT JOIN Warehouse w ON i.warehouse_id = w.warehouse_id
        WHERE i.quantity < 150;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Low Stock Items (Quantity < 150)</h3>";
    echo "<table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Warehouse Location</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['product_name']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['warehouse_location']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runUpdateInventoryQuery($conn, $orderId) {
    // Begin transaction
    sqlsrv_begin_transaction($conn);
    
    try {
        // Get the quantity and product_id from the Order table
        $orderSql = "SELECT product_id, quantity FROM [Order] WHERE order_id = ?";
        $orderStmt = sqlsrv_prepare($conn, $orderSql, array(&$orderId));
        if (!sqlsrv_execute($orderStmt)) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }
        
        $order = sqlsrv_fetch_array($orderStmt, SQLSRV_FETCH_ASSOC);
        if (!$order) {
            throw new Exception("Order ID not found.");
        }
        
        $productId = $order['product_id'];
        $quantityOrdered = $order['quantity'];
        
        // Update the Inventory
        $updateSql = "UPDATE Inventory SET quantity = quantity - ? WHERE product_id = ?";
        $updateStmt = sqlsrv_prepare($conn, $updateSql, array(&$quantityOrdered, &$productId));
        if (!sqlsrv_execute($updateStmt)) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }
        
        // Commit transaction
        sqlsrv_commit($conn);
        echo "<h3>Inventory Updated Successfully for Order ID: {$orderId}</h3>";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        sqlsrv_rollback($conn);
        echo "<h3>Error Updating Inventory:</h3><pre>{$e->getMessage()}</pre>";
    }
}

function runSuggestReorderQuery($conn) {
    $sql = "
        SELECT p.product_name, (250 - i.quantity) AS reorder_quantity
        FROM Inventory i
        JOIN Product p ON i.product_id = p.product_id
        WHERE i.quantity < 250;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Products Suggested for Reordering (Quantity < 250)</h3>";
    echo "<table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Reorder Quantity</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['product_name']}</td>
                <td>{$row['reorder_quantity']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
