<?php
// PHP_Files/common/log.php

function log_error($message) {
    $logFile = __DIR__ . '/../../logs/error_log.txt'; // Adjust path as needed
    $date = date('Y-m-d H:i:s');
    $formattedMessage = "[{$date}] {$message}\n";
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
?>
