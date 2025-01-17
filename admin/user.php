<?php
// admin/users.php
require_once '../config/db.php';
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Gérer les actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $user_id = $_POST['user_id'];
        
        switch ($_POST['action']) {
            case 'activate':
                $sql = "UPDATE users SET status = 'active' WHERE id = ?";
                break;
            case 'block':
                $sql = "UPDATE users SET status = 'blocked' WHERE id = ?";
                break;
            case 'delete':
                $sql = "DELETE FROM users WHERE id = ?";
                break;
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
    }
}

// Récupérer la liste des utilisateurs
$users = $conn->query("
    SELECT id, username, email, role, status, created_at,
        (SELECT COUNT(*) FROM enrollments WHERE user_id = users.id) as course_count
    FROM users
    ORDER BY created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="users.php">Utilisateurs</a>
                <a class="nav-link" href="courses.php">Cours</a>
                <a class="nav-link" href="../logout.php">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Gestion des Utilisateurs</h1>
        
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Status</th>
                    <th>Cours</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $user['status'] == 'active' ? 'success' : 
                                    ($user['status'] == 'pending' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo htmlspecialchars($user['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['course_count']; ?></td>
                        <td><?php echo $user['created_at']; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                
                                <?php if($user['status'] !== 'active'): ?>
                                    <button type="submit" name="action" value="activate" class="btn btn-sm btn-success">
                                        Activer
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($user['status'] !== 'blocked'): ?>
                                    <button type="submit" name="action" value="block" class="btn btn-sm btn-warning">
                                        Bloquer
                                    </button>
                                <?php endif; ?>
                                
                                <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>