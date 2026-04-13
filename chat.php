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

$selected_store = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
$chats = [];
if ($selected_store) {
    $chats = $conn->query("SELECT * FROM chat_messages WHERE store_user_id = $selected_store ORDER BY created_at ASC");
}
$stores = $conn->query("SELECT DISTINCT u.id, u.business_name FROM chat_messages c JOIN users u ON c.store_user_id = u.id ORDER BY u.business_name");
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Store Chats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .sidebar { background: #1a1e2b; min-height: 100vh; }
        .chat-message { border-bottom: 1px solid #eee; padding: 10px; }
        .customer { background: #e3f2fd; }
        .owner { background: #f1f8e9; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar text-white p-3">
            <h4 class="text-center mb-4"><i class="fas fa-crown"></i> Admin Panel</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="index.php" class="nav-link text-white"><i class="fas fa-home"></i> Dashboard</a></li>
                <li class="nav-item"><a href="users.php" class="nav-link text-white"><i class="fas fa-users"></i> Users & Stores</a></li>
                <li class="nav-item"><a href="products.php" class="nav-link text-white"><i class="fas fa-box"></i> Pending Products</a></li>
                <li class="nav-item"><a href="orders.php" class="nav-link text-white"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
                <li class="nav-item"><a href="chat.php" class="nav-link text-white active"><i class="fas fa-comments"></i> Store Chats</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-comments"></i> All Store Chats</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">Stores with Chats</div>
                        <div class="list-group list-group-flush">
                            <?php if($stores->num_rows > 0): ?>
                                <?php while($store = $stores->fetch_assoc()): ?>
                                    <a href="?store_id=<?= $store['id'] ?>" class="list-group-item list-group-item-action <?= ($selected_store == $store['id']) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($store['business_name']) ?>
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="list-group-item">No chats yet</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <?php if($selected_store && $chats && $chats->num_rows > 0): ?>
                        <div class="card">
                            <div class="card-header bg-secondary text-white">Chat Messages</div>
                            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                <?php while($msg = $chats->fetch_assoc()): ?>
                                    <div class="chat-message <?= $msg['sender'] == 'customer' ? 'customer' : 'owner' ?>">
                                        <strong><?= ucfirst($msg['sender']) ?> (<?= htmlspecialchars($msg['customer_name']) ?>):</strong>
                                        <p class="mb-0"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                                        <small class="text-muted"><?= $msg['created_at'] ?></small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php elseif($selected_store): ?>
                        <div class="alert alert-info">No messages for this store.</div>
                    <?php else: ?>
                        <div class="alert alert-secondary">Select a store from the left to view chats.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>