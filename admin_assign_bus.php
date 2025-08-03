<?php
include 'header.php';
include 'dbconfig.php'; // Your PDO connection

// Ensure only admins access
if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, first_name, last_name FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all buses
$stmt = $conn->prepare("SELECT id, vehicle_number FROM buses");
$stmt->execute();
$allBuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $bus_id = $_POST['bus_id'];

    $assignedUser = "User"; // fallback
    foreach ($users as $user) {
        if ($user['id'] == $user_id) {
            $assignedUser = $user['first_name'] . ' ' . $user['last_name'];
            break;
        }
    }

    $stmt = $conn->prepare("UPDATE users SET bus_id = ? WHERE id = ?");
    if ($stmt->execute([$bus_id, $user_id])) {
        $message = "Bus assigned successfully to $assignedUser.";
    } else {
        $message = "Failed to assign the bus.";
    }
}

// Fetch assigned users and their bus + route details
$stmt = $conn->prepare("SELECT u.id, u.first_name, u.last_name, 
                               b.vehicle_number, r.name AS route_name
                        FROM users u
                        LEFT JOIN buses b ON u.bus_id = b.id
                        LEFT JOIN routes r ON b.route_id = r.id
                        WHERE u.bus_id IS NOT NULL");
$stmt->execute();
$assignedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-5">
    <h2 class="mb-4">Assign Bus to User</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm mb-5">
        <div class="mb-3">
            <label class="form-label">Select User</label>
            <select name="user_id" class="form-select" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Bus Number</label>
            <select name="bus_id" class="form-select" required>
                <option value="">-- Select Bus --</option>
                <?php foreach ($allBuses as $bus): ?>
                    <option value="<?= $bus['id'] ?>">
                        <?= htmlspecialchars($bus['vehicle_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-outline-success mt-2 ">Assign bus</button>
    </form>

    <h3 class="mb-3">Assigned Users and Buses</h3>
    <table class="table table-bordered table-striped">
        <thead class="bg-warning text-white">
            <tr>
                <th>User Name</th>
                <th>Assigned Bus</th>
                <th>Route</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignedUsers as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['vehicle_number']) ?></td>
                    <td><?= htmlspecialchars($user['route_name']) ?: 'N/A' ?></td>
                    <td>
                        <a href="edit_user_bus.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-info mt-2">Edit</a>
                        <a href="remove_user_bus.php?id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-outline-danger mt-2"
                           onclick="return confirm('Are you sure you want to remove the assigned bus?')">Remove</a>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
     </table>
</div>
<?php include 'footer.php'; ?>