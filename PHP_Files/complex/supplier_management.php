<?php
// PHP_Files/complex/supplier_management.php
include '../config.php';
include '../logging.php'; // If centralized logging is implemented

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'performance_report':
            runSupplierPerformanceReport($conn);
            break;
        
        case 'inactive_suppliers':
            runInactiveSuppliersQuery($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runSupplierPerformanceReport($conn) {
    $sql = "
        SELECT s.supplier_name, s.rating, 
               AVG(DATEDIFF(day, o.order_date, GETDATE())) AS avg_order_processing_time
        FROM Supplier s
        JOIN [Order] o ON s.supplier_id = o.supplier_id
        GROUP BY s.supplier_name, s.rating;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        log_error("Supplier Performance Report Query Failed: " . print_r(sqlsrv_errors(), true));
        echo "An error occurred while generating the performance report. Please try again later.";
        return;
    }
    
    echo "<h3>Supplier Performance Report</h3>";
    echo "<table border='1'>
            <tr>
                <th>Supplier Name</th>
                <th>Rating</th>
                <th>Average Order Processing Time (Days)</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $avgProcessingTime = round($row['avg_order_processing_time'], 2);
        echo "<tr>
                <td>{$row['supplier_name']}</td>
                <td>{$row['rating']}</td>
                <td>{$avgProcessingTime}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}


function runInactiveSuppliersQuery($conn) {
    $sql = "
        SELECT supplier_name 
        FROM Supplier
        WHERE supplier_id NOT IN (
            SELECT DISTINCT supplier_id 
            FROM [Order] 
            WHERE order_date > DATEADD(year, -1, GETDATE())
        );
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Inactive Suppliers with No Orders in the Past Year</h3>";
    echo "<table border='1'>
            <tr>
                <th>Supplier Name</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['supplier_name']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
