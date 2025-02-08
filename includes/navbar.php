<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-keyboard me-2"></i>Boku no Typing
        </a>
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link d-flex align-items-center" href="profile.php">
                    <?php if (isset($_SESSION['profile_image']) && $_SESSION['profile_image']): ?>
                        <img src="uploads/profile_images/<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" 
                             class="rounded-circle me-2" 
                             width="30" 
                             height="30" 
                             alt="Profile">
                    <?php else: ?>
                        <i class="fas fa-user-circle me-2"></i>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </a>
                <a class="nav-link" href="check_premium_access.php">Premium Lessons</a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            <?php else: ?>
                <a class="nav-link" href="login.php">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>