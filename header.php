<?php 
// This file is included in store/index.php and other store pages
global $store;
?>
<header class="header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <?php if($store['logo'] && $store['logo'] != 'logo.png'): ?>
                    <img src="../<?= $store['logo'] ?>" alt="Logo" height="50">
                <?php else: ?>
                    <h4 class="mb-0"><?= htmlspecialchars($store['business_name']) ?></h4>
                <?php endif; ?>
            </div>
            <div class="col-md-8 text-end">
                <p class="mb-0"><?= htmlspecialchars($store['banner_text']) ?></p>
            </div>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#storeNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="storeNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <?php
                // Get custom pages for this store
                $pages = $GLOBALS['conn']->query("SELECT title, slug FROM pages WHERE user_id = {$store['user_id']}");
                while($page = $pages->fetch_assoc()):
                ?>
                <li class="nav-item"><a class="nav-link" href="page.php?slug=<?= $page['slug'] ?>"><?= htmlspecialchars($page['title']) ?></a></li>
                <?php endwhile; ?>
            </ul>
            <a href="https://wa.me/<?= $store['whatsapp_number'] ?>" class="btn btn-success btn-sm" target="_blank">
                <i class="fab fa-whatsapp"></i> Chat
            </a>
        </div>
    </div>
</nav>