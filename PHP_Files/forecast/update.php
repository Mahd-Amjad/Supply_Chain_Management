<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $forecast_id = intval($_POST['forecast_id']);
    $period = trim($_POST['period']);
    $predicted_demand = isset($_POST['predicted_demand']) ? intval($_POST['predicted_demand']) : null;
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($period !== '') {
        $fields[] = "period = ?";
        $params[] = &$period;
    }
    if ($predicted_demand !== null) {
        $fields[] = "predicted_demand = ?";
        $params[] = &$predicted_demand;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Forecast SET " . implode(", ", $fields) . " WHERE forecast_id = ?";
    $params[] = &$forecast_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Forecast updated successfully.";
        } else {
            echo "No forecast found with the provided ID or no changes made.";
        }
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/forecast/update.html">Back to Update Forecast</a>
