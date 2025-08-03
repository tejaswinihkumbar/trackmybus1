<?php
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

$driverId = $_GET['id'] ?? null;

if (!$driverId) {
    die("Invalid driver ID.");
}

// Fetch driver info
$stmt = $conn->prepare("SELECT d.id, d.name, d.email, d.phone, b.id as bus_id 
                        FROM drivers d 
                        JOIN buses b ON b.driver_id = d.id 
                        WHERE d.id = ?");
$stmt->execute([$driverId]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all buses
$busStmt = $conn->prepare("SELECT id, vehicle_number FROM buses");
$busStmt->execute();
$buses = $busStmt->fetchAll(PDO::FETCH_ASSOC);

// On form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $bus_id = $_POST['bus_id'];

    $conn->beginTransaction();

    // Update driver
    $updateDriver = $conn->prepare("UPDATE drivers SET name = ?, phone = ? WHERE id = ?");
    $updateDriver->execute([$name, $phone, $driverId]);

    // Update bus assignment
    $clearOldBus = $conn->prepare("UPDATE buses SET driver_id = NULL WHERE driver_id = ?");
    $clearOldBus->execute([$driverId]);

    $assignNewBus = $conn->prepare("UPDATE buses SET driver_id = ? WHERE id = ?");
    $assignNewBus->execute([$driverId, $bus_id]);

    $conn->commit();

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!-- HTML -->
<h2>Edit Driver</h2>
<form method="POST">
    <label>Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($driver['name']) ?>" required><br>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($driver['phone']) ?>" required><br>

    <label>Assign Bus</label>
    <select name="bus_id" required>
        <?php foreach ($buses as $bus): ?>
            <option value="<?= $bus['id'] ?>" <?= $bus['id'] == $driver['bus_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($bus['vehicle_number']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Update</button>
</form>