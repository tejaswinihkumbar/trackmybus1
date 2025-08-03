<?php
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('DRIVER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT d.id AS driver_id, u.first_name, u.last_name FROM drivers d JOIN users u ON d.user_id = u.id WHERE u.id = ?");
$stmt->execute([$userId]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$driver) {
    echo "<div class='container mt-5 alert alert-danger'>Driver not found.</div>";
    exit();
}

$driverId = $driver['driver_id'];

$stmt = $conn->prepare("SELECT b.id AS bus_id, b.vehicle_number, r.id AS route_id, r.name AS route_name 
                        FROM buses b 
                        JOIN routes r ON b.route_id = r.id 
                        WHERE b.driver_id = ?");
$stmt->execute([$driverId]);
$busData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$busData) {
    echo "<div class='container mt-5 alert alert-warning'>No bus assigned.</div>";
    exit();
}

$busId = $busData['bus_id'];
$routeId = $busData['route_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'send_notification.php';

    if (isset($_POST['action']) && $_POST['action'] === 'start') {
        $stmt = $conn->prepare("INSERT INTO trips (bus_id, stop_order, start_time, status) VALUES (?, 0, NOW(), 'started') ");
        $stmt->execute([$busId]);

        $stmt = $conn->prepare("SELECT phone_number FROM users WHERE bus_id = ?");
        $stmt->execute([$busId]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            sendNotification($user['phone_number'], "Trip for bus #$busId has started.");
        }
        sendNotification('+919980635318', "Trip for bus #$busId has started.");
    }

    if ($_POST['action'] === 'next_stop') {
        $stmt = $conn->prepare("SELECT stop_order FROM trips WHERE bus_id = ? and status like 'started'");
        $stmt->execute([$busId]);
        $currentStopData = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentOrder = $currentStopData ? $currentStopData['stop_order'] : -1;

        $stmt = $conn->prepare("SELECT stop_order, name, latitude, longitude 
                                FROM stops 
                                WHERE bus_id = ? AND stop_order = ?");
        $stmt->execute([$busId, $currentOrder + 1]);
        $nextStop = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($nextStop) {
            $stmt = $conn->prepare("UPDATE trips set stop_order = ? where bus_id = ? and status like 'started'");
            $stmt->execute([$nextStop['stop_order'], $busId]);

            $stmt = $conn->prepare("SELECT phone_number FROM users WHERE bus_id = ?");
            $stmt->execute([$busId]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as $user) {
                sendNotification('+91' . $user['phone_number'], "Bus has reached: " . $nextStop['name']);
            }
            sendNotification('+919980635318', "Bus #$busId reached stop: " . $nextStop['name']);
        } else {
            echo "<div class='alert alert-warning mt-3'>No more stops remaining.</div>";
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'end') {
        $stmt = $conn->prepare("UPDATE trips SET end_time = NOW(), status = 'ended' WHERE bus_id = ? AND status = 'started'");
        $stmt->execute([$busId]);

        $stmt = $conn->prepare("SELECT phone_number FROM users WHERE bus_id = ?");
        $stmt->execute([$busId]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $userPhoneNumber = '+91'. $user['phone_number'];
            sendNotification($userPhoneNumber, "Trip for bus #$busId has ended.");
        }

        sendNotification('+919980635318', "Trip for bus #$busId has ended.");
    }
}

$stmt = $conn->prepare("SELECT * FROM trips WHERE bus_id = ? AND status = 'started' ORDER BY start_time DESC LIMIT 1");
$stmt->execute([$busId]);
$currentTrip = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT name, latitude, longitude FROM stops WHERE route_id = ? ORDER BY stop_order ASC");
$stmt->execute([$routeId]);
$stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT s.* 
                        FROM trips t 
                        JOIN stops s ON t.bus_id = s.bus_id AND t.stop_order = s.stop_order 
                        WHERE t.bus_id = ?");
$stmt->execute([$busId]);
$currentStop = $stmt->fetch(PDO::FETCH_ASSOC);

$currentStopOrder = null;
if($currentTrip && $currentTrip['stop_order']){
    $currentStopOrder = $currentTrip['stop_order'];
}

if ($currentStopOrder === false) {
    $currentStopOrder = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $nextOrder = $currentStopOrder + 1;
    if ($nextOrder < count($stops)) {
        $stmt = $conn->prepare("INSERT INTO trip_current_stop (bus_id, stop_order) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE stop_order = VALUES(stop_order), updated_at = NOW()");
        $stmt->execute([$busId, $nextOrder]);
        $currentStopOrder = $nextOrder;
    }
}

$currentStop = $stops[$currentStopOrder] ?? null;
?>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    #map {
        height: 300px;
        width: 100%;
        border-radius: 10px;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4">Welcome, <?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?></h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Assigned Bus: <?= htmlspecialchars($busData['vehicle_number']) ?></h5>
            <h5>Route: <?= htmlspecialchars($busData['route_name']) ?></h5>
            <?php
            if ($currentTrip) {
                $statusText = "Trip Started";
                $statusColor = "text-success";
            } else {
                $stmt = $conn->prepare("SELECT * FROM trips WHERE bus_id = ? AND status = 'ended' ORDER BY end_time DESC LIMIT 1");
                $stmt->execute([$busId]);
                $lastEndedTrip = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($lastEndedTrip) {
                    $statusText = "Trip Has Ended";
                    $statusColor = "text-danger";
                } else {
                    $statusText = "Trip Not Yet Started";
                    $statusColor = "text-danger";
                }
            }
            ?>
            <h5>Status: <span class="<?= $statusColor ?>"><?= $statusText ?></span></h5>

            <?php if ($currentTrip && $currentTrip['start_time']): ?>
                <h6>Start Time: <?= $currentTrip['start_time'] ?></h6>
            <?php endif; ?>
            <?php if ($currentTrip && $currentTrip['end_time']): ?>
                <h6>End Time: <?= $currentTrip['end_time'] ?></h6>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$currentTrip): ?>
        <form method="post">
            <input type="hidden" name="action" value="start">
            <button type="submit" class="btn btn-outline-success mt-2">
                <i class="bi bi-play-circle"></i> Start Trip
            </button>
        </form>
    <?php else: ?>
        <form method="post" class="d-inline">
            <input type="hidden" name="action" value="end">
            <button type="submit" class="btn btn-outline-danger mt-2">
                <i class="bi bi-stop-circle"></i> End Trip
            </button>
        </form>

        <form method="post" class="d-inline">
            <input type="hidden" name="action" value="next_stop">
            <button type="submit" class="btn btn-outline-primary mt-2 ms-2">
                <i class="bi bi-arrow-down-circle"></i> Update Stop
            </button>
        </form>

        <div class="card mt-4">
            <div class="card-body">
                <h5>Current Stop: <?= htmlspecialchars($currentStop['name'] ?? 'N/A') ?></h5>
                <?php if ($currentStop): ?>
                    <div id="map" class="mb-3"></div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var map = L.map('map').setView([<?= $currentStop['latitude'] ?>, <?= $currentStop['longitude'] ?>], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);
                            L.marker([<?= $currentStop['latitude'] ?>, <?= $currentStop['longitude'] ?>]).addTo(map)
                                .bindPopup("<?= htmlspecialchars($currentStop['name']) ?>")
                                .openPopup();
                        });
                    </script>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $currentStop['latitude'] ?>,<?= $currentStop['longitude'] ?>"
                       class="btn btn-outline-success mt-2 mb-3" target="_blank">
                        <i class="bi bi-geo-alt"></i> Drive to this Stop
                    </a>
                <?php endif; ?>
                <h5>All Stops:</h5>
                <ol>
                    <?php foreach ($stops as $stop): ?>
                        <li><?= htmlspecialchars($stop['name']) ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    <?php endif; ?>
</div>