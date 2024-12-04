<?php
// PHP_Files/complex/data_integrity.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'remove_inactive_customers':
            runRemoveInactiveCustomers($conn);
            break;
        
        case 'nullify_outdated_inventory':
            runNullifyOutdatedInventory($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runRemoveInactiveCustomers($conn) {
    // Begin transaction
    sqlsrv_begin_transaction($conn);
    
    try {
        $sql = "
            DELETE FROM Customer 
            WHERE customer_id NOT IN (
                SELECT DISTINCT o.customer_id 
                FROM [Order] o
            );
        ";
        
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }
        
        sqlsrv_commit($conn);
        echo "<h3>Inactive Customers Removed Successfully.</h3>";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "<h3>Error Removing Inactive Customers:</h3><pre>{$e->getMessage()}</pre>";
    }
}

function runNullifyOutdatedInventory($conn) {
    // Begin transaction
    sqlsrv_begin_transaction($conn);
    
    try {
        $sql = "
            UPDATE Inventory
            SET quantity = NULL
            WHERE DATEDIFF(month, shipment_date, GETDATE()) > 12;
        ";
        
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }
        
        sqlsrv_commit($conn);
        echo "<h3>Outdated Inventory Records Nullified Successfully.</h3>";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "<h3>Error Nullifying Outdated Inventory Records:</h3><pre>{$e->getMessage()}</pre>";
    }
}
?>
