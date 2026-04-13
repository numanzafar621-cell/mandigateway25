<?php 
include '../config.php'; 
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$user = getUserData($_SESSION['user_id']);
if ($user['role'] != 'admin') {
    die("Access Denied! Only Admin can access this panel.");
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$pending_users = $conn->query("SELECT COUNT(*) as c FROM users WHERE status='pending' AND role='user'")->fetch_assoc()['c'];
$total_products = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$pending_products = $conn->query("SELECT COUNT(*) as c FROM products WHERE status='pending'")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT SUM(total) as sum FROM orders WHERE status='completed'")->fetch_assoc()['sum'];
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - MandiGateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .sidebar { background: #1a1e2b; min-height: 100vh; }
        .stat-card { border-radius: 15px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Admin Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar text-white p-3">
            <h4 class="text-center mb-4"><i class="fas fa-crown"></i> Admin Panel</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="index.php" class="nav-link text-white active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="users.php" class="nav-link text-white"><i class="fas fa-users"></i> Users & Stores</a></li>
                <li class="nav-item"><a href="products.php" class="nav-link text-white"><i class="fas fa-box"></i> Pending Products</a></li>
                <li class="nav-item"><a href="orders.php" class="nav-link text-white"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
                <li class="nav-item"><a href="chat.php" class="nav-link text-white"><i class="fas fa-comments"></i> Store Chats</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2>Welcome, <?= htmlspecialchars($user['full_name']) ?> (Super Admin)</h2>
            <p class="text-muted">Manage all stores, users, and products from here.</p>
            
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card stat-card bg-primary text-white p-3">
                        <div class="d-flex justify-content-between">
                            <div><i class="fas fa-store fa-3x"></i></div>
                            <div><h2 class="display-6"><?= $total_users ?></h2></div>
                        </div>
                        <h5>Total Stores</h5>
                        <small><?= $pending_users ?> pending approval</small>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card stat-card bg-warning text-white p-3">
                        <div class="d-flex justify-content-between">
                            <div><i class="fas fa-box fa-3x"></i></div>
                            <div><h2 class="display-6"><?= $pending_products ?></h2></div>
                        </div>
                        <h5>Pending Products</h5>
                        <small>Need approval</small>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card stat-card bg-success text-white p-3">
                        <div class="d-flex justify-content-between">
                            <div><i class="fas fa-rupee-sign fa-3x"></i></div>
                            <div><h2 class="display-6"><?= number_format($total_revenue ?? 0) ?></h2></div>
                        </div>
                        <h5>Total Revenue</h5>
                        <small>Completed orders</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">Recent Orders</div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Order #</th><th>Store</th><th>Customer</th><th>Total</th></tr></thead>
                                <tbody>
                                    <?php
                                    $recent_orders = $conn->query("SELECT o.*, u.business_name FROM orders o JOIN users u ON o.store_user_id = u.id ORDER BY o.id DESC LIMIT 10");
                                    while($ord = $recent_orders->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td>#<?= $ord['id'] ?></td>
                                        <td><?= htmlspecialchars($ord['business_name']) ?></td>
                                        <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                                        <td>Rs. <?= number_format($ord['total']) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">Recent Users</div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Business</th><th>Email</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php
                                    $recent_users = $conn->query("SELECT * FROM users WHERE role='user' ORDER BY id DESC LIMIT 10");
                                    while($usr = $recent_users->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usr['business_name']) ?></td>
                                        <td><?= $usr['email'] ?></td>
                                        <td><span class="badge bg-<?= $usr['status']=='active'?'success':($usr['status']=='pending'?'warning':'danger') ?>"><?= $usr['status'] ?></span></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>