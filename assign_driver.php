<?php
include 'header.php';
include 'dbconfig.php';

// Check if driver is logged in
if (!isset($_SESSION['roles']) || !in_array('DRIVER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Get logged-in driver's ID from session
$driverId = $_SESSION['user_id'];

// Fetch driver's assigned bus and route
$query = $conn->prepare("
    SELECT d.assigned_bus_id AS bus_id, b.route_id, r.name AS route_name
    FROM drivers d
    JOIN buses b ON d.assigned_bus_id = b.id
    JOIN routes r ON b.route_id = r.id
    WHERE d.id = ?
");
$query->execute([$driverId]);
$info = $query->fetch(PDO::FETCH_ASSOC);

// Fetch stops for the route
$stops = [];
if ($info && $info['route_id']) {
    $stmt = $conn->prepare("SELECT name FROM stops WHERE route_id = ? ORDER BY stop_order ASC");
    $stmt->execute([$info['route_id']]);
    $stops = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Welcome, Driver</h2>

    <?php if ($info): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5>Assigned Route: <?php echo htmlspecialchars($info['route_name']); ?></h5>
                <h6>Stops:</h6>
                <ul>
                    <?php foreach ($stops as $stop): ?>
                        <li><?php echo htmlspecialchars($stop['name']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>Update Your Live Location</h5>
                <form action="update_location.php" method="POST">
                    <div class="form-group">
                        <label>Latitude:</label>
                        <input type="text" name="latitude" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Longitude:</label>
                        <input type="text" name="longitude" class="form-control" required>
                    </div>
                    <input type="hidden" name="bus_id" value="<?php echo $info['bus_id']; ?>">
                    <button type="submit" class="btn btn-primary">Update Location</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p>You are not assigned to any bus/route yet.</p>
    <?php endif; ?>
</div>
</body>
</html>