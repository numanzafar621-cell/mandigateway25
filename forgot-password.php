<?php 
include 'config.php'; 
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = safeInput($_POST['email']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt2 = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $email, $token, $expires);
        $stmt2->execute();
        
        $reset_link = BASE_URL . "reset-password.php?token=" . $token;
        // Send email (simple mail function)
        $to = $email;
        $subject = "Reset Your Password - MandiGateway";
        $message = "Click this link to reset your password: $reset_link\n\nThis link expires in 1 hour.";
        $headers = "From: support@mandigateway.com";
        mail($to, $subject, $message, $headers);
        
        $success = "Reset link sent to your email. Please check your inbox.";
    } else {
        $error = "Email not found in our records.";
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MandiGateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;}
        .card{border-radius:30px;}
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 p-md-5">
                <h3 class="text-center text-primary"><i class="fas fa-key"></i> Forgot Password</h3>
                <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </form>
                <p class="text-center mt-3"><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>