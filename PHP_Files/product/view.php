<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT p.product_id, p.product_name, p.description, p.category, p.weight, p.dimensions, s.supplier_name 
            FROM [Product] p
            LEFT JOIN Supplier s ON p.supplier_id = s.supplier_id";
    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        // Log the error
        error_log(print_r(sqlsrv_errors(), true));
        echo "Error in executing query.";
        exit;
    } else {
        echo "<h2>Product List</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Weight (g)</th>
                    <th>Dimensions</th>
                    <th>Supplier</th>
                </tr>";

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['product_id']) . "</td>
                    <td>" . htmlspecialchars($row['product_name']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['category']) . "</td>
                    <td>" . htmlspecialchars($row['weight']) . "</td>
                    <td>" . htmlspecialchars($row['dimensions']) . "</td>
                    <td>" . htmlspecialchars($row['supplier_name']) . "</td>
                  </tr>";
        }
        echo "</table>";
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/product/view.html">Back to View Products</a>
