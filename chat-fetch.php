<?php
include '../config.php';
include '../includes/functions.php';

if (!isLoggedIn()) {
    die("Unauthorized");
}
$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action == 'customers') {
    // Get unique customers who chatted with this store
    $result = $conn->query("SELECT DISTINCT customer_session, customer_name, 
                            (SELECT message FROM chat_messages WHERE store_user_id = $user_id AND customer_session = c.customer_session ORDER BY id DESC LIMIT 1) as last_message 
                            FROM chat_messages c WHERE store_user_id = $user_id ORDER BY created_at DESC");
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    echo json_encode($customers);
} 
elseif ($action == 'messages') {
    $session = $conn->real_escape_string($_GET['session']);
    $result = $conn->query("SELECT * FROM chat_messages WHERE store_user_id = $user_id AND customer_session = '$session' ORDER BY created_at ASC");
    $msgs = [];
    while ($row = $result->fetch_assoc()) {
        $msgs[] = $row;
    }
    echo json_encode($msgs);
}
?>