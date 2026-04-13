<?php
include '../config.php';
$store_id = isset($_POST['store_id']) ? intval($_POST['store_id']) : 0;
$session = isset($_POST['session']) ? $conn->real_escape_string($_POST['session']) : '';
$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : 'Guest';
$message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : '';
if($store_id && $session && $message) {
    $stmt = $conn->prepare("INSERT INTO chat_messages (store_user_id, sender, customer_session, customer_name, message) VALUES (?, 'customer', ?, ?, ?)");
    $stmt->bind_param("isss", $store_id, $session, $name, $message);
    $stmt->execute();
    echo "ok";
} else {
    echo "error";
}
?>