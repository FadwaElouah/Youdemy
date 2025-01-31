<?php
session_start();

require_once '../config/config.php';
require_once '../classes/User.php';
require_once '../classes/Course.php';
require_once '../classes/Admin.php';
require_once '../classes/Teacher.php';
require_once '../classes/Student.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = Database::getInstance();
$db = $database->connect();

switch ($_SESSION['role']) {
    case 'admin':
        $user = new Admin($db);
        break;
    case 'teacher':
        $user = new Teacher($db);
        break;
    case 'student':
        $user = new Student($db);
        break;
    default:
        header('Location: logout.php');
        exit;
}

$dashboardInfo = $user->getDashboardInfo();

$course = new Course($db);

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-text {
            background: linear-gradient(45deg, #8B4513, #D2691E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .dashboard-card {
            background: linear-gradient(145deg, rgba(255, 248, 220, 0.1), rgba(210, 180, 140, 0.1));
            border: 1px solid rgba(210, 180, 140, 0.2);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
                    <a href="logout.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                      
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 py-8 bg-[#F5F5DC]">
        <h1 class="text-3xl font-bold mb-8 gradient-text">Dashboard</h1>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">  
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-users text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">User Management</h2>
                    </div>
                    <a href="../admin/users.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>Manage Users</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-tags text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Category Management</h2>
                    </div>
                    <a href="../admin/categories.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>Manage Categories</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-tag text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Tags Management</h2>
                    </div>
                    <a href="../admin/tags.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>Manage Tags</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-chart-bar text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Statistics Management</h2>
                    </div>
                    <a href="../admin/statistics.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>View Statistics</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-check-circle text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Course Approval</h2>
                    </div>
                    <a href="../admin/approve-courses.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>Approve Courses</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        <?php elseif ($role === 'teacher'): ?>
            <!-- Teacher Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-book text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">My Courses</h2>
                    </div>
                    <a href="../teacher/courses.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>View My Courses</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-plus-circle text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Create New Course</h2>
                    </div>
                    <a href="../teacher/create-course.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>Create Course</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-chart-line text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Course Statistics</h2>
                    </div>
                    <a href="../teacher/statistics.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>View Statistics</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- Student Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-book text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">My Courses</h2>
                    </div>
                    <a href="../student/my-courses.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>View My Courses</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="dashboard-card p-6 rounded-lg">
                    <div class="flex items-center space-x-4 mb-4">
                        <i class="fas fa-search text-3xl text-brown-600"></i>
                        <h2 class="text-xl font-semibold text-gray-800">Discover Courses</h2>
                    </div>
                    <a href="courses.php" class="text-brown-600 hover:text-brown-800 flex items-center space-x-2">
                        <span>Discover More</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
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