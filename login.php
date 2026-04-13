<?php 
include 'config.php'; 
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = safeInput($_POST['email']);
    $pass = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['business_name'] = $user['business_name'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            if ($user['status'] == 'active') {
                header("Location: dashboard/index.php");
            } else {
                header("Location: loading.php");
            }
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MandiGateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;}
        .login-card{border-radius:30px;box-shadow:0 20px 40px rgba(0,0,0,0.2);}
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card p-4 p-md-5">
                <h2 class="text-center text-primary mb-4"><i class="fas fa-sign-in-alt"></i> Welcome Back!</h2>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">Login</button>
                </form>
                <div class="text-center mt-3">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
                <p class="text-center mt-3">New here? <a href="signup.php">Create Store</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>