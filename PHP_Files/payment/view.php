<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Payment";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Payment Records</h2>";
    echo "<table border='1'>
            <tr>
                <th>Payment ID</th>
                <th>Order ID</th>
                <th>Payment Date</th>
                <th>Payment Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['payment_id']) . "</td>
                <td>" . htmlspecialchars($row['order_id']) . "</td>
                <td>" . ($row['payment_date'] ? htmlspecialchars($row['payment_date']->format('Y-m-d')) : 'N/A') . "</td>
                <td>" . htmlspecialchars($row['payment_amount']) . "</td>
                <td>" . htmlspecialchars($row['payment_method']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/payment/view.html">Back to View Payments</a>
