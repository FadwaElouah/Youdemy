<?php
require_once '../config/config.php';
require_once '../classes/User.php'; 
require_once '../classes/Student.php'; 
require_once '../classes/Teacher.php'; 
require_once '../classes/Admin.php'; 

session_start();
$database = Database::getInstance();
$db = $database->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $query = "SELECT id, role, password FROM users WHERE email = :email AND is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $row['password'])) {
            $role = $row['role'];
            switch ($role) {
                case 'student':
                    $user = new Student($db);
                    break;
                case 'teacher':
                    $user = new Teacher($db);
                    break;
                case 'admin':
                    $user = new Admin($db);
                    break;
                default:
                    $error = 'Invalid role';
                    break;
            }

            if (isset($user)) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $role;
                header('Location: dashboard.php');
                exit;
            }
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = 'Invalid email or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-text {
            background: linear-gradient(45deg, #8B4513, #D2691E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-beige-100 text-gray-800">
    <!-- Navigation -->
    <nav class="bg-white shadow-md w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="../index.php" class="flex items-center space-x-3">
                    <i class="fa-solid fa-school text-3xl text-brown-600"></i>
                        <span class="font-bold text-2xl gradient-text">Youdemy</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                        
                            <span>Login</span>
                        </a>
                        <a href="register.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                        
                            <span>Register</span>
                        </a>
                    <?php else: ?>
                        <a href="dashboard.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                          
                            <span>Dashboard</span>
                        </a>
                        <a href="logout.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                         
                            <span>Logout</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="min-h-screen flex items-center justify-center bg-[#F5F5DC]">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center gradient-text">Login to Youdemy</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-brown-500 focus:border-transparent"
                           id="email" type="email" name="email" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-brown-500 focus:border-transparent"
                           id="password" type="password" name="password" required>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-[#A0522D] text-white hover:bg-[#8B4513] font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-brown-500 focus:border-transparent w-full"
                            type="submit">
                        Sign In
                    </button>
                </div>
            </form>
            <p class="text-center mt-4 text-gray-600">
                Don't have an account? <a href="register.php" class="text-brown-600 hover:text-brown-800">Register</a>
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-beige-100 py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Section -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-black">About Youdemy</h3>
                    <p class="text-sm text-beige-200">
                        Youdemy is an online learning platform dedicated to providing high-quality courses from industry experts. Transform your future with our comprehensive learning resources.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-black">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-beige-200 hover:text-white transition duration-200">Home</a></li>
                        <li><a href="#courses" class="text-beige-200 hover:text-white transition duration-200">Courses</a></li>
                        <li><a href="#categories" class="text-beige-200 hover:text-white transition duration-200">Categories</a></li>
                        <li><a href="pages/login.php" class="text-beige-200 hover:text-white transition duration-200">Login</a></li>
                        <li><a href="pages/register.php" class="text-beige-200 hover:text-white transition duration-200">Register</a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-black">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-beige-200 hover:text-white transition duration-200">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-beige-200 hover:text-white transition duration-200">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-beige-200 hover:text-white transition duration-200">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-beige-200 hover:text-white transition duration-200">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-beige-200 hover:text-white transition duration-200">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-black mt-8 pt-8 text-center">
                <p class="text-sm text-beige-200">
                    &copy; <?php echo date("Y"); ?> Youdemy. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>