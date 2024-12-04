<?php
// PHP_Files/complex/customer_supplier_insights.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'total_quantity_per_customer':
            runTotalQuantityPerCustomer($conn);
            break;
        
        case 'supplier_order_summary':
            runSupplierOrderSummary($conn);
            break;
        
        case 'top_customers_by_status':
            runTopCustomersByStatus($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runTotalQuantityPerCustomer($conn) {
    $sql = "
        SELECT c.customer_name, SUM(o.quantity) AS total_quantity
        FROM [Order] o
        JOIN Customer c ON o.customer_id = c.customer_id
        GROUP BY c.customer_name;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Total Quantity Ordered by Each Customer</h3>";
    echo "<table border='1'>
            <tr>
                <th>Customer Name</th>
                <th>Total Quantity</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['customer_name']}</td>
                <td>{$row['total_quantity']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runSupplierOrderSummary($conn) {
    $sql = "
        SELECT s.supplier_name, COUNT(o.order_id) AS total_orders, SUM(o.quantity) AS total_quantity
        FROM Supplier s
        LEFT JOIN [Order] o ON s.supplier_id = o.supplier_id
        GROUP BY s.supplier_name;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Supplier Order Summary</h3>";
    echo "<table border='1'>
            <tr>
                <th>Supplier Name</th>
                <th>Total Orders</th>
                <th>Total Quantity Supplied</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $totalOrders = $row['total_orders'] ?? 0;
        $totalQuantity = $row['total_quantity'] ?? 0;
        echo "<tr>
                <td>{$row['supplier_name']}</td>
                <td>{$totalOrders}</td>
                <td>{$totalQuantity}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runTopCustomersByStatus($conn) {
    $sql = "
        SELECT c.customer_name, 
               SUM(CASE WHEN o.status = 'Pending' THEN 1 ELSE 0 END) AS pending_orders,
               SUM(CASE WHEN o.status = 'Shipped' THEN 1 ELSE 0 END) AS shipped_orders,
               SUM(CASE WHEN o.status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
        FROM Customer c
        LEFT JOIN [Order] o ON c.customer_id = o.customer_id
        GROUP BY c.customer_name;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Top Customers Based on Order Status</h3>";
    echo "<table border='1'>
            <tr>
                <th>Customer Name</th>
                <th>Pending Orders</th>
                <th>Shipped Orders</th>
                <th>Cancelled Orders</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $pending = $row['pending_orders'] ?? 0;
        $shipped = $row['shipped_orders'] ?? 0;
        $cancelled = $row['cancelled_orders'] ?? 0;
        echo "<tr>
                <td>{$row['customer_name']}</td>
                <td>{$pending}</td>
                <td>{$shipped}</td>
                <td>{$cancelled}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
