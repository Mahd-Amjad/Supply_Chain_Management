<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Retailer";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Retailer List</h2>";
    echo "<table border='1'>
            <tr>
                <th>Retailer ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Warehouse Capacity</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['retailer_id']) . "</td>
                <td>" . htmlspecialchars($row['retailer_name']) . "</td>
                <td>" . htmlspecialchars($row['retailer_location']) . "</td>
                <td>" . htmlspecialchars($row['warehouse_capacity']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/retailer/view.html">Back to View Retailers</a>
