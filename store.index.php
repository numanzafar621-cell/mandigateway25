<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
$user_id = $store['user_id'];

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $search ? "AND (title LIKE '%$search%' OR description LIKE '%$search%')" : '';
$products = mysqli_query($conn, "SELECT * FROM products WHERE user_id = $user_id AND status='active' $where ORDER BY created_at DESC");
$sliders = mysqli_query($conn, "SELECT * FROM sliders WHERE user_id = $user_id ORDER BY position ASC");

// Helper function to get rating for a product
function getProductRating($product_id) {
    global $conn;
    $rating = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE product_id = $product_id")->fetch_assoc();
    return ['avg' => round($rating['avg'], 1), 'total' => $rating['total']];
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['business_name']) ?> - MandiGateway Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 20px 0; }
        .carousel-item img { height: 400px; object-fit: cover; }
        @media (max-width: 768px) { .carousel-item img { height: 200px; } }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<?php if(mysqli_num_rows($sliders) > 0): ?>
<div id="storeCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php $first = true; while($slide = mysqli_fetch_assoc($sliders)): ?>
        <div class="carousel-item <?= $first ? 'active' : '' ?>">
            <img src="../<?= $slide['image'] ?>" class="d-block w-100" alt="Slide">
            <?php if($slide['text']): ?>
            <div class="carousel-caption d-none d-md-block"><h5><?= htmlspecialchars($slide['text']) ?></h5></div>
            <?php endif; ?>
        </div>
        <?php $first = false; endwhile; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#storeCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#storeCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>
<?php endif; ?>

<div class="container mt-4">
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control form-control-lg" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>

    <h4 class="mb-4">Our Products (<?= mysqli_num_rows($products) ?>)</h4>
    
    <div class="grid-container">
        <?php if(mysqli_num_rows($products) > 0): ?>
            <?php while($p = mysqli_fetch_assoc($products)): 
                $rating = getProductRating($p['id']);
            ?>
                <div class="product-card">
                    <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration: none; color: inherit;">
                        <img src="../<?= htmlspecialchars($p['image']) ?>" class="product-img" alt="<?= htmlspecialchars($p['title']) ?>">
                    </a>
                    <div class="product-body">
                        <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration: none; color: inherit;">
                            <h5 class="product-title"><?= htmlspecialchars($p['title']) ?></h5>
                        </a>
                        <div class="product-price">Rs. <?= number_format($p['price']) ?></div>
                        
                        <!-- Star Rating -->
                        <div class="rating">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <?php if($i <= $rating['avg']): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php elseif($i - 0.5 <= $rating['avg']): ?>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-muted"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="text-muted ms-1">(<?= $rating['total'] ?> reviews)</span>
                        </div>
                        
                        <p class="text-muted small mt-2"><?= substr(strip_tags($p['description']), 0, 80) ?>...</p>
                        
                        <div class="mt-auto d-flex gap-2">
                            <a href="cart.php?add=<?= $p['id'] ?>" class="btn btn-primary btn-sm btn-cart flex-grow-1"><i class="fas fa-cart-plus"></i> Cart</a>
                            <a href="https://wa.me/<?= $store['whatsapp_number'] ?>?text=I want to buy <?= urlencode($p['title']) ?> (Rs. <?= $p['price'] ?>)" class="btn btn-success btn-sm btn-wa flex-grow-1" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center"><h5>No products found.</h5></div>
        <?php endif; ?>
    </div>
</div>

<?php if($store['whatsapp_position'] == 'floating' && $store['whatsapp_number']): ?>
    <a href="https://wa.me/<?= $store['whatsapp_number'] ?>" class="whatsapp-float" target="_blank"><i class="fab fa-whatsapp"></i></a>
<?php endif; ?>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
