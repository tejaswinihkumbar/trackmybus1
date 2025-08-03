<?php
include 'dbconfig.php';

if (!isset($_GET['id'])) {
    header("Location: admin_manage_buses.php");
    exit;
}

$bus_id = intval($_GET['id']);

try {
    $conn->beginTransaction();

    // Get route ID for this bus
    $stmt = $conn->prepare("SELECT route_id FROM buses WHERE id = ?");
    $stmt->execute([$bus_id]);
    $bus = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bus) {
        throw new Exception("Bus not found.");
    }

    $route_id = $bus['route_id'];

    // Get stop IDs for this route
    $stmt = $conn->prepare("SELECT stop_id FROM route_stops WHERE route_id = ?");
    $stmt->execute([$route_id]);
    $stop_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete route_stops mapping
$stmt = $conn->prepare("DELETE FROM route_stops WHERE route_id = ?");
$stmt->execute([$route_id]);

// Delete all stops that belong to this bus and route
$stmt = $conn->prepare("DELETE FROM stops WHERE bus_id = ? AND route_id = ?");
$stmt->execute([$bus_id, $route_id]);


// Delete related trips for this bus
$stmt = $conn->prepare("DELETE FROM trips WHERE bus_id = ?");
$stmt->execute([$bus_id]);

// Now delete the bus
$stmt = $conn->prepare("DELETE FROM buses WHERE id = ?");
$stmt->execute([$bus_id]);

// Delete route
$stmt = $conn->prepare("DELETE FROM routes WHERE id = ?");
$stmt->execute([$route_id]);

    $conn->commit();
    header("Location: admin_manage_buses.php?message=Bus removed successfully");
    exit;
} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>