<?php
// courses.php
require_once 'config/db.php';
include 'includes/header.php';

// Gérer l'inscription à un cours
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    if (isset($_POST['enroll_course_id'])) {
        $user_id = $_SESSION['user_id'];
        $course_id = $_POST['enroll_course_id'];

        // Vérifier si déjà inscrit
        $check_sql = "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([$user_id, $course_id]);
        
        if (!$check_stmt->fetch()) {
            // Si pas encore inscrit, créer l'inscription
            $enroll_sql = "INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)";
            $enroll_stmt = $conn->prepare($enroll_sql);
            $enroll_stmt->execute([$user_id, $course_id]);
            $success_message = "Inscription réussie !";
        } else {
            $error_message = "Vous êtes déjà inscrit à ce cours.";
        }
    }
    
    // Gérer l'ajout d'un cours (pour les enseignants)
    elseif ($_SESSION['role'] == 'teacher' && isset($_POST['title'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $teacher_id = $_SESSION['user_id'];

        $sql = "INSERT INTO courses (title, description, teacher_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$title, $description, $teacher_id]);
        $success_message = "Cours ajouté avec succès !";
    }
}

// Récupérer tous les cours
$sql = "SELECT c.*, u.username as teacher_name 
        FROM courses c 
        JOIN users u ON c.teacher_id = u.id";
$stmt = $conn->query($sql);
$courses = $stmt->fetchAll();

// Pour chaque cours, vérifier si l'utilisateur actuel est inscrit
$enrolled_courses = [];
if (isset($_SESSION['user_id'])) {
    $enrolled_sql = "SELECT course_id FROM enrollments WHERE user_id = ?";
    $enrolled_stmt = $conn->prepare($enrolled_sql);
    $enrolled_stmt->execute([$_SESSION['user_id']]);
    while ($row = $enrolled_stmt->fetch()) {
        $enrolled_courses[] = $row['course_id'];
    }
}
?>

<h2>Liste des cours</h2>

<?php if(isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if(isset($error_message)): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'teacher'): ?>
    <div class="mb-4">
        <h3>Ajouter un nouveau cours</h3>
        <form method="POST">
            <div class="mb-3">
                <label>Titre</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter le cours</button>
        </form>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach($courses as $course): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                    <p class="card-text"><small>Par: <?php echo htmlspecialchars($course['teacher_name']); ?></small></p>
                    
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'student'): ?>
                        <?php if(!in_array($course['id'], $enrolled_courses)): ?>
                            <form method="POST">
                                <input type="hidden" name="enroll_course_id" value="<?php echo $course['id']; ?>">
                                <button type="submit" class="btn btn-success">S'inscrire</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Déjà inscrit</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>