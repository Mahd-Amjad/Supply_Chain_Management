<?php
// PHP_Files/complex/auditing_compliance.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'high_value_orders':
            runHighValueOrdersQuery($conn);
            break;
        
        case 'duplicate_payments':
            runDuplicatePaymentsQuery($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runHighValueOrdersQuery($conn) {
    $sql = "
        SELECT o.order_id, o.order_date, c.customer_name, o.total_amount
        FROM [Order] o
        JOIN Customer c ON o.customer_id = c.customer_id
        WHERE o.total_amount > 800;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>High-Value Orders Above $800</h3>";
    echo "<table border='1'>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Customer Name</th>
                <th>Total Amount</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $orderDate = $row['order_date']->format('Y-m-d');
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$orderDate}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['total_amount']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runDuplicatePaymentsQuery($conn) {
    $sql = "
        SELECT order_id, payment_date, payment_amount, COUNT(*) AS duplicate_count
        FROM Payment
        GROUP BY order_id, payment_date, payment_amount
        HAVING COUNT(*) > 1;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Duplicate Payments</h3>";
    echo "<table border='1'>
            <tr>
                <th>Order ID</th>
                <th>Payment Date</th>
                <th>Payment Amount</th>
                <th>Duplicate Count</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $paymentDate = $row['payment_date']->format('Y-m-d');
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$paymentDate}</td>
                <td>{$row['payment_amount']}</td>
                <td>{$row['duplicate_count']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
