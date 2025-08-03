<?php
$pageTitle = "User Dashboard | Track My Bus";
include 'header.php';

if (!isset($_SESSION['roles']) || !in_array('USER', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Welcome to Your Dashboard</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5>Track a Bus</h5>
                <p>Choose a bus to view its latest location update.</p>
               <a href="track_bus.php" class="btn btn-outline-warning mt-2">
    <i class="bi bi-bus-front-fill"></i> Track Bus
</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5>Profile Settings</h5>
                <p>Update your contact details and preferences.</p>
                <a href="profile.php" class="btn btn-outline-info mt-2">
    <i class="bi bi-person-gear"></i> Edit Profile
</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
