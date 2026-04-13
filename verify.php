<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];
$user = getUserData($user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = safeInput($_POST['address']);
    $google_map = safeInput($_POST['google_map']);
    
    $cnic_front = $user['cnic_front'];
    $cnic_back = $user['cnic_back'];
    
    if (isset($_FILES['cnic_front']) && $_FILES['cnic_front']['error'] == 0) {
        $cnic_front = uploadImage($_FILES['cnic_front']);
    }
    if (isset($_FILES['cnic_back']) && $_FILES['cnic_back']['error'] == 0) {
        $cnic_back = uploadImage($_FILES['cnic_back']);
    }
    
    $stmt = $conn->prepare("UPDATE users SET address=?, google_map=?, cnic_front=?, cnic_back=? WHERE id=?");
    $stmt->bind_param("ssssi", $address, $google_map, $cnic_front, $cnic_back, $user_id);
    if ($stmt->execute()) {
        $success = "Verification details submitted. Admin will review and activate your store soon.";
        $user = getUserData($user_id); // refresh
    } else {
        $error = "Failed to save details.";
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-id-card"></i> Store Verification</h2>
            
            <?php if($user['status'] == 'active'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Your store is already ACTIVE. You can sell products.
                </div>
            <?php elseif($user['status'] == 'pending'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-hourglass-half"></i> Your verification is pending. Admin will review your documents.
                </div>
            <?php elseif($user['status'] == 'suspended'): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-ban"></i> Your account has been suspended. Contact admin.
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header bg-primary text-white">Submit CNIC & Address Details</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>CNIC Front Image</label>
                                <?php if($user['cnic_front']): ?>
                                    <div class="mb-2"><a href="../<?= $user['cnic_front'] ?>" target="_blank">View Current</a></div>
                                <?php endif; ?>
                                <input type="file" name="cnic_front" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>CNIC Back Image</label>
                                <?php if($user['cnic_back']): ?>
                                    <div class="mb-2"><a href="../<?= $user['cnic_back'] ?>" target="_blank">View Current</a></div>
                                <?php endif; ?>
                                <input type="file" name="cnic_back" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Full Address</label>
                            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($user['address']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Google Map Location (optional)</label>
                            <input type="text" name="google_map" class="form-control" placeholder="Paste Google Maps embed link" value="<?= htmlspecialchars($user['google_map']) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Verification</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>