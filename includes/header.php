<?php
// includes/header.php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Learning Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Learning</a>
            <div class="navbar-nav">
                <a class="nav-link" href="courses.php">Cours</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="profile.php">Profile</a>
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Connexion</a>
                    <a class="nav-link" href="register.php">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>