<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$user_id = $store['user_id'];
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    header("Location: index.php");
    exit();
}

$product = $conn->query("SELECT * FROM products WHERE id = $product_id AND user_id = $user_id AND status='active'")->fetch_assoc();
if (!$product) {
    die("<h1 class='text-center mt-5'>Product Not Found!</h1>");
}

// Get reviews
$reviews = $conn->query("SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC");
$avg_rating = $conn->query("SELECT AVG(rating) as avg FROM reviews WHERE product_id = $product_id")->fetch_assoc()['avg'];
$avg_rating = round($avg_rating, 1);

// Submit review
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, store_user_id, customer_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $product_id, $user_id, $customer_name, $rating, $comment);
    $stmt->execute();
    header("Location: product.php?id=$product_id&msg=reviewed");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
        .rating i { font-size: 20px; cursor: pointer; }
        .rating-selected { color: #ffc107; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container mt-4">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'reviewed'): ?>
        <div class="alert alert-success">Thank you for your review!</div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-5">
            <?php if($product['image']): ?>
                <img src="../<?= $product['image'] ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['title']) ?>">
            <?php else: ?>
                <img src="../uploads/no-image.png" class="img-fluid rounded" alt="No Image">
            <?php endif; ?>
        </div>
        <div class="col-md-7">
            <h2><?= htmlspecialchars($product['title']) ?></h2>
            <h3 class="text-success">Rs. <?= number_format($product['price']) ?></h3>
            <div class="mb-3">
                <?php for($i=1; $i<=5; $i++): ?>
                    <?php if($i <= $avg_rating): ?>
                        <i class="fas fa-star text-warning"></i>
                    <?php elseif($i - 0.5 <= $avg_rating): ?>
                        <i class="fas fa-star-half-alt text-warning"></i>
                    <?php else: ?>
                        <i class="far fa-star text-muted"></i>
                    <?php endif; ?>
                <?php endfor; ?>
                <span>(<?= $reviews->num_rows ?> reviews)</span>
            </div>
            <p class="mt-3"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <div class="mt-4">
                <a href="cart.php?add=<?= $product['id'] ?>" class="btn btn-primary btn-lg">Add to Cart</a>
                <a href="https://wa.me/<?= $store['whatsapp_number'] ?>?text=I want to buy <?= urlencode($product['title']) ?> (Rs. <?= $product['price'] ?>)" class="btn btn-success btn-lg" target="_blank">Order on WhatsApp</a>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-md-8">
            <h4>Customer Reviews</h4>
            <?php if($reviews->num_rows > 0): ?>
                <?php while($rev = $reviews->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <strong><?= htmlspecialchars($rev['customer_name']) ?></strong>
                            <div>
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <?php if($i <= $rev['rating']): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php else: ?>
                                        <i class="far fa-star text-muted"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="mt-2"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            <small class="text-muted"><?= date('d M Y', strtotime($rev['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Write a Review</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Your Name</label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Rating</label>
                            <div class="rating">
                                <i class="far fa-star" data-value="1"></i>
                                <i class="far fa-star" data-value="2"></i>
                                <i class="far fa-star" data-value="3"></i>
                                <i class="far fa-star" data-value="4"></i>
                                <i class="far fa-star" data-value="5"></i>
                                <input type="hidden" name="rating" id="rating_value" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Your Review</label>
                            <textarea name="comment" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.querySelectorAll('.rating i').forEach(star => {
    star.addEventListener('click', function() {
        let value = this.getAttribute('data-value');
        document.getElementById('rating_value').value = value;
        document.querySelectorAll('.rating i').forEach(s => {
            if(s.getAttribute('data-value') <= value) {
                s.classList.remove('far');
                s.classList.add('fas', 'text-warning');
            } else {
                s.classList.remove('fas', 'text-warning');
                s.classList.add('far');
            }
        });
    });
});
</script>
</body>
</html

