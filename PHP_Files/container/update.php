<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $container_id = intval($_POST['container_id']);
    $status = trim($_POST['status']);
    $port_id = isset($_POST['port_id']) && $_POST['port_id'] !== '' ? intval($_POST['port_id']) : null;

    // Build the SET part of the SQL statement dynamically
    $fields = [];
    $params = [];

    if ($status !== '') {
        $fields[] = "status = ?";
        $params[] = &$status;
    }
    if (isset($_POST['port_id']) && $_POST['port_id'] !== '') {
        $fields[] = "port_id = ?";
        $params[] = &$port_id;
    }

    if (count($fields) === 0) {
        echo "No fields to update.";
        exit;
    }

    $sql = "UPDATE Container SET " . implode(", ", $fields) . " WHERE container_id = ?";
    $params[] = &$container_id;

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_execute($stmt)) {
        if (sqlsrv_rows_affected($stmt) > 0) {
            echo "Container updated successfully.";
        } else {
            echo "No container found with the provided ID or no changes made.";
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
<a href="../../HTML_Files/container/update.html">Back to Update Container</a>
