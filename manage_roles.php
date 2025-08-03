<?php
ob_start();
include 'header.php';
include 'dbconfig.php';

$message = "";

// Fetch roles
$roles = $conn->query("SELECT id, name FROM roles")->fetchAll(PDO::FETCH_ASSOC);

// Assign role to user by email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_role'])) {
    $email = $_POST['email'];
    $roleId = $_POST['role_id'];

    // Fetch user by email
    $userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $userStmt->execute([$email]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userId = $user['id'];

        $check = $conn->prepare("SELECT * FROM users_roles WHERE user_id = ? AND role_id = ?");
        $check->execute([$userId, $roleId]);

        if ($check->rowCount() === 0) {
            $stmt = $conn->prepare("INSERT INTO users_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$userId, $roleId]);
            $message = "Role assigned successfully.";

            // If role is DRIVER, add to drivers table if not exists
            $roleName = $conn->prepare("SELECT name FROM roles WHERE id = ?");
            $roleName->execute([$roleId]);
            $roleRow = $roleName->fetch(PDO::FETCH_ASSOC);

            if (strtoupper($roleRow['name']) === 'DRIVER') {
                $driverCheck = $conn->prepare("SELECT * FROM drivers WHERE user_id = ?");
                $driverCheck->execute([$userId]);

                if ($driverCheck->rowCount() === 0) {
                    $insertDriver = $conn->prepare("INSERT INTO drivers (user_id) VALUES (?)");
                    $insertDriver->execute([$userId]);
                }
            }
        } else {
            $message = "User already has this role.";
        }
    } else {
        $message = "No user found with this email.";
    }
}

// Remove a role from a user
if (isset($_GET['remove_user_role'])) {
    $userId = $_GET['user_id'];
    $roleId = $_GET['role_id'];

    $conn->prepare("DELETE FROM users_roles WHERE user_id = ? AND role_id = ?")->execute([$userId, $roleId]);
    $message = "Role removed from user.";
    header("Location: manage_roles.php");
    exit();
}

// Fetch assigned roles
$assignedRoles = $conn->query("
    SELECT ur.user_id, ur.role_id, r.name AS role_name, u.first_name, u.last_name
    FROM users_roles ur
    JOIN users u ON ur.user_id = u.id
    JOIN roles r ON ur.role_id = r.id
    ORDER BY u.first_name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
.card, .alert, .toast { animation: fadeIn 0.5s ease-in-out; }
.table tbody tr:hover { background-color: #fff8e1; transition: background-color 0.3s; }
.btn-success:hover, .btn-danger:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    transform: scale(1.03);
    transition: 0.2s ease-in-out;
}
</style>

<div class="container mt-5">
    <h2>Manage User Roles</h2>

    <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-sm btn-outline-secondary" id="toggleDarkMode">Toggle Dark Mode</button>
</div>
    <?php if (!empty($message)): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div class="toast show text-white bg-info shadow" role="alert">
            <div class="d-flex">
                <div class="toast-body"><?= htmlspecialchars($message) ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Assign Role -->
    <form method="post" class="card p-4 shadow-sm mb-4">
        <h5>Assign Role to User</h5>
        <div class="mb-3">
            <label>User Email</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter user's email">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role_id" class="form-control" required>
                <option value="">Select role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="assign_role" class="btn btn-outline-success mt-2">Assign Role</button>
    </form>

    <!-- Roles Table -->
    <div class="card p-4 shadow-sm mb-5">
        <h5>Assigned Roles to Users</h5>
        <table class="table table-bordered table-striped">
            <thead class="bg-warning text-white">
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignedRoles as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['role_name']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger mt-2" data-bs-toggle="modal" data-bs-target="#confirmRemoveModal" 
                                data-user-id="<?= $row['user_id'] ?>" data-role-id="<?= $row['role_id'] ?>">
                                Remove
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-labelledby="confirmRemoveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmRemoveModalLabel">Confirm Role Removal</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">Are you sure you want to remove this role from the user?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a id="confirmRemoveBtn" href="#" class="btn btn-danger">Yes, Remove</a>
      </div>
    </div>
  </div>
</div>

<script>
const confirmModal = document.getElementById('confirmRemoveModal');
confirmModal.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  const userId = button.getAttribute('data-user-id');
  const roleId = button.getAttribute('data-role-id');
  const confirmBtn = confirmModal.querySelector('#confirmRemoveBtn');
  confirmBtn.href = '?remove_user_role=1&user_id=' +userId+'&role_id='+roleId;
});
</script>

<?php
ob_end_flush(); 
include 'footer.php'; 
?>