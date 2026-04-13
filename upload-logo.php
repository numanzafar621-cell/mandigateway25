<?php
include '../config.php';
include '../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['logo'])) {
    $file = $_FILES['logo'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        echo json_encode(['error' => 'Invalid file type']);
        exit();
    }
    $new_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = '../uploads/' . $new_name;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        $logo_path = 'uploads/' . $new_name;
        $stmt = $conn->prepare("UPDATE stores SET logo = ? WHERE user_id = ?");
        $stmt->bind_param("si", $logo_path, $user_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'logo_url' => '../' . $logo_path, 'logo_path' => $logo_path]);
    } else {
        echo json_encode(['error' => 'Upload failed']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded']);
}
?>