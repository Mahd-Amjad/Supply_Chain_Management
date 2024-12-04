<?php
$serverName = "LAPTOP-LF0A6BKF";
    $connectionOptions = [
        "Database" => "SC_Project", 
        "Uid" => "sqlpad",          
        "PWD" => "abc"           
    ];
//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn) {
    echo "Connection established.";
} else {
    echo "Connection failed.";
    die(print_r(sqlsrv_errors(), true));
}
?>
