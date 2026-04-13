<?php
include '../config.php';
$store_id = isset($_GET['store_id']) ? intval($_GET['store_id']) : 0;
$session = isset($_GET['session']) ? $conn->real_escape_string($_GET['session']) : '';
if(!$store_id || !$session) {
    echo json_encode([]);
    exit();
}
$result = $conn->query("SELECT * FROM chat_messages WHERE store_user_id = $store_id AND customer_session = '$session' ORDER BY created_at ASC");
$msgs = [];
while($row = $result->fetch_assoc()) {
    $msgs[] = $row;
}
echo json_encode($msgs);
?>