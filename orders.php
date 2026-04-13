<?php 
include '../config.php'; 
include '../includes/functions.php'; 

if (!isLoggedIn()) {
    header("Location: ../login.php"); 
    exit();
}
$user_id = $_SESSION['user_id'];

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = safeInput($_POST['status']);
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND store_user_id = ?");
    $stmt->bind_param("sii", $status, $order_id, $user_id);
    $stmt->execute();
    $success = "Order status updated!";
}
?>
<!DOCTYPE html>
<html lang="ur">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-shopping-cart"></i> Orders</h2>
            <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
            
            <div class="card">
                <div class="card-header bg-secondary text-white">All Orders</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-dark">
                                <tr><th>Order ID</th><th>Customer</th><th>Phone</th><th>Address</th><th>Total</th><th>Payment</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders = $conn->query("SELECT * FROM orders WHERE store_user_id = $user_id ORDER BY id DESC");
                                if($orders->num_rows > 0):
                                    while($order = $orders->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= $order['customer_phone'] ?></td>
                                    <td><?= htmlspecialchars(substr($order['customer_address'], 0, 50)) ?>...</td>
                                    <td>Rs. <?= number_format($order['total']) ?></td>
                                    <td><?= $order['payment_method'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                            <?= $order['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id'] ?>">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        
                                        <!-- Update Status Form inline -->
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                                <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>Pending</option>
                                                <option value="processing" <?= $order['status']=='processing'?'selected':'' ?>>Processing</option>
                                                <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>Completed</option>
                                                <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                </tr>
                                
                                <!-- Modal for order details -->
                                <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order #<?= $order['id'] ?> Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                                                <p><strong>Phone:</strong> <?= $order['customer_phone'] ?></p>
                                                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['customer_address'])) ?></p>
                                                <p><strong>Payment:</strong> <?= $order['payment_method'] ?></p>
                                                <p><strong>Total:</strong> Rs. <?= number_format($order['total']) ?></p>
                                                <hr>
                                                <h6>Products:</h6>
                                                <ul>
                                                <?php
                                                $items = $conn->query("SELECT oi.*, p.title FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$order['id']}");
                                                while($item = $items->fetch_assoc()):
                                                ?>
                                                    <li><?= htmlspecialchars($item['title']) ?> x <?= $item['quantity'] ?> = Rs. <?= number_format($item['price'] * $item['quantity']) ?></li>
                                                <?php endwhile; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; else: ?>
                                <tr><td colspan="8" class="text-center">No orders yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>