<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/">Typing Test</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="lessons.php">Lessons</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="premium-lessons.php">Premium Lessons</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php else: ?>
                    <?php if (!isset($_SESSION['is_premium'])): ?>
                        <li class="nav-item">
                            <a href="transaction" class="nav-link btn-premium">
                                <i class="fas fa-crown me-2"></i>Get Premium
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
.btn-premium {
    background: linear-gradient(45deg, #007bff, #00ff88);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    border: 2px solid;
    border-image: linear-gradient(45deg, #007bff, #00ff88) 1;
    margin-left: 15px;
    padding: 8px 20px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.btn-premium:hover {
    background: linear-gradient(45deg, #007bff, #00ff88);
    -webkit-text-fill-color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,123,255,0.3);
}
</style>