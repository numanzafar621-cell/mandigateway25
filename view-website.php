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
$store = getStoreData($user['id']);
$store_url = "http://" . $store['subdomain'] . ".localhost/mandigateway/store/"; // Change localhost to your domain
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Store - <?= htmlspecialchars($user['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .iframe-container { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; margin-top: 20px; }
        iframe { width: 100%; height: 80vh; border: none; }
        .toolbar { background: #fff; padding: 15px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .store-url { background: #f8f9fa; padding: 8px 15px; border-radius: 50px; font-size: 14px; font-family: monospace; }
        @media (max-width: 768px) { iframe { height: 60vh; } .toolbar { flex-direction: column; align-items: stretch; } }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h2><i class="fas fa-eye text-primary"></i> Live Store Preview</h2>
                <a href="<?= $store_url ?>" target="_blank" class="btn btn-success">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </a>
            </div>
            
            <div class="iframe-container">
                <div class="toolbar">
                    <div class="store-url">
                        <i class="fas fa-link"></i> <?= $store_url ?>
                    </div>
                    <div>
                        <button onclick="document.getElementById('storeFrame').contentWindow.location.reload();" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <iframe id="storeFrame" src="<?= $store_url ?>" title="My Store"></iframe>
            </div>
            
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle"></i> 
                Your website is visible to customers at this link. Share it anywhere.
                <br>
                <strong>Link: </strong> <a href="<?= $store_url ?>" target="_blank"><?= $store_url ?></a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>