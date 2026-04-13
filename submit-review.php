<?php
include '../config.php';
$store = getCurrentStore();
if (!$store) {
    die(json_encode(['error' => 'Store not found']));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    // Verify product belongs to this store
    $check = $conn->query("SELECT id FROM products WHERE id = $product_id AND user_id = {$store['user_id']}");
    if ($check->num_rows == 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['error' => 'Invalid product']);
        } else {
            header("Location: product.php?id=$product_id&error=invalid");
        }
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, store_user_id, customer_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisis", $product_id, $store['user_id'], $customer_name, $rating, $comment);
    if ($stmt->execute()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => 'Review submitted successfully']);
        } else {
            header("Location: product.php?id=$product_id&msg=reviewed");
        }
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['error' => 'Failed to submit review']);
        } else {
            header("Location: product.php?id=$product_id&error=failed");
        }
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .review-container { max-width: 500px; margin: 50px auto; }
        .rating i { font-size: 30px; cursor: pointer; color: #ddd; transition: 0.2s; }
        .rating i.selected, .rating i:hover { color: #ffc107; }
    </style>
</head>
<body>
<div class="container">
    <div class="review-container">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white text-center rounded-top-4">
                <h4 class="mb-0"><i class="fas fa-star"></i> Write a Review</h4>
            </div>
            <div class="card-body p-4">
                <form id="reviewForm" method="POST">
                    <input type="hidden" name="product_id" id="product_id" value="<?= intval($_GET['product_id'] ?? 0) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Your Name</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating">
                            <i class="far fa-star" data-value="1"></i>
                            <i class="far fa-star" data-value="2"></i>
                            <i class="far fa-star" data-value="3"></i>
                            <i class="far fa-star" data-value="4"></i>
                            <i class="far fa-star" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating_value" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Your Review</label>
                        <textarea name="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                </form>
                <div id="result" class="mt-3 text-center"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Star rating functionality
document.querySelectorAll('.rating i').forEach(star => {
    star.addEventListener('click', function() {
        let value = this.getAttribute('data-value');
        document.getElementById('rating_value').value = value;
        document.querySelectorAll('.rating i').forEach(s => {
            if(s.getAttribute('data-value') <= value) {
                s.classList.remove('far');
                s.classList.add('fas', 'selected');
            } else {
                s.classList.remove('fas', 'selected');
                s.classList.add('far');
            }
        });
    });
});

// AJAX form submission
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        let resultDiv = document.getElementById('result');
        if(data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success">' + data.success + ' Redirecting...</div>';
            setTimeout(() => {
                window.location.href = 'product.php?id=' + document.getElementById('product_id').value + '&msg=reviewed';
            }, 2000);
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
        }
    })
    .catch(err => {
        document.getElementById('result').innerHTML = '<div class="alert alert-danger">Network error</div>';
    });
});
</script>
</body>
</html>