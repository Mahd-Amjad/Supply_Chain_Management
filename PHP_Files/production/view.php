<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Production";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Production Records</h2>";
    echo "<table border='1'>
            <tr>
                <th>Production ID</th>
                <th>Item Type</th>
                <th>Start Date</th>
                <th>Status</th>
                <th>Order ID</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['production_id']) . "</td>
                <td>" . htmlspecialchars($row['item_type']) . "</td>
                <td>" . htmlspecialchars($row['start_date']->format('Y-m-d')) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
                <td>" . htmlspecialchars($row['order_id']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/production/view.html">Back to View Production</a>
