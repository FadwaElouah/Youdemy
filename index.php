<?php
session_start();
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Course.php';

$database = Database::getInstance();
$db = $database->connect();
$course = new Course($db);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$stmt = $db->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($search) {
    $courses = $course->searchCourses($search);
} elseif ($category) {
    $courses = $course->getCoursesByCategory($category, $page);
} else {
    $courses = $course->getAllCourses($page);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youdemy - Online Learning Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-pattern {
            background-color: #f5e6d3;
            background-image: radial-gradient(#d2b48c 0.5px, transparent 0.5px), radial-gradient(#d2b48c 0.5px, #f5e6d3 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }
        .gradient-text {
            background: linear-gradient(45deg, #8B4513, #D2691E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-overlay {
            background: linear-gradient(rgba(245, 230, 211, 0.8), rgba(245, 230, 211, 0.9));
        }
        .course-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="bg-[#F5F5DC] text-gray-800">
    <!-- Navigation -->
    <nav class="bg-white shadow-md w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-3">
                    <i class="fa-solid fa-school text-3xl text-brown-600"></i>
                        <span class="font-bold text-2xl gradient-text">Youdemy</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="pages/login.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                           
                            <span>Login</span>
                        </a>
                        <a href="pages/register.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                          
                            <span>Register</span>
                        </a>
                    <?php else: ?>
                        <a href="pages/dashboard.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                        
                            <span>Dashboard</span>
                        </a>
                        <a href="pages/logout.php" class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                          
                            <span>Logout</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Background Image -->
    <!-- <div class="relative h-[600px] overflow-hidden hero-pattern">
        <div class="absolute inset-0 hero-overlay flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 text-brown-800">
                    Transform Your Future with <span class="gradient-text">Youdemy</span>
                </h1>
                <p class="text-xl text-brown-600 max-w-2xl mx-auto mb-8">
                    Access world-class education from anywhere. Learn from industry experts and advance your career with our premium online courses.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="#courses" class="bg-brown-600 hover:bg-brown-700 text-white px-8 py-3 rounded-lg flex items-center space-x-2">
                        <i class="fas fa-play"></i>
                        <span>Start Learning</span>
                    </a>
                    <a href="#categories" class="bg-white hover:bg-beige-100 text-brown-600 px-8 py-3 rounded-lg flex items-center space-x-2 border border-brown-600">
                        <i class="fas fa-th"></i>
                        <span>Browse Categories</span>
                    </a>
                </div>
            </div>
        </div>
    </div> -->

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Search Form -->
        <form action="index.php" method="GET" class="mb-12  ml-[6rem]">
            <div class="flex gap-4 max-w-3xl mx-auto">
                <div class="flex-grow relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="search" placeholder="Search for courses..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class=" w-full pl-10 pr-4 py-2 bg-white border border-brown-300 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brown-500 focus:border-transparent">
                </div>
                <button type="submit" class="bg-brown-600 hover:bg-brown-700 text-black px-6 py-2 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-search"></i>
                    <span>Search</span>
                </button>
            </div>
        </form>

        <!-- Categories -->
        <div class="mb-12" id="categories">
            <h2 class="text-2xl font-bold mb-6 gradient-text">Categories</h2>
            <div class="flex flex-wrap gap-3">
                <a href="index.php" class="bg-white hover:bg-beige-100 text-brown-600 px-4 py-2 rounded-lg transition duration-200 border border-brown-600">
                    All Courses
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="index.php?category=<?php echo urlencode($cat['id']); ?>" 
                       class="<?php echo $category == $cat['id'] ? 'bg-brown-600 text-white' : 'bg-white text-brown-600 hover:bg-beige-100'; ?> px-4 py-2 rounded-lg transition duration-200 border border-brown-600">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($courses)): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8 rounded-lg" role="alert">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-exclamation-circle text-yellow-500"></i>
                    <p>No courses found. Try a different search term or category.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="courses">
            <?php foreach($courses as $course): ?>
                <div class="bg-white rounded-xl overflow-hidden transform hover:scale-105 transition duration-200 shadow-lg">
                    <img src="images/education.jpg" alt="<?php echo htmlspecialchars($course['title']); ?>" class="course-image">
                    <div class="p-6">
                        <div class="flex items-center space-x-2 mb-4">
                            <!-- <i class="fas fa-book text-brown-600"></i> -->
                            <span class="text-sm text-gray-600">Course</span>
                        </div>
                        <h2 class="text-xl font-bold mb-3 text-gray-800"><?php echo htmlspecialchars($course['title']); ?></h2>
                        <p class="text-gray-600 mb-4 text-sm"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-2">
                                <!-- <i class="fas fa-user-tie text-gray-600"></i> -->
                                <span class="text-sm text-gray-600"><?php echo htmlspecialchars($course['teacher_name']); ?></span>
                            </div>
                            <a href="pages/course.php?id=<?php echo $course['id']; ?>" 
                               class="bg-brown-600 hover:bg-brown-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                                <span>View Course</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (!$search && !$category): ?>
            <div class="mt-12 flex justify-center space-x-4">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" 
                       class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                        <i class="fas fa-chevron-left"></i>
                        <span>Previous</span>
                    </a>
                <?php endif; ?>
                <a href="?page=<?php echo $page + 1; ?>" 
                   class="bg-[#A0522D] text-white hover:bg-[#8B4513]  px-4 py-2 rounded-lg flex items-center space-x-2">
                    <span>Next</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
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