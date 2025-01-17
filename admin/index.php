<?php
// admin/index.php
require_once '../config/db.php';
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Récupérer les statistiques
$stats = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'student') as total_students,
        (SELECT COUNT(*) FROM users WHERE role = 'teacher') as total_teachers,
        (SELECT COUNT(*) FROM courses) as total_courses,
        (SELECT COUNT(*) FROM enrollments) as total_enrollments
")->fetch();

// Récupérer les dernières activités
$recent_activities = $conn->query("
    SELECT u.username, c.title, e.enrollment_date
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    ORDER BY e.enrollment_date DESC
    LIMIT 10
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <div class="navbar-nav">
                <a class="nav-link" href="users.php">Utilisateurs</a>
                <a class="nav-link" href="courses.php">Cours</a>
                <a class="nav-link" href="../logout.php">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Tableau de bord administrateur</h1>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3><?php echo $stats['total_students']; ?></h3>
                        <p>Étudiants</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3><?php echo $stats['total_teachers']; ?></h3>
                        <p>Enseignants</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3><?php echo $stats['total_courses']; ?></h3>
                        <p>Cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3><?php echo $stats['total_enrollments']; ?></h3>
                        <p>Inscriptions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h2>Activités récentes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Cours</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_activities as $activity): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['username']); ?></td>
                            <td><?php echo htmlspecialchars($activity['title']); ?></td>
                            <td><?php echo $activity['enrollment_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>