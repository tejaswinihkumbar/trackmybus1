<?php
ob_start();
include 'header.php';
include 'dbconfig.php';

if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Handle assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bus_id'], $_POST['driver_id'])) {
    $stmt = $conn->prepare("UPDATE buses SET driver_id = ? WHERE id = ?");
    $stmt->execute([$_POST['driver_id'], $_POST['bus_id']]);
    header("Location: admin_add_driver.php ? message = success");
    exit();
}

// Handle removal
if (isset($_GET['remove'])) {
    $stmt = $conn->prepare("UPDATE buses SET driver_id = NULL WHERE id = ?");
    $stmt->execute([$_GET['remove']]);
    header("Location: admin_add_driver.php");
    exit();
}

// Fetch users with DRIVER role
$driverUsers = $conn->query("
    SELECT u.id, u.first_name, u.last_name
    FROM users u
    JOIN users_roles ur ON u.id = ur.user_id
    JOIN roles r ON ur.role_id = r.id
    WHERE r.name = 'DRIVER'
")->fetchAll(PDO::FETCH_ASSOC);

// Map users to driver records
$drivers = [];
foreach ($driverUsers as $u) {
    $stmt = $conn->prepare("SELECT id FROM drivers WHERE user_id = ?");
    $stmt->execute([$u['id']]);
    $driverRecord = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($driverRecord) {
        $drivers[] = [
            'id' => $driverRecord['id'],
            'name' => $u['first_name'] . ' ' . $u['last_name']
        ];
    }
}

// Fetch all buses with assigned driver details
$buses = $conn->query("
    SELECT 
        b.id AS bus_id,
        b.vehicle_number,
        d.id AS driver_id,
        u.first_name,
        u.last_name,
        u.email,
        u.phone_number
    FROM buses b
    LEFT JOIN drivers d ON b.driver_id = d.id
    LEFT JOIN users u ON d.user_id = u.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Assign Driver to Bus</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] == 'success'): ?>
    <div class="alert alert-success">Operation completed successfully.</div>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-5">
        <div class="col-md-4">
            <label for="bus_id" class="form-label">Select Bus</label>
            <select name="bus_id" class="form-select" required>
                <option value="">-- Select Bus --</option>
                <?php foreach ($buses as $b): ?>
                    <option value="<?= $b['bus_id'] ?>">
                        <?= htmlspecialchars($b['vehicle_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="driver_id" class="form-label">Select Driver</label>
            <select name="driver_id" class="form-select" required>
                <option value="">-- Select Driver --</option>
                <?php foreach ($drivers as $d): ?>
                    <option value="<?= $d['id'] ?>">
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-success mt-2">Assign Driver</button>
        </div>
    </form>

    <h4 class="mb-3">Current Assignments</h4>
    <table class="table table-bordered">
        <thead class="bg-warning text-white">
            <tr>
                <th>Bus</th>
                <th>Driver Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buses as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['vehicle_number']) ?></td>
                    <td><?= $b['first_name'] ? htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) : 'Not Assigned' ?></td>
                    <td><?= $b['email'] ?? '-' ?></td>
                    <td><?= $b['phone_number'] ?? '-' ?></td>
                    <td>
                        <?php if ($b['driver_id']): ?>
                            <a href="?remove=<?= $b['bus_id'] ?>" class="btn btn-outline-danger mt-2 btn-sm">Remove</a>
                        <?php else: ?>
                            <span class="text-muted">Not Assigned</span> <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php 
ob_end_flush();
include 'footer.php';
?>