<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}

$user = getUserData($_SESSION['user_id']);
if ($user['status'] != 'active') {
    header("Location: verify.php"); 
    exit();
}
$user_id = $user['id'];

// Add Product
if (isset($_POST['add_product'])) {
    $title = safeInput($_POST['title']);
    $price = floatval($_POST['price']);
    $description = safeInput($_POST['description']);
    $category_id = intval($_POST['category_id']);
    
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
    }
    
    $stmt = $conn->prepare("INSERT INTO products (user_id, category_id, title, price, description, image, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisdss", $user_id, $category_id, $title, $price, $description, $image);
    if ($stmt->execute()) {
        $success = "Product added successfully! Waiting for admin approval.";
    } else {
        $error = "Failed to add product.";
    }
}

// Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id AND user_id = $user_id");
    header("Location: products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-box"></i> My Products</h2>
            
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <!-- Add Product Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Add New Product</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Product Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Price (Rs.)</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Category</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    <?php
                                    $cats = $conn->query("SELECT * FROM categories WHERE user_id = $user_id");
                                    while($cat = $cats->fetch_assoc()):
                                    ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
            </div>
            
            <!-- Products List -->
            <div class="card">
                <div class="card-header bg-secondary text-white">All Products</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-dark">
                                <tr><th>Image</th><th>Title</th><th>Price</th><th>Category</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $products = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.user_id = $user_id ORDER BY p.created_at DESC");
                                if($products->num_rows > 0):
                                    while($p = $products->fetch_assoc()):
                                        $status_class = $p['status'] == 'active' ? 'success' : ($p['status'] == 'pending' ? 'warning' : 'danger');
                                ?>
                                <tr>
                                    <td>
                                        <?php if($p['image']): ?>
                                            <img src="../<?= htmlspecialchars($p['image']) ?>" width="60" height="60" style="object-fit:cover;">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['title']) ?></td>
                                    <td>Rs. <?= number_format($p['price']) ?></td>
                                    <td><?= htmlspecialchars($p['cat_name'] ?? 'Uncategorized') ?></td>
                                    <td><span class="badge bg-<?= $status_class ?>"><?= $p['status'] ?></span></td>
                                    <td>
                                        <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr><td colspan="6" class="text-center">No products added yet. Add your first product above.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>