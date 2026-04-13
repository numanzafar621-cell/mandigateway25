<?php 
include 'config.php'; 
include 'includes/functions.php';

$token = $_GET['token'] ?? '';
if (!$token) {
    die("Invalid request. No token provided.");
}

$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$reset = $stmt->get_result()->fetch_assoc();

if (!$reset) {
    die("Token expired or invalid. Please request a new password reset.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if ($new_password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $reset['email'];
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);
        $update->execute();
        
        // Delete the used token
        $conn->query("DELETE FROM password_resets WHERE token = '$token'");
        
        header("Location: login.php?msg=password_reset");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MandiGateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center" style="min-height:100vh; background:#f0f2f5;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <h3 class="text-center">Set New Password</h3>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>