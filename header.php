<?php
session_start();
$isLoggedIn = isset($_SESSION['username']);
$pageTitle = isset($pageTitle) ? $pageTitle : "Home | Track My Bus";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="icon" type="img/x-icon" href="img/bus_logo.jpeg">
    <link rel="stylesheet" href="css/app.css" />
    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <?php if (isset($customLoginCss)): ?>
        <link rel="stylesheet" href="<?= $customLoginCss ?>">
    <?php endif; ?>

    <style>
        /* Smooth form transitions */
form.card {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
form.card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* Input focus transition */
input.form-control {
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
input.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Button hover effects */
.btn {
    transition: all 0.3s ease-in-out;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Table row hover */
table tbody tr {
    transition: background-color 0.3s ease, transform 0.3s ease;
}
table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}
body.dark-mode {
    background-color: #121212;
    color: #e0e0e0;
}

body.dark-mode .card,
body.dark-mode .modal-content,
body.dark-mode .table {
    background-color: #1e1e1e;
    color: #e0e0e0;
    border-color: #333;
}

body.dark-mode .table-bordered th,
body.dark-mode .table-bordered td {
    border-color: #444;
}

body.dark-mode .btn-close {
    filter: invert(1);
}

body.dark-mode .bg-warning {
    background-color: #ffb74d !important;
    color: #000 !important;
}

/* Animate alert appearance */
.alert {
    animation: fadeInDown 0.6s ease-in-out;
}

/* Animate form on load */
form.card {
    animation: fadeInUp 0.6s ease;
}

/* Table rows fade-in */
table tbody tr {
    transition: all 0.3s ease;
}
table tbody tr:hover {
    background-color: #f1f1f1;
    transform: scale(1.01);
}

/* New stop input animation */
@keyframes fadeInNewStop {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.row.mb-2 {
    animation: fadeInNewStop 0.4s ease-in-out;
}

/* Basic fade-in up */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Basic fade-in down */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.btn {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* Fade-in and slight scale animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform : translateY(20px);
     }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.container h2 {
    animation: fadeInUp 0.6s ease-out;
}

/* Card animation and smooth hover effect */
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeInUp 0.8s ease-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Button transition */
.btn {
    transition: transform 0.2s ease;
}

.btn:hover {
    transform: scale(1.05);
}


</style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning" 
      style="background-color:rgb(173, 61, 61) !important;">
        <div class="container-fluid">
        <a href="index.php"> <img src="img/bus_logo.jpeg" style=" width: 50px; border-radius:50%; padding-right:5px" alt="Logo"> </a>
            <a class="navbar-brand" href="index.php">Track My Bus</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Home') !== false ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    <!--  <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Services
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Service 1</a></li>
                            <li><a class="dropdown-item" href="#">Service 2</a></li>
                        </ul> 
                    </li> -->
                     <li class="nav-item">
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'About') !== false ? 'active' : ''; ?>" href="aboutUs.php">About Us</a>
                    </li>
					<li class="nav-item">
                        
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Contact') !== false ? 'active' : ''; ?>" href="contactUs.php">Contact Us</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Track') !== false ? 'active' : ''; ?>" href="track_bus.php">Track Bus</a>
                    </li> -->
                   <!-- <li class="nav-item">
                        <a class="nav-link bi bi-bell-fill text-light <?= isset($pageTitle) && strpos($pageTitle, 'Notification') !== false ? 'active' : ''; ?>" href="notification.php">Notification</a>
                    </li>  -->
                  
                    <?php if ($isLoggedIn): ?>
                        <?php
                        $hrefValue='#';
                        $username=isset($_SESSION['displayName'])?$_SESSION['displayName'] : 'Guest';
                        $initial = strtoupper(substr($username, 0, 1));
                        if (isset($_SESSION['roles']) && in_array('ADMIN',$_SESSION['roles']))  {
                            $hrefValue = 'adminDashboard.php';
                        }
                        else if (isset($_SESSION['roles']) && in_array('USER',$_SESSION['roles']))  {
                             $hrefValue ='userDashboard.php';
                        }
                         else if (isset($_SESSION['roles']) && in_array('DRIVER',$_SESSION['roles']))  {
                             $hrefValue ='driverDashboard.php';
                         }
                        ?>
                       <li class="nav-item">
                        <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Dashboard') !== false ? 'active' : ''; ?>"
                        href="<?=$hrefValue ?>" >Dash Board</a>
                    </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle me-2" style="width: 32px; height: 32px; border-radius: 50%; background-color: #6c757d; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                    <?= $initial; ?>
                                </div>
                                <span><?=htmlspecialchars($username);?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile Settings</a></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= isset($pageTitle) && strpos($pageTitle, 'Login') !== false ? 'active' : ''; ?>" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
