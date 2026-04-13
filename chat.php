<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .chat-container { height: 500px; display: flex; flex-direction: column; background: #f8f9fa; border-radius: 15px; overflow: hidden; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; background: #fff; }
        .message { margin-bottom: 15px; display: flex; }
        .message.customer { justify-content: flex-start; }
        .message.owner { justify-content: flex-end; }
        .message .bubble { max-width: 70%; padding: 10px 15px; border-radius: 20px; }
        .message.customer .bubble { background: #e9ecef; color: #000; border-bottom-left-radius: 5px; }
        .message.owner .bubble { background: #0d6efd; color: white; border-bottom-right-radius: 5px; }
        .message small { display: block; font-size: 11px; margin-top: 5px; opacity: 0.7; }
        .customer-list { max-height: 500px; overflow-y: auto; }
        .customer-item { padding: 12px; border-bottom: 1px solid #ddd; cursor: pointer; transition: background 0.2s; }
        .customer-item:hover { background: #f0f0f0; }
        .customer-item.active { background: #0d6efd; color: white; }
        .chat-input { padding: 15px; background: white; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-comments"></i> Live Chat with Customers</h2>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">Active Customers</div>
                        <div class="customer-list" id="customerList">
                            <div class="text-center p-3">Loading...</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-secondary text-white" id="chatHeader">Select a customer to start chatting</div>
                        <div class="chat-container">
                            <div class="chat-messages" id="chatMessages"></div>
                            <div class="chat-input">
                                <div class="input-group">
                                    <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." disabled>
                                    <button class="btn btn-primary" id="sendBtn" disabled><i class="fas fa-paper-plane"></i> Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentSession = null;
let currentName = null;

function loadCustomers() {
    $.get('chat-fetch.php?action=customers', function(data) {
        let html = '';
        if(data.length === 0) {
            html = '<div class="text-center p-3">No customers yet</div>';
        } else {
            data.forEach(function(c) {
                html += `<div class="customer-item" data-session="${c.customer_session}" data-name="${c.customer_name}" onclick="selectCustomer(this)">
                            <strong>${c.customer_name}</strong><br>
                            <small>${c.last_message.substring(0, 30)}</small>
                        </div>`;
            });
        }
        $('#customerList').html(html);
    }, 'json');
}

function selectCustomer(el) {
    $('.customer-item').removeClass('active');
    $(el).addClass('active');
    currentSession = $(el).data('session');
    currentName = $(el).data('name');
    $('#chatHeader').text('Chat with ' + currentName);
    $('#messageInput').prop('disabled', false);
    $('#sendBtn').prop('disabled', false);
    loadMessages();
    startPolling();
}

function loadMessages() {
    if(!currentSession) return;
    $.get(`chat-fetch.php?action=messages&session=${currentSession}`, function(data) {
        let html = '';
        data.forEach(function(m) {
            let cls = (m.sender === 'owner') ? 'owner' : 'customer';
            html += `<div class="message ${cls}">
                        <div class="bubble">
                            ${m.message}
                            <small>${m.created_at}</small>
                        </div>
                    </div>`;
        });
        $('#chatMessages').html(html);
        $('#chatMessages').scrollTop($('#chatMessages')[0].scrollHeight);
    }, 'json');
}

function sendMessage() {
    let msg = $('#messageInput').val();
    if(!msg || !currentSession) return;
    $.post('chat-send.php', { action: 'owner_send', session: currentSession, message: msg }, function() {
        $('#messageInput').val('');
        loadMessages();
    });
}

let pollingInterval;
function startPolling() {
    if(pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(() => { if(currentSession) loadMessages(); }, 3000);
}

$(document).ready(function() {
    loadCustomers();
    setInterval(loadCustomers, 10000);
    $('#sendBtn').click(sendMessage);
    $('#messageInput').keypress(function(e) { if(e.which == 13) sendMessage(); });
});
</script>
</body>
</html>