<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-keyboard me-2"></i>Boku no Typing
        </a>
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            <?php else: ?>
                <a class="nav-link" href="login.php">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            <?php endif; ?>
            <a class="nav-link" href="premium.php">
                <i class="fas fa-crown me-2"></i>Premium
            </a>
            <a class="nav-link" href="about.php">
                <i class="fas fa-info-circle me-2"></i>About Us
            </a>
        </div>
    </div>
</nav>