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
                <h5>Your Bus Location</h5>
                <p>View your upcoming bus stop.</p>
                <a href="busLocation.php" class="btn btn-primary">View Bus Location </a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5>Profile Settings</h5>
                <p>Update your contact details and preferences.</p>
                <a href="profile.php" class="btn btn-secondary">Edit Profile</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
