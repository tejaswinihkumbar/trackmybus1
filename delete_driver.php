<?php
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$driverId = $_GET['id'] ?? null;

if (!$driverId) {
    die("Invalid driver ID.");
}

$conn->beginTransaction();

// Clear assigned bus
$clearBus = $conn->prepare("UPDATE buses SET driver_id = NULL WHERE driver_id = ?");
$clearBus->execute([$driverId]);

// Delete driver
$deleteDriver = $conn->prepare("DELETE FROM drivers WHERE id = ?");
$deleteDriver->execute([$driverId]);

$conn->commit();

header("Location: admin_dashboard.php");
exit();