<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

// Add Category
if (isset($_POST['add_category'])) {
    $name = safeInput($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
    $stmt = $conn->prepare("INSERT INTO categories (user_id, name, slug) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $name, $slug);
    if ($stmt->execute()) {
        $success = "Category added successfully!";
    } else {
        $error = "Failed to add category.";
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id = $id AND user_id = $user_id");
    header("Location: categories.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-tags"></i> Manage Categories</h2>
            
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Add New Category</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Category Name (e.g. Vegetables, Fruits)" required>
                            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-secondary text-white">Existing Categories</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr><th>ID</th><th>Category Name</th><th>Slug</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $categories = $conn->query("SELECT * FROM categories WHERE user_id = $user_id ORDER BY id DESC");
                            if($categories->num_rows > 0):
                                while($cat = $categories->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= $cat['slug'] ?></td>
                                <td>
                                    <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr><td colspan="4" class="text-center">No categories yet. Add your first category.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>