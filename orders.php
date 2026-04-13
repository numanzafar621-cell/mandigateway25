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
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .sidebar { background: #1a1e2b; min-height: 100vh; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar text-white p-3">
            <h4 class="text-center mb-4"><i class="fas fa-crown"></i> Admin Panel</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="index.php" class="nav-link text-white"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="users.php" class="nav-link text-white"><i class="fas fa-users"></i> Users & Stores</a></li>
                <li class="nav-item"><a href="products.php" class="nav-link text-white"><i class="fas fa-box"></i> Pending Products</a></li>
                <li class="nav-item"><a href="orders.php" class="nav-link text-white active"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
                <li class="nav-item"><a href="chat.php" class="nav-link text-white"><i class="fas fa-comments"></i> Store Chats</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-shopping-cart"></i> All Orders (All Stores)</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th><th>Store</th><th>Customer</th><th>Phone</th><th>Address</th>
                            <th>Total</th><th>Payment</th><th>Status</th><th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $conn->query("SELECT o.*, u.business_name FROM orders o JOIN users u ON o.store_user_id = u.id ORDER BY o.id DESC");
                        if($orders->num_rows > 0):
                            while($ord = $orders->fetch_assoc()):
                                $status_class = $ord['status'] == 'completed' ? 'success' : ($ord['status'] == 'cancelled' ? 'danger' : 'warning');
                        ?>
                        <tr>
                            <td>#<?= $ord['id'] ?></td>
                            <td><?= htmlspecialchars($ord['business_name']) ?></td>
                            <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                            <td><?= $ord['customer_phone'] ?></td>
                            <td><?= htmlspecialchars(substr($ord['customer_address'], 0, 40)) ?>...</td>
                            <td>Rs. <?= number_format($ord['total']) ?></td>
                            <td><?= $ord['payment_method'] ?></td>
                            <td><span class="badge bg-<?= $status_class ?>"><?= $ord['status'] ?></span></td>
                            <td><?= date('d M Y', strtotime($ord['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="9" class="text-center">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>