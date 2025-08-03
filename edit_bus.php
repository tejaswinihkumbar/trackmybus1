<?php
ob_start();
include 'header.php';
include 'dbconfig.php';

if (!isset($_GET['id'])) {
    echo "Invalid request";
    exit;
}

$bus_id = $_GET['id'];
$message = "";

// Fetch bus, route and stops
$stmt = $conn->prepare("
    SELECT b.id AS bus_id, b.vehicle_number, r.id AS route_id, r.name AS route_name
    FROM buses b
    JOIN routes r ON b.route_id = r.id
    WHERE b.id = ?
");
$stmt->execute([$bus_id]);
$bus = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bus) {
    echo "Bus not found";
    exit;
}

// Fetch stops
$stmt = $conn->prepare("
    SELECT s.id, s.name, s.latitude, s.longitude
    FROM route_stops rs
    JOIN stops s ON rs.stop_id = s.id
    WHERE rs.route_id = ?
");
$stmt->execute([$bus['route_id']]);
$stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_number = $_POST['vehicle_number'];
    $route_name = $_POST['route_name'];
    $updated_stops = $_POST['stops'];

    try {
        $conn->beginTransaction();

        // Update bus
        $stmt = $conn->prepare("UPDATE buses SET vehicle_number = ? WHERE id = ?");
        $stmt->execute([$vehicle_number, $bus_id]);

        // Update route
        $stmt = $conn->prepare("UPDATE routes SET name = ? WHERE id = ?");
        $stmt->execute([$route_name, $bus['route_id']]);

        // Delete old route_stops and stops
        $stmt = $conn->prepare("SELECT stop_id FROM route_stops WHERE route_id = ?");
        $stmt->execute([$bus['route_id']]);
        $old_stop_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $conn->prepare("DELETE FROM route_stops WHERE route_id = ?");
        $stmt->execute([$bus['route_id']]);

        if (!empty($old_stop_ids)) {
            $placeholders = implode(',', array_fill(0, count($old_stop_ids), '?'));
            $stmt = $conn->prepare("DELETE FROM stops WHERE id IN ($placeholders)");
            $stmt->execute($old_stop_ids);
        }

        // Insert new stops
       foreach ($updated_stops as $index => $stop) {
    if (!empty($stop['name']) && is_numeric($stop['lat']) && is_numeric($stop['lng'])) {
        $stopOrder = $index + 1;

        $stmt = $conn->prepare("INSERT INTO stops (name, route_id, bus_id, stop_order, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $stop['name'],
            $bus['route_id'],
            $bus_id,
            $stopOrder,
            $stop['lat'],
            $stop['lng']
        ]);

        $stop_id = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO route_stops (route_id, stop_id) VALUES (?, ?)");
        $stmt->execute([$bus['route_id'], $stop_id]);
    }
}


        $conn->commit();
        $message = "Bus and route updated successfully!";
        // Refresh data
        header("Location: admin_manage_buses.php");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}
?>

<div class="container mt-5">
    <h2>Edit Bus</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Bus Number</label>
            <input type="text" name="vehicle_number" class="form-control" value="<?= htmlspecialchars($bus['vehicle_number']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Route Name</label>
            <input type="text" name="route_name" class="form-control" value="<?= htmlspecialchars($bus['route_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stops</label>
            <div id="stopInputs">
                <?php foreach ($stops as $index => $stop): ?>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <input type="text" name="stops[<?= $index ?>][name]" class="form-control" value="<?= htmlspecialchars($stop['name']) ?>" placeholder="Stop Name" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="stops[<?= $index ?>][lat]" class="form-control" value="<?= htmlspecialchars($stop['latitude']) ?>" placeholder="Latitude" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="stops[<?= $index ?>][lng]" class="form-control" value="<?= htmlspecialchars($stop['longitude']) ?>" placeholder="Longitude" required>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addStopInput()">Add Stop</button>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="admin_manage_buses.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
let stopIndex = <?= count($stops) ?>;
function addStopInput() {
    const stopInputs = document.getElementById('stopInputs');
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2';
    div.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="stops[${stopIndex}][name]" class="form-control" placeholder="Stop Name" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="stops[${stopIndex}][lat]" class="form-control" placeholder="Latitude" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="stops[${stopIndex}][lng]" class="form-control" placeholder="Longitude" required>
        </div>
    `;
    stopInputs.appendChild(div);
    stopIndex++;
}
</script>

<?php 
ob_end_flush();
include 'footer.php'; ?>