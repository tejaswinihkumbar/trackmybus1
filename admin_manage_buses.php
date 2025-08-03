<?php
include 'header.php';
include 'dbconfig.php';

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_number = $_POST['vehicle_number'];
    $route_name = $_POST['route_name'];
    $stops = $_POST['stops'];
    $latitudes = $_POST['latitudes'];
    $longitudes = $_POST['longitudes'];

    try {
        $conn->beginTransaction();

        // Insert route
        $stmt = $conn->prepare("INSERT INTO routes (name) VALUES (?)");
        $stmt->execute([$route_name]);
        $route_id = $conn->lastInsertId();

        // Insert bus
        $stmt = $conn->prepare("INSERT INTO buses (vehicle_number, route_id) VALUES (?, ?)");
        $stmt->execute([$vehicle_number, $route_id]);
        $bus_id = $conn->lastInsertId();

        // Insert stops
        for ($i = 0; $i < count($stops); $i++) {
            $stop_name = trim($stops[$i]);
            $lat = trim($latitudes[$i]);
            $lng = trim($longitudes[$i]);
            $stopOrder = $i + 1; // ✅ Auto stop order

            if ($stop_name !== '') {

                $stmt = $conn->prepare("INSERT INTO stops (name, route_id, bus_id, stop_order, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$stop_name, $route_id, $bus_id, $stopOrder, $lat ?: null, $lng ?: null]);

                $stop_id = $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO route_stops (route_id, stop_id) VALUES (?, ?)");
                $stmt->execute([$route_id, $stop_id]);
            }
        }

        $conn->commit();
        $message = "Bus and route with stops assigned successfully!";
    } catch (Exception $e) {
        $conn->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}


// Fetch assigned buses
$stmt = $conn->query("
    SELECT 
        b.id AS bus_id,
        b.vehicle_number,
        r.name AS route_name,
        GROUP_CONCAT(s.name ORDER BY s.id SEPARATOR ', ') AS stops
    FROM buses b
    LEFT JOIN routes r ON b.route_id = r.id
    LEFT JOIN route_stops rs ON rs.route_id = r.id
    LEFT JOIN stops s ON rs.stop_id = s.id
    GROUP BY b.id
");
$assignedBuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Bus Management</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm mb-5">
        <h4>Add Bus, Route, and Stops</h4>

        <div class="mb-3">
            <label class="form-label">Bus Number</label>
            <input type="text" name="vehicle_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Route Name</label>
            <input type="text" name="route_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stops (Name, Latitude, Longitude)</label>
            <div id="stopInputs">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <input type="text" name="stops[]" class="form-control" placeholder="Stop Name" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="latitudes[]" class="form-control" placeholder="Latitude">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="longitudes[]" class="form-control" placeholder="Longitude">
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addStopInput()">Add More Stop</button>
        </div>

        <button type="submit" class="btn btn-outline-success mt-2">Assign</button>
    </form>

    <h4>Assigned Buses and Routes</h4>
    <table class="table table-bordered">
        <thead class="bg-warning text-white">
            <tr>
                <th>Bus Number</th>
                <th>Route Name</th>
                <th>Stops</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignedBuses as $bus): ?>
                <tr><td><?= htmlspecialchars($bus['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($bus['route_name']) ?></td>
                    <td><?= htmlspecialchars($bus['stops']) ?: 'N/A' ?></td>
                    <td>
                        <a href="edit_bus.php?id=<?= $bus['bus_id'] ?>" class="btn btn-sm btn-outline-info mt-2">Edit</a>
                        <a href="remove_bus.php?id=<?= $bus['bus_id'] ?>" class="btn btn-sm btn-outline-danger mt-2"
                           onclick="return confirm('Are you sure you want to remove this bus?')">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function addStopInput() {
    const stopRow = document.createElement('div');
    stopRow.classList.add('row', 'mb-2');
    stopRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="stops[]" class="form-control" placeholder="Stop Name" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="latitudes[]" class="form-control" placeholder="Latitude">
        </div>
        <div class="col-md-4">
            <input type="text" name="longitudes[]" class="form-control" placeholder="Longitude">
        </div>
    `;
    document.getElementById('stopInputs').appendChild(stopRow);
}
</script>

<?php include 'footer.php'; ?>