<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = intval($_POST['supplier_id']);
    $supplier_name = trim($_POST['supplier_name']);
    $contact_info = trim($_POST['contact_info']);
    $country = trim($_POST['country']);
    $rating = $_POST['rating'] !== '' ? floatval($_POST['rating']) : null;
    
    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];
    
    if ($supplier_name !== '') {
        $fields[] = "supplier_name = ?";
        $params[] = &$supplier_name;
    }
    if ($contact_info !== '') {
        $fields[] = "contact_info = ?";
        $params[] = &$contact_info;
    }
    if ($country !== '') {
        $fields[] = "country = ?";
        $params[] = &$country;
    }
    if ($rating !== null) {
        $fields[] = "rating = ?";
        $params[] = &$rating;
    }
    
    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }
    
    $sql = "UPDATE Supplier SET " . implode(", ", $fields) . " WHERE supplier_id = ?";
    $params[] = &$supplier_id;
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    
    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }
    
    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Supplier updated successfully.";
        } else {
            echo "No supplier found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/supplier/update.html">Back to Update Supplier</a>
