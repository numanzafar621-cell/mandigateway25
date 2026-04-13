<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MandiGateway - Apna Store Banaye</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero { background: linear-gradient(135deg, #0d6efd, #6610f2); color: white; padding: 120px 0; }
        .feature-card { transition: transform 0.3s; border-radius: 20px; }
        .feature-card:hover { transform: translateY(-10px); }
    </style>
</head>
<body>
<div class="hero text-center">
    <h1 class="display-3">MandiGateway</h1>
    <p class="lead">Apna Business Online Karo – Free Store Banaye</p>
    <a href="signup.php" class="btn btn-light btn-lg">Abhi Store Banaye</a>
</div>

<div class="container mt-5">
    <div class="row text-center">
        <div class="col-md-4 mb-4">
            <div class="card feature-card p-4 shadow-sm">
                <i class="fas fa-store fa-3x text-primary mb-3"></i>
                <h5>Free Online Store</h5>
                <p>Apna subdomain store banaye bilkul free</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card p-4 shadow-sm">
                <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                <h5>WhatsApp Integration</h5>
                <p>Orders direct WhatsApp par receive karein</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card p-4 shadow-sm">
                <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                <h5>Easy Dashboard</h5>
                <p>Manage products, orders, and customers easily</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>