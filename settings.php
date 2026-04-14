
<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];
$store = getStoreData($user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $whatsapp = safeInput($_POST['whatsapp_number']);
    $position = safeInput($_POST['whatsapp_position']);
    $color = safeInput($_POST['header_color']);
    $banner = safeInput($_POST['banner_text']);
    $facebook = safeInput($_POST['facebook']);
    $twitter = safeInput($_POST['twitter']);
    $instagram = safeInput($_POST['instagram']);
    $footer_text = safeInput($_POST['footer_text']);
    
    // Logo upload
    $logo = $store['logo'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $new_logo = uploadImage($_FILES['logo']);
        if ($new_logo) $logo = $new_logo;
    }
    
    $stmt = $conn->prepare("UPDATE stores SET whatsapp_number=?, whatsapp_position=?, header_color=?, banner_text=?, facebook=?, twitter=?, instagram=?, footer_text=?, logo=? WHERE user_id=?");
    $stmt->bind_param("sssssssssi", $whatsapp, $position, $color, $banner, $facebook, $twitter, $instagram, $footer_text, $logo, $user_id);
    if ($stmt->execute()) {
        $success = "Settings saved successfully!";
        $store = getStoreData($user_id); // refresh
    } else {
        $error = "Failed to save settings.";
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Settings - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-cog"></i> Store Settings</h2>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">Branding</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Store Logo</label>
                                    <?php if($store['logo'] && $store['logo'] != 'logo.png'): ?>
                                        <div class="mb-2"><img src="../<?= $store['logo'] ?>" width="100"></div>
                                    <?php endif; ?>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label>Header Color</label>
                                    <input type="color" name="header_color" class="form-control form-control-color" value="<?= $store['header_color'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Banner Text</label>
                                    <input type="text" name="banner_text" class="form-control" value="<?= htmlspecialchars($store['banner_text']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">WhatsApp Settings</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>WhatsApp Number (with country code)</label>
                                    <input type="text" name="whatsapp_number" class="form-control" value="<?= $store['whatsapp_number'] ?>" placeholder="e.g. 923001234567">
                                </div>
                                <div class="mb-3">
                                    <label>WhatsApp Button Position</label>
                                    <select name="whatsapp_position" class="form-control">
                                        <option value="floating" <?= $store['whatsapp_position'] == 'floating' ? 'selected' : '' ?>>Floating (bottom right)</option>
                                        <option value="below_product" <?= $store['whatsapp_position'] == 'below_product' ? 'selected' : '' ?>>Below each product</option>
                                        <option value="above_product" <?= $store['whatsapp_position'] == 'above_product' ? 'selected' : '' ?>>Above each product</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">Social Media Links</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Facebook URL</label>
                                <input type="url" name="facebook" class="form-control" value="<?= $store['facebook'] ?>" placeholder="https://facebook.com/yourpage">
                            </div>
                            <div class="col-md-4">
                                <label>Twitter URL</label>
                                <input type="url" name="twitter" class="form-control" value="<?= $store['twitter'] ?>" placeholder="https://twitter.com/yourhandle">
                            </div>
                            <div class="col-md-4">
                                <label>Instagram URL</label>
                                <input type="url" name="instagram" class="form-control" value="<?= $store['instagram'] ?>" placeholder="https://instagram.com/yourprofile">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">Footer Settings</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Footer Text</label>
                            <textarea name="footer_text" class="form-control" rows="3"><?= htmlspecialchars($store['footer_text']) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">Save All Settings</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
