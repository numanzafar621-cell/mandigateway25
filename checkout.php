<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$user_id = $store['user_id'];

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Calculate total
$total = 0;
$ids = implode(',', $_SESSION['cart']);
$res = $conn->query("SELECT SUM(price) as total FROM products WHERE id IN ($ids) AND user_id = $user_id");
$total = $res->fetch_assoc()['total'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (store_user_id, customer_name, customer_phone, customer_address, total, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssds", $user_id, $name, $phone, $address, $total, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach ($_SESSION['cart'] as $pid) {
        $prod = $conn->query("SELECT price FROM products WHERE id = $pid")->fetch_assoc();
        $price = $prod['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, 1, $price)");
    }
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Redirect to success page
    header("Location: order-success.php?id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
    </style>
</head>
<body>
<header class="header text-center">
    <div class="container">
        <h4><?= htmlspecialchars($store['business_name']) ?> - Checkout</h4>
    </div>
</header>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">Billing Details</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Delivery Address</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="cod">Cash on Delivery (COD)</option>
                                <option value="jazzcash">JazzCash</option>
                                <option value="easypaisa">EasyPaisa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-secondary text-white">Order Summary</div>
                <div class="card-body">
                    <p><strong>Total Items:</strong> <?= count($_SESSION['cart']) ?></p>
                    <hr>
                    <h5>Total: Rs. <?= number_format($total) ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$user_id = $store['user_id'];

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Calculate total
$total = 0;
$ids = implode(',', $_SESSION['cart']);
$res = $conn->query("SELECT SUM(price) as total FROM products WHERE id IN ($ids) AND user_id = $user_id");
$total = $res->fetch_assoc()['total'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (store_user_id, customer_name, customer_phone, customer_address, total, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssds", $user_id, $name, $phone, $address, $total, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach ($_SESSION['cart'] as $pid) {
        $prod = $conn->query("SELECT price FROM products WHERE id = $pid")->fetch_assoc();
        $price = $prod['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, 1, $price)");
    }
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Redirect to success page
    header("Location: order-success.php?id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
    </style>
</head>
<body>
<header class="header text-center">
    <div class="container">
        <h4><?= htmlspecialchars($store['business_name']) ?> - Checkout</h4>
    </div>
</header>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">Billing Details</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Delivery Address</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="cod">Cash on Delivery (COD)</option>
                                <option value="jazzcash">JazzCash</option>
                                <option value="easypaisa">EasyPaisa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-secondary text-white">Order Summary</div>
                <div class="card-body">
                    <p><strong>Total Items:</strong> <?= count($_SESSION['cart']) ?></p>
                    <hr>
                    <h5>Total: Rs. <?= number_format($total) ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$user_id = $store['user_id'];

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Calculate total
$total = 0;
$ids = implode(',', $_SESSION['cart']);
$res = $conn->query("SELECT SUM(price) as total FROM products WHERE id IN ($ids) AND user_id = $user_id");
$total = $res->fetch_assoc()['total'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (store_user_id, customer_name, customer_phone, customer_address, total, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssds", $user_id, $name, $phone, $address, $total, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach ($_SESSION['cart'] as $pid) {
        $prod = $conn->query("SELECT price FROM products WHERE id = $pid")->fetch_assoc();
        $price = $prod['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, 1, $price)");
    }
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Redirect to success page
    header("Location: order-success.php?id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
    </style>
</head>
<body>
<header class="header text-center">
    <div class="container">
        <h4><?= htmlspecialchars($store['business_name']) ?> - Checkout</h4>
    </div>
</header>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">Billing Details</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Delivery Address</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="cod">Cash on Delivery (COD)</option>
                                <option value="jazzcash">JazzCash</option>
                                <option value="easypaisa">EasyPaisa</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-secondary text-white">Order Summary</div>
                <div class="card-body">
                    <p><strong>Total Items:</strong> <?= count($_SESSION['cart']) ?></p>
                    <hr>
                    <h5>Total: Rs. <?= number_format($total) ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>