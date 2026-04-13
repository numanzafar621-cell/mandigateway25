<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$order_id) {
    header("Location: index.php");
    exit();
}

// Get order details
$order = $conn->query("SELECT * FROM orders WHERE id = $order_id AND store_user_id = {$store['user_id']}")->fetch_assoc();
if (!$order) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
    </style>
</head>
<body>
<header class="header text-center">
    <div class="container">
        <h4><?= htmlspecialchars($store['business_name']) ?></h4>
    </div>
</header>

<div class="container mt-5">
    <div class="card text-center p-5">
        <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
        <h2>Thank You for Your Order!</h2>
        <p class="lead">Your order has been placed successfully.</p>
        <hr>
        <p><strong>Order ID:</strong> #<?= $order_id ?></p>
        <p><strong>Total Amount:</strong> Rs. <?= number_format($order['total']) ?></p>
        <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
        <p>You will receive a confirmation call shortly.</p>
        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>