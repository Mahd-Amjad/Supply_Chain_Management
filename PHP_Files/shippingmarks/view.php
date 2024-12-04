<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM ShippingMarks";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Shipping Marks Records</h2>";
    echo "<table border='1'>
            <tr>
                <th>Shipping ID</th>
                <th>Shipment ID</th>
                <th>Shipping Marks</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['shipping_id']) . "</td>
                <td>" . htmlspecialchars($row['shipment_id']) . "</td>
                <td>" . htmlspecialchars($row['shipping_marks']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/shippingmarks/view.html">Back to View Shipping Marks</a>
