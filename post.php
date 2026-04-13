<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';
if (!$slug) {
    header("Location: index.php");
    exit();
}
$post = $conn->query("SELECT * FROM posts WHERE slug = '$slug' AND user_id = {$store['user_id']}")->fetch_assoc();
if (!$post) {
    die("<h1 class='text-center mt-5'>Post Not Found!</h1>");
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 0; }
        .post-content { background: white; padding: 30px; border-radius: 15px; margin: 30px 0; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <div class="post-content">
        <?php if($post['image']): ?>
            <img src="../<?= $post['image'] ?>" class="img-fluid rounded mb-4" alt="<?= htmlspecialchars($post['title']) ?>">
        <?php endif; ?>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <small class="text-muted">Posted on <?= date('d M Y', strtotime($post['created_at'])) ?></small>
        <hr>
        <div><?= htmlspecialchars_decode($post['content']) ?></div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>