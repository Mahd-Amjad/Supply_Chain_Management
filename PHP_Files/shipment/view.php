<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Shipment";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Shipment Records</h2>";
    echo "<table border='1'>
            <tr>
                <th>Shipment ID</th>
                <th>Shipment Date</th>
                <th>Tracking Status</th>
                <th>Retailer ID</th>
                <th>Warehouse ID</th>
                <th>Delivery to Retailer Status</th>
                <th>Delivery to Customer Status</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['shipment_id']) . "</td>
                <td>" . ($row['shipment_date'] ? htmlspecialchars($row['shipment_date']->format('Y-m-d')) : 'N/A') . "</td>
                <td>" . htmlspecialchars($row['tracking_status']) . "</td>
                <td>" . htmlspecialchars($row['retailer_id']) . "</td>
                <td>" . htmlspecialchars($row['warehouse_id']) . "</td>
                <td>" . htmlspecialchars($row['delivery_to_retailer_status']) . "</td>
                <td>" . htmlspecialchars($row['delivery_to_customer_status']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/shipment/view.html">Back to View Shipments</a>
