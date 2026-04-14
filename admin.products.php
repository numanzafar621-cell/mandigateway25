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

// Approve Product
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE products SET status='active' WHERE id=$id");
    header("Location: products.php");
    exit();
}
// Reject Product
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE products SET status='rejected' WHERE id=$id");
    header("Location: products.php");
    exit();
}
// Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
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
                <li class="nav-item"><a href="products.php" class="nav-link text-white active"><i class="fas fa-box"></i> Pending Products</a></li>
                <li class="nav-item"><a href="orders.php" class="nav-link text-white"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
                <li class="nav-item"><a href="chat.php" class="nav-link text-white"><i class="fas fa-comments"></i> Store Chats</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-clock"></i> Pending Products (Need Approval)</h2>
            
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><a class="nav-link active" href="?tab=pending">Pending</a></li>
                <li class="nav-item"><a class="nav-link" href="?tab=all">All Products</a></li>
            </ul>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Image</th><th>Title</th><th>Store</th><th>Price</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tab = $_GET['tab'] ?? 'pending';
                        if($tab == 'pending') {
                            $query = "SELECT p.*, u.business_name FROM products p JOIN users u ON p.user_id = u.id WHERE p.status='pending' ORDER BY p.created_at DESC";
                        } else {
                            $query = "SELECT p.*, u.business_name FROM products p JOIN users u ON p.user_id = u.id ORDER BY p.id DESC";
                        }
                        $result = $conn->query($query);
                        if($result->num_rows > 0):
                            while($prod = $result->fetch_assoc()):
                                $status_class = $prod['status'] == 'active' ? 'success' : ($prod['status'] == 'pending' ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td><?= $prod['id'] ?></td>
                            <td>
                                <?php if($prod['image']): ?>
                                    <img src="../<?= $prod['image'] ?>" width="60" height="60" style="object-fit:cover;">
                                <?php else: ?>
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($prod['title']) ?></td>
                            <td><?= htmlspecialchars($prod['business_name']) ?></td>
                            <td>Rs. <?= number_format($prod['price']) ?></td>
                            <td><span class="badge bg-<?= $status_class ?>"><?= $prod['status'] ?></span></td>
                            <td>
                                <?php if($prod['status'] == 'pending'): ?>
                                    <a href="?approve=<?= $prod['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this product?')">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <a href="?reject=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this product?')">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
                                <?php endif; ?>
                                <?php if($prod['status'] != 'pending'): ?>
                                    <a href="?delete=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                             </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>