<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $forecast_id = intval($_POST['forecast_id']);
    $product_id = intval($_POST['product_id']);
    $period = trim($_POST['period']);
    $predicted_demand = intval($_POST['predicted_demand']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Forecast (forecast_id, product_id, period, predicted_demand) VALUES (?, ?, ?, ?)";
    $params = array(&$forecast_id, &$product_id, &$period, &$predicted_demand);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Forecast inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/forecast/insert.html">Back to Insert Forecast</a>
