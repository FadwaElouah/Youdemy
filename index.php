<?php
// index.php
require_once 'config/db.php';
include 'includes/header.php';

// Récupérer les statistiques générales
$stats = $conn->query("
    SELECT 
        COUNT(DISTINCT c.id) as total_courses,
        COUNT(DISTINCT e.user_id) as total_students,
        COUNT(DISTINCT c.teacher_id) as total_teachers
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
")->fetch();

// Récupérer les derniers cours ajoutés
$latest_courses = $conn->query("
    SELECT c.*, u.username as teacher_name, cat.name as category_name
    FROM courses c
    JOIN users u ON c.teacher_id = u.id
    LEFT JOIN categories cat ON c.category_id = cat.id
    WHERE c.status = 'published'
    ORDER BY c.created_at DESC
    LIMIT 6
")->fetchAll();
?>

<div class="jumbotron">
    <h1>Bienvenue sur notre plateforme E-Learning</h1>
    <p class="lead">Apprenez à votre rythme avec nos cours en ligne de qualité</p>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3><?php echo $stats['total_courses']; ?></h3>
                <p>Cours disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3><?php echo $stats['total_students']; ?></h3>
                <p>Étudiants inscrits</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h3><?php echo $stats['total_teachers']; ?></h3>
                <p>Enseignants actifs</p>
            </div>
        </div>
    </div>
</div>

<h2>Derniers cours ajoutés</h2>
<div class="row">
    <?php foreach($latest_courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                    <p class="card-text">
                        <small class="text-muted">
                            Catégorie: <?php echo htmlspecialchars($course['category_name']); ?><br>
                            Par: <?php echo htmlspecialchars($course['teacher_name']); ?>
                        </small>
                    </p>
                    <a href="courses.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">Voir le cours</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>