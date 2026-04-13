<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

// Add/Edit Post
if (isset($_POST['save_post'])) {
    $title = safeInput($_POST['title']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $image = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
    }
    
    if (isset($_POST['post_id']) && $_POST['post_id'] > 0) {
        $id = intval($_POST['post_id']);
        if ($image) {
            $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, content=?, image=? WHERE id=? AND user_id=?");
            $stmt->bind_param("ssssii", $title, $slug, $content, $image, $id, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE posts SET title=?, slug=?, content=? WHERE id=? AND user_id=?");
            $stmt->bind_param("sssii", $title, $slug, $content, $id, $user_id);
        }
        $stmt->execute();
        $success = "Post updated!";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, slug, content, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $slug, $content, $image);
        $stmt->execute();
        $success = "Post created!";
    }
}

// Delete Post
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM posts WHERE id = $id AND user_id = $user_id");
    header("Location: posts.php");
    exit();
}

$edit_post = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_post = $conn->query("SELECT * FROM posts WHERE id = $id AND user_id = $user_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
    <script>tinymce.init({ selector: '#content', height: 400 });</script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-blog"></i> Blog Posts</h2>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><?= $edit_post ? 'Edit Post' : 'New Post' ?></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if($edit_post): ?>
                            <input type="hidden" name="post_id" value="<?= $edit_post['id'] ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label>Post Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_post['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Featured Image</label>
                            <?php if($edit_post && $edit_post['image']): ?>
                                <div class="mb-2"><img src="../<?= $edit_post['image'] ?>" width="100"></div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label>Content</label>
                            <textarea id="content" name="content"><?= htmlspecialchars($edit_post['content'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" name="save_post" class="btn btn-primary">Save Post</button>
                        <?php if($edit_post): ?>
                            <a href="posts.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-secondary text-white">All Posts</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-dark"><tr><th>Image</th><th>Title</th><th>Date</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php
                            $posts = $conn->query("SELECT * FROM posts WHERE user_id = $user_id ORDER BY id DESC");
                            if($posts->num_rows > 0):
                                while($post = $posts->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php if($post['image']): ?><img src="../<?= $post['image'] ?>" width="50"><?php else: ?>-<?php endif; ?></td>
                                <td><?= htmlspecialchars($post['title']) ?></td>
                                <td><?= $post['created_at'] ?></td>
                                <td>
                                    <a href="?edit=<?= $post['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?delete=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center">No posts yet.</td></tr>
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