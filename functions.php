<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserData($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getStoreData($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM stores WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function uploadImage($file, $target_dir = UPLOAD_DIR) {
    if ($file['error'] !== UPLOAD_ERR_OK) return '';
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return '';
    $new_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = $target_dir . $new_name;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $target_dir . $new_name;
    }
    return '';
}

function safeInput($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}
?>