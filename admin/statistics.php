<?php
require_once '../config/config.php';
require_once '../classes/Admin.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit;
}

$database = Database::getInstance();
$db = $database->connect();
$admin = new Admin($db);

$stats = $admin->getStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Youdemy</title>
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
<body class="bg-[#F5F5DC] text-gray-800">
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

    <!-- Statistics Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 gradient-text">Platform Statistics</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Total Courses -->
            <div class="dashboard-card p-6 rounded-lg">
                <div class="flex items-center space-x-4 mb-4">
                    <i class="fas fa-book text-3xl text-brown-600"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Total Courses</h2>
                </div>
                <p class="text-3xl font-bold text-brown-600"><?php echo $stats['total_courses']; ?></p>
            </div>

            <!-- Most Popular Course -->
            <div class="dashboard-card p-6 rounded-lg">
                <div class="flex items-center space-x-4 mb-4">
                    <i class="fas fa-star text-3xl text-brown-600"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Most Popular Course</h2>
                </div>
                <?php if ($stats['most_popular_course']): ?>
                    <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($stats['most_popular_course']['title']); ?></p>
                    <p class="text-gray-600"><?php echo $stats['most_popular_course']['enrollments']; ?> enrollments</p>
                <?php else: ?>
                    <p class="text-gray-600">No enrollments yet</p>
                <?php endif; ?>
            </div>

            <!-- Courses by Category -->
            <div class="dashboard-card p-6 rounded-lg">
                <div class="flex items-center space-x-4 mb-4">
                    <i class="fas fa-tags text-3xl text-brown-600"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Courses by Category</h2>
                </div>
                <div class="space-y-2">
                    <?php foreach ($stats['courses_by_category'] as $category): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-800"><?php echo htmlspecialchars($category['name']); ?></span>
                            <span class="font-medium text-gray-800"><?php echo $category['count']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Top Teachers -->
            <div class="dashboard-card p-6 rounded-lg">
                <div class="flex items-center space-x-4 mb-4">
                    <i class="fas fa-chalkboard-teacher text-3xl text-brown-600"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Top Teachers</h2>
                </div>
                <div class="space-y-4">
                    <?php foreach ($stats['top_teachers'] as $index => $teacher): ?>
                        <div class="flex items-center">
                            <span class="text-lg font-bold mr-4 text-brown-600">#<?php echo $index + 1; ?></span>
                            <div>
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($teacher['username']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo $teacher['course_count']; ?> courses</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
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