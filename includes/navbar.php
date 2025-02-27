<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-keyboard me-2"></i>Boku no Typing
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
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
    </div>
</nav>