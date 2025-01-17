<?php
// register.php
require_once 'config/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    try {
        $stmt->execute([$username, $email, $password, $role]);
        header("Location: login.php");
        exit();
    } catch(PDOException $e) {
        $error = "Erreur d'inscription: " . $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Inscription</h2>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Rôle</label>
                <select name="role" class="form-control">
                    <option value="student">Étudiant</option>
                    <option value="teacher">Enseignant</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
    </div>
</div>