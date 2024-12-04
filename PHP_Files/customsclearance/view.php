<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM CustomsClearance";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Customer Clearance Records</h2>";
    echo "<table border='1'>
            <tr>
                <th>Clearance ID</th>
                <th>Shipment ID</th>
                <th>Custom Status</th>
                <th>Clearance Date</th>
                <th>Documents</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['clearance_id']) . "</td>
                <td>" . htmlspecialchars($row['shipment_id']) . "</td>
                <td>" . htmlspecialchars($row['custom_status']) . "</td>
                <td>" . ($row['clearance_date'] ? htmlspecialchars($row['clearance_date']->format('Y-m-d')) : 'N/A') . "</td>
                <td>" . htmlspecialchars($row['documents']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/customerclearance/view.html">Back to View Customer Clearances</a>
