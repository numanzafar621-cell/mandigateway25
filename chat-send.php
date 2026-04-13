<?php
include '../config.php';
include '../includes/functions.php';

if (!isLoggedIn()) {
    die("Unauthorized");
}
$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action == 'owner_send') {
    $session = $conn->real_escape_string($_POST['session']);
    $message = $conn->real_escape_string($_POST['message']);
    $customer_name = $conn->real_escape_string($_POST['customer_name'] ?? 'Customer');
    
    $stmt = $conn->prepare("INSERT INTO chat_messages (store_user_id, sender, customer_session, customer_name, message) VALUES (?, 'owner', ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $session, $customer_name, $message);
    $stmt->execute();
    echo "ok";
}
?>