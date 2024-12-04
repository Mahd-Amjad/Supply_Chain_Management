<?php
require_once 'config.php';

$sqlFile = 'C:\Users\hibak\OneDrive\Documents\SQL Server Management Studio\i212755_Project_M2\Individual Queries\i212755_Project_Creation.sql';
$sqlScript = file_get_contents($sqlFile);

$stmt = sqlsrv_query($conn, $sqlScript);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "Tables created successfully.";
}

sqlsrv_close($conn);
?>
<a href="../HTML_Files/create_table.html">Back</a>
