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

// Activate/Deactivate User
if (isset($_GET['activate'])) {
    $id = intval($_GET['activate']);
    $conn->query("UPDATE users SET status='active' WHERE id=$id");
    header("Location: users.php");
    exit();
}
if (isset($_GET['suspend'])) {
    $id = intval($_GET['suspend']);
    $conn->query("UPDATE users SET status='suspended' WHERE id=$id");
    header("Location: users.php");
    exit();
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Also delete related store and products
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
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
                <li class="nav-item"><a href="users.php" class="nav-link text-white active"><i class="fas fa-users"></i> Users & Stores</a></li>
                <li class="nav-item"><a href="products.php" class="nav-link text-white"><i class="fas fa-box"></i> Pending Products</a></li>
                <li class="nav-item"><a href="orders.php" class="nav-link text-white"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
                <li class="nav-item"><a href="chat.php" class="nav-link text-white"><i class="fas fa-comments"></i> Store Chats</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-users"></i> All Users & Stores</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th><th>Business Name</th><th>Full Name</th><th>Email</th><th>Phone</th>
                            <th>Subdomain</th><th>Status</th><th>CNIC</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT u.*, s.subdomain FROM users u LEFT JOIN stores s ON u.id = s.user_id WHERE u.role='user' ORDER BY u.id DESC");
                        while($row = $result->fetch_assoc()):
                            $status_class = $row['status'] == 'active' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['business_name']) ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><strong><?= $row['subdomain'] ?>.mandigateway.com</strong></td>
                            <td><span class="badge bg-<?= $status_class ?>"><?= $row['status'] ?></span></td>
                            <td>
                                <?php if($row['cnic_front']): ?>
                                    <a href="../<?= $row['cnic_front'] ?>" target="_blank" class="btn btn-sm btn-info">Front</a>
                                <?php endif; ?>
                                <?php if($row['cnic_back']): ?>
                                    <a href="../<?= $row['cnic_back'] ?>" target="_blank" class="btn btn-sm btn-info">Back</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] != 'active'): ?>
                                    <a href="?activate=<?= $row['id'] ?>" class="btn btn-sm btn-success">Activate</a>
                                <?php endif; ?>
                                <?php if($row['status'] != 'suspended' && $row['status'] != 'pending'): ?>
                                    <a href="?suspend=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Suspend</a>
                                <?php endif; ?>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user and all data?')">Delete</a>
                                <a href="../dashboard/index.php?user_id=<?= $row['id'] ?>" class="btn btn-sm btn-info" target="_blank">View Dashboard</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>