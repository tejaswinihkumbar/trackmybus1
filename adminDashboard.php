<?php
$pageTitle = "Admin Dashboard | Track My Bus";
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Fetch counts
try {
    $userCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $busCount = $conn->query("SELECT COUNT(*) FROM buses")->fetchColumn();
    $routeCount = $conn->query("SELECT COUNT(*) FROM routes")->fetchColumn();
    $stopCount = $conn->query("SELECT COUNT(*) FROM stops")->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching dashboard data: " . $e->getMessage());
}
?>

<!-- AOS & Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<style>
/* Button active state */
.btn-warning:active {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: black !important;
}

/* Card hover effect */
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: scale(1.03);
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
}

/* Button hover effect */
.btn-warning {
    transition: all 0.3s ease;
}
.btn-warning:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
}
</style>

<div class="container mt-5">
    <h2 class="text-center mb-4" data-aos="fade-down">Welcome to Admin Dashboard</h2>

    <div class="row text-center mb-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
            <div class="card bg-secondary text-white p-3 shadow-sm">
                <h5><i class="bi bi-people-fill"></i> Total Users</h5>
                <h3><?php echo $userCount; ?></h3>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
            <div class="card bg-secondary text-white p-3 shadow-sm">
                <h5><i class="bi bi-bus-front-fill"></i> Total Buses</h5>
                <h3><?php echo $busCount; ?></h3>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
            <div class="card bg-secondary text-white p-3 shadow-sm">
                <h5><i class="bi bi-signpost-2-fill"></i> Total Routes</h5>
                <h3><?php echo $routeCount; ?></h3>
            </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
            <div class="card bg-secondary text-white p-3 shadow-sm">
                <h5><i class="bi bi-geo-alt-fill"></i> Total Stops</h5>
                <h3><?php echo $stopCount; ?></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4" data-aos="fade-right">
            <div class="card p-3 shadow-sm">
                <h5><i class="bi bi-person-gear"></i> Role Management</h5>
                <p>Assign role to specific user.</p>
                <a href="manage_roles.php" class="btn btn-outline-warning mt-2">Manage Roles</a>
            </div>
        </div>
        <div class="col-md-6 mb-4" data-aos="fade-left">
            <div class="card p-3 shadow-sm">
                <h5><i class="bi bi-truck-front-fill"></i> Bus Management</h5>
                <p>Assign bus number, route and its stops.</p>
                <a href="admin_manage_buses.php" class="btn btn-outline-warning mt-2">Manage Buses</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4" data-aos="fade-right">
            <div class="card p-3 shadow-sm">
                <h5><i class="bi bi-person-badge-fill"></i> Driver Management</h5>
                <p>Assign bus to registered drivers.</p>
                <a href="admin_add_driver.php" class="btn btn-outline-warning mt-2">Manage Drivers</a>
            </div>
        </div> 
        <div class="col-md-6 mb-4" data-aos="fade-left">
            <div class="card p-3 shadow-sm">
                <h5><i class="bi bi-people"></i> User Management</h5>
                <p>Assign bus to registered users.</p>
                <a href="admin_assign_bus.php" class="btn btn-outline-warning mt-2">Manage Users</a>
            </div>
        </div>
        <div class="col-md-6 mb-4" data-aos="fade-up">
            
              <div class="card p-3 shadow-sm">
                <h5><i class="bi bi-person-badge-fill"></i>View Messages </h5>
                <p>View user Messages.</p>
                <a href="admin__contact.php" class="btn btn-outline-warning mt-2">View Messages</a>
              </div>
            
        </div>
    </div>
</div>

<!-- AOS Script -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    once: true
  });
</script>

<?php include 'footer.php'; ?>