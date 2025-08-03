<?php
session_start();
include 'dbconfig.php';

// Only allow admins to access
if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_assign_bus.php");
    exit();
}

$userId = $_GET['id'];

// Remove the bus assignment
$stmt = $conn->prepare("UPDATE users SET bus_id = NULL WHERE id = ?");
$stmt->execute([$userId]);

// Redirect back to the assignment page
header("Location: admin_assign_bus.php");
exit();