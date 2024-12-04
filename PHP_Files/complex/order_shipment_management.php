<?php
// PHP_Files/complex/order_shipment_management.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'order_shipment_status':
            runOrderShipmentStatus($conn);
            break;
        
        case 'in_transit_shipments':
            runInTransitShipments($conn);
            break;
        
        case 'pending_shipments':
            runPendingShipments($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runOrderShipmentStatus($conn) {
    $sql = "
        SELECT o.order_id, o.status AS order_status
        FROM [Order] o;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Order Status</h3>";
    echo "<table border='1'>
            <tr>
                <th>Order ID</th>
                <th>Order Status</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$row['order_status']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}


function runInTransitShipments($conn) {
    $sql = "
        SELECT s.shipment_id, s.tracking_status, s.delivery_to_customer_status
        FROM Shipment s
        WHERE s.tracking_status = 'In Transit';
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>In-Transit Shipments</h3>";
    echo "<table border='1'>
            <tr>
                <th>Shipment ID</th>
                <th>Tracking Status</th>
                <th>Delivery to Customer Status</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['shipment_id']}</td>
                <td>{$row['tracking_status']}</td>
                <td>{$row['delivery_to_customer_status']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}


function runPendingShipments($conn) {
    $sql = "
        SELECT s.shipment_id, s.tracking_status, s.delivery_to_customer_status, s.shipment_date
        FROM Shipment s
        WHERE s.delivery_to_customer_status = 'Pending' 
        AND DATEDIFF(DAY, s.shipment_date, GETDATE()) > 7;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Shipments Pending Over 7 Days</h3>";
    echo "<table border='1'>
            <tr>
                <th>Shipment ID</th>
                <th>Tracking Status</th>
                <th>Delivery to Customer Status</th>
                <th>Shipment Date</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $shipmentDate = $row['shipment_date'] ? $row['shipment_date']->format('Y-m-d') : 'N/A';
        echo "<tr>
                <td>{$row['shipment_id']}</td>
                <td>{$row['tracking_status']}</td>
                <td>{$row['delivery_to_customer_status']}</td>
                <td>{$shipmentDate}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

?>
