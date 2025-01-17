<?php
// profile.php
require_once 'config/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupérer les cours de l'utilisateur
if ($_SESSION['role'] == 'student') {
    $sql = "SELECT c.*, u.username as teacher_name 
            FROM courses c 
            JOIN enrollments e ON c.id = e.course_id 
            JOIN users u ON c.teacher_id = u.id 
            WHERE e.user_id = ?";
} else {
    $sql = "SELECT c.*, u.username as teacher_name 
            FROM courses c 
            JOIN users u ON c.teacher_id = u.id 
            WHERE c.teacher_id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION['user_id']]);
$courses = $stmt->fetchAll();
?>

<h2>Mon Profil</h2>
<p>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
<p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

<h3><?php echo $_SESSION['role'] == 'student' ? 'Mes cours suivis' : 'Mes cours créés'; ?></h3>
<div class="row">
    <?php foreach($courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                    <?php if($_SESSION['role'] == 'student'): ?>
                        <p class="card-text"><small>Par: <?php echo htmlspecialchars($course['teacher_name']); ?></small></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>