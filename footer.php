<?php 
global $store;
?>
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><?= htmlspecialchars($store['business_name']) ?></h5>
                <p class="small"><?= htmlspecialchars($store['footer_text']) ?></p>
            </div>
            <div class="col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="cart.php" class="text-white text-decoration-none">Cart</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6>Follow Us</h6>
                <div>
                    <?php if($store['facebook']): ?>
                        <a href="<?= $store['facebook'] ?>" class="text-white me-3" target="_blank"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <?php endif; ?>
                    <?php if($store['twitter']): ?>
                        <a href="<?= $store['twitter'] ?>" class="text-white me-3" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
                    <?php endif; ?>
                    <?php if($store['instagram']): ?>
                        <a href="<?= $store['instagram'] ?>" class="text-white me-3" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                    <?php endif; ?>
                    <?php if($store['whatsapp_number']): ?>
                        <a href="https://wa.me/<?= $store['whatsapp_number'] ?>" class="text-white" target="_blank"><i class="fab fa-whatsapp fa-lg"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="text-center small">
            &copy; <?= date("Y") ?> <?= htmlspecialchars($store['business_name']) ?> - Powered by MandiGateway
        </div>
    </div>
</footer>