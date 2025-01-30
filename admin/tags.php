<?php
require_once '../config/config.php';
require_once '../classes/Admin.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$database = Database::getInstance();
$db = $database->connect();
$admin = new Admin($db);

$error = '';
$success = '';

// Handle tag addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tags'])) {
        $tags = array_map('trim', explode(',', $_POST['tags']));
        $tags = array_filter($tags); // Remove empty values
        
        try {
            $db->beginTransaction();
            
            foreach ($tags as $tag) {
                $stmt = $db->prepare("INSERT IGNORE INTO tags (name) VALUES (:name)");
                $stmt->execute(['name' => $tag]);
            }
            
            $db->commit();
            $success = 'Tags added successfully!';
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Error adding tags';
        }
    }
}

// Handle tag deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM tags WHERE id = :id");
    if ($stmt->execute(['id' => $delete_id])) {
        $success = 'Tag deleted successfully!';
    } else {
        $error = 'Failed to delete tag.';
    }
}

// Get all tags
$stmt = $db->query("SELECT * FROM tags ORDER BY name");
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tags - Youdemy</title>
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
        .tag {
            background: rgba(210, 180, 140, 0.1);
            border: 1px solid rgba(210, 180, 140, 0.2);
            transition: background 0.2s ease-in-out;
        }
        .tag:hover {
            background: rgba(210, 180, 140, 0.2);
        }
    </style>
</head>
<body class="bg-[#F5F5DC] text-gray-800 ">
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

    <!-- Manage Tags Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 gradient-text">Manage Tags</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Add Multiple Tags Form -->
        <div class="dashboard-card p-6 rounded-lg mb-8">
            <h2 class="text-xl font-semibold mb-4 flex items-center space-x-2">
                <i class="fas fa-tags text-brown-600"></i>
                <span>Add Multiple Tags</span>
            </h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tags">
                        Enter tags (comma-separated)
                    </label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-brown-500 focus:border-transparent"
                        id="tags"
                        name="tags"
                        rows="3"
                        placeholder="web development, javascript, react"
                        required
                    ></textarea>
                </div>
                <button type="submit" class="bg-brown-600 hover:bg-brown-700 text-black px-4 py-2 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Add Tags</span>
                </button>
            </form>
        </div>

        <!-- Existing Tags List -->
        <div class="dashboard-card p-6 rounded-lg">
            <h2 class="text-xl font-semibold mb-4 flex items-center space-x-2">
                <i class="fas fa-list text-brown-600"></i>
                <span>Existing Tags</span>
            </h2>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($tags as $tag): ?>
                    <div class="tag p-2 rounded-lg flex items-center space-x-2">
                        <span class="text-gray-800"><?php echo htmlspecialchars($tag['name']); ?></span>
                        <a href="?delete_id=<?php echo $tag['id']; ?>" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
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