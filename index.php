<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$user_id = $store['user_id'];

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $search ? "AND (title LIKE '%$search%' OR description LIKE '%$search%')" : '';

// Get active products
$products = mysqli_query($conn, "SELECT * FROM products WHERE user_id = $user_id AND status='active' $where ORDER BY created_at DESC");

// Get sliders
$sliders = mysqli_query($conn, "SELECT * FROM sliders WHERE user_id = $user_id ORDER BY position ASC");
?>
<!DOCTYPE html>
<html lang="ur" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($store['business_name']) ?> - MandiGateway Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 20px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .product-card { border: none; border-radius: 16px; overflow: hidden; transition: all 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
        .product-img { height: 220px; object-fit: cover; }
        .whatsapp-float { position: fixed; bottom: 20px; right: 20px; background: #25D366; color: white; width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 999; text-decoration: none; }
        .whatsapp-below { margin-top: 10px; }
        .whatsapp-above { margin-bottom: 10px; }
        .carousel-item img { height: 400px; object-fit: cover; }
        @media (max-width: 768px) { .carousel-item img { height: 200px; } }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Slider Section -->
<?php if(mysqli_num_rows($sliders) > 0): ?>
<div id="storeCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php $first = true; while($slide = mysqli_fetch_assoc($sliders)): ?>
        <div class="carousel-item <?= $first ? 'active' : '' ?>">
            <img src="../<?= $slide['image'] ?>" class="d-block w-100" alt="Slide">
            <?php if($slide['text']): ?>
            <div class="carousel-caption d-none d-md-block">
                <h5><?= htmlspecialchars($slide['text']) ?></h5>
            </div>
            <?php endif; ?>
        </div>
        <?php $first = false; endwhile; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#storeCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#storeCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>
<?php endif; ?>

<div class="container mt-4">
    <!-- Search Bar -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control form-control-lg" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>

    <h4 class="mb-4">Our Products (<?= mysqli_num_rows($products) ?>)</h4>
    
    <div class="row">
        <?php if(mysqli_num_rows($products) > 0): ?>
            <?php while($p = mysqli_fetch_assoc($products)): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <?php if($store['whatsapp_position'] == 'above_product' && $store['whatsapp_number']): ?>
                            <a href="https://wa.me/<?= $store['whatsapp_number'] ?>?text=I want to buy <?= urlencode($p['title']) ?> (Rs. <?= $p['price'] ?>)" class="btn btn-success whatsapp-above" target="_blank">
                                <i class="fab fa-whatsapp"></i> Order on WhatsApp
                            </a>
                        <?php endif; ?>
                        
                        <?php if($p['image']): ?>
                            <img src="../<?= htmlspecialchars($p['image']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($p['title']) ?>">
                        <?php else: ?>
                            <img src="../uploads/no-image.png" class="card-img-top product-img" alt="No Image">
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['title']) ?></h5>
                            <h6 class="text-success fw-bold">Rs. <?= number_format($p['price']) ?></h6>
                            <p class="card-text text-muted small flex-grow-1"><?= substr(strip_tags($p['description']), 0, 85) ?>...</p>
                            
                            <div class="mt-auto">
                                <a href="cart.php?add=<?= $p['id'] ?>" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </a>
                                
                                <?php if($store['whatsapp_position'] == 'below_product' && $store['whatsapp_number']): ?>
                                    <a href="https://wa.me/<?= $store['whatsapp_number'] ?>?text=I want to buy <?= urlencode($p['title']) ?> (Rs. <?= $p['price'] ?>)" class="btn btn-success w-100" target="_blank">
                                        <i class="fab fa-whatsapp"></i> Order on WhatsApp
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <h5>No products found.</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Floating WhatsApp Button -->
<?php if($store['whatsapp_position'] == 'floating' && $store['whatsapp_number']): ?>
    <a href="https://wa.me/<?= $store['whatsapp_number'] ?>" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
<?php endif; ?>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>