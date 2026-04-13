<?php 
include 'config.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$business_name = $_SESSION['business_name'] ?? 'Your Store';
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting up your store - MandiGateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);height:100vh;display:flex;align-items:center;justify-content:center;font-family:'Segoe UI',sans-serif;overflow:hidden;}
        .loading-container{text-align:center;animation:fadeInUp 0.8s ease-out;}
        .brand{font-size:54px;font-weight:800;color:white;text-shadow:0 5px 20px rgba(0,0,0,0.2);margin-bottom:20px;}
        .welcome-text{font-size:28px;color:rgba(255,255,255,0.95);margin-bottom:10px;}
        .store-name{background:rgba(255,255,255,0.2);display:inline-block;padding:5px 20px;border-radius:50px;font-size:22px;margin-bottom:30px;color:white;}
        .loader{width:80px;height:80px;margin:30px auto;position:relative;}
        .loader .circle{width:100%;height:100%;border:6px solid rgba(255,255,255,0.3);border-top:6px solid white;border-radius:50%;animation:spin 1s cubic-bezier(0.68,-0.55,0.265,1.55) infinite;}
        .loader .inner-text{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:white;font-size:14px;font-weight:bold;}
        .progress-bar-container{width:280px;height:6px;background:rgba(255,255,255,0.2);border-radius:10px;margin:20px auto;overflow:hidden;}
        .progress-fill{width:0%;height:100%;background:white;border-radius:10px;transition:width 0.3s ease;}
        .status-message{color:rgba(255,255,255,0.9);font-size:14px;margin-top:15px;font-weight:500;}
        .dots{display:inline-block;width:30px;text-align:left;}
        @keyframes spin{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(30px);}to{opacity:1;transform:translateY(0);}}
        .popup-card{background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border-radius:40px;padding:40px 50px;box-shadow:0 25px 50px rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.2);}
        @media (max-width:576px){.popup-card{padding:30px 20px;}.brand{font-size:40px;}.welcome-text{font-size:22px;}}
    </style>
</head>
<body>
<div class="loading-container">
    <div class="popup-card">
        <div class="brand"><i class="fas fa-store me-2"></i>MandiGateway</div>
        <div class="welcome-text">Welcome, <span id="businessName"><?= htmlspecialchars($business_name) ?></span>!</div>
        <div class="store-name"><i class="fas fa-globe"></i> Your store is being created</div>
        <div class="loader"><div class="circle"></div><div class="inner-text"><i class="fas fa-sync-alt fa-spin"></i></div></div>
        <div class="progress-bar-container"><div class="progress-fill" id="progressFill"></div></div>
        <div class="status-message" id="statusMsg">Setting up your store<span class="dots" id="dots">...</span></div>
    </div>
</div>
<script>
    const minTime = 2000, maxTime = 5000;
    const loadTime = Math.floor(Math.random() * (maxTime - minTime + 1) + minTime);
    const progressFill = document.getElementById('progressFill');
    const statusMsg = document.getElementById('statusMsg');
    const dotsSpan = document.getElementById('dots');
    let startTime = Date.now();
    let dotCount = 0;
    setInterval(() => { dotCount = (dotCount % 3) + 1; dotsSpan.textContent = '.'.repeat(dotCount); }, 500);
    function updateProgress() {
        const elapsed = Date.now() - startTime;
        let percent = Math.min(100, (elapsed / loadTime) * 100);
        progressFill.style.width = percent + '%';
        if (percent < 30) statusMsg.innerHTML = 'Creating your database<span class="dots"></span>';
        else if (percent < 60) statusMsg.innerHTML = 'Setting up your store design<span class="dots"></span>';
        else if (percent < 90) statusMsg.innerHTML = 'Preparing your dashboard<span class="dots"></span>';
        else statusMsg.innerHTML = 'Almost ready, redirecting...<span class="dots"></span>';
        if (elapsed >= loadTime) {
            clearInterval(interval);
            progressFill.style.width = '100%';
            statusMsg.innerHTML = 'Redirecting to your dashboard...';
            setTimeout(() => { window.location.href = "dashboard/index.php"; }, 800);
        }
    }
    let interval = setInterval(updateProgress, 50);
</script>
</body>
</html>