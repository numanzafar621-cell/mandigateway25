<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ur" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'MandiGateway' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        .main-header {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            padding: 15px 0;
        }
        .nav-link {
            color: white !important;
            font-weight: 500;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<!-- Top Header -->
<header class="main-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0">
                    <a href="../index.php" class="text-white text-decoration-none">
                        <strong>MandiGateway</strong>
                    </a>
                </h4>
            </div>
            <div class="col-md-6 text-end">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="me-3">
                        Welcome, <strong><?= htmlspecialchars($_SESSION['business_name'] ?? 'User') ?></strong>
                    </span>
                    <a href="../dashboard/index.php" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="../logout.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="../login.php" class="btn btn-outline-light btn-sm me-2">Login</a>
                    <a href="../signup.php" class="btn btn-light btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../signup.php">Create Store</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="../dashboard/index.php">Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">