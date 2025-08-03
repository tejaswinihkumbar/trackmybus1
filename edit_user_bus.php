<?php
session_start();
include 'dbconfig.php';

// Ensure admin is logged in
if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Check if user ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_assign_bus.php");
    exit();
}

$userId = $_GET['id'];

// Fetch user info
$stmt = $conn->prepare("SELECT first_name, last_name, bus_id FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_assign_bus.php");
    exit();
}

// Fetch all buses
$busesStmt = $conn->prepare("SELECT id, vehicle_number FROM buses");
$busesStmt->execute();
$buses = $busesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newBusId = $_POST['bus_id'];

    $updateStmt = $conn->prepare("UPDATE users SET bus_id = ? WHERE id = ?");
    $updateStmt->execute([$newBusId, $userId]);

    $_SESSION['success'] = "Bus assignment updated.";
    header("Location: admin_assign_bus.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Bus Assignment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Bus Assignment for <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="form-group">
            <label>Select New Bus</label>
            <select name="bus_id" class="form-control" required>
                <option value="">-- Select Bus --</option>
                <?php foreach ($buses as $bus): ?>
                    <option value="<?= $bus['id'] ?>" <?= $bus['id'] == $user['bus_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($bus['vehicle_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update Assignment</button>
        <a href="admin_assign_bus.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>