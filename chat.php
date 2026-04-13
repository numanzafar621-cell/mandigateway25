<?php 
include '../config.php'; 
$store = getCurrentStore();
if (!$store) {
    die("<h1 class='text-center mt-5'>Store Not Found!</h1>");
}
session_id() or session_start();
$customer_session = session_id();
$customer_name = $_POST['customer_name'] ?? $_GET['name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($store['business_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .chat-container { max-width: 600px; margin: 50px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .chat-header { background: <?= $store['header_color'] ?>; color: white; padding: 15px 20px; }
        .chat-box { height: 400px; overflow-y: auto; padding: 15px; background: #f8f9fa; }
        .message { margin-bottom: 15px; clear: both; }
        .customer-msg { background: #0d6efd; color: white; float: right; border-radius: 15px 15px 0 15px; padding: 8px 15px; max-width: 70%; }
        .owner-msg { background: #e9ecef; color: #000; float: left; border-radius: 15px 15px 15px 0; padding: 8px 15px; max-width: 70%; }
        .input-group { margin-top: 10px; }
        .small-text { font-size: 11px; opacity: 0.7; margin-top: 5px; display: block; }
        .whatsapp-float { position: fixed; bottom: 20px; right: 20px; background: #25D366; color: white; width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; z-index: 999; text-decoration: none; }
    </style>
</head>
<body>
<div class="chat-container">
    <div class="chat-header">
        <h5 class="mb-0"><i class="fab fa-whatsapp"></i> Chat with <?= htmlspecialchars($store['business_name']) ?></h5>
        <small>We will reply as soon as possible</small>
    </div>
    <div class="chat-box" id="chatMessages"></div>
    <div class="p-3 bg-white">
        <div class="input-group">
            <input type="text" id="messageInput" class="form-control" placeholder="Type your message...">
            <button class="btn btn-primary" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
        <div class="mt-2">
            <input type="text" id="nameInput" class="form-control" placeholder="Your name (optional)" value="<?= htmlspecialchars($customer_name) ?>">
        </div>
    </div>
</div>

<!-- Optional WhatsApp floating button -->
<?php if($store['whatsapp_number']): ?>
<a href="https://wa.me/<?= $store['whatsapp_number'] ?>" class="whatsapp-float" target="_blank"><i class="fab fa-whatsapp"></i></a>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let sessionId = "<?= $customer_session ?>";
let storeUserId = <?= $store['user_id'] ?>;

function loadMessages() {
    $.get(`chat-fetch.php?store_id=${storeUserId}&session=${sessionId}`, function(data) {
        let html = '';
        data.forEach(function(m) {
            if(m.sender === 'customer') {
                html += `<div class="message customer-msg" style="float:right; clear:both;">${m.message}<small class="small-text">${m.created_at}</small></div>`;
            } else {
                html += `<div class="message owner-msg" style="float:left; clear:both;">${m.message}<small class="small-text">${m.created_at}</small></div>`;
            }
        });
        $('#chatMessages').html(html);
        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
    }, 'json');
}

function sendMessage() {
    let msg = $('#messageInput').val();
    let name = $('#nameInput').val() || 'Guest';
    if(!msg) return;
    $.post('chat-send.php', {
        store_id: storeUserId,
        session: sessionId,
        name: name,
        message: msg
    }, function() {
        $('#messageInput').val('');
        loadMessages();
    });
}

loadMessages();
setInterval(loadMessages, 3000);

$('#messageInput').keypress(function(e) {
    if(e.which == 13) sendMessage();
});
</script>
</body>
</html>