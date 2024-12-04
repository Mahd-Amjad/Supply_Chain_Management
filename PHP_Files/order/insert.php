<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $supplier_id = intval($_POST['supplier_id']);
    $supplier_name = trim($_POST['supplier_name']);
    $contact_info = trim($_POST['contact_info']);
    $country = trim($_POST['country']);
    $rating = floatval($_POST['rating']);
    
    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Supplier (supplier_id, supplier_name, contact_info, country, rating) VALUES (?, ?, ?, ?, ?)";
    $params = array(&$supplier_id, &$supplier_name, &$contact_info, &$country, &$rating);
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        echo "Supplier inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/supplier/insert.html">Back to Insert Supplier</a>
