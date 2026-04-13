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
    $full_name = safeInput($_POST['full_name']);
    $phone = safeInput($_POST['phone']);
    $email = safeInput($_POST['email']);
    
    // Update password if provided
    $password_query = "";
    if (!empty($_POST['new_password'])) {
        $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("ssssi", $full_name, $phone, $email, $new_pass, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name=?, phone=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $full_name, $phone, $email, $user_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['business_name'] = $full_name; // update session name
        $success = "Profile updated successfully!";
        $user = getUserData($user_id);
    } else {
        $error = "Failed to update profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <div class="card">
                <div class="card-header bg-primary text-white">Personal Information</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Business Name</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['business_name']) ?>" disabled>
                                <small class="text-muted">Business name cannot be changed.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        <hr>
                        <h5>Change Password (leave blank to keep current)</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>