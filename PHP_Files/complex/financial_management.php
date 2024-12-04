<?php
// PHP_Files/complex/financial_management.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'monthly_sales':
            $month = intval($_POST['month']);
            $year = intval($_POST['year']);
            runMonthlySalesReport($conn, $month, $year);
            break;
        
        case 'outstanding_payments':
            runOutstandingPayments($conn);
            break;
        
        case 'total_payments':
            $yearPayments = intval($_POST['year_payments']);
            runTotalPaymentsToSuppliers($conn, $yearPayments);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runMonthlySalesReport($conn, $month, $year) {
    $sql = "
        SELECT p.product_name, SUM(o.total_amount) AS monthly_sales
        FROM [Order] o
        JOIN Product p ON o.product_id = p.product_id
        WHERE MONTH(o.order_date) = ? AND YEAR(o.order_date) = ?
        GROUP BY p.product_name;
    ";
    
    $params = array($month, $year);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Monthly Sales Report for " . date("F", mktime(0, 0, 0, $month, 10)) . " $year</h3>";
    echo "<table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Total Sales</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['product_name']}</td>
                <td>{$row['monthly_sales']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runOutstandingPayments($conn) {
    $sql = "
        SELECT o.order_id, o.total_amount, 
               ISNULL(SUM(p.payment_amount), 0) AS paid_amount,
               (o.total_amount - ISNULL(SUM(p.payment_amount), 0)) AS outstanding_amount
        FROM [Order] o
        LEFT JOIN Payment p ON o.order_id = p.order_id
        GROUP BY o.order_id, o.total_amount
        HAVING o.total_amount > ISNULL(SUM(p.payment_amount), 0);
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Outstanding Payments</h3>";
    echo "<table border='1'>
            <tr>
                <th>Order ID</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Outstanding Amount</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$row['total_amount']}</td>
                <td>{$row['paid_amount']}</td>
                <td>{$row['outstanding_amount']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runTotalPaymentsToSuppliers($conn, $year) {
    $sql = "
        SELECT s.supplier_name, SUM(p.payment_amount) AS total_paid
        FROM Payment p
        JOIN [Order] o ON p.order_id = o.order_id
        JOIN Supplier s ON o.supplier_id = s.supplier_id
        WHERE YEAR(p.payment_date) = ?
        GROUP BY s.supplier_name;
    ";
    
    $params = array($year);
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Total Payments to Suppliers in $year</h3>";
    echo "<table border='1'>
            <tr>
                <th>Supplier Name</th>
                <th>Total Paid</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['supplier_name']}</td>
                <td>{$row['total_paid']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
