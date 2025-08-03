<?php
//session_start();
$pageTitle = "Contact Us | Track My Bus";
include 'header.php';
include 'dbconfig.php';

// Check if the user is logged in (you must have a login system that sets $_SESSION['user_id'])
//if (!isset($_SESSION['roles']) || !in_array('USER', $_SESSION['roles'])) {
  //  header("Location: login.php");
    //exit();
//}
// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['name'] ?? '';
    $email     = $_POST['email'] ?? '';
    $message   = $_POST['message'] ?? '';
    $user_id   = $_SESSION['user_id'];

    if ($full_name && $email && $message) {
        $stmt = $conn->prepare("
            INSERT INTO contact_us (contact_us_users_id, full_name, email, message)
            VALUES (:user_id, :full_name, :email, :message)
        ");
        $stmt->execute([
            ':user_id'   => $user_id,
            ':full_name' => $full_name,
            ':email'     => $email,
            ':message'   => $message,
        ]);
        echo "<script>alert('Your message has been submitted successfully!');</script>";
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}

?>

<section class="py-5 ">
    <div class="container">
        <h2 class="text-center mb-5">Contact Us</h2>
        <div class="row">
            <!-- Contact Info + Map -->
            <div class="col-md-6 mb-4 ">
                <h5>Get in Touch</h5>
                <p><i class="bi bi-geo-alt-fill me-2"></i>
                    IBMR College of BCA, Akshaya colony, Vidyanagar, Hubli,<br>
                    Karnataka, India 580024
                </p>
                <p><i class="bi bi-telephone-fill me-2 "></i> Landline 1: <a href="tel:0836-2221111">0836-2221111</a></p>
                <p><i class="bi bi-telephone-outbound-fill me-2"></i> Toll Free: <a href="tel:1800-123-4567">1800-123-4567</a></p>
                <p><i class="bi bi-envelope-fill me-2"></i>
                    Email: <a href="mailto:info@trackmybus.in">info@trackmybus.com</a>
                </p>
                <p>
                    <a href="https://maps.app.goo.gl/8eY9ifaoqPcvvsC37"  target="_blank" class="btn btn-outline-primary mt-2">
                        <i class="bi bi-map-fill me-2"></i>View on Google Maps
                    </a>
                </p>

                <!-- Embedded Map -->
                <div class="ratio ratio-16x9 rounded shadow-sm">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m12!1m8!1m3!1d7694.624684037947!2d75.1100171!3d15.3595615!3m2!1i1024!2i768!4f13.1!2m1!1sibmr%20college%20hubli!5e0!3m2!1sen!2sin!4v1747287796633!5m2!1sen!2sin"

                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-md-6">
                <h5>Send Us a Message</h5>
                <form method="post" action="#">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea name="message" class="form-control" id="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>