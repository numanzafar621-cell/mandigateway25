<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}

$user = getUserData($_SESSION['user_id']);
if ($user['status'] != 'active') {
    header("Location: ../verify.php"); 
    exit();
}

$store = getStoreData($user['id']);

// Count products
$prod_count = $conn->query("SELECT COUNT(*) as c FROM products WHERE user_id = {$user['id']}")->fetch_assoc()['c'];
// Count orders
$order_count = $conn->query("SELECT COUNT(*) as c FROM orders WHERE store_user_id = {$user['id']}")->fetch_assoc()['c'];
// Count pending products
$pending_count = $conn->query("SELECT COUNT(*) as c FROM products WHERE user_id = {$user['id']} AND status='pending'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($user['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
                <a href="../store/index.php?preview=1" target="_blank" class="btn btn-success">
                    <i class="fas fa-eye"></i> View My Store
                </a>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-store"></i> Your store URL: 
                <strong><a href="http://<?= $store['subdomain'] ?>.localhost/mandigateway/store/" target="_blank">
                    http://<?= $store['subdomain'] ?>.mandigateway.com
                </a></strong>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-3 mb-4">
                    <div class="card text-center p-3 bg-primary text-white">
                        <i class="fas fa-box fa-2x mb-2"></i>
                        <h5>Total Products</h5>
                        <h2><?= $prod_count ?></h2>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center p-3 bg-success text-white">
                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                        <h5>Total Orders</h5>
                        <h2><?= $order_count ?></h2>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center p-3 bg-warning text-white">
                        <i class="fas fa-hourglass-half fa-2x mb-2"></i>
                        <h5>Pending Products</h5>
                        <h2><?= $pending_count ?></h2>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center p-3 bg-info text-white">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h5>Store Rating</h5>
                        <h2><?php 
                            $rating = $conn->query("SELECT AVG(rating) as avg FROM reviews WHERE store_user_id = {$user['id']}")->fetch_assoc()['avg'];
                            echo $rating ? number_format($rating, 1) : '0.0';
                        ?></h2>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5><i class="fas fa-bell"></i> Quick Actions</h5>
                        <hr>
                        <a href="products.php" class="btn btn-outline-primary mb-2"><i class="fas fa-plus"></i> Add New Product</a>
                        <a href="categories.php" class="btn btn-outline-secondary mb-2"><i class="fas fa-tags"></i> Manage Categories</a>
                        <a href="orders.php" class="btn btn-outline-info"><i class="fas fa-truck"></i> View Orders</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h5><i class="fas fa-chart-line"></i> Recent Orders</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recent = $conn->query("SELECT * FROM orders WHERE store_user_id = {$user['id']} ORDER BY id DESC LIMIT 5");
                                    if($recent->num_rows > 0):
                                        while($ord = $recent->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td>#<?= $ord['id'] ?></td>
                                        <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                                        <td>Rs. <?= number_format($ord['total']) ?></td>
                                        <td><span class="badge bg-secondary"><?= $ord['status'] ?></span></td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                    <tr><td colspan="4" class="text-center">No orders yet</td></tr>
                                    <?php endif; ?>
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