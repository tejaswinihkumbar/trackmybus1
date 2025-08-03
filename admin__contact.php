<?php
//session_start();
$pageTitle = "Admin | Contact Messages";
include 'header.php';
include 'dbconfig.php';

// Redirect if not admin
// if (!isset($_SESSION['roles']) || !in_array('ADMIN', $_SESSION['roles'])) {
//      header("Location: login.php");
//  exit();
//  }

// Fetch messages from the contact_us table
$stmt = $conn->query("
    SELECT 
        id,
        contact_us_users_id,
        full_name,
        email,
        message,
        created_at
    FROM contact_us
    ORDER BY created_at DESC
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">User Submitted Messages</h2>

        <?php if (count($messages) === 0): ?>
            <p class="text-center">No messages have been submitted yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">User ID</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Message</th>
                            <th scope="col">Date Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?= htmlspecialchars($msg['id']) ?></td>
                                <td><?= htmlspecialchars($msg['contact_us_users_id']) ?></td>
                                <td><?= htmlspecialchars($msg['full_name']) ?></td>
                                <td><?= htmlspecialchars($msg['email']) ?></td>
                                <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                                <td><?= htmlspecialchars($msg['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>