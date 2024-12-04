<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Forecast";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Forecast Records</h2>";
    echo "<table border='1'>
            <tr>
                <th>Forecast ID</th>
                <th>Product ID</th>
                <th>Period</th>
                <th>Predicted Demand</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['forecast_id']) . "</td>
                <td>" . htmlspecialchars($row['product_id']) . "</td>
                <td>" . htmlspecialchars($row['period']) . "</td>
                <td>" . htmlspecialchars($row['predicted_demand']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/forecast/view.html">Back to View Forecasts</a>
