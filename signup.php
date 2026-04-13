<?php 
include 'config.php'; 
include 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $business = safeInput($_POST['business_name']);
    $fullname = safeInput($_POST['full_name']);
    $phone    = safeInput($_POST['phone']);
    $email    = safeInput($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $subdomain = strtolower(preg_replace('/[^a-z0-9]/', '', $business));
    
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO users (business_name, full_name, phone, email, password) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $business, $fullname, $phone, $email, $password);
            $stmt->execute();
            $user_id = $conn->insert_id;
            
            $stmt2 = $conn->prepare("INSERT INTO stores (user_id, subdomain) VALUES (?, ?)");
            $stmt2->bind_param("is", $user_id, $subdomain);
            $stmt2->execute();
            
            $conn->commit();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['business_name'] = $business;
            header("Location: loading.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Signup failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MandiGateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;font-family:'Segoe UI',sans-serif;}
        .signup-card{border-radius:30px;box-shadow:0 20px 40px rgba(0,0,0,0.2);overflow:hidden;}
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card signup-card p-4 p-md-5">
                <h2 class="text-center text-primary mb-4"><i class="fas fa-store"></i> Create Your Store</h2>
                <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control" placeholder="e.g. AlMadinaMart" required>
                        <small class="text-muted">This will become your subdomain: businessname.mandigateway.com</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="03001234567" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" name="signup" class="btn btn-primary w-100 btn-lg">Create My Store</button>
                </form>
                <p class="text-center mt-4">Already have an account? <a href="login.php">Login Here</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>