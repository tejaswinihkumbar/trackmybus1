<?php include 'header.php';

require 'dbconfig.php'; // Make sure this file has your PDO setup

try {
    $stmt = $conn->prepare("SELECT id, vehicle_number FROM buses");
    $stmt->execute();
    $buses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching buses: " . $e->getMessage();
    $buses = []; // fallback to empty array to avoid crash
}
?>

<!-- Add Animate.css CDN for smooth animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<!-- Custom CSS for transitions -->
<style>
    .fade-in {
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .btn-custom:hover {
        transform: scale(1.05);
        transition: 0.3s ease;
    }

    #about {
        background-color: #f9f9f9;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }
</style>

<!-- Hero Section -->
<header class="bg-warning text-light text-center py-5 fade-in">
    <div class="container">
        <h1 class="display-4 animate__animated animate__fadeInDown">Welcome to Bus Tracking!!</h1>
        <p class="lead animate__animated animate__fadeInUp">Track your college bus in real time and never miss it again.</p>
        <a href="aboutUs.php" class="btn btn-light btn-lg text-light btn-custom animate__animated animate__fadeInUp" 
           style="background-color:rgb(199, 91, 77); border:none;">Learn More</a>
    </div>
</header>

<script>
    // Show current date & time
    document.addEventListener("DOMContentLoaded", function () {
        const dateTimeElement = document.getElementById("currentDateTime");
        if (dateTimeElement) {
            const now = new Date();
            const formatted = now.toLocaleString();
            dateTimeElement.innerText = `Date & Time: ${formatted}`;
        }
    });
</script>

<?php include 'homeCarousel.php'; ?>

<!-- About Section -->
<section id="about" class="py-5 fade-in">
    <div class="container">
        <h2 class="text-center">About Us</h2>
        <p class="lead text-center">At Track My Bus, we deliver a reliable, student-focused transit experience by combining real-time tracking with smart technology to ensure timely and stress-free commutes.</p>
    </div>
</section>

<?php include 'footer.php'; ?>