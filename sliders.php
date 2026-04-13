<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

// Add Slider
if (isset($_POST['add_slider'])) {
    $text = safeInput($_POST['text']);
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
    }
    if ($image) {
        $stmt = $conn->prepare("INSERT INTO sliders (user_id, image, text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $image, $text);
        if ($stmt->execute()) {
            $success = "Slider added successfully!";
        } else {
            $error = "Failed to add slider.";
        }
    } else {
        $error = "Please upload a valid image.";
    }
}

// Delete Slider
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Get image to delete from server
    $img = $conn->query("SELECT image FROM sliders WHERE id = $id AND user_id = $user_id")->fetch_assoc();
    if ($img && file_exists("../" . $img['image'])) {
        unlink("../" . $img['image']);
    }
    $conn->query("DELETE FROM sliders WHERE id = $id AND user_id = $user_id");
    header("Location: sliders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sliders - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-images"></i> Homepage Sliders</h2>
            
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Add New Slider</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Slider Image (Recommended: 1200x400px)</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label>Slider Text (optional)</label>
                            <input type="text" name="text" class="form-control" placeholder="e.g. Summer Sale 50% Off">
                        </div>
                        <button type="submit" name="add_slider" class="btn btn-primary">Add Slider</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-secondary text-white">Current Sliders</div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $sliders = $conn->query("SELECT * FROM sliders WHERE user_id = $user_id ORDER BY position ASC, id DESC");
                        if($sliders->num_rows > 0):
                            while($s = $sliders->fetch_assoc()):
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="../<?= htmlspecialchars($s['image']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <p class="card-text"><?= htmlspecialchars($s['text']) ?: '(No text)' ?></p>
                                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slider?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info">No sliders added yet. Add your first slider above.</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>