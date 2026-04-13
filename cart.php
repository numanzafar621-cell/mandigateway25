<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$user_id = $store['user_id'];

// Add to Cart
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (!in_array($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $product_id;
    }
    header("Location: cart.php?msg=added");
    exit();
}

// Remove from Cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_diff($_SESSION['cart'], [$product_id]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header("Location: cart.php?msg=removed");
    exit();
}

// Clear Cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Calculate total and fetch products
$total = 0;
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', $_SESSION['cart']);
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids) AND user_id = $user_id AND status='active'");
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total += $row['price'];
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
        .cart-item { border-bottom: 1px solid #eee; padding: 15px 0; }
        .whatsapp-float { position: fixed; bottom: 20px; right: 20px; background: #25D366; color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; z-index: 999; text-decoration: none; }
    </style>
</head>
<body>
<header class="header text-center">
    <div class="container">
        <h4><?= htmlspecialchars($store['business_name']) ?> - Shopping Cart</h4>
    </div>
</header>

<div class="container mt-4">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success">Product added to cart!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'removed'): ?>
        <div class="alert alert-info">Product removed from cart.</div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Shop</a>

    <?php if (!empty($cart_items)): ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item d-flex align-items-center">
                        <img src="../<?= htmlspecialchars($item['image']) ?>" width="80" height="80" class="rounded me-3" style="object-fit:cover;">
                        <div class="flex-grow-1">
                            <h6><?= htmlspecialchars($item['title']) ?></h6>
                            <strong class="text-success">Rs. <?= number_format($item['price']) ?></strong>
                        </div>
                        <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-4">
                <div class="card sticky-top" style="top:20px;">
                    <div class="card-body">
                        <h5>Order Summary</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Amount:</strong>
                            <strong>Rs. <?= number_format($total) ?></strong>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 mb-3">Proceed to Checkout</a>
                        <a href="cart.php?clear=1" class="btn btn-outline-danger w-100">Clear Cart</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <h3>Your Cart is Empty</h3>
            <a href="index.php" class="btn btn-primary btn-lg mt-3">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<!-- Floating WhatsApp -->
<?php if(!empty($store['whatsapp_number'])): ?>
    <a href="https://wa.me/<?= $store['whatsapp_number'] ?>" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>