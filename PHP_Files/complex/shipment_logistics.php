<?php
// PHP_Files/complex/shipment_logistics.php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queryType = $_POST['query_type'];
    
    switch ($queryType) {
        case 'non_delivered_shipments':
            runNonDeliveredShipmentsQuery($conn);
            break;
        
        case 'avg_delivery_time':
            runAvgDeliveryTimePerWarehouse($conn);
            break;
        
        case 'estimate_shipping_cost':
            runEstimateShippingCost($conn);
            break;
        
        default:
            echo "Invalid query type.";
    }
}

function runNonDeliveredShipmentsQuery($conn) {
    $sql = "
        SELECT sh.shipment_id, sh.shipment_date, sh.tracking_status, sh.delivery_to_customer_status
        FROM Shipment sh
        WHERE sh.tracking_status <> 'Delivered';
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Shipment Status for All Non-Delivered Orders</h3>";
    echo "<table border='1'>
            <tr>
                <th>Shipment ID</th>
                <th>Shipment Date</th>
                <th>Tracking Status</th>
                <th>Delivery to Customer Status</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $shipmentDate = $row['shipment_date'] ? $row['shipment_date']->format('Y-m-d') : 'N/A'; // Handle NULL dates
        echo "<tr>
                <td>{$row['shipment_id']}</td>
                <td>{$shipmentDate}</td>
                <td>{$row['tracking_status']}</td>
                <td>{$row['delivery_to_customer_status']}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runAvgDeliveryTimePerWarehouse($conn) {
    $sql = "
        SELECT w.warehouse_location, 
               AVG(DATEDIFF(day, sh.shipment_date, GETDATE())) AS avg_delivery_time
        FROM Shipment sh
        JOIN Warehouse w ON sh.warehouse_id = w.warehouse_id
        WHERE sh.delivery_to_customer_status = 'Delivered'
        GROUP BY w.warehouse_location;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Average Delivery Time per Warehouse Location</h3>";
    echo "<table border='1'>
            <tr>
                <th>Warehouse Location</th>
                <th>Average Delivery Time (Days)</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $avgDelivery = round($row['avg_delivery_time'], 2);
        echo "<tr>
                <td>{$row['warehouse_location']}</td>
                <td>{$avgDelivery}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

function runEstimateShippingCost($conn) {
    $sql = "
        SELECT sh.shipment_id, 
               COALESCE(SUM(p.weight * 0.05), 0) AS estimated_shipping_cost
        FROM Shipment sh
        JOIN Warehouse w ON sh.warehouse_id = w.warehouse_id
        JOIN Inventory i ON i.warehouse_id = w.warehouse_id
        JOIN Product p ON i.product_id = p.product_id
        GROUP BY sh.shipment_id;
    ";
    
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    echo "<h3>Estimated Shipping Costs Based on Product Weight</h3>";
    echo "<table border='1'>
            <tr>
                <th>Shipment ID</th>
                <th>Estimated Shipping Cost ($)</th>
            </tr>";
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $estimatedCost = number_format($row['estimated_shipping_cost'], 2);
        echo "<tr>
                <td>{$row['shipment_id']}</td>
                <td>{$estimatedCost}</td>
              </tr>";
    }
    echo "</table>";
    sqlsrv_free_stmt($stmt);
}

?>
