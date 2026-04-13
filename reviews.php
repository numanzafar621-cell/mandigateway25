<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

// Get all reviews for this store's products
$reviews = $conn->query("SELECT r.*, p.title as product_name FROM reviews r 
                         JOIN products p ON r.product_id = p.id 
                         WHERE p.user_id = $user_id 
                         ORDER BY r.created_at DESC");
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-star"></i> Customer Reviews & Ratings</h2>
            
            <div class="card">
                <div class="card-header bg-secondary text-white">All Reviews (<?= $reviews->num_rows ?>)</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-dark">
                                <tr><th>Product</th><th>Customer</th><th>Rating</th><th>Review</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                <?php if($reviews->num_rows > 0): ?>
                                    <?php while($rev = $reviews->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($rev['product_name']) ?></td>
                                        <td><?= htmlspecialchars($rev['customer_name']) ?></td>
                                        <td>
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <?php if($i <= $rev['rating']): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star text-muted"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                            (<?= $rev['rating'] ?>/5)
                                        </td>
                                        <td><?= nl2br(htmlspecialchars($rev['comment'])) ?></td>
                                        <td><?= date('d M Y', strtotime($rev['created_at'])) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">No reviews yet. Customers will leave reviews here.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-info text-white">Average Store Rating</div>
                <div class="card-body text-center">
                    <?php
                    $avg = $conn->query("SELECT AVG(rating) as avg FROM reviews r JOIN products p ON r.product_id = p.id WHERE p.user_id = $user_id")->fetch_assoc();
                    $avg_rating = round($avg['avg'] ?? 0, 1);
                    ?>
                    <h1 class="display-1"><?= $avg_rating ?></h1>
                    <div>
                        <?php for($i=1; $i<=5; $i++): ?>
                            <?php if($i <= $avg_rating): ?>
                                <i class="fas fa-star fa-2x text-warning"></i>
                            <?php elseif($i - 0.5 <= $avg_rating): ?>
                                <i class="fas fa-star-half-alt fa-2x text-warning"></i>
                            <?php else: ?>
                                <i class="far fa-star fa-2x text-muted"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <p class="mt-3">Based on <?= $reviews->num_rows ?> customer reviews</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>