<div class="col-md-3 col-lg-2 bg-dark text-white vh-100 p-3 sidebar">
    <h5 class="text-center mb-4"><i class="fas fa-store"></i> MandiGateway</h5>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="products.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Products
            </a>
        </li>
        <li class="nav-item">
            <a href="categories.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i> Categories
            </a>
        </li>
        <li class="nav-item">
            <a href="sliders.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sliders.php' ? 'active' : '' ?>">
                <i class="fas fa-images"></i> Sliders
            </a>
        </li>
        <li class="nav-item">
            <a href="pages.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pages.php' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> Pages
            </a>
        </li>
        <li class="nav-item">
            <a href="posts.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'posts.php' ? 'active' : '' ?>">
                <i class="fas fa-blog"></i> Blog Posts
            </a>
        </li>
        <li class="nav-item">
            <a href="orders.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
        </li>
        <li class="nav-item">
            <a href="reviews.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Reviews
            </a>
        </li>
        <li class="nav-item">
            <a href="settings.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Store Settings
            </a>
        </li>
        <li class="nav-item">
            <a href="verify.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'verify.php' ? 'active' : '' ?>">
                <i class="fas fa-id-card"></i> Verification
            </a>
        </li>
        <li class="nav-item">
            <a href="view-website.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'view-website.php' ? 'active' : '' ?>">
                <i class="fas fa-globe"></i> View Website
            </a>
        </li>
        <li class="nav-item">
            <a href="chat.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : '' ?>">
                <i class="fas fa-comments"></i> Live Chat
            </a>
        </li>
        <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>