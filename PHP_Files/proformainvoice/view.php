<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT * FROM Supplier";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt === false) {
        echo "Error in executing query.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h2>Supplier List</h2>";
    echo "<table border='1'>
            <tr>
                <th>Supplier ID</th>
                <th>Name</th>
                <th>Contact Info</th>
                <th>Country</th>
                <th>Rating</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['supplier_id']) . "</td>
                <td>" . htmlspecialchars($row['supplier_name']) . "</td>
                <td>" . htmlspecialchars($row['contact_info']) . "</td>
                <td>" . htmlspecialchars($row['country']) . "</td>
                <td>" . htmlspecialchars($row['rating']) . "</td>
              </tr>";
    }
    echo "</table>";
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/supplier/view.html">Back to View Suppliers</a>
