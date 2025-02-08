<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php"><i class="fas fa-users me-2"></i>Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_lessons.php"><i class="fas fa-book me-2"></i>Lessons</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="transactions.php"><i class="fas fa-money-bill-wave me-2"></i>Transactions</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>