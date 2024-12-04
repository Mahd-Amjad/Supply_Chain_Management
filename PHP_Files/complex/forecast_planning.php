<?php
// PHP_Files/complex/forecast_planning.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'forecasted_demand':
            runForecastedDemandQuery($conn);
            break;
        
        case 'compare_forecast_actual':
            runCompareForecastActualQuery($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runForecastedDemandQuery($conn) {
    $sql = "
        SELECT p.product_name, f.period, f.predicted_demand
        FROM Forecast f
        JOIN Product p ON f.product_id = p.product_id
        ORDER BY f.period DESC;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Forecasted Product Demand Data</h3>";
    echo "<table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Period</th>
                <th>Predicted Demand</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['product_name']}</td>
                <td>{$row['period']}</td>
                <td>{$row['predicted_demand']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runCompareForecastActualQuery($conn) {
    $sql = "
        SELECT p.product_name, f.period, f.predicted_demand, SUM(o.quantity) AS actual_sales
        FROM Forecast f
        LEFT JOIN [Order] o ON f.product_id = o.product_id 
            AND DATEPART(quarter, o.order_date) = CASE 
                WHEN f.period LIKE 'Q1%' THEN 1 
                WHEN f.period LIKE 'Q2%' THEN 2 
                WHEN f.period LIKE 'Q3%' THEN 3 
                ELSE 4 
            END
        JOIN Product p ON f.product_id = p.product_id
        GROUP BY p.product_name, f.period, f.predicted_demand;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Compare Forecast vs. Actual Sales by Period</h3>";
    echo "<table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Period</th>
                <th>Predicted Demand</th>
                <th>Actual Sales</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $actualSales = $row['actual_sales'] ?? 0;
        echo "<tr>
                <td>{$row['product_name']}</td>
                <td>{$row['period']}</td>
                <td>{$row['predicted_demand']}</td>
                <td>{$actualSales}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}
?>
