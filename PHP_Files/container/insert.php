<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../HTML_Files/login.html");
    exit;
}

require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $container_id = intval($_POST['container_id']);
    $status = trim($_POST['status']);
    $port_id = isset($_POST['port_id']) && $_POST['port_id'] !== '' ? intval($_POST['port_id']) : null;

    // Prepare the SQL INSERT statement with parameters
    $sql = "INSERT INTO Container (container_id, status, port_id) VALUES (?, ?, ?)";
    $params = array(&$container_id, &$status, &$port_id);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        echo "Error in preparing statement.";
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_execute($stmt)) {
        echo "Container inserted successfully.";
    } else {
        echo "Error in executing statement.";
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>
<br>
<a href="../../HTML_Files/container/insert.html">Back to Insert Container</a>
