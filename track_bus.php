<?php
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('USER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}
try {
    // Get bus_id assigned to this user
    $stmt = $conn->prepare("SELECT bus_id FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !$user['bus_id']) {
        echo "No bus assigned.";
        exit();
    }

    $bus_id = $user['bus_id'];

    // Check if a trip is started for this bus
    $stmt = $conn->prepare("SELECT COUNT(*) FROM trips WHERE bus_id = ? AND status = 'started'");
    $stmt->execute([$bus_id]);
    $tripStarted = $stmt->fetchColumn();

   if (!$tripStarted) {
        echo "<div class='container mt-5 alert alert-info text-center'><h4>Trip not yet started</h4></div>";
        include 'footer.php';
        exit();
   }

    // Get bus number and route_id
    $stmt = $conn->prepare("SELECT vehicle_number, route_id FROM buses WHERE id = ?");
    $stmt->execute([$bus_id]);
    $bus = $stmt->fetch(PDO::FETCH_ASSOC);
    $bus_number = $bus['vehicle_number'];
    $route_id = $bus['route_id'];

    // Get route name
    $stmt = $conn->prepare("SELECT name FROM routes WHERE id = ?");
    $stmt->execute([$route_id]);
    $route_name = $stmt->fetchColumn();

    // Get all stops on the route
    $stmt = $conn->prepare("SELECT name FROM stops WHERE route_id = ? ORDER BY stop_order ASC");
    $stmt->execute([$route_id]);
    $route_stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Get Stop order
    $stmt = $conn->prepare("SELECT stop_order FROM trips WHERE bus_id = ? and status like 'started'");
    $stmt->execute([$bus_id]);
    $stop_orderResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stop_order = $stop_orderResult[0]['stop_order'];

    //print_r($stop_order);
    // Get latest stop with lat/lng
    $stmt = $conn->prepare("select name,latitude,longitude from stops s 
 join trips t 
 on s.bus_id = t.bus_id 
 where t.bus_id =? and t.status like 'started' and s.stop_order = ?");
    $stmt->execute([$bus_id, $stop_order]);

   // $stmt->execute();
    $current_stop = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        /* Fade-in animation for the card */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.8s ease-out;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
    </style>


<div class="container mt-5">
    <h2 class="mb-4 text-center text-primary">Bus Tracking - Bus : <?php echo htmlspecialchars($bus_number); ?></h2>
    <div class="card shadow bg-warning">
        <div class="card-body">
           <h5><i class="bi bi-geo-alt-fill"></i> Current Stop :</h5>
            <p class="text-success"><?php echo htmlspecialchars($current_stop['name']); ?></p>

            <!-- Map -->
            <div id="map" class="mb-4"></div>

            <!-- Drive Button -->
            <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $current_stop['latitude']; ?>,<?php echo $current_stop['longitude']; ?>"
               target="_blank"
               class="btn btn-outline-success mt-2 mb-4">
                Drive to this location
            </a>

            <h5><i class="bi bi-signpost-split-fill"></i> Route: <?php echo htmlspecialchars($route_name); ?></h5>
            <br>
            <ol>
                <h5><i class="bi bi-list-ol"></i> Stop Details:</h5>
                <?php foreach ($route_stops as $stop): ?>
                    <li><?php echo htmlspecialchars($stop['name']); ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const latitude = <?php echo $current_stop['latitude']; ?>;
        const longitude = <?php echo $current_stop['longitude']; ?>;
        const stopName = "<?php echo htmlspecialchars($current_stop['name']); ?>";

        const map = L.map('map').setView([latitude, longitude], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([latitude, longitude]).addTo(map)
            .bindPopup(stopName)
            .openPopup();
    });
</script>

<?
ob_end_flush();
include 'footer.php';
?>