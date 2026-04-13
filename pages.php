<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

// Add/Edit Page
if (isset($_POST['save_page'])) {
    $title = safeInput($_POST['title']);
    $slug = safeInput($_POST['slug']);
    $content = mysqli_real_escape_string($conn, $_POST['content']); // preserve HTML
    
    if (isset($_POST['page_id']) && $_POST['page_id'] > 0) {
        $id = intval($_POST['page_id']);
        $stmt = $conn->prepare("UPDATE pages SET title=?, slug=?, content=? WHERE id=? AND user_id=?");
        $stmt->bind_param("sssii", $title, $slug, $content, $id, $user_id);
        $stmt->execute();
        $success = "Page updated!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pages (user_id, title, slug, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $slug, $content);
        $stmt->execute();
        $success = "Page created!";
    }
}

// Delete Page
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM pages WHERE id = $id AND user_id = $user_id");
    header("Location: pages.php");
    exit();
}

// Edit Page - fetch data
$edit_page = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_page = $conn->query("SELECT * FROM pages WHERE id = $id AND user_id = $user_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
    <script>
        tinymce.init({ selector: '#content', height: 400, plugins: 'link image lists code', toolbar: 'undo redo | bold italic | alignleft aligncenter | bullist numlist | code' });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-file-alt"></i> Manage Pages</h2>
            
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><?= $edit_page ? 'Edit Page' : 'Create New Page' ?></div>
                <div class="card-body">
                    <form method="POST">
                        <?php if($edit_page): ?>
                            <input type="hidden" name="page_id" value="<?= $edit_page['id'] ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label>Page Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_page['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>URL Slug (e.g., about-us)</label>
                            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($edit_page['slug'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Page Content</label>
                            <textarea id="content" name="content"><?= htmlspecialchars($edit_page['content'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" name="save_page" class="btn btn-primary">Save Page</button>
                        <?php if($edit_page): ?>
                            <a href="pages.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-secondary text-white">All Pages</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark">
                            <tr><th>ID</th><th>Title</th><th>Slug</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $pages = $conn->query("SELECT * FROM pages WHERE user_id = $user_id ORDER BY id DESC");
                            if($pages->num_rows > 0):
                                while($p = $pages->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['title']) ?></td>
                                <td><?= $p['slug'] ?></td>
                                <td>
                                    <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this page?')"><i class="fas fa-trash"></i> Delete</a>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>