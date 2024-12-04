<?php
// PHP_Files/complex/crm.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'top_customers':
            runTopCustomersQuery($conn);
            break;
        
        case 'customer_orders':
            $customerId = intval($_POST['customer_id']);
            runCustomerOrdersQuery($conn, $customerId);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runTopCustomersQuery($conn) {
    $sql = "
        SELECT c.customer_name, SUM(o.total_amount) AS total_spent
        FROM [Order] o
        JOIN Customer c ON o.customer_id = c.customer_id
        WHERE o.order_date BETWEEN '2024-01-01' AND '2024-12-31'
        GROUP BY c.customer_name
        ORDER BY total_spent DESC;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Top Customers by Total Spending in 2024</h3>";
    echo "<table border='1'>
            <tr>
                <th>Customer Name</th>
                <th>Total Spent</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['customer_name']}</td>
                <td>{$row['total_spent']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runCustomerOrdersQuery($conn, $customerId) {
    // Validate customer existence
    $checkSql = "SELECT customer_name FROM Customer WHERE customer_id = ?";
    $params = array($customerId);
    $checkStmt = sqlsrv_query($conn, $checkSql, $params);
    if ($checkStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $customer = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    if (!$customer) {
        echo "<h3>No customer found with ID: {$customerId}</h3>";
        return;
    }
    
    $sql = "
        SELECT o.order_id, o.order_date, p.product_name, o.quantity, o.status, o.total_amount
        FROM [Order] o
        JOIN Product p ON o.product_id = p.product_id
        WHERE o.customer_id = ?
    ";
    
    $stmt = sqlsrv_query($conn, $sql, array($customerId));
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>All Orders for Customer: {$customer['customer_name']} (ID: {$customerId})</h3>";
    echo "<table border='1'>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Total Amount</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $orderDate = $row['order_date']->format('Y-m-d'); // Formatting date
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$orderDate}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['status']}</td>
                <td>{$row['total_amount']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
